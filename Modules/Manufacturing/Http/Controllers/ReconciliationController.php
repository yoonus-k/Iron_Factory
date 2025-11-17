<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\PurchaseInvoice;
use App\Models\ReconciliationLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'financial_impact' => $discrepancy * 50, // مثال: 50 ريال/كيلو
                'action' => 'pending',
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

        $completed = $query->with([
            'supplier',
            'purchaseInvoice',
            'reconciledBy',
            'reconciliationLogs',
        ])->orderBy('reconciled_at', 'desc')->paginate(15);

        return view('manufacturing::warehouses.reconciliation.history', compact('completed'));
    }

    /**
     * Get supplier performance report
     */
    public function supplierReport(Request $request)
    {
        $suppliers = \App\Models\Supplier::with(['deliveryNotes' => function ($q) {
            $q->whereNotNull('reconciliation_status');
        }])->get();

        $report = $suppliers->map(function ($supplier) {
            $deliveries = $supplier->deliveryNotes()->where('type', 'incoming')->get();

            $total = $deliveries->count();
            $matched = $deliveries->where('reconciliation_status', 'matched')->count();
            $discrepancies = $deliveries->where('reconciliation_status', 'discrepancy')->count();
            $adjusted = $deliveries->where('reconciliation_status', 'adjusted')->count();
            $rejected = $deliveries->where('reconciliation_status', 'rejected')->count();

            $totalDiscrepancy = $deliveries->sum('weight_discrepancy');
            $avgDiscrepancy = $total > 0 ? $deliveries->avg('discrepancy_percentage') : 0;

            return [
                'supplier' => $supplier,
                'total' => $total,
                'matched' => $matched,
                'discrepancies' => $discrepancies,
                'adjusted' => $adjusted,
                'rejected' => $rejected,
                'accuracy' => $total > 0 ? (($matched + $adjusted) / $total) * 100 : 0,
                'total_discrepancy' => $totalDiscrepancy,
                'avg_discrepancy' => $avgDiscrepancy,
            ];
        });

        return view('manufacturing::warehouses.reconciliation.supplier-report', compact('report'));
    }
}
