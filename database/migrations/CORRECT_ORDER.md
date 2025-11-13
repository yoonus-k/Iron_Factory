# 🔢 الترتيب الصحيح لتشغيل الـ Migrations

## ⚠️ تنبيه مهم: الترتيب الصحيح

تم إعادة ترتيب ملفات الـ Migrations لضمان التشغيل الصحيح بدون أخطاء Foreign Keys.

---

## ✅ الترتيب الصحيح (27 ملف)

### المرحلة 1: المستخدمين (3 ملفات)
```
000001 - create_users_table
000002 - create_user_permissions_table
000003 - create_shift_assignments_table
```

### المرحلة 2: الموردين والفواتير (2 ملفات)
```
000004 - create_suppliers_table
000005 - create_purchase_invoices_table
```

### المرحلة 3: التصنيفات الأساسية (3 ملفات) ⭐ جديد
```
000022 - create_warehouses_table ⭐
000023 - create_material_types_table ⭐
000024 - create_units_table ⭐
```

### المرحلة 4: المواد (2 ملفات)
```
000025 - create_materials_table ⭐ (تم التحديث - يحتوي على جميع الحقول)
000026 - create_delivery_notes_table ⭐ (تم إعادة الترقيم)
```

### المرحلة 5: الحركات والتفاصيل (2 ملفات) ⭐ جديد
```
000027 - create_warehouse_transactions_table ⭐
000028 - create_material_details_table ⭐
```

### المرحلة 6: المراحل الإنتاجية (6 ملفات)
```
000008 - create_stage1_stands_table
000009 - create_stage2_processed_table
000010 - create_additives_inventory_table
000011 - create_stage3_coils_table
000012 - create_stage4_boxes_table
000013 - create_box_coils_table
```

### المرحلة 7: المراقبة والتتبع (4 ملفات)
```
000014 - create_waste_limits_table
000015 - create_waste_tracking_table
000016 - create_shift_handovers_table
000017 - create_operation_logs_table
```

### المرحلة 8: التقارير والإعدادات (4 ملفات)
```
000018 - create_generated_reports_table
000019 - create_daily_statistics_table
000020 - create_system_formulas_table
000021 - create_system_settings_table
```

---

## 🔄 التغييرات التي تم إجراؤها

### ✅ تم الدمج:
- ❌ حذف: `000025_add_warehouse_relations_to_materials_table.php` (مكرر)
- ✅ دمج جميع حقول `materials` في ملف واحد

### ✅ تم إعادة الترقيم:
- `000006_create_materials_table` → `000025_create_materials_table`
- `000007_create_delivery_notes_table` → `000026_create_delivery_notes_table`
- `000026_create_warehouse_transactions_table` → `000027_create_warehouse_transactions_table`
- `000027_create_material_details_table` → `000028_create_material_details_table`

---

## 🎯 السبب:

جدول `materials` يحتاج إلى:
1. ✅ `warehouses` (000022)
2. ✅ `material_types` (000023)
3. ✅ `units` (000024)

لذلك يجب أن ينشأ **بعدها** وليس قبلها.

---

## 🚀 أمر التشغيل

```bash
# تشغيل جميع الـ migrations بالترتيب الصحيح
php artisan migrate

# أو البدء من الصفر
php artisan migrate:fresh
```

---

## 📋 جدول materials المُحدث

الآن جدول `materials` يحتوي على **جميع** الحقول:

### الحقول الأساسية:
- `id`
- `warehouse_id` ⭐ (Foreign Key)
- `material_type_id` ⭐ (Foreign Key)
- `barcode`
- `batch_number` ⭐ جديد
- `material_type` (نصي للتوافقية)

### حقول الوزن والوحدة:
- `original_weight`
- `remaining_weight`
- `unit` (enum للتوافقية)
- `unit_id` ⭐ (Foreign Key)

### حقول المورد والفاتورة:
- `supplier_id`
- `delivery_note_number`
- `purchase_invoice_id`

### حقول التواريخ والمواقع:
- `manufacture_date` ⭐ جديد
- `expiry_date` ⭐ جديد
- `shelf_location` ⭐ جديد

### حقول الحالة:
- `status`
- `notes`
- `created_by`
- `created_at`, `updated_at`

---

## ✅ جاهز الآن!

الترتيب أصبح صحيحاً ويمكن تشغيل الـ migrations بدون أخطاء! 🎉
