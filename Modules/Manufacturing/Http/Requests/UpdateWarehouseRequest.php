<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
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
        $warehouseId = $this->route('warehouse');

        return [
            'name' => 'required|string|max:255|unique:warehouses,warehouse_name,' . $warehouseId,
            'name_en' => 'nullable|string|max:255|unique:warehouses,warehouse_name_en,' . $warehouseId,
            'code' => 'required|string|max:50|unique:warehouses,warehouse_code,' . $warehouseId,
            'location' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'manager_id' => 'nullable|integer|exists:users,id',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المستودع مطلوب',
            'name.unique' => 'اسم المستودع موجود بالفعل',
            'name_en.unique' => 'اسم المستودع بالإنجليزية موجود بالفعل',
            'code.required' => 'رمز المستودع مطلوب',
            'code.unique' => 'رمز المستودع موجود بالفعل',
            'manager_id.exists' => 'المسؤول المختار غير موجود',
            'capacity.numeric' => 'السعة يجب أن تكون رقماً',
            'status.in' => 'الحالة غير صحيحة',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'phone.max' => 'رقم الهاتف طويل جداً',
        ];
    }
}