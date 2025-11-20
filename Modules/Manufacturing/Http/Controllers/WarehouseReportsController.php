<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\DeliveryNote;
use App\Models\PurchaseInvoice;
use App\Models\AdditivesInventory;
use App\Models\Supplier;

class WarehouseReportsController extends Controller
{
    /**
     * عرض لوحة التقارير الرئيسية
     */
    public function index()
    {
        return view('manufacturing::warehouses.reports.index');
    }

    /**
     * تقرير إحصائيات المستودعات
     */
    public function warehousesStatistics(Request $request)
    {
        $warehouses = Warehouse::with(['materials', 'transactions'])
            ->withCount(['materials', 'transactions'])
            ->get();

        $stats = [
            'total_warehouses' => $warehouses->count(),
            'active_warehouses' => $warehouses->where('is_active', true)->count(),
            'inactive_warehouses' => $warehouses->where('is_active', false)->count(),
            'total_materials' => Material::count(),
            'total_capacity' => $warehouses->sum('capacity'),
        ];

        // تقسيم المستودعات حسب النوع
        $warehousesByType = Warehouse::select('warehouse_type', DB::raw('count(*) as count'))
            ->groupBy('warehouse_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->warehouse_type => $item->count];
            });

        return view('manufacturing::warehouses.reports.warehouses-statistics', compact('warehouses', 'stats', 'warehousesByType'));
    }

    /**
     * تقرير المواد والمخزون
     */
    public function materialsReport(Request $request)
    {
        // جلب تفاصيل المواد من material_details باستخدام Eloquent
        $materialDetailsQuery = MaterialDetail::with(['material', 'warehouse', 'unit'])
            ->when($request->warehouse_id, function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('material', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            });

        $materialDetails = $materialDetailsQuery->orderBy('created_at', 'desc')->paginate(20);

        // حساب الإحصائيات باستخدام Eloquent
        $stats = [
            'total_materials' => MaterialDetail::count(),
            'total_quantity' => MaterialDetail::sum('quantity'),
            'low_stock' => MaterialDetail::whereNotNull('min_quantity')
                ->where('min_quantity', '>', 0)
                ->whereColumn('quantity', '<=', 'min_quantity')
                ->count(),
            'total_weight' => MaterialDetail::sum('actual_weight'),
        ];

        $warehouses = Warehouse::where('is_active', true)->get();

        return view('manufacturing::warehouses.reports.materials-report', compact('materialDetails', 'stats', 'warehouses'));
    }

    /**
     * تقرير أذون التسليم
     */
    public function deliveryNotesReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $deliveryNotes = DeliveryNote::with(['supplier', 'warehouse', 'recordedBy'])
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->when($request->supplier_id, function($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('delivery_date', 'desc')
            ->paginate(20);

        $stats = [
            'total_notes' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->count(),
            'pending_notes' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->where('status', 'pending')->count(),
            'received_notes' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->where('status', 'received')->count(),
            'total_quantity' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->sum('delivery_quantity'),
        ];

        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('manufacturing::warehouses.reports.delivery-notes-report', compact('deliveryNotes', 'stats', 'suppliers', 'warehouses', 'startDate', 'endDate'));
    }

    /**
     * تقرير فواتير المشتريات
     */
    public function purchaseInvoicesReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $invoices = PurchaseInvoice::with(['supplier'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->when($request->supplier_id, function($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('payment_status', $request->status);
            })
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);

        $stats = [
            'total_invoices' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->count(),
            'total_amount' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total_amount'),
            'paid_amount' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->where('payment_status', 'paid')->sum('total_amount'),
            'pending_amount' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->where('payment_status', 'pending')->sum('total_amount'),
        ];

        $suppliers = Supplier::where('is_active', true)->get();

        return view('manufacturing::warehouses.reports.purchase-invoices-report', compact('invoices', 'stats', 'suppliers', 'startDate', 'endDate'));
    }

    /**
     * تقرير الصبغات والبلاستيك
     */
    public function additivesReport(Request $request)
    {
        $additives = AdditivesInventory::with(['supplier'])
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_additives' => AdditivesInventory::count(),
            'active_additives' => AdditivesInventory::where('is_active', true)->count(),
            'low_stock' => AdditivesInventory::where('quantity', '<=', 10)->count(),
            'total_value' => AdditivesInventory::selectRaw('SUM(quantity * cost_per_unit) as total')->value('total') ?? 0,
        ];

        return view('manufacturing::warehouses.reports.additives-report', compact('additives', 'stats'));
    }

    /**
     * تقرير الموردين
     */
    public function suppliersReport(Request $request)
    {
        $suppliers = Supplier::withCount(['deliveryNotes', 'purchaseInvoices'])
            ->when($request->is_active !== null, function($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'inactive_suppliers' => Supplier::where('is_active', false)->count(),
        ];

        return view('manufacturing::warehouses.reports.suppliers-report', compact('suppliers', 'stats'));
    }

    /**
     * تقرير الحركات والتحويلات
     */
    public function movementsReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $movements = DB::table('warehouse_transactions')
            ->join('warehouses', 'warehouse_transactions.warehouse_id', '=', 'warehouses.id')
            ->join('materials', 'warehouse_transactions.material_id', '=', 'materials.id')
            ->join('units', 'warehouse_transactions.unit_id', '=', 'units.id')
            ->select(
                'warehouse_transactions.*',
                'warehouses.warehouse_name',
                'materials.material_name',
                'units.unit_name'
            )
            ->whereBetween('warehouse_transactions.created_at', [$startDate, $endDate])
            ->when($request->warehouse_id, function($query) use ($request) {
                $query->where('warehouse_transactions.warehouse_id', $request->warehouse_id);
            })
            ->when($request->transaction_type, function($query) use ($request) {
                $query->where('warehouse_transactions.transaction_type', $request->transaction_type);
            })
            ->orderBy('warehouse_transactions.created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_movements' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'receive_count' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'receive')->count(),
            'issue_count' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'issue')->count(),
            'transfer_count' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'transfer')->count(),
        ];

        $warehouses = Warehouse::where('is_active', true)->get();

        return view('manufacturing::warehouses.reports.movements-report', compact('movements', 'stats', 'warehouses', 'startDate', 'endDate'));
    }

    /**
     * تقرير شامل للمستودع
     */
    public function comprehensiveReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $data = [
            'warehouses' => [
                'total' => Warehouse::count(),
                'active' => Warehouse::where('is_active', true)->count(),
                'total_capacity' => Warehouse::sum('capacity'),
            ],
            'materials' => [
                'total' => MaterialDetail::count(),
                'total_quantity' => MaterialDetail::sum('quantity'),
                'total_weight' => MaterialDetail::sum('actual_weight'),
                'low_stock' => MaterialDetail::whereColumn('quantity', '<=', 'min_quantity')->count(),
            ],
            'delivery_notes' => [
                'total' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->count(),
                'pending' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->where('status', 'pending')->count(),
                'approved' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->where('status', 'approved')->count(),
                'completed' => DeliveryNote::whereBetween('delivery_date', [$startDate, $endDate])->where('status', 'completed')->count(),
            ],
            'invoices' => [
                'total' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->count(),
                'total_amount' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total_amount'),
                'paid' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->where('status', 'paid')->count(),
                'pending' => PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->where('status', 'pending')->count(),
            ],
            'movements' => [
                'total' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'receive' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'receive')->count(),
                'issue' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'issue')->count(),
                'transfer' => DB::table('warehouse_transactions')->whereBetween('created_at', [$startDate, $endDate])->where('transaction_type', 'transfer')->count(),
            ],
            'additives' => [
                'total' => AdditivesInventory::count(),
                'active' => AdditivesInventory::where('is_active', true)->count(),
                'total_quantity' => AdditivesInventory::sum('quantity'),
                'total_value' => AdditivesInventory::selectRaw('SUM(quantity * cost_per_unit) as total')->value('total') ?? 0,
            ],
            'suppliers' => [
                'total' => Supplier::count(),
                'active' => Supplier::where('is_active', true)->count(),
            ],
        ];

        return view('manufacturing::warehouses.reports.comprehensive', compact('data', 'startDate', 'endDate'));
    }
}