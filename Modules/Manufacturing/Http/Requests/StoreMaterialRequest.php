<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'barcode' => 'required|string|unique:materials,barcode',
            'batch_number' => 'nullable|string',
            'name_ar' => 'required|string|min:2|max:255',
            'name_en' => 'nullable|string|min:2|max:255',
            'material_type_id' => 'required|exists:material_types,id',
            'unit_id' => 'required|exists:units,id',

            'delivery_note_number' => 'nullable|string|max:255',
          
            'shelf_location' => 'nullable|string|max:255',
            'shelf_location_en' => 'nullable|string|max:255',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',

            'notes' => 'nullable|string',
            'notes_en' => 'nullable|string',

            'min_quantity' => 'nullable|numeric|min:0',
            'max_quantity' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'barcode.required' => 'رمز المادة مطلوب',
            'barcode.unique' => 'رمز المادة موجود بالفعل',
            'name_ar.required' => 'اسم المادة مطلوب',
            'material_type_id.required' => 'نوع المادة مطلوب',
            'material_type_id.exists' => 'نوع المادة المختار غير موجود',
            'unit_id.required' => 'وحدة القياس مطلوبة',
            'unit_id.exists' => 'وحدة القياس المختارة غير موجودة',
        ];
    }
}
