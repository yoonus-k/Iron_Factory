<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\MaterialMovement;
use App\Models\PurchaseInvoice;
use App\Models\ReconciliationLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\StoresNotifications;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconciliationController extends Controller
{
    use StoresNotifications;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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

            // إرسال إشعار بربط الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'ربط فاتورة بأذن تسليم',
                        'تم ربط الفاتورة برقم ' . $invoice->invoice_number . ' بأذن التسليم رقم ' . $deliveryNote->note_number,
                        'link_invoice_to_delivery_note',
                        'info',
                        'feather icon-link',
                        route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين الإشعار
                $this->storeNotification(
                    'invoice_linked',
                    'ربط الفاتورة بأذن التسليم',
                    'تم ربط الفاتورة برقم ' . $invoice->invoice_number . ' مع أذن التسليم رقم ' . $deliveryNote->note_number,
                    'success',
                    'fas fa-link',
                    route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send link invoice notifications: ' . $notifError->getMessage());
            }

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

            // إرسال إشعار بقرار التسوية
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                $actionLabel = match($validated['action']) {
                    'accepted' => 'موافقة',
                    'rejected' => 'رفض',
                    'adjusted' => 'تعديل',
                    default => 'تحديث'
                };

                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'قرار تسوية أذن تسليم',
                        'تم ' . $actionLabel . ' تسوية أذن التسليم رقم ' . $deliveryNote->note_number,
                        'reconciliation_decision',
                        'info',
                        'feather icon-check-square',
                        route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين الإشعار
                $this->storeNotification(
                    'reconciliation_decided',
                    'قرار تسوية أذن تسليم',
                    'تم ' . $actionLabel . ' تسوية أذن التسليم رقم ' . $deliveryNote->note_number,
                    $validated['action'] === 'rejected' ? 'danger' : 'success',
                    $validated['action'] === 'rejected' ? 'fas fa-times-circle' : 'fas fa-check-circle',
                    route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send reconciliation decision notifications: ' . $notifError->getMessage());
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
            ->get()
            ->map(function($note) {
                return [
                    'id' => $note->id,
                    'note_number' => $note->note_number,
                    'supplier' => ['id' => $note->supplier?->id, 'name' => $note->supplier?->name],
                    'delivery_date' => $note->delivery_date?->format('Y-m-d'),
                    'actual_weight' => $note->actual_weight,
                    'created_at' => $note->created_at?->format('Y-m-d'),
                ];
            })
            ->toArray();

        // جلب الفواتير المتاحة للربط مع الـ items
        $invoices = PurchaseInvoice::with(['supplier', 'items.material'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'supplier' => ['id' => $invoice->supplier?->id, 'name' => $invoice->supplier?->name],
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                    'weight' => $invoice->weight,
                    'weight_unit' => $invoice->weight_unit ?? 'كجم',
                    'invoice_reference_number' => $invoice->invoice_reference_number,
                    'items' => $invoice->items->map(function($item) {
                        // الحصول على اسم المادة من علاقة Material
                        $materialName = $item->material?->name_ar ?? $item->material?->name ?? $item->item_name;

                        return [
                            'id' => $item->id,
                            'item_name' => $materialName,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'weight' => $item->weight,
                            'weight_unit' => $item->weight_unit ?? 'كجم',
                            'material' => ['id' => $item->material?->id, 'name' => $materialName],
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                            'total' => $item->total,
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();

        return view('manufacturing::warehouses.reconciliation.link-invoice', compact('deliveryNotes', 'invoices'));
    }
    public function storeLinkInvoice(Request $request)
    {
        $validated = $request->validate([
            'delivery_note_id' => 'required|exists:delivery_notes,id',
            'invoice_id' => 'required|exists:purchase_invoices,id',
            'invoice_weight' => 'required|numeric|min:0.01',
            'invoice_reference_number' => 'nullable|string|max:100',
            'reconciliation_notes' => 'nullable|string|max:1000',
        ], [
            'delivery_note_id.required' => 'يجب اختيار أذن التسليم',
            'delivery_note_id.exists' => 'أذن التسليم المختارة غير موجودة',
            'invoice_id.required' => 'يجب اختيار فاتورة',
            'invoice_id.exists' => 'الفاتورة المختارة غير موجودة',
            'invoice_weight.required' => 'وزن الفاتورة مطلوب',
            'invoice_weight.numeric' => 'وزن الفاتورة يجب أن يكون رقماً',
            'invoice_weight.min' => 'وزن الفاتورة يجب أن يكون أكبر من صفر',
        ]);

        try {
            DB::beginTransaction();

            $deliveryNote = DeliveryNote::findOrFail($validated['delivery_note_id']);
            $invoice = PurchaseInvoice::findOrFail($validated['invoice_id']);

            // التحقق من أن الأذن لا تملك فاتورة بالفعل
            if ($deliveryNote->purchase_invoice_id) {
                return back()->with('error', 'هذه الأذن مربوطة بفاتورة بالفعل');
            }

            // ربط الفاتورة بالأذن
            $updateData = [
                'purchase_invoice_id' => $invoice->id,
                'invoice_weight' => $validated['invoice_weight'],
                'invoice_date' => $invoice->invoice_date,
            ];

            // إضافة الحقول الاختيارية إذا كانت موجودة
            if (isset($validated['invoice_reference_number'])) {
                $updateData['invoice_reference_number'] = $validated['invoice_reference_number'];
            }
            if (isset($validated['reconciliation_notes'])) {
                $updateData['reconciliation_notes'] = $validated['reconciliation_notes'];
            }

            $deliveryNote->update($updateData);

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
                'action' => $reconciliationStatus === 'matched' ? 'accepted' : 'pending',
                'comments' => $validated['reconciliation_notes'],
                'decided_by' => Auth::id(),
                'decided_at' => now(),
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

            // إرسال إشعار بربط الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'ربط فاتورة بأذن تسليم',
                        'تم ربط الفاتورة برقم ' . $invoice->invoice_number . ' بأذن التسليم رقم ' . $deliveryNote->note_number . ' | ' . $message,
                        'link_invoice_to_delivery_note',
                        'success',
                        'feather icon-link',
                        route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                    );
                }

                // ✅ تخزين الإشعار
                $this->storeNotification(
                    'invoice_linked_from_bulk',
                    'ربط فاتورة بأذن تسليم',
                    'تم ربط الفاتورة برقم ' . $invoice->invoice_number . ' مع أذن التسليم رقم ' . $deliveryNote->note_number,
                    'success',
                    'fas fa-link',
                    route('manufacturing.warehouses.reconciliation.show', $deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send link invoice notifications: ' . $notifError->getMessage());
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

    /**
     * ✅ API: البحث عن أذونات التسليم
     */
    public function searchDeliveryNotes(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $notes = DeliveryNote::where('type', 'incoming')
            ->where('registration_status', 'registered')
            ->whereNull('purchase_invoice_id')
            ->where(function($query) use ($search) {
                $query->where('note_number', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhere('id', $search);
            })
            ->with('supplier')
            ->limit(10)
            ->get()
            ->map(function($note) {
                return [
                    'id' => $note->id,
                    'text' => "أذن #{$note->note_number} - {$note->supplier->name} - الوزن: {$note->actual_weight} كيلو",
                    'note_number' => $note->note_number,
                    'supplier_name' => $note->supplier->name,
                    'actual_weight' => $note->actual_weight,
                    'delivery_date' => $note->delivery_date?->format('Y-m-d'),
                ];
            });

        return response()->json(['results' => $notes]);
    }

    /**
     * ✅ API: البحث عن الفواتير
     */
    public function searchInvoices(Request $request)
    {
        $search = $request->get('q', '');
        $supplierId = $request->get('supplier_id');

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $query = PurchaseInvoice::where(function($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('reference_number', 'like', "%{$search}%")
              ->orWhereHas('supplier', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              });
        });

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $invoices = $query->with('supplier')
            ->limit(10)
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'text' => "#{$invoice->invoice_number} - {$invoice->supplier->name} - التاريخ: {$invoice->invoice_date?->format('Y-m-d')}",
                    'invoice_number' => $invoice->invoice_number,
                    'supplier_name' => $invoice->supplier->name,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                    'status' => $invoice->status,
                ];
            });

        return response()->json(['results' => $invoices]);
    }

    /**
     * ✅ API: الحصول على تفاصيل أذن التسليم
     */
    public function getDeliveryNoteDetails($id)
    {
        $note = DeliveryNote::with(['supplier', 'materialDetail'])
            ->find($id);

        if (!$note) {
            return response()->json(['error' => 'الأذن غير موجودة'], 404);
        }

        return response()->json([
            'id' => $note->id,
            'note_number' => $note->note_number,
            'supplier_id' => $note->supplier_id,
            'supplier_name' => $note->supplier->name,
            'actual_weight' => $note->actual_weight,
            'delivery_date' => $note->delivery_date?->format('Y-m-d'),
            'warehouse_id' => $note->warehouse_id,
            'material_id' => $note->material_id,
            'material_name' => $note->material?->name,
        ]);
    }

    /**
     * ✅ API: الحصول على تفاصيل الفاتورة
     */
    public function getInvoiceDetails($id)
    {
        $invoice = PurchaseInvoice::with('supplier')->find($id);

        if (!$invoice) {
            return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
        }

        return response()->json([
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'supplier_id' => $invoice->supplier_id,
            'supplier_name' => $invoice->supplier->name,
            'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
            'status' => $invoice->status,
            'total_amount' => $invoice->total_amount,
        ]);
    }

    /**
     * ✅ صفحة تعديل الأذن
     */
    public function editDeliveryNote(DeliveryNote $deliveryNote)
    {
        return view('manufacturing::warehouses.reconciliation.edit-delivery-note', compact('deliveryNote'));
    }

    /**
     * ✅ صفحة تعديل الفاتورة
     */
    public function editInvoice(PurchaseInvoice $purchaseInvoice)
    {
        return view('manufacturing::warehouses.reconciliation.edit-invoice', compact('purchaseInvoice'));
    }

    /**
     * ✅ صفحة تعديل التسوية
     */
    public function editReconciliation(ReconciliationLog $reconciliationLog)
    {
        return view('manufacturing::warehouses.reconciliation.edit-reconciliation', compact('reconciliationLog'));
    }

    /**
     * ✅ عرض صفحة تعديل ربط الفاتورة
     */
    public function editLinkInvoice($id)
    {
        $reconciliation = ReconciliationLog::with(['deliveryNote', 'deliveryNote.supplier', 'purchaseInvoice'])->find($id);

        if (!$reconciliation) {
            return back()->with('error', 'السجل غير موجود');
        }

        return view('manufacturing::warehouses.reconciliation.edit-link-invoice', compact('reconciliation'));
    }

    /**
     * ✅ تحديث ربط الفاتورة
     */
    public function updateLinkInvoice(Request $request, $id)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'invoice_weight' => 'required|numeric|min:0.01',
            'invoice_reference_number' => 'nullable|string',
            'reconciliation_notes' => 'nullable|string',
            'edit_reason' => 'required|string|min:5',
        ]);

        try {
            DB::beginTransaction();

            $reconciliation = ReconciliationLog::find($id);

            if (!$reconciliation) {
                return back()->with('error', 'السجل غير موجود');
            }

            // تحديث البيانات
            $oldWeight = $reconciliation->invoice_weight;
            $oldNotes = $reconciliation->reconciliation_notes;

            $updateData = [
                'invoice_weight' => $validated['invoice_weight'],
                'reconciliation_notes' => isset($validated['reconciliation_notes']) ? $validated['reconciliation_notes'] : null,
            ];

            // إضافة الحقول الاختيارية إذا كانت موجودة في البيانات المدخلة
            if (isset($validated['invoice_reference_number'])) {
                $updateData['invoice_reference_number'] = $validated['invoice_reference_number'];
            }

            $reconciliation->update($updateData);

            // إعادة حساب الفرق إذا تغير الوزن
            if ($oldWeight != $validated['invoice_weight']) {
                $actualWeight = $reconciliation->deliveryNote->actual_weight;
                $newDiscrepancy = $actualWeight - $validated['invoice_weight'];
                $newPercentage = $validated['invoice_weight'] > 0 ?
                    (($newDiscrepancy / $validated['invoice_weight']) * 100) : 0;

                $reconciliation->update([
                    'discrepancy' => $newDiscrepancy,
                    'discrepancy_percentage' => $newPercentage,
                ]);

                // تحديث حالة التسوية بناءً على الفرق الجديد
                $newStatus = abs($newPercentage) <= 1 ? 'matched' : 'discrepancy';
                $reconciliation->deliveryNote->update(['reconciliation_status' => $newStatus]);
            }

            // تسجيل التعديل
            $reconciliation->update([
                'edit_count' => ($reconciliation->edit_count ?? 0) + 1,
                'updated_by' => Auth::id(),
            ]);

            // إرسال إشعار بتحديث ربط الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'تحديث ربط فاتورة بأذن تسليم',
                        'تم تحديث ربط الفاتورة برقم ' . $reconciliation->purchaseInvoice->invoice_number . ' بأذن التسليم رقم ' . $reconciliation->deliveryNote->note_number . ' | السبب: ' . $validated['edit_reason'],
                        'update_link_invoice',
                        'warning',
                        'feather icon-edit',
                        route('manufacturing.warehouses.reconciliation.show', $reconciliation->deliveryNote->id)
                    );
                }

                // ✅ تخزين الإشعار
                $this->storeNotification(
                    'invoice_link_updated',
                    'تحديث ربط فاتورة',
                    'تم تحديث ربط الفاتورة برقم ' . $reconciliation->purchaseInvoice->invoice_number . ' مع أذن التسليم رقم ' . $reconciliation->deliveryNote->note_number,
                    'warning',
                    'fas fa-edit',
                    route('manufacturing.warehouses.reconciliation.show', $reconciliation->deliveryNote->id)
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send update link invoice notifications: ' . $notifError->getMessage());
            }

            DB::commit();

            return redirect()->route('manufacturing.warehouses.reconciliation.index')
                ->with('success', 'تم تحديث ربط الفاتورة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * ✅ حذف ربط الفاتورة
     */
    public function deleteLinkInvoice(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $reconciliation = ReconciliationLog::find($id);

            if (!$reconciliation) {
                return back()->with('error', 'السجل غير موجود');
            }

            // تحديث حالة الأذن إلى pending
            $reconciliation->deliveryNote->update([
                'reconciliation_status' => 'pending',
                'purchase_invoice_id' => null,
                'invoice_weight' => null,
                'invoice_date' => null,
            ]);

            // حذف السجل
            $reconciliation->delete();

            // إرسال إشعار بحذف ربط الفاتورة
            try {
                $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
                foreach ($managers as $manager) {
                    $this->notificationService->notifyCustom(
                        $manager,
                        'حذف ربط فاتورة بأذن تسليم',
                        'تم حذف ربط الفاتورة برقم ' . $reconciliation->purchaseInvoice->invoice_number . ' من أذن التسليم رقم ' . $reconciliation->deliveryNote->note_number,
                        'delete_link_invoice',
                        'danger',
                        'feather icon-trash-2',
                        route('manufacturing.warehouses.reconciliation.index')
                    );
                }

                // ✅ تخزين الإشعار
                $this->storeNotification(
                    'invoice_link_deleted',
                    'حذف ربط فاتورة',
                    'تم حذف ربط الفاتورة برقم ' . $reconciliation->purchaseInvoice->invoice_number . ' من أذن التسليم رقم ' . $reconciliation->deliveryNote->note_number,
                    'danger',
                    'fas fa-trash',
                    route('manufacturing.warehouses.reconciliation.index')
                );
            } catch (\Exception $notifError) {
                Log::warning('Failed to send delete link invoice notifications: ' . $notifError->getMessage());
            }

            DB::commit();

            return back()->with('success', 'تم حذف ربط الفاتورة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * ✅ API: إنشاء أذن تسليم من الفاتورة
     */

}
