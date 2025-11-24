@extends('master')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="mb-4">
        <h2>اختبار نظام الصلاحيات</h2>
        <p class="text-muted">هذه الصفحة لاختبار صلاحيات المستخدم الحالي</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات المستخدم</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>الاسم:</th>
                            <td>{{ auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <th>الدور:</th>
                            <td>
                                @if(auth()->user()->role)
                                    {{ auth()->user()->role->role_name }}
                                    <span class="badge bg-secondary">مستوى {{ auth()->user()->role->level }}</span>
                                @else
                                    <span class="text-danger">لا يوجد دور</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>هل أنت أدمن؟</th>
                            <td>
                                @if(isAdmin())
                                    <span class="badge bg-success">نعم</span>
                                @else
                                    <span class="badge bg-secondary">لا</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">اختبار الدوال المساعدة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>الدالة</th>
                                <th>النتيجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>isAdmin()</code></td>
                                <td>{!! isAdmin() ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</td>
                            </tr>
                            <tr>
                                <td><code>hasRole('ADMIN')</code></td>
                                <td>{!! hasRole('ADMIN') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</td>
                            </tr>
                            <tr>
                                <td><code>getRoleLevel()</code></td>
                                <td><span class="badge bg-secondary">{{ getRoleLevel() }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role)
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">صلاحياتك ({{ auth()->user()->role->permissions->count() }} صلاحية)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الصلاحية</th>
                            <th>الكود</th>
                            <th>الوحدة</th>
                            <th class="text-center">إنشاء</th>
                            <th class="text-center">قراءة</th>
                            <th class="text-center">تعديل</th>
                            <th class="text-center">حذف</th>
                            <th class="text-center">موافقة</th>
                            <th class="text-center">تصدير</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(auth()->user()->role->permissions->groupBy('module') as $module => $permissions)
                            <tr class="table-secondary">
                                <td colspan="9"><strong>{{ $module }}</strong></td>
                            </tr>
                            @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->permission_name }}</td>
                                <td><code>{{ $permission->permission_code }}</code></td>
                                <td><span class="badge bg-secondary">{{ $permission->module }}</span></td>
                                <td class="text-center">{!! $permission->pivot->can_create ? '✓' : '✗' !!}</td>
                                <td class="text-center">{!! $permission->pivot->can_read ? '✓' : '✗' !!}</td>
                                <td class="text-center">{!! $permission->pivot->can_update ? '✓' : '✗' !!}</td>
                                <td class="text-center">{!! $permission->pivot->can_delete ? '✓' : '✗' !!}</td>
                                <td class="text-center">{!! $permission->pivot->can_approve ? '✓' : '✗' !!}</td>
                                <td class="text-center">{!! $permission->pivot->can_export ? '✓' : '✗' !!}</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا توجد صلاحيات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0">اختبار الصلاحيات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>صلاحيات الإنتاج:</h6>
                    <ul class="list-unstyled">
                        <li>المرحلة الأولى (قراءة): {!! canRead('STAGE1_STANDS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>المرحلة الأولى (إنشاء): {!! canCreate('STAGE1_STANDS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>المرحلة الثانية (قراءة): {!! canRead('STAGE2_PROCESSING') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>المرحلة الثالثة (قراءة): {!! canRead('STAGE3_COILS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>المرحلة الرابعة (قراءة): {!! canRead('STAGE4_PACKAGING') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>صلاحيات إدارية:</h6>
                    <ul class="list-unstyled">
                        <li>إدارة المستخدمين: {!! canRead('MANAGE_USERS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>إدارة الأدوار: {!! canRead('MANAGE_ROLES') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>إدارة المخازن: {!! canRead('MANAGE_WAREHOUSES') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>التقارير: {!! canRead('VIEW_REPORTS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                        <li>تصدير: {!! canExport('VIEW_REPORTS') ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-danger">✗</span>' !!}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .table td, .table th {
        vertical-align: middle;
    }
    code {
        font-size: 0.875rem;
    }
</style>
@endsection
