@extends('master')

@section('title', 'نقل وردية جديدة')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/style-cours.css') }}">

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="course-icon">
                        <i class="feather icon-exchange-2"></i>
                    </div>
                    <div class="header-info">
                        <h1>نقل وردية جديدة</h1>
                        <p style="color: #666; margin-top: 5px; font-size: 14px;">قم بملء البيانات أدناه لنقل الوردية من عامل إلى آخر</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('manufacturing.shift-handovers.index') }}" class="btn btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        الرجوع
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if(is_object($errors) && $errors->any())
        <div style="background: #fee; border-left: 4px solid #dc3545; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h5 style="color: #dc3545; margin-top: 0; margin-bottom: 8px; font-size: 16px;">
                <i class="feather icon-alert-circle"></i>
                خطأ في البيانات
            </h5>
            <ul style="margin: 0; padding-right: 20px; color: #666;">
                @foreach ($errors->all() as $error)
                    <li style="margin: 4px 0;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @error('error')
        <div style="background: #fee; border-left: 4px solid #dc3545; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h5 style="color: #dc3545; margin: 0; font-size: 16px;">
                <i class="feather icon-x-circle"></i>
                {{ $message }}
            </h5>
        </div>
        @enderror

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="card-title">بيانات النقل</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('manufacturing.shift-handovers.store') }}" method="POST">
                    @csrf

                    <!-- From and To Users -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="from_user_id">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                                من العامل
                            </label>
                            <select name="from_user_id" id="from_user_id" class="form-control @error('from_user_id') is-invalid @enderror" required>
                                <option value="">-- اختر العامل --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('from_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('from_user_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="to_user_id">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                                إلى العامل
                            </label>
                            <select name="to_user_id" id="to_user_id" class="form-control @error('to_user_id') is-invalid @enderror" required>
                                <option value="">-- اختر العامل --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_user_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Stage Number -->
                    <div class="form-group">
                        <label for="stage_number">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            المرحلة
                        </label>
                        <select name="stage_number" id="stage_number" class="form-control @error('stage_number') is-invalid @enderror" required>
                            <option value="">-- اختر المرحلة --</option>
                            <option value="1" {{ old('stage_number') == 1 ? 'selected' : '' }}>المرحلة الأولى</option>
                            <option value="2" {{ old('stage_number') == 2 ? 'selected' : '' }}>المرحلة الثانية</option>
                            <option value="3" {{ old('stage_number') == 3 ? 'selected' : '' }}>المرحلة الثالثة</option>
                            <option value="4" {{ old('stage_number') == 4 ? 'selected' : '' }}>المرحلة الرابعة</option>
                        </select>
                        @error('stage_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Handover Items -->
                    <div class="form-group">
                        <label for="handover_items">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            عناصر النقل
                        </label>
                        <textarea name="handover_items" id="handover_items" class="form-control @error('handover_items') is-invalid @enderror" rows="4" placeholder="اكتب العناصر المنقولة (كل عنصر في سطر جديد)">{{ old('handover_items') ? implode("\n", (array)old('handover_items')) : '' }}</textarea>
                        <small style="color: #999; display: block; margin-top: 5px;">اترك فارغاً إذا لم يكن هناك عناصر محددة</small>
                        @error('handover_items')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            ملاحظات (اختياري)
                        </label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="أضف أي ملاحظات إضافية عن النقل...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        @if(auth()->user()->hasPermission('SHIFT_HANDOVERS_CREATE')
                        <button type="submit" class="action-btn activate" style="width: 48%;">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                    <polyline points="5 5 12 12 5 19"></polyline>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>نقل الوردية</h4>
                                <p>تأكيد نقل الوردية</p>
                            </div>
                        </button>
                        @endif

                        <a href="{{ route('manufacturing.shift-handovers.index') }}" class="action-btn" style="width: 48%; background: #e0e0e0; color: #333; text-decoration: none; display: flex; align-items: center; justify-content: flex-end; gap: 12px; padding: 15px 20px;">
                            <div class="action-icon" style="color: #333;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h4>إلغاء</h4>
                                <p>العودة بدون حفظ</p>
                            </div>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .form-group label svg {
            width: 18px;
            height: 18px;
            color: #667eea;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            resize: vertical;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            display: block;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .action-btn {
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 15px 20px;
        }

        .action-btn.activate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .action-btn.activate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-icon svg {
            width: 20px;
            height: 20px;
        }

        .action-text h4 {
            margin: 0;
            font-size: 15px;
        }

        .action-text p {
            margin: 2px 0 0 0;
            font-size: 12px;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .action-btn {
                width: 100% !important;
            }
        }
    </style>

    <script>
        document.getElementById('handover_items').addEventListener('blur', function() {
            let lines = this.value.split('\n').filter(line => line.trim());
            if (lines.length > 0) {
                document.querySelector('input[name="handover_items[]"]').value = JSON.stringify(lines);
            }
        });
    </script>

@endsection

