<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'material_type_id' => 'required|exists:material_types,id',
            'unit_id' => 'required|exists:units,id',
            'barcode' => 'required|string|max:50|unique:materials,barcode',
            'batch_number' => 'nullable|string|max:100',
            'delivery_note_number' => 'nullable|string|max:100',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'status' => 'required|in:available,in_use,consumed,expired',
            'name_ar' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'shelf_location' => 'nullable|string|max:100',
            'shelf_location_en' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'notes_en' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'المستودع مطلوب',
            'warehouse_id.exists' => 'المستودع غير موجود',
            'material_type_id.required' => 'نوع المادة مطلوب',
            'material_type_id.exists' => 'نوع المادة غير موجود',
            'unit_id.required' => 'الوحدة مطلوبة',
            'unit_id.exists' => 'الوحدة غير موجودة',
            'barcode.required' => 'الباركود مطلوب',
            'barcode.unique' => 'الباركود موجود بالفعل',
            'status.required' => 'الحالة مطلوبة',
            'name_ar.required' => 'الاسم بالعربية مطلوب',
        ];
    }
}
