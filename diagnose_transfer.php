<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== فحص تفصيلي لعملية النقل الأخيرة ===\n\n";

// 1. آخر تأكيد تم إنشاؤه
$lastConfirmation = \App\Models\ProductionConfirmation::latest()->first();

if ($lastConfirmation) {
    echo "📋 آخر تأكيد:\n";
    echo "   ID: {$lastConfirmation->id}\n";
    echo "   actual_received_quantity: " . ($lastConfirmation->actual_received_quantity ?? 'NULL') . " كجم\n";
    echo "   DeliveryNote ID: {$lastConfirmation->delivery_note_id}\n";
    echo "   Batch ID: " . ($lastConfirmation->batch_id ?? 'NULL') . "\n";
    echo "   Created: {$lastConfirmation->created_at}\n\n";
    
    // 2. فحص DeliveryNote المرتبط
    $dn = $lastConfirmation->deliveryNote;
    if ($dn) {
        echo "📦 أذن التسليم المرتبط:\n";
        echo "   ID: {$dn->id}\n";
        echo "   Type: {$dn->type}\n";
        echo "   Quantity: " . ($dn->quantity ?? 'NULL') . " كجم\n";
        echo "   quantity_used: " . ($dn->quantity_used ?? 'NULL') . " كجم\n";
        echo "   material_detail_id: " . ($dn->material_detail_id ?? 'NULL') . "\n\n";
        
        // 3. فحص MaterialDetail
        if ($dn->material_detail_id) {
            $md = \App\Models\MaterialDetail::find($dn->material_detail_id);
            if ($md) {
                echo "🏭 سجل المستودع (MaterialDetail):\n";
                echo "   ID: {$md->id}\n";
                echo "   Material ID: {$md->material_id}\n";
                echo "   Warehouse ID: {$md->warehouse_id}\n";
                echo "   Quantity: {$md->quantity} كجم\n";
                echo "   Last Updated: {$md->updated_at}\n\n";
            } else {
                echo "❌ MaterialDetail ID:{$dn->material_detail_id} غير موجود!\n\n";
            }
        } else {
            echo "⚠️  material_detail_id = NULL (هذه هي المشكلة!)\n\n";
        }
        
        // 4. فحص الكويلات
        $coils = \App\Models\DeliveryNoteCoil::where('delivery_note_id', $dn->id)->get();
        echo "🔷 الكويلات المرتبطة:\n";
        foreach ($coils as $coil) {
            echo sprintf(
                "   - Coil ID:%d | Number:%s | Original:%s | Remaining:%s | Status:%s\n",
                $coil->id,
                $coil->coil_number,
                $coil->coil_weight,
                $coil->remaining_weight,
                $coil->status
            );
        }
    } else {
        echo "❌ DeliveryNote غير موجود!\n";
    }
} else {
    echo "❌ لا توجد تأكيدات!\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "🔍 التشخيص:\n";
echo "إذا كان material_detail_id = NULL، فالمشكلة أن الكويلات لم يتم ربطها بالمستودع بشكل صحيح.\n";
