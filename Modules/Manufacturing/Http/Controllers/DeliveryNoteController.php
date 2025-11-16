<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\DeliveryNote;
use App\Models\Material;
use Modules\Manufacturing\Traits\LogsOperations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DeliveryNoteController extends Controller
{
    use LogsOperations;
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
        try {
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
            $validated['received_by'] = $validated['received_by'] ?? Auth::id() ?? 1;

            // حفظ البيانات في قاعدة البيانات
            $deliveryNote = DeliveryNote::create($validated);

            // تسجيل العملية
            try {
                $this->logOperation(
                    'create',
                    'Create Delivery Note',
                    'تم إنشاء أذن تسليم جديدة: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    null,
                    $deliveryNote->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note creation: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل إنشاء أذن التسليم: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', 'تم إضافة أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating delivery note: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة أذن التسليم: ' . $e->getMessage())
                ->withInput();
        }
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
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

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
            $newValues = $deliveryNote->fresh()->toArray();

            // تسجيل العملية
            try {
                $this->logOperation(
                    'update',
                    'Update Delivery Note',
                    'تم تحديث أذن تسليم: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note update: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل تحديث أذن التسليم: ' . $logError->getMessage());
            }

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', 'تم تحديث أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating delivery note: ' . $e->getMessage(), [
                'exception' => $e,
                'delivery_note_id' => $id,
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث أذن التسليم: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // حذف البيانات
            $deliveryNote = DeliveryNote::findOrFail($id);
            $oldValues = $deliveryNote->toArray();

            // تسجيل العملية قبل الحذف
            try {
                $this->logOperation(
                    'delete',
                    'Delete Delivery Note',
                    'تم حذف أذن تسليم: ' . $deliveryNote->note_number,
                    'delivery_notes',
                    $deliveryNote->id,
                    $oldValues,
                    null
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note deletion: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل حذف أذن التسليم: ' . $logError->getMessage());
            }

            $deliveryNote->delete();

            return redirect()->route('manufacturing.delivery-notes.index')
                ->with('success', 'تم حذف أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting delivery note: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف أذن التسليم: ' . $e->getMessage());
        }
    }

    /**
     * Toggle delivery note status (active/inactive).
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $deliveryNote = DeliveryNote::findOrFail($id);
            
            $oldStatus = $deliveryNote->is_active;
            $newStatus = !$oldStatus;
            
            $deliveryNote->update(['is_active' => $newStatus]);
            
            // Log the status change
            try {
                $this->logOperation(
                    'update',
                    'Toggle Status',
                    'تم تغيير حالة أذن التسليم من ' . ($oldStatus ? 'مفعلة' : 'معطلة') . ' إلى ' . ($newStatus ? 'مفعلة' : 'معطلة'),
                    'delivery_notes',
                    $deliveryNote->id,
                    ['is_active' => $oldStatus],
                    ['is_active' => $newStatus]
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log delivery note status change: ' . $logError->getMessage());
                throw new \Exception('فشل تسجيل تغيير حالة أذن التسليم: ' . $logError->getMessage());
            }
            
            return redirect()->back()
                           ->with('success', 'تم تغيير حالة أذن التسليم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error toggling delivery note status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير حالة أذن التسليم: ' . $e->getMessage()]);
        }
    }
}