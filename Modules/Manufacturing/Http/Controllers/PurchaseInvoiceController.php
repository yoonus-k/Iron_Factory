<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manufacturing::warehouses.purchase-invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::warehouses.purchase-invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices',
            'invoice_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
        ]);

        // حفظ البيانات في قاعدة البيانات
        // PurchaseInvoice::create($validated);

        return redirect()->route('manufacturing.purchase-invoices.index')
            ->with('success', 'تم إضافة الفاتورة بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manufacturing::warehouses.purchase-invoices.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manufacturing::warehouses.purchase-invoices.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
        ]);

        // تحديث البيانات
        // $invoice = PurchaseInvoice::find($id);
        // $invoice->update($validated);

        return redirect()->route('manufacturing.purchase-invoices.index')
            ->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف البيانات
        // PurchaseInvoice::find($id)->delete();

        return redirect()->route('manufacturing.purchase-invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح');
    }
}
