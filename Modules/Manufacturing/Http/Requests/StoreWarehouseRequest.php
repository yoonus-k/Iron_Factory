<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:warehouses,warehouse_name',
            'code' => 'required|string|max:50|unique:warehouses,warehouse_code',
            'is_active' => 'nullable|in:0,1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // إضافة البيانات المحولة مع الحفاظ على الأصلية
        $this->merge([
            'warehouse_name' => $this->input('name'),
            'warehouse_code' => $this->input('code'),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // إعادة تعيين البيانات - استبدل name و code بـ warehouse_name و warehouse_code
        $all = $this->all();
        unset($all['name'], $all['code']);
        $this->replace($all);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المستودع مطلوب',
            'name.unique' => 'اسم المستودع موجود بالفعل',
            'code.required' => 'رمز المستودع مطلوب',
            'code.unique' => 'رمز المستودع موجود بالفعل',
        ];
    }
}
