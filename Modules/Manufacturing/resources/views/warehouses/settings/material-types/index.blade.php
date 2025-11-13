@extends('master')

@section('title', 'إدارة أنواع المواد')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <!-- رأس الصفحة -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <div class="header-info">
                        <h1>إدارة أنواع المواد</h1>
                        <p>قائمة بجميع أنواع المواد المتاحة في النظام</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.warehouse-settings.material-types.create') }}" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        إضافة نوع جديد
                    </a>
                </div>
            </div>
        </div>

        <!-- رسائل النجاح والأخطاء -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ $message }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    document.querySelector('.alert')?.remove();
                }, 3000);
            </script>
        @endif

        <!-- قسم البحث والتصفية -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-body">
                <form method="GET" action="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="filter-form">
                    <div class="filter-row">
                        <!-- البحث -->
                        <div class="filter-group">
                            <input type="text" name="search" class="form-input" placeholder="ابحث عن النوع..."
                                   value="{{ request('search') }}">
                        </div>

                        <!-- تصفية حسب الفئة -->
                        <div class="filter-group">
                            <select name="category" class="form-input">
                                <option value="">-- جميع الفئات --</option>
                                <option value="raw_material" {{ request('category') == 'raw_material' ? 'selected' : '' }}>مادة خام</option>
                                <option value="finished_product" {{ request('category') == 'finished_product' ? 'selected' : '' }}>منتج نهائي</option>
                                <option value="semi_finished" {{ request('category') == 'semi_finished' ? 'selected' : '' }}>منتج شبه مكتمل</option>
                                <option value="additive" {{ request('category') == 'additive' ? 'selected' : '' }}>مادة مضافة</option>
                                <option value="packing_material" {{ request('category') == 'packing_material' ? 'selected' : '' }}>مادة تغليف</option>
                                <option value="component" {{ request('category') == 'component' ? 'selected' : '' }}>مكون</option>
                            </select>
                        </div>

                        <!-- تصفية حسب الحالة -->
                        <div class="filter-group">
                            <select name="is_active" class="form-input">
                                <option value="">-- جميع الحالات --</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        <!-- زر البحث -->
                        <button type="submit" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            بحث
                        </button>

                        <!-- إعادة تعيين -->
                        @if (request()->anyFilled(['search', 'category', 'is_active']))
                            <a href="{{ route('manufacturing.warehouse-settings.material-types.index') }}" class="btn btn-light">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <path d="M20.49 15a9 9 0 1 1-2-9.12"></path>
                                </svg>
                                إعادة تعيين
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- الجدول -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رمز النوع</th>
                                <th>الاسم</th>
                                <th>الفئة</th>
                                <th>الوصف</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($materialTypes as $type)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge badge-primary">{{ $type->type_code }}</span></td>
                                    <td><strong>{{ $type->type_name }}</strong></td>
                                    <td>
                                        @php
                                            $categories = [
                                                'raw_material' => 'مادة خام',
                                                'finished_product' => 'منتج نهائي',
                                                'semi_finished' => 'منتج شبه مكتمل',
                                                'additive' => 'مادة مضافة',
                                                'packing_material' => 'مادة تغليف',
                                                'component' => 'مكون'
                                            ];
                                        @endphp
                                        <span class="badge badge-light">{{ $categories[$type->category] ?? $type->category }}</span>
                                    </td>
                                    <td>{{ Str::limit($type->description, 30) }}</td>
                                    <td>
                                        @if ($type->is_active)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.show', $type->id) }}"
                                               class="btn-icon" title="عرض">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="{{ route('manufacturing.warehouse-settings.material-types.edit', $type->id) }}"
                                               class="btn-icon" title="تعديل">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('manufacturing.warehouse-settings.material-types.destroy', $type->id) }}"
                                                  style="display:inline;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon delete-btn" title="حذف">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="empty-state">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                <polyline points="13 2 13 9 20 9"></polyline>
                                            </svg>
                                            <p>لا توجد أنواع مواد</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- الترقيم -->
                @if ($materialTypes->hasPages())
                    <div class="pagination-section">
                        {{ $materialTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                if (confirm('⚠️ هل أنت متأكد من حذف هذا النوع؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
                    form.submit();
                }
            });
        });
    </script>

@endsection
