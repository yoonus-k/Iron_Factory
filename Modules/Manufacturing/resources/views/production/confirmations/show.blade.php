@extends('master')

@section('title', 'تفاصيل التأكيد')

@section('content')
<div class="container-fluid" style="padding: 20px; direction: rtl; font-family: 'Cairo', sans-serif;">
    
    <!-- العنوان الرئيسي -->
    <div style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="color: white; margin: 0 0 10px 0; font-size: 32px; font-weight: bold;">
                    📋 تفاصيل التأكيد
                </h1>
                <p style="color: rgba(255, 255, 255, 0.9); margin: 0; font-size: 16px;">
                    معلومات كاملة عن طلب التأكيد
                </p>
            </div>
            
            <!-- الحالة الكبيرة -->
            <div>
                @if($confirmation->status == 'pending')
                    <div style="background: #f39c12; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);">
                        ⏳ معلق
                    </div>
                @elseif($confirmation->status == 'confirmed')
                    <div style="background: #27ae60; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);">
                        ✓ مؤكد
                    </div>
                @else
                    <div style="background: #e74c3c; color: white; padding: 15px 30px; border-radius: 12px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);">
                        ✕ مرفوض
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- زر العودة -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('manufacturing.production.confirmations.index') }}" 
           style="background: #95a5a6; color: white; text-decoration: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; display: inline-block; transition: all 0.3s;"
           onmouseover="this.style.background='#7f8c8d'"
           onmouseout="this.style.background='#95a5a6'">
            ← العودة للقائمة
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        
        <!-- العمود الأيسر - التفاصيل الرئيسية -->
        <div>
            
            <!-- معلومات الدفعة -->
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 25px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #3498db; padding-bottom: 15px;">
                    📦 معلومات الدفعة
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #9b59b6;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">رمز الدفعة</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 20px;">{{ $confirmation->batch->batch_code }}</div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #3498db;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">المادة</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">{{ $confirmation->batch->material->name }}</div>
                        <div style="color: #95a5a6; font-size: 13px; margin-top: 3px;">{{ $confirmation->batch->material->category }}</div>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #27ae60;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">الكمية المنقولة</div>
                        <div style="font-weight: bold; color: #27ae60; font-size: 20px;">
                            {{ number_format($confirmation->deliveryNote->quantity, 2) }} <span style="font-size: 14px;">كجم</span>
                        </div>
                    </div>
                    
                    @if($confirmation->actual_received_quantity)
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #16a085;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">الكمية المستلمة فعلياً</div>
                        <div style="font-weight: bold; color: #16a085; font-size: 20px;">
                            {{ number_format($confirmation->actual_received_quantity, 2) }} <span style="font-size: 14px;">كجم</span>
                        </div>
                    </div>
                    @endif
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #e67e22;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">المرحلة الإنتاجية</div>
                        <div style="font-weight: bold; color: #2c3e50; font-size: 18px;">{{ $confirmation->deliveryNote->production_stage_name }}</div>
                    </div>
                    
                    @if($confirmation->batch->coil_number)
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-right: 4px solid #8e44ad;">
                        <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">رقم الكويل</div>
                        <div style="font-weight: bold; color: #8e44ad; font-size: 18px;">{{ $confirmation->batch->coil_number }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- الباركود -->
            @if($confirmation->batch->production_barcode)
            <div style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3); text-align: center;">
                <h3 style="color: white; margin-bottom: 20px; font-size: 22px; font-weight: bold;">
                    🏷️ الباركود الإنتاجي
                </h3>
                <div style="background: white; padding: 25px; border-radius: 10px; display: inline-block;">
                    {!! DNS1D::getBarcodeHTML($confirmation->batch->production_barcode, 'C128', 2, 80) !!}
                    <div style="margin-top: 15px; font-weight: bold; color: #2c3e50; font-size: 18px;">
                        {{ $confirmation->batch->production_barcode }}
                    </div>
                </div>
            </div>
            @endif

            <!-- الملاحظات -->
            @if($confirmation->deliveryNote->notes || $confirmation->notes)
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #f39c12; padding-bottom: 15px;">
                    📝 الملاحظات
                </h3>
                
                @if($confirmation->deliveryNote->notes)
                <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 10px; font-size: 15px;">عند النقل:</div>
                    <div style="color: #856404; line-height: 1.8; font-size: 15px;">{{ $confirmation->deliveryNote->notes }}</div>
                </div>
                @endif
                
                @if($confirmation->notes)
                <div style="background: #d1ecf1; border: 2px solid #17a2b8; border-radius: 10px; padding: 20px;">
                    <div style="font-weight: bold; color: #0c5460; margin-bottom: 10px; font-size: 15px;">عند التأكيد:</div>
                    <div style="color: #0c5460; line-height: 1.8; font-size: 15px;">{{ $confirmation->notes }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- سبب الرفض -->
            @if($confirmation->rejection_reason)
            <div style="background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #e74c3c; margin-bottom: 20px; font-size: 22px; font-weight: bold; border-bottom: 3px solid #e74c3c; padding-bottom: 15px;">
                    ⚠️ سبب الرفض
                </h3>
                <div style="background: #f8d7da; border: 2px solid #e74c3c; border-radius: 10px; padding: 20px;">
                    <div style="color: #721c24; line-height: 1.8; font-size: 16px;">{{ $confirmation->rejection_reason }}</div>
                </div>
            </div>
            @endif

        </div>

        <!-- العمود الأيمن - الجدول الزمني -->
        <div>
            
            <!-- معلومات الموظفين -->
            <div style="background: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 20px; font-weight: bold; border-bottom: 3px solid #9b59b6; padding-bottom: 12px;">
                    👥 الموظفين
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">الموظف المستلم</div>
                    <div style="font-weight: bold; color: #2c3e50; font-size: 16px;">{{ $confirmation->assignedUser->name }}</div>
                </div>
                
                @if($confirmation->confirmedByUser)
                <div style="margin-bottom: 20px;">
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">تم التأكيد بواسطة</div>
                    <div style="font-weight: bold; color: #27ae60; font-size: 16px;">{{ $confirmation->confirmedByUser->name }}</div>
                </div>
                @endif
                
                @if($confirmation->rejectedByUser)
                <div>
                    <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">تم الرفض بواسطة</div>
                    <div style="font-weight: bold; color: #e74c3c; font-size: 16px;">{{ $confirmation->rejectedByUser->name }}</div>
                </div>
                @endif
            </div>

            <!-- الجدول الزمني -->
            <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #2c3e50; margin-bottom: 25px; font-size: 20px; font-weight: bold; border-bottom: 3px solid #3498db; padding-bottom: 12px;">
                    ⏱️ الجدول الزمني
                </h3>
                
                <!-- Timeline -->
                <div style="position: relative; padding-right: 30px;">
                    
                    <!-- خط الجدول الزمني -->
                    <div style="position: absolute; right: 9px; top: 0; bottom: 0; width: 2px; background: #ecf0f1;"></div>
                    
                    <!-- تم الإنشاء -->
                    <div style="position: relative; margin-bottom: 30px;">
                        <div style="position: absolute; right: -27px; width: 20px; height: 20px; background: #3498db; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #3498db;"></div>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; border-right: 4px solid #3498db;">
                            <div style="font-weight: bold; color: #2c3e50; margin-bottom: 5px; font-size: 15px;">✓ تم الإنشاء</div>
                            <div style="color: #7f8c8d; font-size: 13px;">{{ $confirmation->created_at->format('Y/m/d - h:i A') }}</div>
                            <div style="color: #95a5a6; font-size: 12px; margin-top: 3px;">{{ $confirmation->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    
                    <!-- تم التأكيد / الرفض -->
                    @if($confirmation->status == 'confirmed')
                        <div style="position: relative; margin-bottom: 30px;">
                            <div style="position: absolute; right: -27px; width: 20px; height: 20px; background: #27ae60; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #27ae60;"></div>
                            <div style="background: #d5f4e6; padding: 15px; border-radius: 10px; border-right: 4px solid #27ae60;">
                                <div style="font-weight: bold; color: #27ae60; margin-bottom: 5px; font-size: 15px;">✓ تم التأكيد</div>
                                <div style="color: #229954; font-size: 13px;">{{ $confirmation->confirmed_at->format('Y/m/d - h:i A') }}</div>
                                <div style="color: #52b788; font-size: 12px; margin-top: 3px;">{{ $confirmation->confirmed_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @elseif($confirmation->status == 'rejected')
                        <div style="position: relative; margin-bottom: 30px;">
                            <div style="position: absolute; right: -27px; width: 20px; height: 20px; background: #e74c3c; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #e74c3c;"></div>
                            <div style="background: #f8d7da; padding: 15px; border-radius: 10px; border-right: 4px solid #e74c3c;">
                                <div style="font-weight: bold; color: #e74c3c; margin-bottom: 5px; font-size: 15px;">✕ تم الرفض</div>
                                <div style="color: #c0392b; font-size: 13px;">{{ $confirmation->rejected_at->format('Y/m/d - h:i A') }}</div>
                                <div style="color: #e57373; font-size: 12px; margin-top: 3px;">{{ $confirmation->rejected_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @else
                        <div style="position: relative; margin-bottom: 30px;">
                            <div style="position: absolute; right: -27px; width: 20px; height: 20px; background: #f39c12; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 2px #f39c12; animation: pulse 2s infinite;"></div>
                            <div style="background: #fef5e7; padding: 15px; border-radius: 10px; border-right: 4px solid #f39c12;">
                                <div style="font-weight: bold; color: #f39c12; margin-bottom: 5px; font-size: 15px;">⏳ بانتظار التأكيد</div>
                                <div style="color: #e67e22; font-size: 13px;">منذ {{ $confirmation->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif
                    
                </div>
                
            </div>

        </div>

    </div>

</div>

<style>
@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 2px #f39c12;
    }
    50% {
        box-shadow: 0 0 0 6px rgba(243, 156, 18, 0.3);
    }
}
</style>

@endsection
