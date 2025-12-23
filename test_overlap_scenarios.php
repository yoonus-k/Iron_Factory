<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== سيناريوهات التداخل ===\n\n";

// السيناريو 1: ورديتان متصلتان (لا تداخل)
echo "سيناريو 1: ورديتان متصلتان\n";
echo "الوردية A: 06:00 → 18:00\n";
echo "الوردية B: 18:00 → 06:00\n";
testOverlap('2025-12-21', '06:00', '18:00', '18:00', '06:00');

echo "\n" . str_repeat("=", 50) . "\n\n";

// السيناريو 2: تداخل جزئي
echo "سيناريو 2: تداخل جزئي\n";
echo "الوردية A: 06:00 → 18:00\n";
echo "الوردية B: 14:00 → 22:00\n";
testOverlap('2025-12-21', '06:00', '18:00', '14:00', '22:00');

echo "\n" . str_repeat("=", 50) . "\n\n";

// السيناريو 3: تداخل كامل
echo "سيناريو 3: تداخل كامل\n";
echo "الوردية A: 06:00 → 18:00\n";
echo "الوردية B: 08:00 → 16:00\n";
testOverlap('2025-12-21', '06:00', '18:00', '08:00', '16:00');

echo "\n" . str_repeat("=", 50) . "\n\n";

// السيناريو 4: نفس الوقت تماماً
echo "سيناريو 4: نفس الوقت تماماً\n";
echo "الوردية A: 06:00 → 18:00\n";
echo "الوردية B: 06:00 → 18:00\n";
testOverlap('2025-12-21', '06:00', '18:00', '06:00', '18:00');

function testOverlap($date, $start1, $end1, $start2, $end2) {
    $shift1Start = \Carbon\Carbon::parse($date . ' ' . $start1);
    $shift1End = \Carbon\Carbon::parse($date . ' ' . $end1);
    $shift2Start = \Carbon\Carbon::parse($date . ' ' . $start2);
    $shift2End = \Carbon\Carbon::parse($date . ' ' . $end2);
    
    // إصلاح الورديات التي تمتد لليوم التالي
    if ($shift1End->lessThanOrEqualTo($shift1Start)) {
        $shift1End->addDay();
    }
    if ($shift2End->lessThanOrEqualTo($shift2Start)) {
        $shift2End->addDay();
    }
    
    echo "الفترة A: " . $shift1Start->format('Y-m-d H:i') . " → " . $shift1End->format('Y-m-d H:i') . "\n";
    echo "الفترة B: " . $shift2Start->format('Y-m-d H:i') . " → " . $shift2End->format('Y-m-d H:i') . "\n";
    
    // فحص التداخل: هل هناك أي جزء من الوقت مشترك؟
    $hasOverlap = (
        // A يبدأ خلال B
        ($shift1Start->greaterThanOrEqualTo($shift2Start) && $shift1Start->lessThan($shift2End)) ||
        // A ينتهي خلال B
        ($shift1End->greaterThan($shift2Start) && $shift1End->lessThanOrEqualTo($shift2End)) ||
        // B يبدأ خلال A
        ($shift2Start->greaterThanOrEqualTo($shift1Start) && $shift2Start->lessThan($shift1End)) ||
        // B ينتهي خلال A
        ($shift2End->greaterThan($shift1Start) && $shift2End->lessThanOrEqualTo($shift1End))
    );
    
    if ($hasOverlap) {
        echo "🔴 يوجد تداخل! لا يمكن للعامل أن يكون في الورديتين\n";
    } else {
        echo "🟢 لا يوجد تداخل - يمكن للعامل أن يكون في الورديتين\n";
    }
}
