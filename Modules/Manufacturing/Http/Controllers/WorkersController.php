<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\Worker;
use App\Models\User;
use App\Models\Role;
use App\Traits\StoresNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkersController extends Controller
{
    use StoresNotifications;

    /**
     * خريطة تحويل رمز الدور إلى الوظيفة
     */
    protected const POSITION_MAP = [
        'WORKER' => 'worker',
        'SUPERVISOR' => 'supervisor',
        'TECHNICIAN' => 'technician',
        'QUALITY_INSPECTOR' => 'quality_inspector',
        'STAGE1_WORKER' => 'stage1_worker',
        'STAGE2_WORKER' => 'stage2_worker',
        'STAGE3_WORKER' => 'stage3_worker',
        'STAGE4_WORKER' => 'stage4_worker',
    ];
    /**
     * Display a listing of workers.
     */
    public function index(Request $request)
    {
        $query = Worker::query();

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by system access
        if ($request->filled('system_access')) {
            if ($request->system_access === 'with_access') {
                $query->whereNotNull('user_id');
            } elseif ($request->system_access === 'without_access') {
                $query->whereNull('user_id');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('worker_code', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $workers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Worker::count(),
            'active' => Worker::where('is_active', true)->count(),
            'with_system_access' => Worker::whereNotNull('user_id')->count(),
            'without_system_access' => Worker::whereNull('user_id')->count(),
            'supervisors' => Worker::where('position', Worker::POSITION_SUPERVISOR)->where('is_active', true)->count(),
            'workers' => Worker::where('position', Worker::POSITION_WORKER)->where('is_active', true)->count(),
        ];

        return view('manufacturing::workers.index', compact('workers', 'stats'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create()
    {
        // Get available users (those who don't have worker profiles yet)
        $availableUsers = User::whereDoesntHave('worker')->where('is_active', true)->get();

        // Get roles for positions
        $roles = Role::all();

        return view('manufacturing::workers.create', compact('availableUsers', 'roles'));
    }

    /**
     * Store a newly created worker.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getWorkerValidationRules(),
            $this->getWorkerValidationMessages()
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $userId = $this->processUserAccess($request);
            $position = $this->getPositionFromRole($request->role_id);

            $worker = Worker::create($this->getWorkerData($request, $userId, $position, true));

            $this->syncWorkerPermissions($worker, $request->role_id);
            $this->updateUserRole($userId, $request->role_id);

            // Store notification
            $this->notifyCreate(
                'عامل',
                $worker->worker_code,
                route('manufacturing.workers.show', $worker->id)
            );

            DB::commit();

            return redirect()->route('manufacturing.workers.index')
                ->with('success', 'تم إضافة العامل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العامل: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified worker.
     */
    public function show(Request $request, $id)
    {
        $worker = Worker::with(['user'])->findOrFail($id);

        // Build shift assignments query with filters
        $shiftQuery = $worker->shiftAssignments()->with(['supervisor']);

        // Filter by shift type
        if ($request->filled('shift_type')) {
            $shiftQuery->where('shift_type', $request->shift_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $shiftQuery->whereDate('shift_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $shiftQuery->whereDate('shift_date', '<=', $request->date_to);
        }

        // Get paginated shift assignments
        $shifts = $shiftQuery->orderBy('shift_date', 'desc')->paginate(20);

        return view('manufacturing::workers.show', compact('worker', 'shifts'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit($id)
    {
        $worker = Worker::findOrFail($id);

        // Get available users
        $availableUsers = User::where(function($q) use ($worker) {
            $q->whereDoesntHave('worker')
              ->orWhere('id', $worker->user_id);
        })->where('is_active', true)->get();

        // Get roles for positions
       $roles = Role::all();

        return view('manufacturing::workers.edit', compact('worker', 'availableUsers', 'roles'));
    }    /**
     * Update the specified worker.
     */
    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getWorkerValidationRules($id),
            $this->getWorkerValidationMessages()
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $userId = $this->processUserAccess($request, $worker->user_id);
            $position = $this->getPositionFromRole($request->role_id);

            $worker->update($this->getWorkerData($request, $userId, $position, false));

            $this->syncWorkerPermissions($worker, $request->role_id);
            $this->updateUserRole($userId, $request->role_id);

            // Store notification
            $this->notifyUpdate(
                'عامل',
                $worker->worker_code,
                route('manufacturing.workers.show', $worker->id)
            );

            DB::commit();

            return redirect()->route('manufacturing.workers.index')
                ->with('success', 'تم تحديث بيانات العامل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified worker.
     */
    public function destroy($id)
    {
        try {
            $worker = Worker::findOrFail($id);

            // Check if worker has active shift assignments
            $activeShifts = $worker->shiftAssignments()
                ->where('status', 'active')
                ->count();

            if ($activeShifts > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف عامل لديه ورديات نشطة');
            }

            $workerCode = $worker->worker_code;
            $worker->delete();

            // Store notification
            $this->notifyDelete(
                'عامل',
                $workerCode,
                route('manufacturing.workers.index')
            );

            return redirect()->route('manufacturing.workers.index')
                ->with('success', 'تم حذف العامل بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف العامل: ' . $e->getMessage());
        }
    }

    /**
     * Toggle worker active status.
     */
    public function toggleStatus($id)
    {
        try {
            $worker = Worker::findOrFail($id);

            $oldStatus = $worker->is_active ? 'مفعل' : 'معطل';
            $newStatus = !$worker->is_active ? 'مفعل' : 'معطل';

            $worker->update([
                'is_active' => !$worker->is_active,
                'termination_date' => !$worker->is_active ? now() : null
            ]);

            // Store notification
            $this->notifyStatusChange(
                'عامل',
                $oldStatus,
                $newStatus,
                $worker->worker_code,
                route('manufacturing.workers.show', $worker->id)
            );

            $status = $worker->is_active ? 'تم تفعيل' : 'تم تعطيل';

            return redirect()->back()
                ->with('success', "{$status} العامل بنجاح");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Generate worker code automatically.
     */
    public function generateWorkerCode(Request $request)
    {
        $position = $request->input('position', 'worker');

        $positionPrefix = match($position) {
            'supervisor' => 'SUP',
            'technician' => 'TEC',
            'quality_inspector' => 'QI',
            default => 'WRK',
        };

        // احصل على أعلى رقم عامل من الكود الأخير
        $lastWorker = Worker::orderBy('id', 'desc')->first();

        if ($lastWorker && preg_match('/(\d+)$/', $lastWorker->worker_code, $matches)) {
            // إذا كان هناك عامل سابق، زيد الرقم بـ 1
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            // بداية جديدة
            $newNumber = 1;
        }

        $code = "{$positionPrefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['worker_code' => $code]);
    }

    /**
     * Get workers available for a specific shift.
     */
    public function getAvailableWorkers(Request $request)
    {
        $shiftDate = $request->input('shift_date');
        $shiftType = $request->input('shift_type');
        $stageNumber = $request->input('stage_number');

        $query = Worker::active();

        // Filter by stage if provided
        if ($stageNumber) {
            $query->where(function($q) use ($stageNumber) {
                $q->whereJsonContains('allowed_stages', $stageNumber)
                  ->orWhereNull('allowed_stages');
            });
        }

        // Filter by shift preference
        if ($shiftType) {
            $query->where(function($q) use ($shiftType) {
                $q->where('shift_preference', $shiftType)
                  ->orWhere('shift_preference', 'any');
            });
        }

        $workers = $query->get();

        return response()->json($workers);
    }

    /**
     * الحصول على الصلاحيات الافتراضية حسب الدور/الوظيفة
     */
    public function getDefaultPermissions(Request $request)
    {
        $roleId = $request->input('role_id');

        if (!$roleId) {
            return response()->json([
                'success' => false,
                'message' => 'role_id مطلوب'
            ], 400);
        }

        $role = Role::find($roleId);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'الدور غير موجود'
            ], 404);
        }

        // الحصول على الصلاحيات المرتبطة بالدور
        $permissions = $role->permissions()->get(['id', 'name', 'display_name', 'group_name']);

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
            'total' => count($permissions)
        ]);
    }

    /**
     * الحصول على قواعد التحقق من صحة بيانات العامل
     */
    private function getWorkerValidationRules($id = null)
    {
        $baseRules = [
            'worker_code' => $id ? "required|string|max:50|unique:workers,worker_code,{$id}" : 'required|string|max:50|unique:workers,worker_code',
            'name' => 'required|string|max:255',
            'national_id' => $id ? "nullable|string|max:20|unique:workers,national_id,{$id}" : 'nullable|string|max:20|unique:workers,national_id',
            'phone' => 'nullable|string|max:20',
            'email' => $id ? "nullable|email|max:255|unique:workers,email,{$id}" : 'nullable|email|max:255|unique:workers,email',
            'role_id' => 'required|exists:roles,id',
            'allowed_stages' => 'nullable|array',
            'allowed_stages.*' => 'integer|between:1,4',
            'hourly_rate' => 'required|numeric|min:0',
            'shift_preference' => 'required|in:morning,evening,night,any',
            'hire_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'allow_system_access' => 'nullable|in:no,existing,new',
            'new_username' => 'nullable|string|max:255|unique:users,username',
            'new_email' => 'nullable|email|max:255|unique:users,email',
        ];

        // إضافة قاعدة is_active فقط للتحديث
        if ($id) {
            $baseRules['is_active'] = 'boolean';
        }

        return $baseRules;
    }

    /**
     * رسائل التحقق المخصصة بالعربية
     */
    private function getWorkerValidationMessages()
    {
        return [
            'worker_code.required' => 'كود العامل مطلوب',
            'worker_code.unique' => 'كود العامل موجود مسبقاً',
            'worker_code.max' => 'كود العامل يجب ألا يتجاوز 50 حرف',

            'name.required' => 'اسم العامل مطلوب',
            'name.max' => 'اسم العامل يجب ألا يتجاوز 255 حرف',

            'national_id.unique' => 'الرقم الوطني مستخدم من قبل',
            'national_id.max' => 'الرقم الوطني يجب ألا يتجاوز 20 حرف',

            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',

            'email.email' => 'البريد الإلكتروني يجب أن يكون بتنسيق صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',

            'role_id.required' => 'يجب اختيار الوظيفة',
            'role_id.exists' => 'الوظيفة المختارة غير موجودة',

            'allowed_stages.array' => 'صيغة المراحل المسموحة غير صحيحة',
            'allowed_stages.*.integer' => 'رقم المرحلة يجب أن يكون رقماً صحيحاً',
            'allowed_stages.*.between' => 'رقم المرحلة يجب أن يكون بين 1 و 4',

            'hourly_rate.required' => 'الأجر بالساعة مطلوب',
            'hourly_rate.numeric' => 'الأجر بالساعة يجب أن يكون رقماً',
            'hourly_rate.min' => 'الأجر بالساعة يجب أن يكون صفر أو أكثر',

            'shift_preference.required' => 'يجب اختيار تفضيل الوردية',
            'shift_preference.in' => 'تفضيل الوردية المختار غير صحيح',

            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'hire_date.date' => 'تاريخ التوظيف يجب أن يكون تاريخاً صحيحاً',

            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',

            'emergency_contact.max' => 'جهة الاتصال الطارئ يجب ألا تتجاوز 255 حرف',
            'emergency_phone.max' => 'هاتف الطوارئ يجب ألا يتجاوز 20 حرف',

            'user_id.exists' => 'المستخدم المختار غير موجود',

            'allow_system_access.in' => 'خيار الوصول للنظام غير صحيح',

            'new_username.unique' => 'اسم المستخدم الجديد موجود مسبقاً',
            'new_username.max' => 'اسم المستخدم يجب ألا يتجاوز 255 حرف',

            'new_email.email' => 'البريد الإلكتروني الجديد يجب أن يكون بتنسيق صحيح',
            'new_email.unique' => 'البريد الإلكتروني الجديد مستخدم من قبل',
            'new_email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',

            'is_active.boolean' => 'حالة التفعيل يجب أن تكون نعم أو لا',
        ];
    }

    /**
     * معالجة الوصول والمستخدم (إنشاء أو تحديد موجود)
     */
    private function processUserAccess(Request $request, $existingUserId = null)
    {
        $userId = $request->user_id;

        // إذا كان العامل لديه مستخدم موجود ولم نختر تغييره
        if ($existingUserId && $request->input('allow_system_access') !== 'new') {
            return $existingUserId;
        }

        // إنشاء مستخدم جديد إذا لزم الأمر
        if ($request->input('allow_system_access') === 'new') {
            if ($request->new_username && $request->new_email) {
                $tempPassword = \Illuminate\Support\Str::random(12);

                $newUser = User::create([
                    'name' => $request->name,
                    'username' => $request->new_username,
                    'email' => $request->new_email,
                    'password' => bcrypt($tempPassword),
                    'is_active' => true,
                    'role_id' => $request->role_id,
                ]);

                $userId = $newUser->id;
                // TODO: إرسال البريد الإلكتروني مع كلمة المرور المؤقتة
            }
        }

        return $userId;
    }

    /**
     * الحصول على الوظيفة من رمز الدور
     */
    private function getPositionFromRole($roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            return 'worker';
        }

        return self::POSITION_MAP[$role->role_code] ?? 'worker';
    }

    /**
     * الحصول على بيانات العامل للإنشاء/التحديث
     */
    private function getWorkerData(Request $request, $userId, $position, $isCreating = false)
    {
        $data = [
            'worker_code' => $request->worker_code,
            'name' => $request->name,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'position' => $position,
            'allowed_stages' => $request->allowed_stages,
            'hourly_rate' => $request->hourly_rate,
            'shift_preference' => $request->shift_preference,
            'hire_date' => $request->hire_date,
            'notes' => $request->notes,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'user_id' => $userId,
        ];

        // للإنشاء فقط
        if ($isCreating) {
            $data['is_active'] = true;
        } else {
            // للتحديث فقط
            $data['is_active'] = $request->input('is_active', true);
        }

        return $data;
    }

    /**
     * مزامنة/إرفاق الصلاحيات للعامل بناءً على الدور
     */
    private function syncWorkerPermissions(Worker $worker, $roleId)
    {
        // تم تعطيل مزامنة الصلاحيات
        return;
    }

    /**
     * تحديث دور المستخدم
     */
    private function updateUserRole($userId, $roleId)
    {
        if ($userId) {
            User::find($userId)->update(['role_id' => $roleId]);
        }
    }
}
