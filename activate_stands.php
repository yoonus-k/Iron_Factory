<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Stand;
use Illuminate\Support\Facades\DB;

echo "=== تفعيل جميع الاستاندات ===\n\n";

$updated = DB::table('stands')->update(['is_active' => true]);

echo "تم تفعيل {$updated} استاند بنجاح! ✅\n\n";

// عرض البيانات المحدثة
$stands = Stand::all();
echo "الاستاندات المتاحة الآن:\n";
echo "=====================\n\n";

foreach ($stands as $stand) {
    echo "- رقم: {$stand->stand_number} | وزن: {$stand->weight} كجم | حالة: {$stand->status} | نشط: " . ($stand->is_active ? '✓' : '✗') . "\n";
}
