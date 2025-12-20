# تعليمات تثبيت المزامنة التلقائية على Windows

## الطريقة 1: Windows Task Scheduler (موصى بها)

1. افتح PowerShell كـ Administrator (مهم جداً)
2. انتقل لمجلد المشروع:
   ```
   cd C:\Users\mon3em\Desktop\tesr_docker
   ```
3. نفذ الأمر:
   ```
   Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force
   .\install_sync_task.ps1
   ```

## الطريقة 2: تشغيل يدوي مع Startup

1. اضغط Win + R واكتب: `shell:startup`
2. انسخ الملف `start_sync_scheduler.bat` للمجلد الذي سيفتح
3. عند إعادة تشغيل الكمبيوتر، سيبدأ تلقائياً

## الطريقة 3: تشغيل يدوي الآن

```powershell
php artisan schedule:work
```

اتركه يعمل في الخلفية

## التحقق من المزامنة

```powershell
# عرض قائمة المهام المجدولة
php artisan schedule:list

# معالجة يدوية
php artisan sync:process-pending

# فحص العمليات المعلقة
php artisan tinker
>>> \App\Models\PendingSync::pending()->count()
```

## ملاحظات

- المزامنة تحدث كل 5 دقائق
- البيانات تُحفظ في `pending_syncs` حتى تتم المزامنة
- عند الاتصال بالإنترنت، ستظهر رسالة "تم الاتصال بالإنترنت"
