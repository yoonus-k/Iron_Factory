{{-- 
نموذج Form لإنشاء/تعديل مادة مع ترجمات
Material Create/Edit Form with Translations Example
--}}

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            {{-- Headers --}}
            @if(isset($material))
                <h2>تعديل المادة / Edit Material</h2>
            @else
                <h2>إنشاء مادة جديدة / Create New Material</h2>
            @endif

            {{-- Form Start --}}
            <form action="{{ isset($material) ? route('materials.update', $material) : route('materials.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($material))
                    @method('PUT')
                @endif

                {{-- Basic Information --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">معلومات أساسية / Basic Information</h5>
                    </div>
                    <div class="card-body">
                        {{-- Barcode --}}
                        <div class="mb-3">
                            <label for="barcode" class="form-label">رمز المادة / Barcode *</label>
                            <input type="text" 
                                   class="form-control @error('barcode') is-invalid @enderror" 
                                   id="barcode" 
                                   name="barcode" 
                                   value="{{ old('barcode', $material->barcode ?? '') }}"
                                   required>
                            @error('barcode')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Warehouse --}}
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">المستودع / Warehouse *</label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" 
                                    id="warehouse_id" 
                                    name="warehouse_id" 
                                    required>
                                <option value="">اختر المستودع / Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" 
                                            @selected(old('warehouse_id', $material->warehouse_id ?? '') == $warehouse->id)>
                                        {{ $warehouse->warehouse_name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Material Type --}}
                        <div class="mb-3">
                            <label for="material_type_id" class="form-label">نوع المادة / Material Type</label>
                            <select class="form-select @error('material_type_id') is-invalid @enderror" 
                                    id="material_type_id" 
                                    name="material_type_id">
                                <option value="">اختر النوع / Select Type</option>
                                @foreach($materialTypes as $type)
                                    <option value="{{ $type->id }}" 
                                            @selected(old('material_type_id', $material->material_type_id ?? '') == $type->id)>
                                        {{ $type->type_name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Unit --}}
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">وحدة القياس / Unit *</label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" 
                                    id="unit_id" 
                                    name="unit_id" 
                                    required>
                                <option value="">اختر الوحدة / Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" 
                                            @selected(old('unit_id', $material->unit_id ?? '') == $unit->id)>
                                        {{ $unit->unit_name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Batch Number --}}
                        <div class="mb-3">
                            <label for="batch_number" class="form-label">رقم الدفعة / Batch Number</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="batch_number" 
                                   name="batch_number" 
                                   value="{{ old('batch_number', $material->batch_number ?? '') }}">
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة / Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" @selected(old('status', $material->status ?? '') == 'available')>
                                    متاح / Available
                                </option>
                                <option value="in_use" @selected(old('status', $material->status ?? '') == 'in_use')>
                                    قيد الاستخدام / In Use
                                </option>
                                <option value="consumed" @selected(old('status', $material->status ?? '') == 'consumed')>
                                    مستهلك / Consumed
                                </option>
                                <option value="expired" @selected(old('status', $material->status ?? '') == 'expired')>
                                    منتهي الصلاحية / Expired
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Multilingual Content --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">🌍 المحتوى متعدد اللغات / Multilingual Content</h5>
                    </div>
                    <div class="card-body">
                        {{-- Name --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name_ar" class="form-label">الاسم بالعربية / Name (Arabic) *</label>
                                <input type="text" 
                                       class="form-control @error('name_ar') is-invalid @enderror" 
                                       id="name_ar" 
                                       name="name_ar" 
                                       value="{{ old('name_ar', isset($translations) ? $translations['ar']['name'] : $material->name_ar ?? '') }}"
                                       placeholder="مثال: مادة خام"
                                       required>
                                @error('name_ar')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name_en" class="form-label">الاسم بالإنجليزية / Name (English)</label>
                                <input type="text" 
                                       class="form-control @error('name_en') is-invalid @enderror" 
                                       id="name_en" 
                                       name="name_en" 
                                       value="{{ old('name_en', isset($translations) ? $translations['en']['name'] : $material->name_en ?? '') }}"
                                       placeholder="Example: Raw Material">
                                @error('name_en')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="notes_ar" class="form-label">الملاحظات / Notes (Arabic)</label>
                                <textarea class="form-control" 
                                          id="notes_ar" 
                                          name="notes_ar" 
                                          rows="4"
                                          placeholder="ملاحظات باللغة العربية">{{ old('notes_ar', isset($translations) ? $translations['ar']['notes'] : $material->notes ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="notes_en" class="form-label">الملاحظات / Notes (English)</label>
                                <textarea class="form-control" 
                                          id="notes_en" 
                                          name="notes_en" 
                                          rows="4"
                                          placeholder="Notes in English">{{ old('notes_en', isset($translations) ? $translations['en']['notes'] : $material->notes_en ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Shelf Location --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="shelf_location_ar" class="form-label">موقع الرف / Shelf Location (Arabic)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="shelf_location_ar" 
                                       name="shelf_location_ar" 
                                       value="{{ old('shelf_location_ar', isset($translations) ? $translations['ar']['shelf_location'] : $material->shelf_location ?? '') }}"
                                       placeholder="مثال: الرف A-1">
                            </div>
                            <div class="col-md-6">
                                <label for="shelf_location_en" class="form-label">موقع الرف / Shelf Location (English)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="shelf_location_en" 
                                       name="shelf_location_en" 
                                       value="{{ old('shelf_location_en', isset($translations) ? $translations['en']['shelf_location'] : $material->shelf_location_en ?? '') }}"
                                       placeholder="Example: Shelf A-1">
                            </div>
                        </div>

                        {{-- Language Support Info --}}
                        <div class="alert alert-info" role="alert">
                            <strong>ℹ️ معلومة:</strong> الحقول الإنجليزية اختيارية. إذا تركتها فارغة، سيتم استخدام النسخة العربية.
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        @if(isset($material))
                            <i class="bi bi-pencil"></i> تحديث / Update
                        @else
                            <i class="bi bi-plus-circle"></i> إنشاء / Create
                        @endif
                    </button>
                    <a href="{{ route('materials.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> رجوع / Back
                    </a>
                </div>
            </form>
        </div>

        {{-- Sidebar: Preview --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">معاينة / Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">الاسم بالعربية / Arabic Name:</small>
                        <p id="preview_name_ar" class="text-primary">
                            {{ $material->getDisplayName('ar') ?? 'سيظهر هنا' }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">الاسم بالإنجليزية / English Name:</small>
                        <p id="preview_name_en" class="text-primary">
                            {{ $material->getDisplayName('en') ?? 'Will appear here' }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">الملاحظات / Notes:</small>
                        <p id="preview_notes" class="small">
                            {{ $material->getDisplayNotes() ?? 'سيظهر هنا' }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">موقع الرف / Shelf Location:</small>
                        <p id="preview_location" class="small text-secondary">
                            {{ $material->getDisplayShelfLocation() ?? 'سيظهر هنا' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update preview in real-time
    document.getElementById('name_ar').addEventListener('input', function() {
        document.getElementById('preview_name_ar').textContent = this.value || 'سيظهر هنا';
    });

    document.getElementById('name_en').addEventListener('input', function() {
        document.getElementById('preview_name_en').textContent = this.value || 'Will appear here';
    });

    document.getElementById('notes_ar').addEventListener('input', function() {
        document.getElementById('preview_notes').textContent = this.value || 'سيظهر هنا';
    });

    document.getElementById('shelf_location_ar').addEventListener('input', function() {
        document.getElementById('preview_location').textContent = this.value || 'سيظهر هنا';
    });
</script>
