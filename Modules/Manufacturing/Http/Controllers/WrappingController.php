<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\Wrapping;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WrappingController extends Controller
{
    /**
     * عرض قائمة اللفافات
     */
    public function index()
    {
        $wrappings = Wrapping::latest()->paginate(15);
        
        return view('manufacturing::wrappings.index', compact('wrappings'));
    }

    /**
     * عرض صفحة إضافة لفاف جديد
     */
    public function create()
    {
        return view('manufacturing::wrappings.create');
    }

    /**
     * حفظ لفاف جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wrapping_number' => 'required|string|max:50|unique:wrappings,wrapping_number',
            'weight' => 'required|numeric|min:0.01|max:99999',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'wrapping_number.required' => 'رقم اللفاف مطلوب',
            'wrapping_number.unique' => 'رقم اللفاف موجود مسبقاً',
            'weight.required' => 'وزن اللفاف مطلوب',
            'weight.min' => 'الوزن يجب أن يكون أكبر من صفر',
        ]);

        try {
            Wrapping::create($validated);

            Log::info('تم إضافة لفاف جديد', [
                'wrapping_number' => $validated['wrapping_number'],
                'weight' => $validated['weight']
            ]);

            return redirect()->route('manufacturing.wrappings.index')
                ->with('success', 'تم إضافة اللفاف بنجاح');

        } catch (\Exception $e) {
            Log::error('خطأ في إضافة لفاف: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة اللفاف')->withInput();
        }
    }

    /**
     * عرض صفحة تعديل لفاف
     */
    public function edit($id)
    {
        $wrapping = Wrapping::findOrFail($id);
        
        return view('manufacturing::wrappings.edit', compact('wrapping'));
    }

    /**
     * تحديث بيانات لفاف
     */
    public function update(Request $request, $id)
    {
        $wrapping = Wrapping::findOrFail($id);

        $validated = $request->validate([
            'wrapping_number' => 'required|string|max:50|unique:wrappings,wrapping_number,' . $id,
            'weight' => 'required|numeric|min:0.01|max:99999',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'wrapping_number.required' => 'رقم اللفاف مطلوب',
            'wrapping_number.unique' => 'رقم اللفاف موجود مسبقاً',
            'weight.required' => 'وزن اللفاف مطلوب',
            'weight.min' => 'الوزن يجب أن يكون أكبر من صفر',
        ]);

        try {
            $wrapping->update($validated);

            Log::info('تم تحديث لفاف', [
                'wrapping_id' => $id,
                'wrapping_number' => $validated['wrapping_number']
            ]);

            return redirect()->route('manufacturing.wrappings.index')
                ->with('success', 'تم تحديث اللفاف بنجاح');

        } catch (\Exception $e) {
            Log::error('خطأ في تحديث لفاف: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث اللفاف')->withInput();
        }
    }

    /**
     * حذف لفاف
     */
    public function destroy($id)
    {
        try {
            $wrapping = Wrapping::findOrFail($id);
            $wrappingNumber = $wrapping->wrapping_number;
            
            $wrapping->delete();

            Log::info('تم حذف لفاف', [
                'wrapping_id' => $id,
                'wrapping_number' => $wrappingNumber
            ]);

            return redirect()->route('manufacturing.wrappings.index')
                ->with('success', 'تم حذف اللفاف بنجاح');

        } catch (\Exception $e) {
            Log::error('خطأ في حذف لفاف: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف اللفاف');
        }
    }

    /**
     * الحصول على بيانات لفاف (AJAX)
     */
    public function getWrapping($id)
    {
        try {
            $wrapping = Wrapping::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'wrapping' => [
                    'id' => $wrapping->id,
                    'wrapping_number' => $wrapping->wrapping_number,
                    'weight' => $wrapping->weight,
                    'description' => $wrapping->description,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'اللفاف غير موجود'
            ], 404);
        }
    }
}
