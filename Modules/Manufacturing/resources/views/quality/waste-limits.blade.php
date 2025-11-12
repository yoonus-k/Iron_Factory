@extends('master')

@section('title', 'إعدادات حدود الهدر')

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-settings"></i>
                إعدادات حدود الهدر المسموحة
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> لوحة التحكم
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>الجودة والهدر</span>
                <i class="feather icon-chevron-left"></i>
                <span>حدود الهدر</span>
            </nav>
        </div>

        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="um-alert-custom um-alert-success" role="alert">
                <i class="feather icon-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="um-alert-custom um-alert-error" role="alert">
                <i class="feather icon-x-circle"></i>
                {{ session('error') }}
                <button type="button" class="um-alert-close" onclick="this.parentElement.style.display='none'">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        @endif

        <!-- Info Alert -->
        <div class="um-alert-custom um-alert-info" role="alert">
            <i class="feather icon-info"></i>
            <div>
                <strong>ملاحظة:</strong> عند تجاوز نسبة التحذير، سيتم إرسال تنبيه للمشرف. عند تجاوز الحد الأقصى، سيتم إيقاف العملية الإنتاجية حتى موافقة المشرف.
            </div>
        </div>

        <!-- Waste Limits Configuration -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-sliders"></i>
                    تكوين حدود الهدر لكل مرحلة
                </h4>
            </div>

            <div class="um-card-body">
                <form method="POST" action="#" class="um-waste-limits-form">
                    @csrf

                    <!-- Stage 1: Cutting/Stands -->
                    <div class="um-stage-limit-section">
                        <div class="um-stage-header">
                            <div class="um-stage-icon um-stage-1">
                                <i class="feather icon-scissors"></i>
                            </div>
                            <h5 class="um-stage-title">المرحلة 1: التقسيم والاستاندات</h5>
                        </div>

                        <div class="um-form-row">
                            <div class="um-form-group">
                                <label class="um-form-label">نسبة التحذير (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage1_warning" class="um-form-control" value="1.5" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                                <small class="um-form-hint">سيتم إرسال تنبيه عند الوصول لهذه النسبة</small>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">الحد الأقصى للهدر (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage1_max" class="um-form-control" value="2.5" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                                <small class="um-form-hint">سيتم إيقاف الإنتاج عند تجاوز هذه النسبة</small>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إيقاف الإنتاج</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="stage1_stop_production" id="stage1_stop" checked>
                                    <label for="stage1_stop"></label>
                                </div>
                                <small class="um-form-hint">إيقاف تلقائي عند التجاوز</small>
                            </div>
                        </div>
                    </div>

                    <hr class="um-divider">

                    <!-- Stage 2: Processing -->
                    <div class="um-stage-limit-section">
                        <div class="um-stage-header">
                            <div class="um-stage-icon um-stage-2">
                                <i class="feather icon-cpu"></i>
                            </div>
                            <h5 class="um-stage-title">المرحلة 2: المعالجة</h5>
                        </div>

                        <div class="um-form-row">
                            <div class="um-form-group">
                                <label class="um-form-label">نسبة التحذير (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage2_warning" class="um-form-control" value="2.0" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">الحد الأقصى للهدر (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage2_max" class="um-form-control" value="3.5" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إيقاف الإنتاج</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="stage2_stop_production" id="stage2_stop" checked>
                                    <label for="stage2_stop"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="um-divider">

                    <!-- Stage 3: Coils -->
                    <div class="um-stage-limit-section">
                        <div class="um-stage-header">
                            <div class="um-stage-icon um-stage-3">
                                <i class="feather icon-codesandbox"></i>
                            </div>
                            <h5 class="um-stage-title">المرحلة 3: تصنيع الكويلات</h5>
                        </div>

                        <div class="um-form-row">
                            <div class="um-form-group">
                                <label class="um-form-label">نسبة التحذير (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage3_warning" class="um-form-control" value="3.5" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">الحد الأقصى للهدر (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage3_max" class="um-form-control" value="5.0" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إيقاف الإنتاج</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="stage3_stop_production" id="stage3_stop" checked>
                                    <label for="stage3_stop"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="um-divider">

                    <!-- Stage 4: Packaging -->
                    <div class="um-stage-limit-section">
                        <div class="um-stage-header">
                            <div class="um-stage-icon um-stage-4">
                                <i class="feather icon-package"></i>
                            </div>
                            <h5 class="um-stage-title">المرحلة 4: التعبئة والتغليف</h5>
                        </div>

                        <div class="um-form-row">
                            <div class="um-form-group">
                                <label class="um-form-label">نسبة التحذير (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage4_warning" class="um-form-control" value="1.0" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">الحد الأقصى للهدر (%)</label>
                                <div class="um-input-group">
                                    <input type="number" name="stage4_max" class="um-form-control" value="2.0" min="0" max="100" step="0.1" required>
                                    <span class="um-input-addon">%</span>
                                </div>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إيقاف الإنتاج</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="stage4_stop_production" id="stage4_stop" checked>
                                    <label for="stage4_stop"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="um-divider">

                    <!-- Notification Settings -->
                    <div class="um-stage-limit-section">
                        <div class="um-stage-header">
                            <div class="um-stage-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="feather icon-bell"></i>
                            </div>
                            <h5 class="um-stage-title">إعدادات الإشعارات</h5>
                        </div>

                        <div class="um-form-row">
                            <div class="um-form-group">
                                <label class="um-form-label">إرسال إشعارات للمشرفين</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="notify_supervisors" id="notify_supervisors" checked>
                                    <label for="notify_supervisors"></label>
                                </div>
                                <small class="um-form-hint">تفعيل الإشعارات التلقائية</small>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إرسال إشعارات بريد إلكتروني</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="notify_email" id="notify_email" checked>
                                    <label for="notify_email"></label>
                                </div>
                                <small class="um-form-hint">إرسال تنبيهات عبر البريد</small>
                            </div>

                            <div class="um-form-group">
                                <label class="um-form-label">إرسال إشعارات SMS</label>
                                <div class="um-toggle-switch">
                                    <input type="checkbox" name="notify_sms" id="notify_sms">
                                    <label for="notify_sms"></label>
                                </div>
                                <small class="um-form-hint">إرسال رسائل نصية عاجلة</small>
                            </div>
                        </div>
                    </div>

                    <div class="um-form-actions">
                        <button type="submit" class="um-btn um-btn-primary">
                            <i class="feather icon-save"></i>
                            حفظ الإعدادات
                        </button>
                        <button type="reset" class="um-btn um-btn-outline">
                            <i class="feather icon-refresh-cw"></i>
                            استعادة الافتراضية
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Current Limits Summary -->
        <section class="um-main-card">
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-bar-chart"></i>
                    ملخص الحدود الحالية
                </h4>
            </div>

            <!-- Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>المرحلة</th>
                            <th>نسبة التحذير</th>
                            <th>الحد الأقصى</th>
                            <th>إيقاف الإنتاج</th>
                            <th>آخر تحديث</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="um-stage-label">
                                    <span class="um-stage-icon um-stage-1"><i class="feather icon-scissors"></i></span>
                                    المرحلة 1: التقسيم
                                </div>
                            </td>
                            <td><span class="um-badge um-badge-warning">1.5%</span></td>
                            <td><span class="um-badge um-badge-danger">2.5%</span></td>
                            <td><span class="um-badge um-badge-success"><i class="feather icon-check"></i> نعم</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="um-stage-label">
                                    <span class="um-stage-icon um-stage-2"><i class="feather icon-cpu"></i></span>
                                    المرحلة 2: المعالجة
                                </div>
                            </td>
                            <td><span class="um-badge um-badge-warning">2.0%</span></td>
                            <td><span class="um-badge um-badge-danger">3.5%</span></td>
                            <td><span class="um-badge um-badge-success"><i class="feather icon-check"></i> نعم</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="um-stage-label">
                                    <span class="um-stage-icon um-stage-3"><i class="feather icon-codesandbox"></i></span>
                                    المرحلة 3: الكويلات
                                </div>
                            </td>
                            <td><span class="um-badge um-badge-warning">3.5%</span></td>
                            <td><span class="um-badge um-badge-danger">5.0%</span></td>
                            <td><span class="um-badge um-badge-success"><i class="feather icon-check"></i> نعم</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="um-stage-label">
                                    <span class="um-stage-icon um-stage-4"><i class="feather icon-package"></i></span>
                                    المرحلة 4: التغليف
                                </div>
                            </td>
                            <td><span class="um-badge um-badge-warning">1.0%</span></td>
                            <td><span class="um-badge um-badge-danger">2.0%</span></td>
                            <td><span class="um-badge um-badge-success"><i class="feather icon-check"></i> نعم</span></td>
                            <td>{{ date('Y-m-d H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cards - Mobile View -->
            <div class="um-mobile-view">
                <div class="um-category-card">
                    <div class="um-category-card-header">
                        <div class="um-category-info">
                            <h5>المرحلة 1: التقسيم</h5>
                        </div>
                    </div>
                    <div class="um-category-card-body">
                        <div class="um-info-row">
                            <span>نسبة التحذير:</span>
                            <span class="um-badge um-badge-warning">1.5%</span>
                        </div>
                        <div class="um-info-row">
                            <span>الحد الأقصى:</span>
                            <span class="um-badge um-badge-danger">2.5%</span>
                        </div>
                        <div class="um-info-row">
                            <span>إيقاف الإنتاج:</span>
                            <span class="um-badge um-badge-success">نعم</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .um-stage-limit-section {
            margin-bottom: 2rem;
        }

        .um-stage-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .um-stage-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .um-stage-icon.um-stage-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .um-stage-icon.um-stage-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .um-stage-icon.um-stage-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .um-stage-icon.um-stage-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .um-stage-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .um-toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .um-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .um-toggle-switch label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 26px;
        }

        .um-toggle-switch label:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        .um-toggle-switch input:checked + label {
            background-color: #4caf50;
        }

        .um-toggle-switch input:checked + label:before {
            transform: translateX(24px);
        }

        .um-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 2rem 0;
        }

        .um-stage-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .um-stage-label .um-stage-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    </style>

    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.um-alert-custom');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => alert.style.display = 'none', 300);
                }, 8000);
            });

            // Form validation
            document.querySelector('.um-waste-limits-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate warning < max for each stage
                for (let i = 1; i <= 4; i++) {
                    const warning = parseFloat(document.querySelector(`input[name="stage${i}_warning"]`).value);
                    const max = parseFloat(document.querySelector(`input[name="stage${i}_max"]`).value);
                    
                    if (warning >= max) {
                        alert(`خطأ في المرحلة ${i}: نسبة التحذير يجب أن تكون أقل من الحد الأقصى`);
                        return false;
                    }
                }
                
                alert('تم حفظ الإعدادات بنجاح!');
                // هنا سيتم إضافة كود الحفظ الفعلي لاحقاً
            });
        });
    </script>

@endsection
