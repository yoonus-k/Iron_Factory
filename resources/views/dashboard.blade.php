@extends('master')

@section('content')
<div class="dashboard-wrapper">
    <!-- صفحة الرئيسية/لوحة التحكم -->
    <div class="page-title-section">
        <h1>لوحة التحكم</h1>
        <p class="subtitle">مرحبا بك في نظام إدارة التصنيع</p>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="statistics-grid">
        <!-- إحصائيات الإنتاج -->
        <div class="stat-card card-primary">
            <div class="stat-header">
                <i class="fas fa-industry"></i>
                <h3>الإنتاج اليومي</h3>
            </div>
            <div class="stat-value">1,250</div>
            <div class="stat-unit">وحدة</div>
            <div class="stat-footer">
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 15% عن أمس
                </span>
            </div>
        </div>

        <!-- إحصائيات الجودة -->
        <div class="stat-card card-success">
            <div class="stat-header">
                <i class="fas fa-check-circle"></i>
                <h3>معدل الجودة</h3>
            </div>
            <div class="stat-value">98.5%</div>
            <div class="stat-unit">من الإنتاج</div>
            <div class="stat-footer">
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> ممتاز
                </span>
            </div>
        </div>

        <!-- إحصائيات الأعطال -->
        <div class="stat-card card-warning">
            <div class="stat-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>الأعطال والتوقفات</h3>
            </div>
            <div class="stat-value">3</div>
            <div class="stat-unit">توقفات</div>
            <div class="stat-footer">
                <span class="stat-change negative">
                    <i class="fas fa-arrow-up"></i> بحاجة متابعة
                </span>
            </div>
        </div>

        <!-- إحصائيات الكفاءة -->
        <div class="stat-card card-info">
            <div class="stat-header">
                <i class="fas fa-tachometer-alt"></i>
                <h3>كفاءة الآلات</h3>
            </div>
            <div class="stat-value">92.3%</div>
            <div class="stat-unit">معدل الاستخدام</div>
            <div class="stat-footer">
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> تشغيل فعال
                </span>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية والمخططات -->
    <div class="charts-section">
        <!-- مخطط الإنتاج الأسبوعي -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>الإنتاج الأسبوعي</h3>
                <span class="chart-label">الوحدات المنتجة</span>
            </div>
            <div class="chart-content">
                <div class="chart-bars">
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 75%;"></div>
                        <span class="bar-label">السبت</span>
                        <span class="bar-value">950</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 85%;"></div>
                        <span class="bar-label">الأحد</span>
                        <span class="bar-value">1050</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 90%;"></div>
                        <span class="bar-label">الاثنين</span>
                        <span class="bar-value">1100</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 80%;"></div>
                        <span class="bar-label">الثلاثاء</span>
                        <span class="bar-value">1000</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 88%;"></div>
                        <span class="bar-label">الأربعاء</span>
                        <span class="bar-value">1080</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 92%;"></div>
                        <span class="bar-label">الخميس</span>
                        <span class="bar-value">1130</span>
                    </div>
                    <div class="bar-item">
                        <div class="bar-graph" style="height: 78%;"></div>
                        <span class="bar-label">الجمعة</span>
                        <span class="bar-value">950</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع حالات الإنتاج -->
        <div class="pie-container">
            <div class="chart-header">
                <h3>توزيع الإنتاج</h3>
                <span class="chart-label">حسب الحالة</span>
            </div>
            <div class="pie-chart">
                <svg viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="55" fill="none" stroke="#4CAF50" stroke-width="20"
                            stroke-dasharray="172 450" stroke-dashoffset="0"></circle>
                    <circle cx="60" cy="60" r="55" fill="none" stroke="#FFC107" stroke-width="20"
                            stroke-dasharray="135 450" stroke-dashoffset="-172"></circle>
                    <circle cx="60" cy="60" r="55" fill="none" stroke="#F44336" stroke-width="20"
                            stroke-dasharray="68 450" stroke-dashoffset="-307"></circle>
                </svg>
                <div class="pie-labels">
                    <div class="pie-label">
                        <span class="pie-color" style="background-color: #4CAF50;"></span>
                        <span class="pie-text">جاهزة: 70%</span>
                    </div>
                    <div class="pie-label">
                        <span class="pie-color" style="background-color: #FFC107;"></span>
                        <span class="pie-text">قيد المعالجة: 20%</span>
                    </div>
                    <div class="pie-label">
                        <span class="pie-color" style="background-color: #F44336;"></span>
                        <span class="pie-text">متوقفة: 10%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الخطوط الإنتاجية -->
    <div class="production-lines">
        <h3 class="section-title">حالة الخطوط الإنتاجية</h3>
        <div class="lines-grid">
            <!-- الخط الأول -->
            <div class="production-line-card">
                <div class="line-header">
                    <h4>الخط الإنتاجي #1</h4>
                    <span class="status-badge status-active">نشط</span>
                </div>
                <div class="line-stats">
                    <div class="stat">
                        <span class="stat-label">الحالة:</span>
                        <span class="stat-val">تشغيل عادي</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">الإنتاجية:</span>
                        <span class="stat-val">320 وحدة/ساعة</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">التشغيل:</span>
                        <span class="stat-val">94%</span>
                    </div>
                </div>
            </div>

            <!-- الخط الثاني -->
            <div class="production-line-card">
                <div class="line-header">
                    <h4>الخط الإنتاجي #2</h4>
                    <span class="status-badge status-active">نشط</span>
                </div>
                <div class="line-stats">
                    <div class="stat">
                        <span class="stat-label">الحالة:</span>
                        <span class="stat-val">تشغيل عادي</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">الإنتاجية:</span>
                        <span class="stat-val">315 وحدة/ساعة</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">التشغيل:</span>
                        <span class="stat-val">91%</span>
                    </div>
                </div>
            </div>

            <!-- الخط الثالث -->
            <div class="production-line-card">
                <div class="line-header">
                    <h4>الخط الإنتاجي #3</h4>
                    <span class="status-badge status-maintenance">صيانة</span>
                </div>
                <div class="line-stats">
                    <div class="stat">
                        <span class="stat-label">الحالة:</span>
                        <span class="stat-val">توقف للصيانة</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">المتوقع:</span>
                        <span class="stat-val">ساعتين</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">التقدم:</span>
                        <span class="stat-val">45%</span>
                    </div>
                </div>
            </div>

            <!-- الخط الرابع -->
            <div class="production-line-card">
                <div class="line-header">
                    <h4>الخط الإنتاجي #4</h4>
                    <span class="status-badge status-active">نشط</span>
                </div>
                <div class="line-stats">
                    <div class="stat">
                        <span class="stat-label">الحالة:</span>
                        <span class="stat-val">تشغيل عادي</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">الإنتاجية:</span>
                        <span class="stat-val">325 وحدة/ساعة</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">التشغيل:</span>
                        <span class="stat-val">89%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الأنشطة الأخيرة -->
    <div class="recent-activities">
        <h3 class="section-title">الأنشطة الأخيرة</h3>
        <div class="activities-list">
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">تم إنجاز دفعة من 500 وحدة بنجاح</p>
                    <span class="activity-time">منذ 2 ساعة</span>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">بدء صيانة دورية للخط #3</p>
                    <span class="activity-time">منذ ساعة</span>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">تنبيه: انخفاض في معدل الجودة للخط #2</p>
                    <span class="activity-time">منذ 30 دقيقة</span>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">تم تسجيل دخول مشرف الإنتاج</p>
                    <span class="activity-time">منذ 15 دقيقة</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-wrapper {
        padding: 20px;
        background-color: #f5f5f5;
    }

    .page-title-section {
        margin-bottom: 30px;
    }

    .page-title-section h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .page-title-section .subtitle {
        color: #666;
        font-size: 14px;
    }

    /* شبكة الإحصائيات */
    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #333;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .stat-card.card-primary {
        border-left-color: #007bff;
    }

    .stat-card.card-success {
        border-left-color: #28a745;
    }

    .stat-card.card-warning {
        border-left-color: #ffc107;
    }

    .stat-card.card-info {
        border-left-color: #17a2b8;
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-header i {
        font-size: 24px;
        color: #666;
    }

    .stat-header h3 {
        font-size: 14px;
        color: #666;
        margin: 0;
        flex-grow: 1;
        margin-right: 10px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }

    .stat-unit {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
    }

    .stat-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }

    .stat-change {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-change.positive {
        color: #28a745;
    }

    .stat-change.negative {
        color: #dc3545;
    }

    /* الرسوم البيانية */
    .charts-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-container,
    .pie-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .chart-header h3 {
        margin: 0;
        font-size: 16px;
        color: #1a1a1a;
    }

    .chart-label {
        font-size: 12px;
        color: #999;
    }

    /* المخطط البياني */
    .chart-bars {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        height: 200px;
        gap: 10px;
    }

    .bar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .bar-graph {
        width: 100%;
        background: linear-gradient(to top, #007bff, #4d94ff);
        border-radius: 4px 4px 0 0;
        min-height: 20px;
        transition: all 0.3s ease;
    }

    .bar-graph:hover {
        background: linear-gradient(to top, #0056b3, #2970cc);
    }

    .bar-label {
        font-size: 11px;
        color: #666;
        margin-top: 10px;
    }

    .bar-value {
        font-size: 10px;
        color: #999;
        margin-top: 5px;
    }

    /* الرسم الدائري */
    .pie-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
    }

    .pie-chart svg {
        width: 120px;
        height: 120px;
    }

    .pie-labels {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pie-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }

    .pie-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .pie-text {
        color: #666;
    }

    /* الخطوط الإنتاجية */
    .production-lines {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
    }

    .lines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .production-line-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .line-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .line-header h4 {
        margin: 0;
        font-size: 14px;
        color: #1a1a1a;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-maintenance {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .line-stats {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .stat {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
    }

    .stat-val {
        color: #1a1a1a;
        font-weight: 600;
    }

    /* الأنشطة الأخيرة */
    .recent-activities {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .activities-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .activity-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        padding: 12px;
        background: #f9f9f9;
        border-radius: 6px;
        border-right: 3px solid #007bff;
    }

    .activity-icon {
        font-size: 18px;
        color: #007bff;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        margin: 0;
        font-size: 13px;
        color: #1a1a1a;
        font-weight: 500;
    }

    .activity-time {
        font-size: 11px;
        color: #999;
        margin-top: 3px;
        display: inline-block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-wrapper {
            padding: 15px;
        }

        .statistics-grid {
            grid-template-columns: 1fr;
        }

        .charts-section {
            grid-template-columns: 1fr;
        }

        .page-title-section h1 {
            font-size: 22px;
        }

        .chart-bars {
            height: 150px;
        }
    }
</style>
@endsection
