# تحديثات WorkerTeamsController - المطلوبة

## ملخص التحديثات المطلوبة

لكي تعمل الميزات الجديدة في الـ Views بشكل صحيح، يجب تحديث الـ Controller كما يلي:

---

## 1. تحديث Method `index()`

### الحالي (الملف الحالي):
```php
public function index()
{
    $teams = WorkerTeam::latest()->paginate(15);
    
    $stats = [
        'total' => WorkerTeam::count(),
        'active' => WorkerTeam::where('is_active', true)->count(),
        'workers' => 0,
    ];
    
    return view('manufacturing::worker-teams.index', compact('teams', 'stats'));
}
```

### المطلوب:
```php
public function index()
{
    $query = WorkerTeam::query();
    
    // البحث
    if (request('search')) {
        $search = request('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('team_code', 'like', "%{$search}%");
        });
    }
    
    // التصفية حسب الحالة
    if (request('is_active') !== '') {
        $query->where('is_active', (bool) request('is_active'));
    }
    
    $teams = $query->latest()->paginate(15);
    
    // الإحصائيات
    $stats = [
        'total' => WorkerTeam::count(),
        'active' => WorkerTeam::where('is_active', true)->count(),
        'total_workers' => DB::table('worker_teams')
                            ->selectRaw('COUNT(DISTINCT worker_ids) as count')
                            ->first()->count ?? 0,
        'avg_workers' => WorkerTeam::count() > 0 
                         ? round(DB::table('worker_teams')
                                   ->selectRaw('COUNT(DISTINCT worker_ids) as count')
                                   ->first()->count / WorkerTeam::count(), 1)
                         : 0,
    ];
    
    return view('manufacturing::worker-teams.index', compact('teams', 'stats'));
}
```

---

## 2. إضافة Method `toggleStatus()`

إذا لم يكن موجوداً، أضفه:

```php
/**
 * Toggle team status
 */
public function toggleStatus(WorkerTeam $team)
{
    if (!auth()->user()->hasPermission('WORKER_TEAMS_UPDATE')) {
        abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء');
    }
    
    try {
        $team->update([
            'is_active' => !$team->is_active
        ]);
        
        $message = $team->is_active ? 'تم تفعيل المجموعة بنجاح' : 'تم تعطيل المجموعة بنجاح';
        return redirect()->back()->with('success', $message);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}
```

---

## 3. إضافة Method `destroy()`

```php
/**
 * Delete a team (only if inactive)
 */
public function destroy(WorkerTeam $team)
{
    if (!auth()->user()->hasPermission('WORKER_TEAMS_DELETE')) {
        abort(403, 'غير مصرح لك بالحذف');
    }
    
    if ($team->is_active) {
        return redirect()->back()->with('error', 'لا يمكن حذف مجموعة نشطة. قم بتعطيلها أولاً.');
    }
    
    try {
        $team->delete();
        return redirect()->route('manufacturing.worker-teams.index')
                       ->with('success', 'تم حذف المجموعة بنجاح');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'حدث خطأ في الحذف');
    }
}
```

---

## 4. تحديث Method `store()`

### إضافة تصريح:
```php
public function store(Request $request)
{
    // إضافة هذا في البداية
    if (!auth()->user()->hasPermission('WORKER_TEAMS_CREATE')) {
        abort(403, 'غير مصرح لك بإنشاء مجموعات');
    }
    
    // بقية الكود...
}
```

---

## 5. تحديث Method `update()`

### إضافة تصريح:
```php
public function update(Request $request, WorkerTeam $team)
{
    // إضافة هذا في البداية
    if (!auth()->user()->hasPermission('WORKER_TEAMS_UPDATE')) {
        abort(403, 'غير مصرح لك بتعديل المجموعات');
    }
    
    // بقية الكود...
}
```

---

## 6. تحديث Method `show()`

### إضافة معلومات العمال:
```php
public function show(WorkerTeam $team)
{
    if (!auth()->user()->hasPermission('WORKER_TEAMS_READ')) {
        abort(403, 'غير مصرح لك بعرض التفاصيل');
    }
    
    // الحصول على العمال في المجموعة
    $workerIds = json_decode($team->worker_ids, true) ?? [];
    $workers = User::whereIn('id', $workerIds)->get();
    
    return view('manufacturing::worker-teams.show', compact('team', 'workers'));
}
```

---

## 7. Routes المطلوبة

تأكد من وجود هذه الـ Routes في `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    // Worker Teams Routes
    Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
        Route::resource('worker-teams', WorkerTeamsController::class);
        
        // إضافية
        Route::patch('worker-teams/{team}/toggle-status', [WorkerTeamsController::class, 'toggleStatus'])
             ->name('worker-teams.toggle-status');
        Route::get('worker-teams/generate-code', [WorkerTeamsController::class, 'generateTeamCode'])
             ->name('worker-teams.generate-code');
    });
});
```

---

## 8. تحديثات النموذج

تأكد من أن WorkerTeam Model يحتوي على:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerTeam extends Model
{
    protected $table = 'worker_teams';
    
    protected $fillable = [
        'team_code',
        'name',
        'description',
        'worker_ids',
        'workers_count',
        'is_active',
    ];
    
    protected $casts = [
        'worker_ids' => 'array',  // تخزين كـ JSON
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the supervisor (optional)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    
    /**
     * Get the workers
     */
    public function workers()
    {
        $ids = $this->worker_ids ?? [];
        return User::whereIn('id', $ids)->get();
    }
    
    /**
     * Get workers count
     */
    public function getWorkersCountAttribute()
    {
        return count($this->worker_ids ?? []);
    }
    
    /**
     * Get status name
     */
    public function getStatusNameAttribute()
    {
        return $this->is_active ? 'نشطة' : 'غير نشطة';
    }
}
```

---

## 9. الاختبار

بعد التحديثات، اختبر:

### 1. الفهرس (Index):
```
✅ عرض الإحصائيات (4 بطاقات)
✅ البحث يعمل
✅ التصفية حسب الحالة تعمل
✅ الجدول يظهر صحيحاً
✅ الـ Pagination تعمل
✅ الأزرار محمية بالصلاحيات
```

### 2. الإنشاء (Create):
```
✅ توليد الكود يعمل
✅ الحفظ يعمل
✅ الرسائل تظهر
```

### 3. التعديل (Edit):
```
✅ التعديل يعمل
✅ البيانات تُحفظ
```

### 4. العرض (Show):
```
✅ التفاصيل تظهر
✅ العمال يُعرضون
```

### 5. الحذف (Delete):
```
✅ الحذف متاح للمجموعات غير النشطة فقط
✅ تأكيد الحذف يعمل
```

### 6. تغيير الحالة (Toggle):
```
✅ التفعيل/التعطيل يعمل
✅ الحالة تتحدث في الجدول
```

---

## 10. ملف المرجع الكامل

الملف الكامل يجب أن يحتوي على جميع الـ Methods بهذا الترتيب:

```
1. index()           - الفهرس مع البحث والتصفية
2. create()          - عرض نموذج الإنشاء
3. generateTeamCode()  - توليد الكود
4. store()           - حفظ المجموعة الجديدة
5. show()            - عرض التفاصيل مع العمال
6. edit()            - عرض نموذج التعديل
7. update()          - حفظ التعديلات
8. destroy()         - حذف المجموعة
9. toggleStatus()    - تفعيل/تعطيل المجموعة
```

---

## الخلاصة

✅ جميع الـ Methods تحتاج إلى تصريحات
✅ الـ index يحتاج إلى البحث والتصفية
✅ الـ show يحتاج إلى تحميل العمال
✅ يجب وجود route للـ toggle-status

بعد هذه التحديثات، ستعمل جميع الميزات الجديدة بشكل مثالي!

---

**تم التوثيق بنجاح** ✅
