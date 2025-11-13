<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitId = $this->route('unit');

        return [
            'unit_code' => 'required|string|unique:units,unit_code,' . $unitId,
            'unit_name' => 'required|string|min:2|max:255',
            'unit_name_en' => 'nullable|string|min:2|max:255',
            'unit_symbol' => 'required|string|max:10',
            'unit_type' => 'required|in:weight,length,volume,area,quantity,time,temperature,other',
            'conversion_factor' => 'nullable|numeric|min:0',
            'base_unit' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'unit_code.required' => 'رمز الوحدة مطلوب',
            'unit_code.unique' => 'رمز الوحدة موجود بالفعل',
            'unit_name.required' => 'اسم الوحدة مطلوب',
            'unit_symbol.required' => 'اختصار الوحدة مطلوب',
            'unit_type.required' => 'نوع الوحدة مطلوب',
        ];
    }
}
