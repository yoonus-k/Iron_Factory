<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_code' => 'required|string|unique:material_types,type_code',
            'type_name' => 'required|string|min:2|max:255',
            'type_name_en' => 'nullable|string|min:2|max:255',
            'category' => 'required|in:raw_material,finished_product,semi_finished,additive,packing_material,component',
            'category_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'specifications' => 'nullable|json',
            'default_unit' => 'nullable|exists:units,id',
            'standard_cost' => 'nullable|numeric|min:0',
            'storage_conditions' => 'nullable|string|max:500',
            'storage_conditions_en' => 'nullable|string|max:500',
            'shelf_life_days' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'type_code.required' => 'رمز النوع مطلوب',
            'type_code.unique' => 'رمز النوع موجود بالفعل',
            'type_name.required' => 'اسم النوع مطلوب',
            'category.required' => 'الفئة مطلوبة',
        ];
    }
}
