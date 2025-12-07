@extends('master')

@section('title', __('app.production_confirmations.title'))

@section('content')
<div class="container-fluid" style="padding: 20px; direction: rtl; font-family: 'Cairo', sans-serif;">

    <!-- العنوان الرئيسي -->
    <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);">
        <h1 style="color: white; margin: 0; font-size: 32px; font-weight: bold;">
            📊 {{ __('app.production_confirmations.title') }}
        </h1>
        <p style="color: rgba(255, 255, 255, 0.9); margin: 10px 0 0 0; font-size: 16px;">
            {{ __('app.production_confirmations.subtitle') }}
        </p>
    </div>

    <!-- الإحصائيات السريعة -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- معلق -->
        <div style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.pending') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['pending'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">⏳</div>
            </div>
        </div>

        <!-- مؤكد -->
        <div style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.confirmed') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['confirmed'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✓</div>
            </div>
        </div>

        <!-- مرفوض -->
        <div style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.rejected') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['rejected'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✕</div>
            </div>
        </div>

        <!-- الإجمالي -->
        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">{{ __('app.production_confirmations.stats.total') }}</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['total'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">📦</div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px; font-weight: bold;">🔍 {{ __('app.production_confirmations.search_and_filter') }}</h3>

        <form method="GET" action="{{ route('manufacturing.production.confirmations.index') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">

                <!-- الحالة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_status') }}</label>
                    <select name="status" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ {{ __('app.production_confirmations.pending') }}</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✓ {{ __('app.production_confirmations.confirmed') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>✕ {{ __('app.production_confirmations.rejected') }}</option>
                    </select>
                </div>

                <!-- المرحلة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_stage') }}</label>
                    <select name="stage" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        @foreach(\App\Models\ProductionStage::getActiveStages() as $stage)
                            <option value="{{ $stage->stage_code }}" {{ request('stage') == $stage->stage_code ? 'selected' : '' }}>
                                {{ $stage->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- الموظف -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_worker') }}</label>
                    <select name="worker" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">{{ __('app.common.all') }}</option>
                        @foreach(\App\Models\User::where('is_active', 1)->orderBy('name')->get() as $worker)
                            <option value="{{ $worker->id }}" {{ request('worker') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- من تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_from_date') }}</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- إلى تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">{{ __('app.production_confirmations.filter_to_date') }}</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- أزرار -->
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <button type="submit"
                            style="flex: 1; background: #3498db; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#2980b9'"
                            onmouseout="this.style.background='#3498db'">
                        🔍 {{ __('app.production_confirmations.search_btn') }}
                    </button>
                    <a href="{{ route('manufacturing.production.confirmations.index') }}"
                       style="flex: 1; background: #95a5a6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; text-decoration: none; text-align: center; transition: all 0.3s;"
                       onmouseover="this.style.background='#7f8c8d'"
                       onmouseout="this.style.background='#95a5a6'">
                        ↻ {{ __('app.production_confirmations.reset_btn') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- جدول التأكيدات -->
    @if($confirmations->isEmpty())
        <div style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 15px; padding: 60px; text-align: center;">
            <div style="font-size: 80px; margin-bottom: 20px; opacity: 0.3;">📭</div>
            <h3 style="color: #6c757d; margin-bottom: 15px;">{{ __('app.production_confirmations.no_results') }}</h3>
            <p style="color: #adb5bd; font-size: 16px;">{{ __('app.production_confirmations.no_results_desc') }}</p>
        </div>
    @else
        <div style="background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); color: white;">
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.number') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.batch_code') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.material') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.quantity') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.stage') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.worker') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.status') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">{{ __('app.production_confirmations.table.date') }}</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px;">{{ __('app.production_confirmations.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($confirmations as $index => $confirmation)
                        <tr style="border-bottom: 1px solid #ecf0f1; transition: background 0.3s;"
                            onmouseover="this.style.background='#f8f9fa'"
                            onmouseout="this.style.background='white'">

                            <td style="padding: 18px; text-align: center; font-weight: bold; color: #7f8c8d;">
                                {{ $confirmations->firstItem() + $index }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <span style="background: #9b59b6; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                    {{ $confirmation->batch?->batch_code ?? __('app.production_confirmations.table.not_specified') }}
                                </span>
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <div style="font-weight: bold; color: #2c3e50; font-size: 15px;">
                                    {{ $confirmation->batch?->material?->name ?? __('app.production_confirmations.table.not_specified') }}
                                </div>
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                @if($confirmation->deliveryNote?->quantity)
                                    <span style="font-size: 16px; font-weight: bold; color: #27ae60;">
                                        {{ number_format($confirmation->deliveryNote->quantity, 2) }}
                                    </span>
                                    <span style="color: #7f8c8d; font-size: 13px;">كجم</span>
                                @else
                                    <span style="color: #e74c3c; font-size: 14px;">{{ __('app.production_confirmations.table.data_unavailable') }}</span>
                                @endif
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <span style="background: #3498db; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                                    {{ $confirmation->deliveryNote?->production_stage_name ?? __('app.production_confirmations.table.not_specified') }}
                                </span>
                            </td>

                            <td style="padding: 18px; text-align: center; color: #2c3e50; font-weight: 600;">
                                {{ $confirmation->assignedUser?->name ?? __('app.production_confirmations.table.not_specified') }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                @if($confirmation->status == 'pending')
                                    <span style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">⏳ {{ __('app.production_confirmations.pending') }}</span>
                                @elseif($confirmation->status == 'confirmed')
                                    <span style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">✓ {{ __('app.production_confirmations.confirmed') }}</span>
                                @else
                                    <span style="background: #e74c3c; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">✕ {{ __('app.production_confirmations.rejected') }}</span>
                                @endif
                            </td>

                            <td style="padding: 18px; text-align: center; color: #7f8c8d; font-size: 14px;">
                                {{ $confirmation->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('manufacturing.production.confirmations.show', $confirmation->id) }}"
                                       style="background: #3498db; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; transition: all 0.3s;"
                                       onmouseover="this.style.background='#2980b9'"
                                       onmouseout="this.style.background='#3498db'">
                                        👁️ {{ __('app.production_confirmations.view_details') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $confirmations->appends(request()->query())->links() }}
        </div>
    @endif

</div>

@endsection

    <!-- الإحصائيات السريعة -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- معلق -->
        <div style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">معلق</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['pending'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">⏳</div>
            </div>
        </div>

        <!-- مؤكد -->
        <div style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">مؤكد</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['confirmed'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✓</div>
            </div>
        </div>

        <!-- مرفوض -->
        <div style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">مرفوض</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['rejected'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">✕</div>
            </div>
        </div>

        <!-- الإجمالي -->
        <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 25px; border-radius: 15px; color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">الإجمالي</div>
                    <div style="font-size: 32px; font-weight: bold;">{{ $stats['total'] }}</div>
                </div>
                <div style="font-size: 48px; opacity: 0.3;">📦</div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px; font-weight: bold;">🔍 البحث والفلترة</h3>

        <form method="GET" action="{{ route('manufacturing.production.confirmations.index') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">

                <!-- الحالة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">الحالة</label>
                    <select name="status" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ معلق</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✓ مؤكد</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>✕ مرفوض</option>
                    </select>
                </div>

                <!-- المرحلة -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">المرحلة</label>
                    <select name="stage" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">الكل</option>
                        @foreach(\App\Models\ProductionStage::getActiveStages() as $stage)
                            <option value="{{ $stage->stage_code }}" {{ request('stage') == $stage->stage_code ? 'selected' : '' }}>
                                {{ $stage->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- الموظف -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">الموظف</label>
                    <select name="worker" style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        <option value="">الكل</option>
                        @foreach(\App\Models\User::where('is_active', 1)->orderBy('name')->get() as $worker)
                            <option value="{{ $worker->id }}" {{ request('worker') == $worker->id ? 'selected' : '' }}>
                                {{ $worker->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- من تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- إلى تاريخ -->
                <div>
                    <label style="display: block; font-weight: bold; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           style="width: 100%; padding: 12px; border: 2px solid #bdc3c7; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- أزرار -->
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <button type="submit"
                            style="flex: 1; background: #3498db; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#2980b9'"
                            onmouseout="this.style.background='#3498db'">
                        🔍 بحث
                    </button>
                    <a href="{{ route('manufacturing.production.confirmations.index') }}"
                       style="flex: 1; background: #95a5a6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; text-decoration: none; text-align: center; transition: all 0.3s;"
                       onmouseover="this.style.background='#7f8c8d'"
                       onmouseout="this.style.background='#95a5a6'">
                        ↻ إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- جدول التأكيدات -->
    @if($confirmations->isEmpty())
        <div style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 15px; padding: 60px; text-align: center;">
            <div style="font-size: 80px; margin-bottom: 20px; opacity: 0.3;">📭</div>
            <h3 style="color: #6c757d; margin-bottom: 15px;">لا توجد نتائج</h3>
            <p style="color: #adb5bd; font-size: 16px;">لم يتم العثور على تأكيدات تطابق معايير البحث</p>
        </div>
    @else
        <div style="background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); color: white;">
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">#</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">رمز الدفعة</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">المادة</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">الكمية</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">المرحلة</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">الموظف</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">الحالة</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px; border-left: 1px solid rgba(255,255,255,0.1);">التاريخ</th>
                        <th style="padding: 18px; text-align: center; font-weight: bold; font-size: 15px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($confirmations as $index => $confirmation)
                        <tr style="border-bottom: 1px solid #ecf0f1; transition: background 0.3s;"
                            onmouseover="this.style.background='#f8f9fa'"
                            onmouseout="this.style.background='white'">

                            <td style="padding: 18px; text-align: center; font-weight: bold; color: #7f8c8d;">
                                {{ $confirmations->firstItem() + $index }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <span style="background: #9b59b6; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 14px;">
                                    {{ $confirmation->batch?->batch_code ?? 'غير محدد' }}
                                </span>
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <div style="font-weight: bold; color: #2c3e50; font-size: 15px;">
                                    {{ $confirmation->batch?->material?->name ?? 'غير محدد' }}
                                </div>
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                @if($confirmation->deliveryNote?->quantity)
                                    <span style="font-size: 16px; font-weight: bold; color: #27ae60;">
                                        {{ number_format($confirmation->deliveryNote->quantity, 2) }}
                                    </span>
                                    <span style="color: #7f8c8d; font-size: 13px;">كجم</span>
                                @else
                                    <span style="color: #e74c3c; font-size: 14px;">بيانات غير متوفرة</span>
                                @endif
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <span style="background: #3498db; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                                    {{ $confirmation->deliveryNote?->production_stage_name ?? 'غير محدد' }}
                                </span>
                            </td>

                            <td style="padding: 18px; text-align: center; color: #2c3e50; font-weight: 600;">
                                {{ $confirmation->assignedUser?->name ?? 'غير محدد' }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                @if($confirmation->status == 'pending')
                                    <span style="background: #f39c12; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                                        ⏳ معلق
                                    </span>
                                @elseif($confirmation->status == 'confirmed')
                                    <span style="background: #27ae60; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                                        ✓ مؤكد
                                    </span>
                                @else
                                    <span style="background: #e74c3c; color: white; padding: 6px 12px; border-radius: 8px; font-weight: bold; font-size: 13px;">
                                        ✕ مرفوض
                                    </span>
                                @endif
                            </td>

                            <td style="padding: 18px; text-align: center; color: #7f8c8d; font-size: 14px;">
                                {{ $confirmation->created_at->format('Y/m/d') }}
                            </td>

                            <td style="padding: 18px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('manufacturing.production.confirmations.show', $confirmation->id) }}"
                                       style="background: #3498db; color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 13px; transition: all 0.3s; display: inline-block;"
                                       onmouseover="this.style.background='#2980b9'"
                                       onmouseout="this.style.background='#3498db'">
                                        👁️ التفاصيل
                                    </a>

                                    @if($confirmation->status == 'pending' && $confirmation->assigned_to == auth()->id())
                                        <button onclick="quickConfirm({{ $confirmation->id }})"
                                                style="background: #27ae60; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 13px; cursor: pointer; transition: all 0.3s;"
                                                onmouseover="this.style.background='#229954'"
                                                onmouseout="this.style.background='#27ae60'">
                                            ✓ تأكيد
                                        </button>
                                        <button onclick="quickReject({{ $confirmation->id }})"
                                                style="background: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 13px; cursor: pointer; transition: all 0.3s;"
                                                onmouseover="this.style.background='#c0392b'"
                                                onmouseout="this.style.background='#e74c3c'">
                                            ✕ رفض
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $confirmations->appends(request()->query())->links() }}
        </div>
    @endif

</div>

<!-- Modals -->
<div class="modal fade" id="quickConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="direction: rtl;">
            <div class="modal-header" style="background: #27ae60; color: white;">
                <h5 class="modal-title">✓ تأكيد الاستلام</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="confirm-id">
                <div class="mb-3">
                    <label class="form-label">ملاحظات (اختياري)</label>
                    <textarea id="confirm-notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" onclick="submitConfirm()">✓ تأكيد</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="direction: rtl;">
            <div class="modal-header" style="background: #e74c3c; color: white;">
                <h5 class="modal-title">✕ رفض الاستلام</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject-id">
                <div class="mb-3">
                    <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                    <textarea id="reject-reason" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">✕ رفض</button>
            </div>
        </div>
    </div>
</div>

<script>
let confirmModal, rejectModal;

document.addEventListener('DOMContentLoaded', function() {
    confirmModal = new bootstrap.Modal(document.getElementById('quickConfirmModal'));
    rejectModal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
});

function quickConfirm(id) {
    document.getElementById('confirm-id').value = id;
    document.getElementById('confirm-notes').value = '';
    confirmModal.show();
}

function quickReject(id) {
    document.getElementById('reject-id').value = id;
    document.getElementById('reject-reason').value = '';
    rejectModal.show();
}

function submitConfirm() {
    const id = document.getElementById('confirm-id').value;
    const notes = document.getElementById('confirm-notes').value;

    fetch(`{{ url('manufacturing/production/confirmations') }}/${id}/confirm`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            confirmModal.hide();
            alert('✓ تم التأكيد بنجاح!');
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error(error);
        alert('❌ حدث خطأ أثناء التأكيد');
    });
}

function submitReject() {
    const id = document.getElementById('reject-id').value;
    const reason = document.getElementById('reject-reason').value;

    if (!reason.trim()) {
        alert('الرجاء إدخال سبب الرفض');
        return;
    }

    fetch(`{{ url('manufacturing/production/confirmations') }}/${id}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rejection_reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            rejectModal.hide();
            alert('✓ تم الرفض بنجاح!');
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error(error);
        alert('❌ حدث خطأ أثناء الرفض');
    });
}
</script>

@endsection
