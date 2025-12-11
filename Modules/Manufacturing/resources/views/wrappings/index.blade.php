@extends('master')

@section('title', 'إدارة اللفافات')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-tape me-2" style="color: #3498db;"></i>
                إدارة اللفافات
            </h2>
            <p class="text-muted mb-0">إضافة وإدارة اللفافات المستخدمة في المرحلة الثالثة</p>
        </div>
        <a href="{{ route('manufacturing.wrappings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            إضافة لفاف جديد
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Wrappings Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($wrappings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">#</th>
                                <th>رقم اللفاف</th>
                                <th class="text-center">الوزن (كجم)</th>
                                <th>الوصف</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center" style="width: 150px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wrappings as $index => $wrapping)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $wrappings->firstItem() + $index }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $wrapping->wrapping_number }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ number_format($wrapping->weight, 2) }} كجم</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $wrapping->description ?? 'لا يوجد وصف' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($wrapping->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                نشط
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times-circle me-1"></i>
                                                غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('manufacturing.wrappings.edit', $wrapping->id) }}" 
                                               class="btn btn-outline-primary" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="confirmDelete({{ $wrapping->id }}, '{{ $wrapping->wrapping_number }}')"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $wrappings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tape fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد لفافات</h5>
                    <p class="text-muted">ابدأ بإضافة لفاف جديد</p>
                    <a href="{{ route('manufacturing.wrappings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة لفاف جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(wrappingId, wrappingNumber) {
    if (confirm('هل أنت متأكد من حذف اللفاف "' + wrappingNumber + '"؟')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ route("manufacturing.wrappings.index") }}/' + wrappingId;
        form.submit();
    }
}
</script>
@endsection
