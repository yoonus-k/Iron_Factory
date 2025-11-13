# تعليمات الاستخدام - نظام إدارة المواد

## 🔑 الوصول السريع للصفحات

### عرض قائمة المواد
```
http://yourapp.com/warehouse-products
```

### إضافة مادة جديدة
```
http://yourapp.com/warehouse-products/create
```

### عرض تفاصيل مادة (مثال ID = 1)
```
http://yourapp.com/warehouse-products/1
```

### تعديل مادة (مثال ID = 1)
```
http://yourapp.com/warehouse-products/1/edit
```

---

## 📋 مثال لإضافة مادة جديدة

### البيانات المطلوبة (*):

```json
{
  "barcode": "MAT-2024-001",
  "material_type": "مسمار حديد",
  "material_type_en": "Iron Nail",
  "material_category": "raw",
  "original_weight": 100,
  "unit_id": 1,
  "supplier_id": 1,
  "status": "available"
}
```

### البيانات الاختيارية:

```json
{
  "batch_number": "BATCH-001",
  "remaining_weight": 100,
  "delivery_note_number": "DN-2024-001",
  "manufacture_date": "2024-11-01",
  "expiry_date": "2024-12-01",
  "shelf_location": "رف 1",
  "shelf_location_en": "Shelf 1",
  "purchase_invoice_id": 1,
  "notes": "مادة عالية الجودة",
  "notes_en": "High quality material"
}
```

---

## 🔍 أمثلة على البحث والفلترة

### البحث عن مادة:
```
/warehouse-products?search=حديد
/warehouse-products?search=MAT-2024
```

### تصفية حسب الفئة:
```
/warehouse-products?category=raw
/warehouse-products?category=manufactured
/warehouse-products?category=finished
```

### تصفية حسب الحالة:
```
/warehouse-products?status=available
/warehouse-products?status=in_use
/warehouse-products?status=consumed
/warehouse-products?status=expired
```

### تصفية حسب المورد:
```
/warehouse-products?supplier_id=1
```

### دمج عدة فلاتر:
```
/warehouse-products?search=حديد&category=raw&status=available&supplier_id=1
```

---

## 📊 البيانات المعروضة في القائمة

| الحقل | الوصف |
|------|-------|
| # | الترقيم |
| رمز المادة | barcode |
| اسم المادة | material_type (عربي) + material_type_en (إنجليزي) |
| الفئة | getCategoryLabel() |
| الوزن الأصلي | original_weight + unit |
| الوزن المتبقي | remaining_weight + unit |
| الوحدة | unit.name |
| المورد | supplier.name |
| الحالة | status (مع badges ملونة) |
| الإجراءات | عرض، تعديل، حذف |

---

## 🛠️ الحالات والألوان

### الفئات:
- 🔴 **raw** → خام
- 🟢 **manufactured** → مصنع
- 🔵 **finished** → جاهز

### الحالات:
- ✅ **available** → متوفر (أخضر)
- ⚠️ **in_use** → قيد الاستخدام (أصفر)
- ❌ **consumed** → مستهلك (أحمر)
- ⏰ **expired** → منتهي الصلاحية (رمادي)

---

## ⚠️ رسائل الخطأ الشائعة

### خطأ: "رمز المادة مطلوب"
**الحل:** تأكد من إدخال barcode

### خطأ: "رمز المادة موجود بالفعل"
**الحل:** استخدم رمز مختلف (أو عدّل الموجود)

### خطأ: "المورد مطلوب"
**الحل:** اختر مورد من القائمة

### خطأ: "تاريخ الصلاحية يجب أن يكون بعد تاريخ الصنع"
**الحل:** تأكد أن تاريخ الصلاحية أكبر من تاريخ الصنع

---

## 🔐 الصلاحيات المطلوبة

جميع العمليات تتطلب:
- ✅ تسجيل دخول
- ✅ المصادقة (Authentication)

---

## 📚 قاعدة البيانات

### جدول materials:

```sql
CREATE TABLE materials (
  id BIGINT PRIMARY KEY,
  warehouse_id BIGINT,
  material_type_id BIGINT,
  barcode VARCHAR (UNIQUE),
  batch_number VARCHAR,
  material_type VARCHAR,
  material_type_en VARCHAR,
  material_category VARCHAR,
  original_weight DECIMAL,
  remaining_weight DECIMAL,
  unit_id BIGINT,
  supplier_id BIGINT,
  delivery_note_number VARCHAR,
  manufacture_date DATE,
  expiry_date DATE,
  shelf_location VARCHAR,
  shelf_location_en VARCHAR,
  purchase_invoice_id BIGINT,
  status VARCHAR,
  notes TEXT,
  notes_en TEXT,
  created_by BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

---

## 🚀 نصائح سريعة

1. **استخدم البحث السريع** للعثور على مادة بسهولة
2. **تحقق من تاريخ الصلاحية** لتجنب استخدام مواد منتهية
3. **حدّث الوزن المتبقي** عند استهلاك من المادة
4. **أضف ملاحظات** لتسهيل تتبع المواد
5. **استخدم الفلاتر المتعددة** للحصول على نتائج دقيقة

---

## 📞 التواصل والدعم

للإبلاغ عن مشاكل أو طلب ميزات جديدة، يرجى التواصل مع فريق التطوير.

---

**آخر تحديث:** 15 نوفمبر 2025 ✨
