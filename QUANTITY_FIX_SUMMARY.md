# ✅ تصحيح حقل الكمية - الأذن الواردة

## المشكلة ❌
كان النظام يستخدم `invoice_weight` و `actual_weight` بدل استخدام الحقل الصحيح `quantity` من جدول `delivery_notes`.

## الحل ✅

### 1️⃣ تصحيح الـ Forms

#### Create Form (`create.blade.php`)
```blade
<!-- قبل -->
<input type="number" name="weight_discrepancy" id="weight_discrepancy" ...>

<!-- بعد -->
<input type="number" name="quantity" id="quantity_incoming" ...>
```

#### Edit Form (`edit.blade.php`)
```blade
<!-- قبل -->
<input type="number" name="invoice_weight" id="invoice_weight" ...>

<!-- بعد -->
<input type="number" name="quantity" id="quantity_incoming_edit" ...>
```

---

### 2️⃣ تصحيح الـ Validation (DeliveryNoteController)

#### Store Method
```php
// قبل
'delivery_quantity' => $type === 'outgoing' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',

// بعد
'quantity' => $type === 'incoming' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
'delivery_quantity' => $type === 'outgoing' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
```

#### Update Method
```php
// قبل
'invoice_weight' => $type === 'incoming' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',

// بعد
'quantity' => $type === 'incoming' ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
```

---

### 3️⃣ تصحيح تحديث MaterialDetail

#### Store (إضافة أذن واردة جديدة)
```php
// قبل
$quantityToAdd = $validated['invoice_weight'] ?? $validated['actual_weight'] ?? 0;

// بعد
$quantityToAdd = $validated['quantity'] ?? 0;
```

#### Update (تعديل أذن واردة)
```php
// قبل
$oldQuantity = $oldValues['invoice_weight'] ?? $oldValues['actual_weight'] ?? 0;
$newQuantity = $validated['invoice_weight'] ?? $validated['actual_weight'] ?? 0;

// بعد
$oldQuantity = $oldValues['quantity'] ?? 0;
$newQuantity = $validated['quantity'] ?? 0;
```

#### Destroy (حذف أذن واردة)
```php
// قبل
$quantityToRemove = $deliveryNote->invoice_weight ?? $deliveryNote->actual_weight ?? 0;

// بعد
$quantityToRemove = $deliveryNote->quantity ?? 0;
```

---

## المقارنة 📊

| العملية | حقل الـ Form | حقل الـ Validation | الحقل المستخدم |
|--------|-----------|------------------|--------------|
| **الأذن الواردة** | `quantity` | `quantity` | `$validated['quantity']` |
| **الأذن الصادرة** | `delivery_quantity` | `delivery_quantity` | `$validated['delivery_quantity']` |

---

## النتيجة 🎯

✅ **الآن الكمية تُحفظ في الحقل الصحيح (`quantity`)**  
✅ **عند إضافة أذن واردة: تزيد الكمية في MaterialDetail**  
✅ **عند تعديل الأذن: يتم حساب الفرق بدقة**  
✅ **عند حذف الأذن: تُسترجع الكمية للمستودع**  

---

## الملفات المعدلة 📝

1. ✅ `DeliveryNoteController.php` - store(), update(), destroy() methods
2. ✅ `create.blade.php` - تغيير اسم الحقل إلى quantity
3. ✅ `edit.blade.php` - تغيير اسم الحقل إلى quantity

---

## الاختبار 🧪

### للتحقق من العملية:
```bash
# إضافة أذن واردة
POST /manufacturing/delivery-notes
{
  "type": "incoming",
  "material_id": 1,
  "warehouse_id": 1,
  "delivery_date": "2025-11-23",
  "quantity": 100  // ✅ الحقل الصحيح
}

# النتيجة: MaterialDetail.quantity += 100
```

