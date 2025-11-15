<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\DeliveryNote;

use App\Models\Material;

class DeliveryNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryNotes = DeliveryNote::with(['material', 'receiver'])->get();
        return view('manufacturing::warehouses.delivery-notes.index', compact('deliveryNotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $materials = Material::all();
        return view('manufacturing::warehouses.delivery-notes.create', compact( 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'delivery_number' => 'required|string|unique:delivery_notes,note_number',
            'delivery_date' => 'required|date',

            'material_id' => 'required|exists:materials,id',
            'delivered_weight' => 'required|numeric|min:0',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'received_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Add received_by from authenticated user if not provided
        $validated['received_by'] = $validated['received_by'] ?? auth()->id();

        // حفظ البيانات في قاعدة البيانات
        $deliveryNote = DeliveryNote::create($validated);

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم إضافة أذن التسليم بنجاح');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $deliveryNote = DeliveryNote::with(['material',  'receiver'])->findOrFail($id);
        return view('manufacturing::warehouses.delivery-notes.show', compact('deliveryNote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);

        $materials = Material::all();
        return view('manufacturing::warehouses.delivery-notes.edit', compact('deliveryNote',  'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);

        // التحقق من البيانات
        $validated = $request->validate([
            'delivery_number' => 'required|string|unique:delivery_notes,note_number,' . $deliveryNote->id,
            'delivery_date' => 'required|date',

            'material_id' => 'required|exists:materials,id',
            'delivered_weight' => 'required|numeric|min:0',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'received_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // تحديث البيانات
        $deliveryNote->update($validated);

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم تحديث أذن التسليم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // حذف البيانات
        $deliveryNote = DeliveryNote::findOrFail($id);
        $deliveryNote->delete();

        return redirect()->route('manufacturing.delivery-notes.index')
            ->with('success', 'تم حذف أذن التسليم بنجاح');
    }
}