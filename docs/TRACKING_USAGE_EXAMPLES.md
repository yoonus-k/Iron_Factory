# 🔍 أمثلة عملية على التتبع

## 📝 أمثلة استخدام دوال التتبع

### 1️⃣ التتبع من باركود الإنتاج إلى المصدر الأصلي

```php
use App\Models\ProductTracking;

// مثال: لديك منتج نهائي بباركود PR-2025-003
// تريد معرفة من أين جاءت المادة الخام

$productionBarcode = 'PR-2025-003';

// الطريقة 1: سلسلة كاملة
$chain = ProductTracking::traceBack($productionBarcode);

/*
النتيجة:
[
    {
        "barcode": "PR-2025-001",
        "stage": "warehouse",
        "action": "transferred_to_production",
        "date": "2025-01-15 10:30:00",
        "input": "RW-2025-001"
    },
    {
        "barcode": "PR-2025-002",
        "stage": "cutting",
        "action": "cut_material",
        "date": "2025-01-15 14:20:00",
        "input": "PR-2025-001"
    },
    {
        "barcode": "PR-2025-003",
        "stage": "forming",
        "action": "form_product",
        "date": "2025-01-16 09:15:00",
        "input": "PR-2025-002"
    }
]
*/

// الطريقة 2: الحصول فقط على باركود المستودع الأصلي
$originalBarcode = ProductTracking::getOriginalWarehouseBarcode($productionBarcode);
echo $originalBarcode; // RW-2025-001
```

---

### 2️⃣ التتبع من المستودع إلى جميع المنتجات

```php
use Modules\Manufacturing\Entities\MaterialBatch;

// لديك دفعة في المستودع، تريد معرفة جميع المنتجات التي صُنعت منها

$batch = MaterialBatch::where('batch_code', 'RW-2025-001')->first();

// الحصول على جميع عمليات الإنتاج
$productions = $batch->getAllProductions();

/*
النتيجة:
{
    "warehouse_barcode": "RW-2025-001",
    "total_productions": 2,
    "production_barcodes": ["PR-2025-001", "PR-2025-005"],
    "total_transferred_weight": 700,
    "productions": [
        {
            "production_barcode": "PR-2025-001",
            "date": "2025-01-15 10:30",
            "weight": 300,
            "stage": "warehouse",
            "metadata": {
                "batch_id": 1,
                "delivery_note_id": 5,
                "original_barcode": "RW-2025-001"
            }
        },
        {
            "production_barcode": "PR-2025-005",
            "date": "2025-01-20 14:45",
            "weight": 400,
            "stage": "warehouse",
            "metadata": {
                "batch_id": 1,
                "delivery_note_id": 8,
                "original_barcode": "RW-2025-001"
            }
        }
    ]
}
*/
```

---

### 3️⃣ تقرير شامل عن دفعة

```php
use Modules\Manufacturing\Entities\MaterialBatch;

$batch = MaterialBatch::find(1);
$report = $batch->getFullReport();

/*
النتيجة:
{
    "batch_code": "RW-2025-001",
    "latest_production_barcode": "PR-2025-005",
    "initial_quantity": 1000,
    "available_quantity": 300,
    "consumption_percentage": 70,
    "status": "in_production",
    "material": {
        "name": "حديد خام",
        "unit": "كجم"
    },
    "warehouse": "مستودع رئيسي",
    "total_deliveries": 2,
    "total_productions": 2,
    "production_barcodes": ["PR-2025-001", "PR-2025-005"],
    "total_transferred_weight": 700,
    "delivery_notes": [
        {
            "id": 5,
            "date": "2025-01-15",
            "quantity": 300,
            "production_barcode": "PR-2025-001"
        },
        {
            "id": 8,
            "date": "2025-01-20",
            "quantity": 400,
            "production_barcode": "PR-2025-005"
        }
    ]
}
*/

// عرض في View
echo "الدفعة: {$report['batch_code']}<br>";
echo "الكمية الأولية: {$report['initial_quantity']} {$report['material']['unit']}<br>";
echo "المتبقي: {$report['available_quantity']} {$report['material']['unit']}<br>";
echo "نسبة الاستهلاك: {$report['consumption_percentage']}%<br>";
echo "عدد أذونات النقل: {$report['total_deliveries']}<br>";
```

---

### 4️⃣ تقرير تفصيلي عن منتج

```php
use App\Models\ProductTracking;

$barcode = 'PR-2025-003';
$report = ProductTracking::fullReport($barcode);

/*
النتيجة:
{
    "barcode": "PR-2025-003",
    "total_records": 5,
    "total_waste": 15,
    "total_cost": 1250.50,
    "stages": ["warehouse", "cutting", "forming", "finishing"],
    "workers": [12, 15, 18],
    "timeline": [
        {
            "date": "2025-01-15 10:30",
            "stage": "warehouse",
            "action": "transferred_to_production",
            "waste": 0,
            "cost": 0
        },
        {
            "date": "2025-01-15 14:20",
            "stage": "cutting",
            "action": "cut_material",
            "waste": 10,
            "cost": 300
        },
        {
            "date": "2025-01-16 09:15",
            "stage": "forming",
            "action": "form_product",
            "waste": 5,
            "cost": 500
        }
    ],
    "current_status": "form_product"
}
*/
```

---

### 5️⃣ البحث عن جميع إنتاج دفعة معينة

```php
use App\Models\ProductTracking;

$batchId = 1;
$records = ProductTracking::getByBatchId($batchId);

foreach ($records as $record) {
    echo "باركود: {$record->output_barcode}<br>";
    echo "الوزن: {$record->input_weight} كجم<br>";
    echo "التاريخ: {$record->created_at->format('Y-m-d H:i')}<br>";
    echo "المرحلة: {$record->stage}<br>";
    echo "---<br>";
}
```

---

### 6️⃣ استخدام في Controller

```php
// في WarehouseRegistrationController

public function showBatchTracking($batchId)
{
    $batch = MaterialBatch::findOrFail($batchId);
    $report = $batch->getFullReport();
    
    return view('warehouse.batch-tracking', [
        'batch' => $batch,
        'report' => $report
    ]);
}

public function showProductTracking($barcode)
{
    $history = ProductTracking::traceBack($barcode);
    $report = ProductTracking::fullReport($barcode);
    
    // الحصول على الدفعة الأصلية
    $originalBarcode = ProductTracking::getOriginalWarehouseBarcode($barcode);
    $batch = null;
    if ($originalBarcode) {
        $batch = MaterialBatch::where('batch_code', $originalBarcode)->first();
    }
    
    return view('warehouse.product-tracking', [
        'barcode' => $barcode,
        'history' => $history,
        'report' => $report,
        'batch' => $batch
    ]);
}
```

---

### 7️⃣ استخدام في Blade View

```blade
{{-- عرض تتبع دفعة --}}
@php
    $report = $batch->getFullReport();
@endphp

<div class="card">
    <div class="card-header">
        <h4>تقرير الدفعة: {{ $batch->batch_code }}</h4>
    </div>
    <div class="card-body">
        <p><strong>المادة:</strong> {{ $report['material']['name'] }}</p>
        <p><strong>الكمية الأولية:</strong> {{ $report['initial_quantity'] }} {{ $report['material']['unit'] }}</p>
        <p><strong>المتبقي:</strong> {{ $report['available_quantity'] }} {{ $report['material']['unit'] }}</p>
        <p><strong>نسبة الاستهلاك:</strong> {{ number_format($report['consumption_percentage'], 2) }}%</p>
        
        <h5 class="mt-4">أذونات النقل ({{ $report['total_deliveries'] }})</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الكمية</th>
                    <th>باركود الإنتاج</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['delivery_notes'] as $note)
                <tr>
                    <td>{{ $note['date'] }}</td>
                    <td>{{ $note['quantity'] }}</td>
                    <td>
                        <code>{{ $note['production_barcode'] }}</code>
                        <a href="{{ route('warehouse.product-tracking', $note['production_barcode']) }}" 
                           class="btn btn-sm btn-info">
                            تتبع
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

---

### 8️⃣ API Endpoints

```php
// في routes/api.php

Route::prefix('tracking')->group(function () {
    // تتبع عكسي
    Route::get('trace-back/{barcode}', function ($barcode) {
        return response()->json([
            'success' => true,
            'chain' => ProductTracking::traceBack($barcode),
            'original' => ProductTracking::getOriginalWarehouseBarcode($barcode)
        ]);
    });
    
    // تقرير دفعة
    Route::get('batch/{id}', function ($id) {
        $batch = MaterialBatch::findOrFail($id);
        return response()->json([
            'success' => true,
            'report' => $batch->getFullReport()
        ]);
    });
    
    // تقرير منتج
    Route::get('product/{barcode}', function ($barcode) {
        return response()->json([
            'success' => true,
            'report' => ProductTracking::fullReport($barcode),
            'history' => ProductTracking::getProductHistory($barcode)
        ]);
    });
});
```

---

### 9️⃣ استخدام في Livewire Component

```php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ProductTracking;
use Modules\Manufacturing\Entities\MaterialBatch;

class BarcodeTracker extends Component
{
    public $barcode;
    public $history = [];
    public $batch = null;
    
    public function search()
    {
        $this->history = ProductTracking::traceBack($this->barcode);
        
        $originalBarcode = ProductTracking::getOriginalWarehouseBarcode($this->barcode);
        if ($originalBarcode) {
            $this->batch = MaterialBatch::where('batch_code', $originalBarcode)->first();
        }
    }
    
    public function render()
    {
        return view('livewire.barcode-tracker');
    }
}
```

---

## ✅ ملخص الدوال المتاحة

### ProductTracking Model
| الدالة | الوصف | المعامل | الإرجاع |
|--------|------|---------|---------|
| `traceBack($barcode)` | تتبع عكسي للمصدر | `string $barcode` | `array` |
| `getOriginalWarehouseBarcode($barcode)` | الحصول على باركود المستودع الأصلي | `string $barcode` | `string\|null` |
| `getAllProductionFromWarehouse($barcode)` | جميع الإنتاج من باركود مستودع | `string $barcode` | `array` |
| `fullReport($barcode)` | تقرير شامل عن منتج | `string $barcode` | `array` |
| `getProductHistory($barcode)` | سجل كامل لمنتج | `string $barcode` | `Collection` |
| `getByBatchId($id)` | جميع سجلات دفعة | `int $batchId` | `Collection` |

### MaterialBatch Model
| الدالة | الوصف | المعامل | الإرجاع |
|--------|------|---------|---------|
| `getProductionHistory()` | سجل الإنتاج من الدفعة | - | `Collection` |
| `getAllProductions()` | جميع الإنتاج بالتفصيل | - | `array` |
| `getFullReport()` | تقرير شامل عن الدفعة | - | `array` |
| `deliveryNotes()` | علاقة أذونات التسليم | - | `HasMany` |
