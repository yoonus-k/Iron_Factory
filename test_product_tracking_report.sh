#!/bin/bash

# ملف اختبار سريع لتقرير التتبع الشامل للمنتجات

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ اختبار سريع - تقرير التتبع الشامل للمنتجات"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 1. التحقق من المتحكم
echo "1️⃣  التحقق من المتحكم..."
if [ -f "Modules/Manufacturing/Http/Controllers/ProductTrackingReportController.php" ]; then
    echo "   ✅ ProductTrackingReportController موجود"
else
    echo "   ❌ ProductTrackingReportController غير موجود"
fi

# 2. التحقق من العرض
echo ""
echo "2️⃣  التحقق من العرض..."
if [ -f "Modules/Manufacturing/resources/views/reports/product-tracking-report.blade.php" ]; then
    echo "   ✅ product-tracking-report.blade.php موجود"
else
    echo "   ❌ product-tracking-report.blade.php غير موجود"
fi

# 3. التحقق من المسارات
echo ""
echo "3️⃣  التحقق من المسارات..."
if grep -q "ProductTrackingReportController" Modules/Manufacturing/routes/production.php; then
    echo "   ✅ ProductTrackingReportController مستورد في routes"
else
    echo "   ❌ ProductTrackingReportController غير مستورد"
fi

if grep -q "product-tracking" Modules/Manufacturing/routes/production.php; then
    echo "   ✅ المسار product-tracking موجود"
else
    echo "   ❌ المسار product-tracking غير موجود"
fi

# 4. التحقق من القائمة الجانبية
echo ""
echo "4️⃣  التحقق من القائمة الجانبية..."
if grep -q "product-tracking" resources/views/layout/sidebar.blade.php; then
    echo "   ✅ الرابط موجود في القائمة الجانبية"
else
    echo "   ❌ الرابط غير موجود في القائمة الجانبية"
fi

# 5. التحقق من التوثيق
echo ""
echo "5️⃣  التحقق من التوثيق..."
if [ -f "docs/PRODUCT_TRACKING_REPORT_GUIDE.md" ]; then
    echo "   ✅ دليل التقرير موجود"
else
    echo "   ❌ دليل التقرير غير موجود"
fi

if [ -f "PRODUCT_TRACKING_REPORT_COMPLETION.md" ]; then
    echo "   ✅ ملف الإنجاز موجود"
else
    echo "   ❌ ملف الإنجاز غير موجود"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ انتهى الاختبار"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🚀 للوصول إلى التقرير:"
echo "   URL: /manufacturing/reports/product-tracking"
echo "   الصلاحية: REPORTS_PRODUCT_TRACKING"
echo "   الموقع في القائمة: التقارير > تقارير التتبع > تقرير التتبع الشامل"
