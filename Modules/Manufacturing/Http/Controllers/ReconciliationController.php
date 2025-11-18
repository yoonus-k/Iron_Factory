<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\MaterialMovement;
use App\Models\PurchaseInvoice;
use App\Models\ReconciliationLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconciliationController extends Controller
{
    /**
     * Show pending reconciliations
     */
    public function index(Request $request)
    {
        $query = DeliveryNote::where('type', 'incoming')
            ->where('purchase_invoice_id', '!=', null)
            ->whereIn('reconciliation_status', ['pending', 'discrepancy']);

        // فلتر حسب المورد
        if ($request->supplier_id) {
            $query->whereHas('supplier', function ($q) {
                $q->where('id', request('supplier_id'));
            });
        }

        // فلتر حسب التاريخ
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $pending = $query->with([
            'supplier',
            'purchaseInvoice',
            'purchaseInvoice.supplier',
            'registrationLogs',
        ])->orderBy('created_at', 'desc')->paginate(15);

        // احسب الإحصائيات
        $stats = [
            'total_pending' => DeliveryNote::where('reconciliation_status', 'pending')->count(),
            'total_discrepancy' => DeliveryNote::where('reconciliation_status', 'discrepancy')->count(),
            'total_matched' => DeliveryNote::where('reconciliation_status', 'matched')->count(),
            'total_adjusted' => DeliveryNote::where('reconciliation_status', 'adjusted')->count(),
            'total_rejected' => DeliveryNote::where('reconciliation_status', 'rejected')->count(),
        ];

        return view('manufacturing::warehouses.reconciliation.index', compact('pending', 'stats'));
    }

    /**
     * Show reconciliation panel for a specific delivery note
     */
    public function show(DeliveryNote $deliveryNote)
    {
        if (!$deliveryNote->purchaseInvoice) {
            return back()->with('error', 'هذه التسليمة لا تملك فاتورة مرتبطة بها');
        }

        $deliveryNote->load([
            'purchaseInvoice',
            'purchaseInvoice.supplier',
            'supplier',
            'registrationLogs',
            'reconciliationLogs',
        ]);

        $canReconcile = $deliveryNote->reconciliation_status !== 'matched' &&
                       $deliveryNote->reconciliation_status !== 'adjusted' &&
                       $deliveryNote->reconciliation_status !== 'rejected' &&
                       !$deliveryNote->is_locked;

        return view('manufacturing::warehouses.reconciliation.show', compact(
            'deliveryNote',
            'canReconcile'
        ));
    }

    /**
     * Link invoice to delivery note
     */
    public function link(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'invoice_weight' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $invoice = PurchaseInvoice::find($validated['purchase_invoice_id']);

            // تحديث البيانات
            $deliveryNote->update([
                'purchase_invoice_id' => $validated['purchase_invoice_id'],
                'invoice_weight' => $validated['invoice_weight'],
                'invoice_date' => $invoice->invoice_date,
            ]);

            // حساب الفرق
            $discrepancy = $deliveryNote->getDiscrepancy();
            $discrepancyPercentage = $deliveryNote->getDiscrepancyPercentage();

            // تحديد الحالة بناءً على الفرق
            if (abs($discrepancyPercentage) <= 1) {
                // فرق صغير جداً - اقبله تلقائياً
                $deliveryNote->update(['reconciliation_status' => 'matched']);
            } else if (abs($discrepancyPercentage) <= 5) {
                // فرق معقول - احتاج موافقة
                $deliveryNote->update(['reconciliation_status' => 'discrepancy']);
            } else {
                // فرق كبير - لازم يراجع
                $deliveryNote->update(['reconciliation_status' => 'discrepancy']);
            }

            // إنشاء سجل التسوية
            ReconciliationLog::create([
                'delivery_note_id' => $deliveryNote->id,
                'purchase_invoice_id' => $validated['purchase_invoice_id'],
                'actual_weight' => $deliveryNote->actual_weight,
                'invoice_weight' => $validated['invoice_weight'],
                'discrepancy' => $discrepancy,
                'discrepancy_percentage' => $discrepancyPercentage,
                'financial_impact' => $discrepancy * 50, // مثال: 50 ريال/كيلو
                'reconciliation_status' => $deliveryNote->reconciliation_status,
                'action' => 'pending',
                'created_by' => Auth::id(),
                'decided_by' => Auth::id(),
            ]);

            DB::commit();

            $message = $deliveryNote->reconciliation_status === 'matched' ?
                'تم ربط الفاتورة بنجاح! الأوزان متطابقة.' :
                'تم ربط الفاتورة. يوجد فرق يحتاج موافقة';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Make reconciliation decision
     */
    public function decide(Request $request, DeliveryNote $deliveryNote)
    {
        if (!in_array($deliveryNote->reconciliation_status, ['pending', 'discrepancy'])) {
            return back()->with('error', 'هذه التسليمة تم تسويتها بالفعل');
        }

        $validated = $request->validate([
            'action' => 'required|in:accepted,rejected,adjusted',
            'reason' => 'required|string|max:255',
            'comments' => 'nullable|string|max:1000',
            'adjusted_weight' => 'nullable|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // تحديث حالة التسليمة
            $newStatus = match($validated['action']) {
                'accepted' => 'adjusted',
                'rejected' => 'rejected',
                'adjusted' => 'adjusted',
                default => 'discrepancy'
            };

            if ($validated['action'] === 'adjusted' && $validated['adjusted_weight']) {
                $deliveryNote->update([
                    'actual_weight' => $validated['adjusted_weight'],
                ]);
            }

            $deliveryNote->update([
                'reconciliation_status' => $newStatus,
                'reconciliation_notes' => $validated['comments'] ?? null,
                'reconciled_by' => Auth::id(),
                'reconciled_at' => now(),
            ]);

            // تحديث سجل التسوية
            $log = $deliveryNote->reconciliationLogs()->latest()->first();
            if ($log) {
                $log->update([
                    'action' => $validated['action'],
                    'reason' => $validated['reason'],
                    'comments' => $validated['comments'] ?? null,
                    'decided_by' => Auth::id(),
                    'decided_at' => now(),
                ]);
            }

            // إذا تم الرفض، حدّث حالة الفاتورة أيضاً
            if ($validated['action'] === 'rejected') {
                $deliveryNote->purchaseInvoice->update([
                    'status' => 'rejected',
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم حفظ القرار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Show reconciliation history
     */
    public function history(Request $request)
    {
        $query = DeliveryNote::where('type', 'incoming')
            ->where('purchase_invoice_id', '!=', null)
            ->whereIn('reconciliation_status', ['matched', 'adjusted', 'rejected']);

        // فلتر حسب المورد
        if ($request->supplier_id) {
            $query->whereHas('supplier', function ($q) {
                $q->where('id', request('supplier_id'));
            });
        }

        // فلتر حسب التاريخ
        if ($request->from_date) {
            $query->whereDate('reconciled_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('reconciled_at', '<=', $request->to_date);
        }

        // فلتر حسب الحالة
        if ($request->status) {
            $query->where('reconciliation_status', $request->status);
        }

        $reconciliations = $query->with([
            'supplier',
            'purchaseInvoice',
            'reconciledBy',
            'reconciliationLogs',
        ])->orderBy('reconciled_at', 'desc')->paginate(15);

        // إحصائيات التسويات المكتملة
        $stats = [
            'matched' => DeliveryNote::where('reconciliation_status', 'matched')->count(),
            'adjusted' => DeliveryNote::where('reconciliation_status', 'adjusted')->count(),
            'rejected' => DeliveryNote::where('reconciliation_status', 'rejected')->count(),
        ];

        // جلب قائمة الموردين للفلتر
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();

        return view('manufacturing::warehouses.reconciliation.history', compact('reconciliations', 'stats', 'suppliers'));
    }

    /**
     * Get supplier performance report
     */
    public function supplierReport(Request $request)
    {
        // جلب جميع الموردين مع علاقاتهم
        $suppliers = \App\Models\Supplier::with(['deliveryNotes' => function ($q) {
            $q->where('type', 'incoming')->whereNotNull('reconciliation_status');
        }])->get();

        // حساب إحصائيات إجمالية
        $totalShipments = 0;
        $totalDiscrepancy = 0;
        $accuracySum = 0;
        $suppliersWithData = 0;

        foreach ($suppliers as $supplier) {
            $deliveries = $supplier->deliveryNotes;
            $total = $deliveries->count();

            if ($total > 0) {
                $totalShipments += $total;
                $matched = $deliveries->where('reconciliation_status', 'matched')->count();
                $adjusted = $deliveries->where('reconciliation_status', 'adjusted')->count();
                $accuracy = (($matched + $adjusted) / $total) * 100;
                $accuracySum += $accuracy;
                $suppliersWithData++;
                $totalDiscrepancy += $deliveries->sum('weight_discrepancy') ?? 0;
            }
        }

        $averageAccuracy = $suppliersWithData > 0 ? ($accuracySum / $suppliersWithData) : 0;

        return view('manufacturing::warehouses.reconciliation.supplier-report', compact(
            'suppliers',
            'totalShipments',
            'totalDiscrepancy',
            'averageAccuracy'
        ));
    }

    /**
     * ✅ جديد: عرض صفحة ربط الفواتير المتأخرة
     */
    public function showLinkInvoice()
    {
        // جلب الأذونات التي تم تسجيلها ولكن لا تملك فاتورة
        $deliveryNotes = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', 'registered')
            ->whereNull('purchase_invoice_id')
            ->with(['supplier', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('manufacturing::warehouses.reconciliation.link-invoice', compact('deliveryNotes'));
    }

    /**
     * ✅ جديد: معالجة ربط الفاتورة المتأخرة
     */
    public function storeLinkInvoice(Request $request)
    {
        $validated = $request->validate([
            'delivery_note_id' => 'required|exists:delivery_notes,id',
            'invoice_number' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            'invoice_weight' => 'required|numeric|min:0.01',
            'invoice_reference_number' => 'nullable|string|max:100',
            'reconciliation_notes' => 'nullable|string|max:1000',
        ], [
            'delivery_note_id.required' => 'يجب اختيار أذن التسليم',
            'delivery_note_id.exists' => 'أذن التسليم المختارة غير موجودة',
            'invoice_number.required' => 'رقم الفاتورة مطلوب',
            'invoice_date.required' => 'تاريخ الفاتورة مطلوب',
            'invoice_date.date' => 'تاريخ الفاتورة غير صحيح',
            'invoice_weight.required' => 'وزن الفاتورة مطلوب',
            'invoice_weight.numeric' => 'وزن الفاتورة يجب أن يكون رقماً',
            'invoice_weight.min' => 'وزن الفاتورة يجب أن يكون أكبر من صفر',
        ]);

        try {
            DB::beginTransaction();

            $deliveryNote = DeliveryNote::findOrFail($validated['delivery_note_id']);

            // التحقق من أن الأذن لا تملك فاتورة بالفعل
            if ($deliveryNote->purchase_invoice_id) {
                return back()->with('error', 'هذه الأذن مربوطة بفاتورة بالفعل');
            }

            // إنشاء فاتورة جديدة أو البحث عن موجودة
            $invoice = PurchaseInvoice::firstOrCreate(
                ['invoice_number' => $validated['invoice_number']],
                [
                    'supplier_id' => $deliveryNote->supplier_id,
                    'invoice_date' => $validated['invoice_date'],
                    'total_amount' => 0, // سيتم تحديثه لاحقاً
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]
            );

            // ربط الفاتورة بالأذن
            $deliveryNote->update([
                'purchase_invoice_id' => $invoice->id,
                'invoice_weight' => $validated['invoice_weight'],
                'invoice_date' => $validated['invoice_date'],
                'invoice_reference_number' => $validated['invoice_reference_number'],
                'reconciliation_notes' => $validated['reconciliation_notes'],
            ]);

            // حساب الفرق
            $actualWeight = $deliveryNote->actual_weight ?? 0;
            $invoiceWeight = $validated['invoice_weight'];
            $discrepancy = $actualWeight - $invoiceWeight;
            $discrepancyPercentage = $invoiceWeight > 0 ? (($discrepancy / $invoiceWeight) * 100) : 0;

            // تحديد حالة التسوية بناءً على الفرق
            if (abs($discrepancyPercentage) <= 1) {
                // فرق صغير جداً (أقل من 1%) - قبول تلقائي
                $reconciliationStatus = 'matched';
                $message = 'تم ربط الفاتورة بنجاح! الأوزان متطابقة تقريباً (فرق ' . number_format($discrepancyPercentage, 2) . '%)';
            } else if (abs($discrepancyPercentage) <= 5) {
                // فرق مقبول (1-5%) - يحتاج مراجعة
                $reconciliationStatus = 'discrepancy';
                $message = 'تم ربط الفاتورة. يوجد فرق مقبول (' . number_format($discrepancyPercentage, 2) . '%) يحتاج موافقة';
            } else {
                // فرق كبير (أكثر من 5%) - يحتاج تدقيق
                $reconciliationStatus = 'discrepancy';
                $message = 'تم ربط الفاتورة. يوجد فرق كبير (' . number_format($discrepancyPercentage, 2) . '%) يحتاج مراجعة دقيقة';
            }

            $deliveryNote->update([
                'reconciliation_status' => $reconciliationStatus,
            ]);

            // إنشاء سجل التسوية
            $reconciliationLog = ReconciliationLog::create([
                'delivery_note_id' => $deliveryNote->id,
                'purchase_invoice_id' => $invoice->id,
                'actual_weight' => $actualWeight,
                'invoice_weight' => $invoiceWeight,
                'discrepancy' => $discrepancy,
                'discrepancy_percentage' => $discrepancyPercentage,
                'reconciliation_status' => $reconciliationStatus,
                'notes' => $validated['reconciliation_notes'],
                'created_by' => Auth::id(),
                'decided_by' => Auth::id(),
                'decided_at' => now(),
                'action' => $reconciliationStatus === 'matched' ? 'accepted' : 'pending',
                'created_at' => now(),
            ]);

            // ✅ تسجيل حركة التسوية
            if ($discrepancy != 0) {
                MaterialMovement::create([
                    'movement_number' => MaterialMovement::generateMovementNumber(),
                    'movement_type' => $discrepancy > 0 ? 'adjustment' : 'reconciliation',
                    'source' => 'reconciliation',
                    'delivery_note_id' => $deliveryNote->id,
                    'reconciliation_log_id' => $reconciliationLog->id,
                    'material_detail_id' => $deliveryNote->material_detail_id,
                    'material_id' => $deliveryNote->material_id,
                    'unit_id' => $deliveryNote->materialDetail->unit_id ?? null,
                    'quantity' => abs($discrepancy),
                    'from_warehouse_id' => $deliveryNote->warehouse_id,
                    'supplier_id' => $deliveryNote->supplier_id,
                    'description' => 'تسوية فاتورة - أذن رقم ' . ($deliveryNote->note_number ?? $deliveryNote->id) . ' | فرق: ' . number_format($discrepancy, 2) . ' كيلو',
                    'notes' => $validated['reconciliation_notes'] ?? 'فرق بين الوزن الفعلي ووزن الفاتورة',
                    'reference_number' => $invoice->invoice_number,
                    'created_by' => Auth::id(),
                    'movement_date' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'status' => 'completed',
                ]);
            }

            DB::commit();

            return redirect()->route('manufacturing.warehouses.reconciliation.show', $deliveryNote)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to link invoice: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء ربط الفاتورة: ' . $e->getMessage());
        }
    }
}
