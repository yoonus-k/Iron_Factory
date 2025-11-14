# 📊 مخططات Mermaid إضافية للمشروع

## 1. مخطط تسلسل العمليات (Sequence Diagram)

```mermaid
sequenceDiagram
    participant U as المستخدم
    participant S as الماسح الضوئي
    participant DB as قاعدة البيانات
    participant BG as مولد الباركود
    
    U->>S: مسح باركود المادة الخام
    S->>DB: البحث عن الباركود
    DB-->>S: بيانات المادة
    S-->>U: عرض البيانات
    
    U->>U: إدخال تفاصيل الاستاند
    U->>BG: طلب توليد باركود جديد
    BG->>BG: توليد باركود ST1-XXX
    BG-->>U: الباركود الجديد
    
    U->>DB: حفظ الاستاند
    DB->>DB: تحديث الوزن المتبقي
    DB-->>U: تأكيد الحفظ
    
    U->>U: طباعة الباركود
```

---

## 2. مخطط حالات المنتج (State Diagram)

```mermaid
stateDiagram-v2
    [*] --> RawMaterial: إدخال المادة الخام
    
    RawMaterial --> Stage1: تقسيم إلى استاندات
    Stage1 --> Stage2: معالجة
    Stage2 --> Stage3: تصنيع كويلات
    Stage3 --> Stage4: تعبئة
    Stage4 --> ReadyToShip: جاهز للشحن
    ReadyToShip --> Shipped: شحن
    Shipped --> [*]
    
    RawMaterial --> Wasted: هدر في المستودع
    Stage1 --> Wasted: هدر في المرحلة 1
    Stage2 --> Wasted: هدر في المرحلة 2
    Stage3 --> Wasted: هدر في المرحلة 3
    Stage4 --> Wasted: هدر في المرحلة 4
    
    Wasted --> [*]
    
    note right of RawMaterial
        الوزن: 1000 كجم
        الحالة: متاح
    end note
    
    note right of Stage3
        إضافة اللون
        إضافة المقاس
    end note
```

---

## 3. مخطط الفئات (Class Diagram)

```mermaid
classDiagram
    class Material {
        +String barcode
        +String type
        +Float weight
        +Float remainingWeight
        +Date createdAt
        +List~String~ children
        +addChild()
        +updateWeight()
        +getHistory()
    }
    
    class Stand {
        +String barcode
        +String parentBarcode
        +String standNumber
        +String wireSize
        +Float weight
        +Float waste
        +Date createdAt
        +List~String~ children
        +process()
        +split()
    }
    
    class ProcessedItem {
        +String barcode
        +String parentBarcode
        +String processDetails
        +Float processedQuantity
        +Float waste
        +Date createdAt
        +List~String~ children
        +validate()
        +advance()
    }
    
    class Coil {
        +String barcode
        +String parentBarcode
        +String coilNumber
        +String wireSize
        +String color
        +Float weight
        +Float waste
        +Date createdAt
        +List~String~ children
        +package()
        +getSpecs()
    }
    
    class Box {
        +String barcode
        +String parentBarcode
        +String packagingType
        +Int quantityPerBox
        +Int boxCount
        +Float waste
        +String status
        +Date createdAt
        +ship()
        +track()
    }
    
    class BarcodeGenerator {
        +Map~String, Int~ counters
        +generateBarcode(stage)
        +parseBarcode(code)
        +getStage(code)
    }
    
    class Inventory {
        +Map~String, Material~ materials
        +addMaterial(data)
        +createStand(parent, data)
        +processStage2(stand, data)
        +createCoil(processed, data)
        +createBox(coil, data)
        +trackProduct(barcode)
        +calculateWaste(barcode)
    }
    
    Material "1" --> "0..*" Stand : creates
    Stand "1" --> "0..*" ProcessedItem : produces
    ProcessedItem "1" --> "0..*" Coil : generates
    Coil "1" --> "0..*" Box : packed into
    
    Inventory --> Material : manages
    Inventory --> Stand : manages
    Inventory --> ProcessedItem : manages
    Inventory --> Coil : manages
    Inventory --> Box : manages
    
    BarcodeGenerator --> Material : generates for
    BarcodeGenerator --> Stand : generates for
    BarcodeGenerator --> ProcessedItem : generates for
    BarcodeGenerator --> Coil : generates for
    BarcodeGenerator --> Box : generates for
```

---

## 4. مخطط تدفق اتخاذ القرار

```mermaid
flowchart TD
    Start([بدء عملية الإنتاج]) --> CheckMaterial{هل المادة<br/>متوفرة؟}
    
    CheckMaterial -->|نعم| ScanBarcode[مسح الباركود]
    CheckMaterial -->|لا| OrderMaterial[طلب مادة جديدة]
    OrderMaterial --> WaitDelivery[انتظار التوريد]
    WaitDelivery --> Start
    
    ScanBarcode --> ValidateBarcode{هل الباركود<br/>صحيح؟}
    ValidateBarcode -->|لا| ErrorMsg[عرض رسالة خطأ]
    ErrorMsg --> ScanBarcode
    
    ValidateBarcode -->|نعم| LoadData[تحميل البيانات]
    LoadData --> CheckWeight{هل الوزن<br/>كافي؟}
    
    CheckWeight -->|لا| InsufficientWeight[وزن غير كافي]
    InsufficientWeight --> ScanBarcode
    
    CheckWeight -->|نعم| InputDetails[إدخال التفاصيل]
    InputDetails --> ValidateInput{هل البيانات<br/>صحيحة؟}
    
    ValidateInput -->|لا| ShowError[عرض الأخطاء]
    ShowError --> InputDetails
    
    ValidateInput -->|نعم| CalculateWaste[حساب الهدر]
    CalculateWaste --> CheckWasteLimit{هل الهدر<br/>ضمن الحد؟}
    
    CheckWasteLimit -->|لا| WasteAlert[⚠️ تحذير: هدر مرتفع]
    WasteAlert --> ConfirmProceed{الاستمرار؟}
    ConfirmProceed -->|لا| InputDetails
    
    CheckWasteLimit -->|نعم| GenerateBarcode[توليد باركود جديد]
    ConfirmProceed -->|نعم| GenerateBarcode
    
    GenerateBarcode --> SaveData[حفظ في قاعدة البيانات]
    SaveData --> UpdateParent[تحديث الوزن المتبقي]
    UpdateParent --> PrintLabel[طباعة الملصق]
    
    PrintLabel --> CheckNextStage{هل يوجد<br/>مرحلة تالية؟}
    CheckNextStage -->|نعم| MoveToNext[الانتقال للمرحلة التالية]
    CheckNextStage -->|لا| Complete([اكتمال العملية])
    
    MoveToNext --> Start
    
    style Start fill:#667eea,color:#fff
    style Complete fill:#2ecc71,color:#fff
    style ErrorMsg fill:#e74c3c,color:#fff
    style WasteAlert fill:#f39c12,color:#fff
    style InsufficientWeight fill:#e74c3c,color:#fff
```

---

## 5. مخطط التوزيع (Deployment Diagram)

```mermaid
graph TB
    subgraph "سحابة الإنتاج"
        subgraph "خوادم التطبيق"
            API[API Server<br/>Node.js / Express]
            Auth[خادم المصادقة<br/>JWT]
        end
        
        subgraph "قاعدة البيانات"
            DB[(PostgreSQL<br/>قاعدة البيانات الرئيسية)]
            Cache[(Redis<br/>التخزين المؤقت)]
        end
        
        subgraph "التخزين"
            FileStorage[تخزين الملفات<br/>AWS S3]
        end
    end
    
    subgraph "العملاء"
        WebApp[تطبيق الويب<br/>React]
        Mobile[تطبيق الموبايل<br/>React Native]
        Scanner[أجهزة المسح<br/>الضوئي]
    end
    
    WebApp -->|HTTPS| API
    Mobile -->|HTTPS| API
    Scanner -->|HTTPS| API
    
    API -->|Query| DB
    API -->|Cache| Cache
    API -->|Store| FileStorage
    
    Auth -.->|Verify| API
    
    style API fill:#3498db,color:#fff
    style DB fill:#2ecc71,color:#fff
    style WebApp fill:#9b59b6,color:#fff
    style Mobile fill:#f39c12,color:#fff
```

---

## 6. مخطط الأنشطة اليومية

```mermaid
gantt
    title جدول الإنتاج اليومي
    dateFormat HH:mm
    axisFormat %H:%M
    
    section المستودع
    استلام المواد           :done, 08:00, 1h
    فحص الجودة             :done, 09:00, 30m
    إدخال البيانات          :done, 09:30, 30m
    
    section المرحلة 1
    تقسيم المواد            :active, 10:00, 2h
    توليد الباركود          :active, 11:00, 1h
    
    section المرحلة 2
    المعالجة الأولية        :12:00, 2h
    الفحص                  :14:00, 30m
    
    section المرحلة 3
    التصنيع                :14:30, 3h
    مراقبة الجودة          :17:00, 1h
    
    section المرحلة 4
    التعبئة                :crit, 18:00, 2h
    الشحن                  :crit, 20:00, 1h
```

---

## 7. مخطط العلاقات والاعتماديات

```mermaid
graph LR
    subgraph Frontend
        UI[واجهة المستخدم]
        Scanner[ماسح الباركود]
        Forms[النماذج]
        Reports[التقارير]
    end
    
    subgraph Backend
        API[REST API]
        Auth[المصادقة]
        BarcodeService[خدمة الباركود]
        InventoryService[خدمة المخزون]
        ReportService[خدمة التقارير]
    end
    
    subgraph Database
        Materials[(المواد)]
        Users[(المستخدمين)]
        Logs[(السجلات)]
    end
    
    subgraph External
        Printer[الطابعة]
        Email[البريد الإلكتروني]
    end
    
    UI --> API
    Scanner --> API
    Forms --> API
    Reports --> ReportService
    
    API --> Auth
    API --> BarcodeService
    API --> InventoryService
    API --> ReportService
    
    InventoryService --> Materials
    Auth --> Users
    ReportService --> Logs
    
    BarcodeService --> Printer
    ReportService --> Email
    
    style UI fill:#9b59b6,color:#fff
    style API fill:#3498db,color:#fff
    style Materials fill:#2ecc71,color:#fff
```

---

## 8. مخطط الأدوار والصلاحيات

```mermaid
graph TD
    System[نظام الإنتاج] --> Admin[مدير النظام]
    System --> Manager[مدير الإنتاج]
    System --> Supervisor[مشرف]
    System --> Worker[عامل]
    System --> Viewer[مراقب]
    
    Admin --> AdminPerms[صلاحيات المدير]
    Manager --> ManagerPerms[صلاحيات المدير]
    Supervisor --> SupervisorPerms[صلاحيات المشرف]
    Worker --> WorkerPerms[صلاحيات العامل]
    Viewer --> ViewerPerms[صلاحيات المراقب]
    
    AdminPerms --> P1[إدارة المستخدمين]
    AdminPerms --> P2[تعديل الإعدادات]
    AdminPerms --> P3[حذف البيانات]
    AdminPerms --> P4[عرض جميع التقارير]
    
    ManagerPerms --> P5[الموافقة على العمليات]
    ManagerPerms --> P6[تعديل البيانات]
    ManagerPerms --> P7[عرض التقارير]
    ManagerPerms --> P8[إدارة المخزون]
    
    SupervisorPerms --> P9[مراقبة الإنتاج]
    SupervisorPerms --> P10[تعديل بيانات المرحلة]
    SupervisorPerms --> P11[عرض التقارير الأساسية]
    
    WorkerPerms --> P12[مسح الباركود]
    WorkerPerms --> P13[إدخال البيانات]
    WorkerPerms --> P14[عرض المهام]
    
    ViewerPerms --> P15[عرض البيانات فقط]
    ViewerPerms --> P16[عرض التقارير]
    
    style Admin fill:#e74c3c,color:#fff
    style Manager fill:#f39c12,color:#fff
    style Supervisor fill:#3498db,color:#fff
    style Worker fill:#2ecc71,color:#fff
    style Viewer fill:#95a5a6,color:#fff
```

---

## 9. مخطط تدفق الهدر والخسائر

```mermaid
graph TD
    Start[بدء العملية<br/>1000 كجم] --> Stage1Process[المرحلة 1<br/>التقسيم]
    
    Stage1Process --> S1Output[الإنتاج: 950 كجم]
    Stage1Process --> S1Waste[الهدر: 50 كجم<br/>5%]
    
    S1Output --> Stage2Process[المرحلة 2<br/>المعالجة]
    Stage2Process --> S2Output[الإنتاج: 900 كجم]
    Stage2Process --> S2Waste[الهدر: 50 كجم<br/>5.3%]
    
    S2Output --> Stage3Process[المرحلة 3<br/>التصنيع]
    Stage3Process --> S3Output[الإنتاج: 850 كجم]
    Stage3Process --> S3Waste[الهدر: 50 كجم<br/>5.6%]
    
    S3Output --> Stage4Process[المرحلة 4<br/>التعبئة]
    Stage4Process --> S4Output[الإنتاج النهائي<br/>820 كجم]
    Stage4Process --> S4Waste[الهدر: 30 كجم<br/>3.5%]
    
    S1Waste --> TotalWaste[إجمالي الهدر<br/>180 كجم<br/>18%]
    S2Waste --> TotalWaste
    S3Waste --> TotalWaste
    S4Waste --> TotalWaste
    
    TotalWaste --> WasteAnalysis{تحليل الهدر}
    WasteAnalysis --> Acceptable[مقبول<br/>< 20%]
    WasteAnalysis --> High[مرتفع<br/>> 20%]
    
    High --> Investigation[التحقيق في الأسباب]
    Investigation --> Improvement[خطة التحسين]
    
    style Start fill:#2ecc71,color:#fff
    style S4Output fill:#3498db,color:#fff
    style TotalWaste fill:#e74c3c,color:#fff
    style High fill:#e74c3c,color:#fff
    style Acceptable fill:#2ecc71,color:#fff
```

---

## 10. مخطط الأداء والمقاييس (Metrics)

```mermaid
mindmap
  root((مؤشرات الأداء))
    الإنتاجية
      الإنتاج اليومي
      الإنتاج الشهري
      معدل الإنجاز
      الطاقة الإنتاجية
    الجودة
      نسبة العيوب
      مطابقة المواصفات
      رضا العملاء
      معدل الإرجاع
    الكفاءة
      استهلاك المواد
      نسبة الهدر
      وقت التصنيع
      تكلفة الإنتاج
    الموارد
      عدد العاملين
      ساعات العمل
      استخدام المعدات
      صيانة الآلات
    المالية
      الإيرادات
      التكاليف
      الأرباح
      العائد على الاستثمار
```

---

## كيفية استخدام هذه المخططات

1. **في التوثيق**: انسخ الكود والصقه في ملفات Markdown
2. **في GitHub**: سيتم عرضها تلقائياً
3. **في المواقع**: استخدم مكتبة Mermaid.js
4. **في التطبيقات**: دمج مع أدوات مثل Draw.io أو PlantUML

### مثال HTML:
```html
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>mermaid.initialize({startOnLoad:true});</script>
</head>
<body>
    <div class="mermaid">
        graph TD
            A[Start] --> B[Process]
            B --> C[End]
    </div>
</body>
</html>
```
