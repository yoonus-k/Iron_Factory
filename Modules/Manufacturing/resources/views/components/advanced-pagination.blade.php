@if ($paginator->hasPages())
    <div class="pagination-container" style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">

        <!-- Page Info & Records Per Page -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="padding: 10px 15px; background-color: white; border-radius: 6px; border: 1px solid #dee2e6;">
                        <small style="color: #666; font-weight: 600;">
                            📄 الصفحة <strong>{{ $paginator->currentPage() }}</strong> من <strong>{{ $paginator->lastPage() }}</strong>
                        </small>
                    </div>
                    <div style="padding: 10px 15px; background-color: white; border-radius: 6px; border: 1px solid #dee2e6;">
                        <small style="color: #666; font-weight: 600;">
                            📊 السجلات: <strong>{{ $paginator->count() }}</strong> من <strong>{{ $paginator->total() }}</strong>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Items Per Page Selector -->
            <div class="col-md-6" style="text-align: left;">
                <form method="GET" action="{{ url()->current() }}" style="display: inline;">
                    @foreach (request()->query() as $key => $value)
                        @if ($key !== 'page' && $key !== 'per_page')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="color: #666; font-weight: 600; margin: 0; font-size: 13px;">
                            عدد السجلات لكل صفحة:
                        </label>
                        <select name="per_page" onchange="this.form.submit()"
                                style="padding: 6px 10px; border: 1px solid #dee2e6; border-radius: 4px; background-color: white; cursor: pointer;">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Pagination -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: wrap;">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button disabled style="padding: 8px 12px; background-color: #e9ecef; color: #999; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-weight: 600; font-size: 12px;">
                    <i class="fas fa-chevron-right"></i> السابق
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 12px; background-color: white; color: #0051E5; border: 1px solid #0051E5; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 12px; text-decoration: none; transition: all 0.2s;">
                    <i class="fas fa-chevron-right"></i> السابق
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 8px 12px; color: #999; font-weight: 600;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button style="padding: 8px 12px; background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white; border: none; border-radius: 4px; font-weight: 600; font-size: 12px; cursor: default; box-shadow: 0 2px 6px rgba(0, 81, 229, 0.2);">
                                {{ $page }}
                            </button>
                        @else
                            <a href="{{ $url }}" style="padding: 8px 12px; background-color: white; color: #0051E5; border: 1px solid #dee2e6; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 12px; text-decoration: none; transition: all 0.2s;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 12px; background-color: white; color: #0051E5; border: 1px solid #0051E5; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 12px; text-decoration: none; transition: all 0.2s;">
                    التالي <i class="fas fa-chevron-left"></i>
                </a>
            @else
                <button disabled style="padding: 8px 12px; background-color: #e9ecef; color: #999; border: 1px solid #dee2e6; border-radius: 4px; cursor: not-allowed; font-weight: 600; font-size: 12px;">
                    التالي <i class="fas fa-chevron-left"></i>
                </button>
            @endif
        </div>

        <!-- Quick Jump to Page -->
        <div style="margin-top: 15px; text-align: center;">
            <form method="GET" action="{{ url()->current() }}" style="display: inline-flex; gap: 8px; justify-content: center;">
                @foreach (request()->query() as $key => $value)
                    @if ($key !== 'page' && $key !== 'per_page')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <label style="color: #666; font-weight: 600; margin: 0; font-size: 13px; display: flex; align-items: center;">
                    🚀 قفزة سريعة للصفحة:
                </label>
                <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}"
                       value="{{ $paginator->currentPage() }}" placeholder="رقم الصفحة"
                       style="padding: 6px 10px; border: 1px solid #dee2e6; border-radius: 4px; width: 80px; font-weight: 600;">
                <button type="submit" style="padding: 6px 12px; background: linear-gradient(135deg, #0051E5 0%, #003FA0 100%); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 12px;">
                    اذهب <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                </button>
            </form>
        </div>

        <!-- Statistics Bar -->
        <div style="margin-top: 15px; padding: 12px; background-color: white; border-radius: 6px; border-right: 4px solid #0051E5;">
            <small style="color: #0051E5; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-info-circle"></i>
                عرض السجلات من <strong>{{ ($paginator->currentPage() - 1) * $paginator->perPage() + 1 }}</strong>
                إلى <strong>{{ min($paginator->currentPage() * $paginator->perPage(), $paginator->total()) }}</strong>
                من إجمالي <strong>{{ $paginator->total() }}</strong> سجل
            </small>
        </div>
    </div>
@endif

<style>
    .pagination-container a:hover {
        background-color: #e8f0ff !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 81, 229, 0.15);
    }

    .pagination-container select:hover {
        border-color: #0051E5;
    }

    .pagination-container input[type="number"]:focus {
        border-color: #0051E5;
        box-shadow: 0 0 0 3px rgba(0, 81, 229, 0.1);
    }

    @media (max-width: 768px) {
        .pagination-container {
            padding: 15px;
        }

        .pagination-container button,
        .pagination-container a {
            padding: 6px 8px;
            font-size: 11px;
        }

        .pagination-container .row {
            flex-direction: column;
            gap: 15px;
        }

        .pagination-container .col-md-6 {
            width: 100%;
            text-align: center !important;
        }
    }
</style>
