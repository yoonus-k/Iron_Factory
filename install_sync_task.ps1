# PowerShell Script to create Windows Task Scheduler for Laravel Sync

$taskName = "Laravel Sync Scheduler"
$taskDescription = "Auto sync every 5 minutes between local and online server"
$projectPath = "C:\Users\mon3em\Desktop\tesr_docker"
$phpPath = "php" # أو المسار الكامل مثل: "C:\php\php.exe"

# حذف المهمة إذا كانت موجودة مسبقاً
Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

# إنشاء Action (تشغيل الأمر)
$action = New-ScheduledTaskAction -Execute $phpPath `
    -Argument "artisan schedule:work" `
    -WorkingDirectory $projectPath

# إنشاء Trigger (عند بدء تشغيل Windows)
$trigger = New-ScheduledTaskTrigger -AtStartup

# إعدادات إضافية
$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RestartCount 3 `
    -RestartInterval (New-TimeSpan -Minutes 1)

# إنشاء المهمة
Register-ScheduledTask `
    -TaskName $taskName `
    -Description $taskDescription `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -RunLevel Highest `
    -Force

Write-Host "Task created successfully: $taskName" -ForegroundColor Green
Write-Host ""
Write-Host "Sync will start automatically on system boot" -ForegroundColor Cyan
Write-Host "To start now: Start-ScheduledTask -TaskName '$taskName'" -ForegroundColor Yellow
Write-Host "To check status: Get-ScheduledTask -TaskName '$taskName'" -ForegroundColor Yellow
Write-Host "To remove: Unregister-ScheduledTask -TaskName '$taskName' -Confirm:`$false" -ForegroundColor Yellow
