<?php

namespace Modules\Manufacturing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
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
        $materialId = $this->route('warehouse_product');

        return [
            'barcode' => 'required|string|unique:materials,barcode,' . $materialId,
            'batch_number' => 'nullable|string',
            'name_ar' => 'required|string|min:2|max:255',
            'name_en' => 'nullable|string|min:2|max:255',



            'delivery_note_number' => 'nullable|string|max:255',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacture_date',
            'shelf_location' => 'nullable|string|max:255',
            'shelf_location_en' => 'nullable|string|max:255',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'status' => 'required|in:available,in_use,consumed,expired',
            'notes' => 'nullable|string',
            'notes_en' => 'nullable|string',

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



        ];
    }
}
