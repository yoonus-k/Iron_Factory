# 🎨 ملف CSS المحسّن للنظام

## استخدام هذا الملف
يمكنك إضافة هذا الملف كـ `style.css` منفصل أو دمج الأكواد في ملف HTML الحالي.

```css
/* ========================================
   المتغيرات والألوان الأساسية
   ======================================== */

:root {
    /* ألوان المراحل */
    --warehouse-gradient: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    --stage1-gradient: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    --stage2-gradient: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    --stage3-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    --stage4-gradient: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    
    /* ألوان الحالة */
    --success-color: #2ecc71;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --info-color: #3498db;
    
    /* ألوان محايدة */
    --gray-50: #fafafa;
    --gray-100: #f5f5f5;
    --gray-200: #eeeeee;
    --gray-300: #e0e0e0;
    --gray-400: #bdbdbd;
    --gray-500: #9e9e9e;
    --gray-600: #757575;
    --gray-700: #616161;
    --gray-800: #424242;
    --gray-900: #212121;
    
    /* المسافات */
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
    --space-10: 40px;
    --space-12: 48px;
    
    /* الحدود */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --radius-2xl: 24px;
    --radius-full: 9999px;
    
    /* الظلال */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
    --shadow-xl: 0 16px 40px rgba(0, 0, 0, 0.16);
    --shadow-2xl: 0 24px 48px rgba(0, 0, 0, 0.20);
    
    /* الانتقالات */
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
    
    /* الخطوط */
    --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Arial', sans-serif;
    --font-mono: 'Courier New', Courier, monospace;
}

/* ========================================
   إعادة تعيين أساسية
   ======================================== */

*,
*::before,
*::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html {
    font-size: 16px;
    scroll-behavior: smooth;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    font-family: var(--font-sans);
    background: var(--primary-gradient);
    color: var(--gray-900);
    line-height: 1.6;
    min-height: 100vh;
}

/* ========================================
   تحسينات النصوص
   ======================================== */

h1, h2, h3, h4, h5, h6 {
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: var(--space-4);
}

h1 { font-size: 2.5rem; }
h2 { font-size: 2rem; }
h3 { font-size: 1.75rem; }
h4 { font-size: 1.5rem; }
h5 { font-size: 1.25rem; }
h6 { font-size: 1rem; }

p {
    margin-bottom: var(--space-4);
}

/* ========================================
   الحاوية الرئيسية
   ======================================== */

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: var(--space-6);
}

.page-wrapper {
    background: white;
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-2xl);
    overflow: hidden;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========================================
   شريط التنقل
   ======================================== */

.navbar {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    padding: var(--space-4) var(--space-6);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-md);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
}

.navbar-logo {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.navbar-menu {
    display: flex;
    gap: var(--space-4);
    list-style: none;
}

.navbar-item {
    position: relative;
}

.navbar-link {
    color: white;
    text-decoration: none;
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
    display: block;
}

.navbar-link:hover {
    background: rgba(255, 255, 255, 0.1);
}

.navbar-link.active {
    background: rgba(255, 255, 255, 0.2);
}

.navbar-user {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    color: white;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: transform var(--transition-fast);
}

.user-avatar:hover {
    transform: scale(1.1);
}

/* ========================================
   Dashboard الرئيسية
   ======================================== */

.dashboard {
    padding: var(--space-8);
}

.dashboard-header {
    margin-bottom: var(--space-8);
}

.dashboard-title {
    font-size: 2.5rem;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.dashboard-subtitle {
    color: var(--gray-600);
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-6);
    margin-bottom: var(--space-8);
}

.stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-md);
    border: 2px solid transparent;
    transition: all var(--transition-base);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--info-color);
}

.stat-card.warehouse::before {
    background: var(--warehouse-gradient);
}

.stat-card.stage1::before {
    background: var(--stage1-gradient);
}

.stat-card.stage2::before {
    background: var(--stage2-gradient);
}

.stat-card.stage3::before {
    background: var(--stage3-gradient);
}

.stat-card.stage4::before {
    background: var(--stage4-gradient);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-lg);
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: var(--space-4);
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.95rem;
    margin-bottom: var(--space-2);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.stat-change {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    font-size: 0.9rem;
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-full);
    font-weight: 600;
}

.stat-change.positive {
    background: #d4edda;
    color: #155724;
}

.stat-change.negative {
    background: #f8d7da;
    color: #721c24;
}

/* ========================================
   الجداول
   ======================================== */

.table-container {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: var(--space-8);
}

.table-header {
    padding: var(--space-6);
    border-bottom: 2px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gray-50);
}

.table-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--gray-900);
}

.table-actions {
    display: flex;
    gap: var(--space-3);
}

.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: var(--primary-gradient);
    color: white;
}

.data-table th {
    padding: var(--space-4);
    text-align: right;
    font-weight: 600;
    white-space: nowrap;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.data-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background var(--transition-fast);
}

.data-table tbody tr:hover {
    background: var(--gray-50);
}

.data-table td {
    padding: var(--space-4);
    text-align: right;
}

/* ========================================
   الأزرار
   ======================================== */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    padding: var(--space-3) var(--space-5);
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    white-space: nowrap;
}

.btn:active {
    transform: scale(0.97);
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.btn-success {
    background: var(--stage2-gradient);
    color: white;
}

.btn-danger {
    background: var(--warehouse-gradient);
    color: white;
}

.btn-warning {
    background: var(--stage1-gradient);
    color: white;
}

.btn-info {
    background: var(--stage3-gradient);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 2px solid currentColor;
    color: var(--gray-700);
}

.btn-outline:hover {
    background: var(--gray-100);
}

.btn-sm {
    padding: var(--space-2) var(--space-3);
    font-size: 0.9rem;
}

.btn-lg {
    padding: var(--space-4) var(--space-6);
    font-size: 1.1rem;
}

.btn-icon {
    padding: var(--space-2);
    border-radius: var(--radius-md);
    background: var(--gray-100);
    border: none;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-icon:hover {
    background: var(--gray-200);
    transform: scale(1.1);
}

/* ========================================
   النماذج
   ======================================== */

.form {
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--space-8);
    box-shadow: var(--shadow-md);
}

.form-section {
    margin-bottom: var(--space-8);
}

.form-section-title {
    font-size: 1.3rem;
    margin-bottom: var(--space-4);
    padding-bottom: var(--space-3);
    border-bottom: 2px solid var(--gray-200);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-5);
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--space-2);
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: var(--danger-color);
}

.form-input,
.form-select,
.form-textarea {
    padding: var(--space-3);
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-md);
    font-size: 1rem;
    font-family: inherit;
    transition: all var(--transition-fast);
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--info-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-input.error,
.form-select.error,
.form-textarea.error {
    border-color: var(--danger-color);
}

.form-error {
    color: var(--danger-color);
    font-size: 0.85rem;
    margin-top: var(--space-1);
}

.form-help {
    color: var(--gray-600);
    font-size: 0.85rem;
    margin-top: var(--space-1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

/* ========================================
   البطاقات
   ======================================== */

.card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: all var(--transition-base);
}

.card:hover {
    box-shadow: var(--shadow-lg);
}

.card-header {
    padding: var(--space-5);
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.card-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--gray-900);
}

.card-body {
    padding: var(--space-5);
}

.card-footer {
    padding: var(--space-5);
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: flex-end;
    gap: var(--space-3);
}

/* ========================================
   الشارات والأيقونات
   ======================================== */

.badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-primary {
    background: #e3f2fd;
    color: #1565c0;
}

.badge-success {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-warning {
    background: #fff3e0;
    color: #e65100;
}

.badge-danger {
    background: #ffebee;
    color: #c62828;
}

.badge-info {
    background: #e1f5fe;
    color: #01579b;
}

/* ========================================
   Toast/الإشعارات
   ======================================== */

.toast-container {
    position: fixed;
    top: var(--space-6);
    left: var(--space-6);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.toast {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--space-4) var(--space-5);
    box-shadow: var(--shadow-xl);
    display: flex;
    align-items: center;
    gap: var(--space-3);
    min-width: 300px;
    opacity: 0;
    transform: translateX(-100%);
    transition: all var(--transition-base);
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toast-success .toast-icon {
    background: var(--success-color);
    color: white;
}

.toast-error .toast-icon {
    background: var(--danger-color);
    color: white;
}

.toast-warning .toast-icon {
    background: var(--warning-color);
    color: white;
}

.toast-message {
    flex: 1;
    color: var(--gray-900);
}

/* ========================================
   Loading/التحميل
   ======================================== */

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ========================================
   Responsive/متجاوب
   ======================================== */

@media (max-width: 1024px) {
    .container {
        padding: var(--space-4);
    }
    
    .navbar-menu {
        display: none;
    }
}

@media (max-width: 768px) {
    h1 { font-size: 2rem; }
    h2 { font-size: 1.75rem; }
    h3 { font-size: 1.5rem; }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .table-header {
        flex-direction: column;
        gap: var(--space-4);
        align-items: stretch;
    }
}

/* ========================================
   Utilities/الأدوات المساعدة
   ======================================== */

.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.mt-1 { margin-top: var(--space-1); }
.mt-2 { margin-top: var(--space-2); }
.mt-3 { margin-top: var(--space-3); }
.mt-4 { margin-top: var(--space-4); }
.mt-5 { margin-top: var(--space-5); }
.mt-6 { margin-top: var(--space-6); }
.mt-8 { margin-top: var(--space-8); }

.mb-1 { margin-bottom: var(--space-1); }
.mb-2 { margin-bottom: var(--space-2); }
.mb-3 { margin-bottom: var(--space-3); }
.mb-4 { margin-bottom: var(--space-4); }
.mb-5 { margin-bottom: var(--space-5); }
.mb-6 { margin-bottom: var(--space-6); }
.mb-8 { margin-bottom: var(--space-8); }

.hidden { display: none; }
.visible { display: block; }

.d-flex { display: flex; }
.flex-column { flex-direction: column; }
.justify-center { justify-content: center; }
.align-center { align-items: center; }
.gap-2 { gap: var(--space-2); }
.gap-3 { gap: var(--space-3); }
.gap-4 { gap: var(--space-4); }

/* ========================================
   طباعة
   ======================================== */

@media print {
    .navbar,
    .btn,
    .form,
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
    }
    
    .page-wrapper {
        box-shadow: none;
    }
}
```

## 📝 ملاحظات الاستخدام

1. **المتغيرات CSS**: استخدم `var(--variable-name)` في أي مكان
2. **Responsive**: التصميم متجاوب تلقائياً
3. **الألوان**: سهل التخصيص من المتغيرات
4. **الأدوات المساعدة**: فئات جاهزة للاستخدام السريع
