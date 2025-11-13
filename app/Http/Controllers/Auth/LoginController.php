<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // يمكن أن يكون email أو username
            'password' => 'required|string|min:6',
        ], [
            'login.required' => __('app.messages.error.validation'),
            'password.required' => __('app.messages.error.validation'),
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('login', 'remember'));
        }

        // تحديد نوع الإدخال (email أو username)
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // محاولة تسجيل الدخول
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // التوجيه حسب دور المستخدم
            return $this->authenticated($request, Auth::user());
        }

        // فشل تسجيل الدخول
        return back()
            ->withErrors([
                'login' => 'بيانات الدخول غير صحيحة',
            ])
            ->withInput($request->only('login', 'remember'));
    }

    /**
     * التوجيه بعد تسجيل الدخول الناجح
     */
    protected function authenticated(Request $request, $user)
    {
        // يمكنك التحكم في التوجيه حسب دور المستخدم
        // if ($user->role === 'admin') {
        //     return redirect()->route('admin.dashboard');
        // }
        
        return redirect()->intended('/dashboard');
    }
}
