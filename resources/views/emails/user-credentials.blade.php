<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات دخول حسابك</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .greeting strong {
            color: #667eea;
        }
        .info-box {
            background-color: #f8f9fa;
            border-right: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #667eea;
            width: 40%;
        }
        .info-value {
            color: #333;
            word-break: break-all;
            direction: ltr;
            text-align: left;
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .credentials-section {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            border-right: none;
        }
        .credentials-section h3 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .cred-item {
            margin-bottom: 12px;
        }
        .cred-label {
            color: #856404;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cred-value {
            background-color: #ffffff;
            padding: 12px;
            margin-top: 5px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #333;
            direction: ltr;
            text-align: left;
            border: 1px solid #ffc107;
            font-size: 14px;
            font-weight: 600;
        }
        .action-section {
            text-align: center;
            margin: 30px 0;
        }
        .login-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-weight: 600;
            font-size: 14px;
        }
        .login-button:hover {
            opacity: 0.9;
        }
        .security-note {
            background-color: #f1f3f5;
            border-right: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #495057;
        }
        .security-note strong {
            color: #dc3545;
        }
        .instructions {
            background-color: #d1ecf1;
            border-right: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #0c5460;
        }
        .instructions strong {
            color: #0c5460;
        }
        .instructions ol {
            margin: 10px 0 0 20px;
            padding-left: 0;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
        .footer-text {
            margin-bottom: 10px;
        }
        .divider {
            border: none;
            border-top: 1px solid #dee2e6;
            margin: 20px 0;
        }
        .highlight {
            background-color: #fff8e1;
            padding: 2px 4px;
            border-radius: 2px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🔐 بيانات دخول حسابك</h1>
            <p>مرحباً بك في نظام إدارة المستودع</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                مرحباً بك <strong>{{ $user->name }}</strong> 👋
            </div>

            <p>
                تم إنشاء حسابك بنجاح في نظام إدارة المستودع. يمكنك الآن تسجيل الدخول باستخدام البيانات أدناه:
            </p>

            <!-- Credentials -->
            <div class="credentials-section">
                <h3>📝 بيانات الدخول الخاصة بك</h3>

                <div class="cred-item">
                    <div class="cred-label">👤 اسم المستخدم</div>
                    <div class="cred-value">{{ $username }}</div>
                </div>

                <div class="cred-item">
                    <div class="cred-label">🔑 كلمة المرور</div>
                    <div class="cred-value">{{ $password }}</div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <strong>📌 خطوات الدخول:</strong>
                <ol>
                    <li>اذهب إلى صفحة تسجيل الدخول</li>
                    <li>أدخل اسم المستخدم أعلاه</li>
                    <li>أدخل كلمة المرور أعلاه</li>
                    <li>اضغط على "دخول"</li>
                    <li><strong>غيّر كلمة المرور الخاصة بك فوراً</strong> (يفضل في أول دخول)</li>
                </ol>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <strong>⚠️ تنبيه أمني:</strong>
                <ul style="margin-left: 20px; padding-left: 0;">
                    <li>لا تشارك بيانات الدخول مع أحد</li>
                    <li>غيّر كلمة المرور الخاصة بك بعد الدخول الأول</li>
                    <li>استخدم كلمات مرور قوية وفريدة</li>
                    <li>تأكد من تسجيل الخروج بعد انتهاء العمل</li>
                </ul>
            </div>

            <!-- Action Button -->
            <div class="action-section">
                <a href="{{ config('app.url') }}/login" class="login-button">
                    🔓 تسجيل الدخول الآن
                </a>
            </div>

            <!-- Additional Info -->
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">📧 البريد الإلكتروني:</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">👨‍💼 اسم كامل:</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏷️ الدور:</span>
                    <span class="info-value">
                        @if($user->roleRelation)
                            {{ $user->roleRelation->role_name }}
                        @else
                            غير محدد
                        @endif
                    </span>
                </div>
            </div>

            <hr class="divider">

            <p style="color: #6c757d; font-size: 13px; margin-top: 20px;">
                إذا واجهت أي مشاكل في تسجيل الدخول، يرجى التواصل مع قسم الدعم الفني.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                <strong>نظام إدارة المستودع - Iron Factory</strong>
            </div>
            <div class="footer-text">
                © {{ date('Y') }} جميع الحقوق محفوظة
            </div>
            <div class="footer-text" style="color: #999; margin-top: 10px;">
                هذا البريد تم إرساله تلقائياً، يرجى عدم الرد عليه
            </div>
        </div>
    </div>
</body>
</html>
