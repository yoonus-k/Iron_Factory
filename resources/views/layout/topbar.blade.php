<div class="topbar">
    <!-- تنبيه Impersonation -->


    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb">
            <span>{{ __('app.menu.dashboard') }}</span>
        </div>
    </div>

    <div class="topbar-right">
        <!-- الإشعارات -->
        <div class="notification-wrapper">
            <div class="notification-icon" id="notificationBell" onclick="toggleNotificationDropdown()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notifBadge">0</span>
            </div>

            <!-- Dropdown الإشعارات -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h6>الإشعارات</h6>
                    <button class="mark-all-read-btn" onclick="markAllNotificationsRead(event)" title="وضع علامة على الكل كمقروء">
                        <i class="fas fa-check-double"></i>
                    </button>
                </div>

                <div class="notification-list" id="notificationList">
                    <!-- سيتم ملؤها ديناميكياً -->
                    <div class="notification-loading">
                        <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
                    </div>
                </div>

                <div class="notification-footer">
                    <a href="{{ route('notifications.index') }}" class="view-all-btn">
                        <i class="fas fa-list"></i> عرض جميع الإشعارات
                    </a>
                </div>
            </div>
        </div>

        <!-- اللغة -->
        <div class="language-switcher">
            <button class="language-btn" onclick="toggleLanguageMenu()" title="اختر اللغة">
                <i class="fas fa-globe"></i>
                <span class="current-lang">{{ app()->getLocale() === 'ar' ? 'AR' : 'EN' }}</span>
            </button>
            <div class="language-menu" id="languageMenu" style="display: none;">
                <a href="#" data-lang="ar" onclick="changeLanguage('ar', event)" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                    <i class="fas fa-check"></i> العربية
                </a>
                <a href="#" data-lang="en" onclick="changeLanguage('en', event)" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">
                    <i class="fas fa-check"></i> English
                </a>
            </div>
        </div>

        <!-- البحث -->
        <div class="search-box">
            <input type="text" placeholder="البحث..." class="form-control">
            <i class="fas fa-search"></i>
        </div>

        <!-- ملف المستخدم -->
        <div class="user-profile-wrapper" style="position: relative;">
            <div class="user-profile" onclick="toggleUserMenu()">
                <img src="{{ asset('assets/images/avatars/manager.png') }}" alt="الصورة الشخصية" class="user-avatar">
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <small class="user-role">{{ '@' . auth()->user()->username }}</small>
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>

            <!-- قائمة المستخدم -->
            <div class="user-menu" id="userMenu">
                <a href="{{ route('profile') }}"><i class="fas fa-user"></i> الملف الشخصي</a>
                <hr>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('app.users.logout') }}
                </a>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<style>
/* تصميم الإشعارات */
.notification-wrapper {
    position: relative;
    margin: 0 15px;
}

.notification-icon {
    position: relative;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.notification-icon:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: scale(1.05);
}

.notification-icon i {
    font-size: 20px;
    color: #3b82f6;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white !important;
    font-size: 11px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Dropdown الإشعارات */
.notification-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 380px;
    max-height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    z-index: 1000;
    overflow: hidden;
    animation: slideDown 0.3s ease;
}

.notification-dropdown.show {
    display: flex;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
    color: white;
}

.mark-all-read-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.mark-all-read-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.notification-list {
    flex: 1;
    overflow-y: auto;
    max-height: 360px;
}

.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    gap: 12px;
    align-items: start;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #eff6ff;
    border-right: 3px solid #3b82f6;
}

.notification-item.unread:hover {
    background: #dbeafe;
}

.notification-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon-wrapper.success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.notification-icon-wrapper.danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.notification-icon-wrapper.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.notification-icon-wrapper.info {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.notification-title .new-badge {
    background: #3b82f6;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}

.notification-message {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 6px;
    line-height: 1.4;
}

.notification-time {
    font-size: 11px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 4px;
}

.notification-loading,
.notification-empty {
    padding: 40px 20px;
    text-align: center;
    color: #9ca3af;
}

.notification-empty i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
}

.notification-footer {
    padding: 12px 20px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

.view-all-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
</style>

<script>
    // إحلاب بيانات الإشعارات من الـ index
    const notificationsFromIndex = {!! json_encode($notifications ?? []) !!};

    window.notificationsData = notificationsFromIndex;

    let notificationDropdownOpen = false;

    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        notificationDropdownOpen = !notificationDropdownOpen;

        if (notificationDropdownOpen) {
            dropdown.classList.add('show');
            loadNotifications();
        } else {
            dropdown.classList.remove('show');
        }
    }

    function loadNotifications() {
        const listEl = document.getElementById('notificationList');

        // استخدام البيانات المدرجة مباشرة في الـ blade بدلاً من الـ fetch
        const notifications = window.notificationsData || [];

        if (notifications && notifications.length > 0) {
            // عرض فقط أول 10 إشعارات وتحميل باقي عند الحاجة
            listEl.innerHTML = notifications.slice(0, 10).map((notif, index) => {
                const colorClass = notif.color === 'success' ? 'success' :
                                  notif.color === 'danger' ? 'danger' :
                                  notif.color === 'warning' ? 'warning' : 'info';

                const icon = notif.icon || 'fas fa-bell';

                return `
                    <div class="notification-item ${!notif.is_read ? 'unread' : ''}" onclick="handleNotificationClick(${notif.id}, '${notif.action_url || ''}')">
                        <div class="notification-icon-wrapper ${colorClass}">
                            <i class="${icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">
                                ${notif.title}
                                ${!notif.is_read ? '<span class="new-badge">جديد</span>' : ''}
                            </div>
                            <div class="notification-message">${notif.message}</div>
                            <div class="notification-time">
                                <i class="fas fa-clock"></i> ${notif.created_at}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // إذا كان هناك أكثر من 10 إشعارات، أضف زر "تحميل المزيد"
            if (notifications.length > 10) {
                listEl.innerHTML += `
                    <div style="padding: 12px 20px; text-align: center; border-top: 1px solid #e5e7eb; background: #f9fafb;">
                        <button onclick="toggleNotificationDropdown()" style="background: none; border: none; color: #3b82f6; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-chevron-down"></i> عرض جميع الإشعارات
                        </button>
                    </div>
                `;
            }
        } else {
            listEl.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p style="margin-top: 12px; font-size: 14px;">لا توجد إشعارات جديدة</p>
                </div>
            `;
        }
    }

    function handleNotificationClick(notifId, actionUrl) {
        // وضع علامة كمقروء
        fetch(`/notifications/${notifId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(() => {
            updateNotificationBadge();
            if (actionUrl && actionUrl !== '' && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function markAllNotificationsRead(event) {
        event.stopPropagation();

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge();
                loadNotifications();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('show');
    }

    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    function changeLanguage(lang, event) {
        event.preventDefault();
        localStorage.setItem('language', lang);

        fetch(`/locale/${lang}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        }).then(response => {
            window.location.reload();
        }).catch(error => {
            console.error('Error:', error);
            window.location.href = `/locale/${lang}`;
        });
    }

    function updateNotificationBadge() {
        // استخدام البيانات المدرجة بدلاً من الـ fetch
        const unreadCount = (window.notificationsData || []).filter(n => !n.is_read).length;
        const badge = document.getElementById('notifBadge');

        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    // إغلاق القوائم عند النقر خارجها
    document.addEventListener('click', function(event) {
        const notificationWrapper = document.querySelector('.notification-wrapper');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationWrapper && !notificationWrapper.contains(event.target)) {
            notificationDropdown.classList.remove('show');
            notificationDropdownOpen = false;
        }

        const userProfileWrapper = document.querySelector('.user-profile-wrapper');
        const userMenu = document.getElementById('userMenu');

        if (userProfileWrapper && !userProfileWrapper.contains(event.target)) {
            userMenu.classList.remove('show');
        }

        const languageBtn = document.querySelector('.language-btn');
        const languageMenu = document.getElementById('languageMenu');

        if (languageBtn && !languageBtn.contains(event.target) && languageMenu && !languageMenu.contains(event.target)) {
            languageMenu.style.display = 'none';
        }
    });

    // تحميل عند بدء الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationBadge();

        // لا حاجة للـ polling الآن لأن البيانات مدرجة في الـ index
        // setInterval(updateNotificationBadge, 30000);

        const currentLang = document.documentElement.getAttribute('lang') || 'ar';
        document.body.classList.remove('lang-ar', 'lang-en');
        document.body.classList.add(`lang-${currentLang}`);

        document.querySelectorAll('.language-menu a').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-lang') === currentLang) {
                link.classList.add('active');
            }
        });

        const currentLangSpan = document.querySelector('.current-lang');
        if (currentLangSpan) {
            currentLangSpan.textContent = currentLang === 'ar' ? 'AR' : 'EN';
        }
    });
</script>
