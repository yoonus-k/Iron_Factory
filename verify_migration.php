<?php
// Quick verification script for database schema
require 'bootstrap/app.php';

$db = app('db');

// Check materials table
echo "=== MATERIALS TABLE SCHEMA ===\n";
$materials_columns = $db->select("DESCRIBE materials");
$weight_fields_found = [];
foreach ($materials_columns as $col) {
    if (in_array($col->Field, ['original_weight', 'remaining_weight', 'unit_id'])) {
        $weight_fields_found[] = $col->Field;
    }
}

if (!empty($weight_fields_found)) {
    echo "❌ ERROR: Weight fields still found in materials: " . implode(', ', $weight_fields_found) . "\n";
} else {
    echo "✅ SUCCESS: Weight/unit fields removed from materials table\n";
}

// Check material_details table
echo "\n=== MATERIAL_DETAILS TABLE SCHEMA ===\n";
$details_columns = $db->select("DESCRIBE material_details");
$new_fields_found = [];
foreach ($details_columns as $col) {
    if (in_array($col->Field, ['original_weight', 'remaining_weight', 'unit_id'])) {
        $new_fields_found[] = $col->Field;
    }
}

if (count($new_fields_found) === 3) {
    echo "✅ SUCCESS: All weight/unit fields added to material_details table\n";
    echo "   - original_weight: " . (in_array('original_weight', $new_fields_found) ? "✅" : "❌") . "\n";
    echo "   - remaining_weight: " . (in_array('remaining_weight', $new_fields_found) ? "✅" : "❌") . "\n";
    echo "   - unit_id: " . (in_array('unit_id', $new_fields_found) ? "✅" : "❌") . "\n";
} else {
    echo "❌ ERROR: Not all weight/unit fields found in material_details\n";
    echo "   Found: " . implode(', ', $new_fields_found) . "\n";
}

// Show sample data
echo "\n=== SAMPLE DATA ===\n";
$sample = $db->table('materials')->with('materialDetails.unit')->first();
if ($sample) {
    echo "Material: {$sample->material_type}\n";
    echo "Material Details Count: " . count($sample->materialDetails) . "\n";
    if (count($sample->materialDetails) > 0) {
        $detail = $sample->materialDetails[0];
        echo "First Detail:\n";
        echo "  - original_weight: {$detail->original_weight}\n";
        echo "  - remaining_weight: {$detail->remaining_weight}\n";
        echo "  - unit: {$detail->unit?->name}\n";
    }
} else {
    echo "No materials found in database\n";
}

echo "\n✅ Verification complete!\n";
?>
