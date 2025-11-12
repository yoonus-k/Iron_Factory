#!/usr/bin/env bash

# 🚀 Iron Factory - Warehouse Views Installation Script
# تم إنشاء هذا السكريبت لتسهيل تثبيت الـ Views الجديدة

echo "================================"
echo "🏭 Iron Factory - Warehouse Setup"
echo "================================"
echo ""

echo "✅ الملفات المُنشأة:"
echo ""
echo "📂 أذون التسليم (Delivery Notes)"
echo "   • delivery-notes/index.blade.php"
echo "   • delivery-notes/create.blade.php"
echo "   • delivery-notes/edit.blade.php"
echo "   • delivery-notes/show.blade.php"
echo ""

echo "📂 فواتير المشتريات (Purchase Invoices)"
echo "   • purchase-invoices/index.blade.php"
echo "   • purchase-invoices/create.blade.php"
echo "   • purchase-invoices/edit.blade.php"
echo "   • purchase-invoices/show.blade.php"
echo ""

echo "📂 الموردين (Suppliers)"
echo "   • suppliers/index.blade.php"
echo "   • suppliers/create.blade.php"
echo "   • suppliers/edit.blade.php"
echo "   • suppliers/show.blade.php"
echo ""

echo "📂 الصبغات والبلاستيك (Additives)"
echo "   • additives/index.blade.php"
echo "   • additives/create.blade.php"
echo "   • additives/edit.blade.php"
echo "   • additives/show.blade.php"
echo ""

echo "✅ التحديثات:"
echo "   • resources/views/layout/sidebar.blade.php (تحديث الروابط)"
echo ""

echo "================================"
echo "📋 الخطوات التالية"
echo "================================"
echo ""

echo "1️⃣  إنشاء Migration Files:"
echo "   php artisan make:migration create_delivery_notes_table"
echo "   php artisan make:migration create_purchase_invoices_table"
echo "   php artisan make:migration create_suppliers_table"
echo "   php artisan make:migration create_additives_table"
echo ""

echo "2️⃣  إنشاء Models:"
echo "   php artisan make:model DeliveryNote"
echo "   php artisan make:model PurchaseInvoice"
echo "   php artisan make:model Supplier"
echo "   php artisan make:model Additive"
echo ""

echo "3️⃣  إنشاء Controllers:"
echo "   php artisan make:controller Warehouses/DeliveryNoteController --resource"
echo "   php artisan make:controller Warehouses/PurchaseInvoiceController --resource"
echo "   php artisan make:controller Warehouses/SupplierController --resource"
echo "   php artisan make:controller Warehouses/AdditiveController --resource"
echo ""

echo "4️⃣  تطبيق الـ Migrations:"
echo "   php artisan migrate"
echo ""

echo "================================"
echo "✨ الحالة: مكتمل وجاهز للاستخدام"
echo "================================"
