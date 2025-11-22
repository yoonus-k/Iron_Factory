{{-- File: Modules/Manufacturing/Resources/views/quality/partials/trace-item.blade.php --}}

<div class="trace-item" style="margin-right: {{ $level * 20 }}px;">
    <div style="background: white; border: 2px solid {{ $direction == 'backward' ? '#e74c3c' : '#2ecc71' }}; border-radius: 10px; padding: 15px; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        
        {{-- رأس البطاقة --}}
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 15px; color: #0f172a; margin-bottom: 5px;">
                    <i class="feather icon-barcode"></i> {{ $item['barcode'] }}
                </div>
                <div style="font-size: 13px; color: #64748b;">
                    <i class="feather icon-layers"></i> {{ $item['stage_name'] }}
                </div>
            </div>
            
            <div style="text-align: left;">
                <div style="background: {{ $direction == 'backward' ? '#fee' : '#efffef' }}; 
                            color: {{ $direction == 'backward' ? '#c0392b' : '#27ae60' }}; 
                            padding: 5px 12px; 
                            border-radius: 6px; 
                            font-size: 12px; 
                            font-weight: 600;">
                    @if($direction == 'backward')
                        <i class="feather icon-arrow-left"></i> مصدر
                    @else
                        <i class="feather icon-arrow-right"></i> منتج
                    @endif
                </div>
            </div>
        </div>

        {{-- تفاصيل العنصر --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 10px;">
            <div style="background: #f8fafc; padding: 10px; border-radius: 6px;">
                <div style="font-size: 11px; color: #64748b; margin-bottom: 3px;">الوزن</div>
                <div style="font-size: 15px; font-weight: 600; color: #2563eb;">
                    <i class="feather icon-weight"></i> {{ number_format($item['weight'], 2) }} كجم
                </div>
            </div>
            
            <div style="background: #f8fafc; padding: 10px; border-radius: 6px;">
                <div style="font-size: 11px; color: #64748b; margin-bottom: 3px;">المستوى</div>
                <div style="font-size: 15px; font-weight: 600; color: #7c3aed;">
                    <i class="feather icon-trending-up"></i> المستوى {{ $level + 1 }}
                </div>
            </div>
        </div>

        {{-- التاريخ --}}
        <div style="font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px;">
            <i class="feather icon-clock"></i> 
            {{ \Carbon\Carbon::parse($item['timestamp'])->format('Y-m-d H:i:s') }}
        </div>

        {{-- مؤشر المستوى --}}
        @if($level > 0)
        <div style="position: absolute; right: -15px; top: 20px; width: 15px; height: 2px; background: #cbd5e1;"></div>
        @endif
    </div>

    {{-- العناصر الفرعية (Children) --}}
    @if(isset($item['children']) && count($item['children']) > 0)
        <div class="trace-children" style="margin-top: 15px;">
            @foreach($item['children'] as $child)
                @include('manufacturing::quality.partials.trace-item', [
                    'item' => $child, 
                    'direction' => $direction,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div>