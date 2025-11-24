<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Mail\UserCredentialsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::with('roleRelation');

        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }

        // التصفية حسب الحالة
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // التصفية حسب الدور
        if ($request->has('role') && $request->role) {
            $query->where('role_id', $request->role);
        }

        $users = $query->paginate(20);
        $roles = Role::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * عرض صفحة إنشاء مستخدم جديد
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'shift' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // توليد كلمة مرور عشوائية (لم تُرسل هنا - ستُرسل لاحقاً)
            $temporaryPassword = Str::random(12);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($temporaryPassword),
                'role_id' => $request->role_id,
                'shift' => $request->shift,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();
            return redirect()->route('users.show', $user)->with('success', '✅ تم إنشاء المستخدم بنجاح! يمكنك الآن إرسال بيانات الدخول من زر "إرسال بيانات الدخول"');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * عرض تفاصيل المستخدم
     */
    public function show(User $user)
    {
        $user->load('roleRelation', 'userPermissions');
        $operationLogs = $user->operationLogs()->orderBy('created_at', 'desc')->get();
        return view('users.show', compact('user', 'operationLogs'));
    }

    /**
     * عرض صفحة تعديل المستخدم
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'shift' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'shift' => $request->shift,
                'is_active' => $request->has('is_active'),
            ];

            // تحديث كلمة المرور فقط إذا تم إدخالها
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();
            return redirect()->route('users.index')->with('success', '✅ تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * حذف المستخدم
     */
    public function destroy(User $user)
    {
        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return back()->with('error', '❌ لا يمكنك حذف حسابك الخاص');
        }

        DB::beginTransaction();
        try {
            // حذف الصلاحيات الخاصة بالمستخدم
            $user->userPermissions()->delete();

            // حذف المستخدم
            $user->delete();

            DB::commit();
            return redirect()->route('users.index')->with('success', '✅ تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تفعيل/تعطيل المستخدم
     */
    public function toggleActive(User $user)
    {
        // منع تعطيل المستخدم الحالي
        if ($user->id === auth()->id() && $user->is_active) {
            return back()->with('error', '❌ لا يمكنك تعطيل حسابك الخاص');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? '✅ تم تفعيل المستخدم بنجاح' : '✅ تم تعطيل المستخدم بنجاح';
        return back()->with('success', $message);
    }

    /**
     * إعادة إرسال بيانات الدخول للمستخدم
     */
    public function resendCredentials(User $user)
    {
        // التحقق من الصلاحيات
        if (!auth()->user()->hasPermission('MANAGE_USERS', 'update')) {
            return back()->with('error', '❌ لا توجد لديك صلاحيات كافية');
        }

        try {
            // توليد كلمة مرور عشوائية جديدة
            $newPassword = Str::random(12);

            // تحديث كلمة المرور في قاعدة البيانات
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // إرسال البريد الإلكتروني - استخدام Mail::to()->send()
            Mail::to($user->email)->send(new UserCredentialsMail($user, $newPassword));

            // تسجيل في السجل
            Log::info('Credentials email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()
            ]);

            return back()->with('success', '✅ تم إرسال بيانات الدخول الجديدة إلى البريد الإلكتروني بنجاح للعنوان: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);            // رسالة خطأ واضحة
            $errorMessage = '❌ حدث خطأ في إرسال البريد: ' . $e->getMessage();

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * تسجيل دخول المستخدم الحالي كموظف آخر (للاختبار)
     */
    public function impersonate(User $user)
    {
        // التحقق من الصلاحيات - فقط Admin
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return back()->with('error', '❌ لا توجد لديك صلاحيات كافية');
        }

        // منع تسجيل دخول بحساب معطل
        if (!$user->is_active) {
            return back()->with('error', '❌ لا يمكن تسجيل الدخول بحساب معطل');
        }

        // حفظ معرف المستخدم الأصلي في الجلسة
        session(['impersonated_by' => $currentUser->id]);

        // تسجيل الدخول بحساب الموظف
        auth()->guard('web')->login($user);

        return redirect('/dashboard')->with('success', '✅ تم تسجيل الدخول كموظف: ' . $user->name);
    }

    /**
     * العودة إلى حسابك الأصلي
     */
    public function exitImpersonation()
    {
        if (!session('impersonated_by')) {
            return redirect('/dashboard');
        }

        $originalUserId = session('impersonated_by');
        $originalUser = User::find($originalUserId);

        // حذف من الجلسة
        session()->forget('impersonated_by');

        // تسجيل دخول بالحساب الأصلي
        auth()->guard('web')->login($originalUser);

        return redirect('/dashboard')->with('success', '✅ تم العودة إلى حسابك الأصلي');
    }
}
