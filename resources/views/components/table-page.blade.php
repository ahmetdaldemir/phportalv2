{{-- ============================================
     TABLE PAGE COMPONENT
     Tüm tablo sayfaları için ortak component
     ============================================ --}}

@props([
    'title' => 'Sayfa Başlığı',
    'subtitle' => 'Sayfa açıklaması',
    'icon' => 'bx-grid-alt',
    'actions' => [],
    'filters' => [],
    'summary' => [],
    'totals' => [],
    'table' => [],
    'pagination' => null,
    'loading' => false,
    'empty' => false,
    'emptyMessage' => 'Veri bulunamadı'
])

{{-- CSS Framework'ü dahil et --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/table-page-framework.css') }}">
@endpush

<div id="app">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        {{-- ============================================
            1. PAGE HEADER - Sayfa Başlığı
            ============================================ --}}
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="{{ $icon }} me-2"></i>
                            {{ $title }}
                        </h2>
                        <p>{{ $subtitle }}</p>
                    </div>
                </div>
                
                @if(!empty($actions))
                <div class="header-actions">
                    @foreach($actions as $action)
                        @if(isset($action['type']) && $action['type'] === 'link')
                            <a href="{{ $action['url'] }}" 
                               class="btn {{ $action['class'] ?? 'btn-primary' }} btn-sm">
                                @if(isset($action['icon']))
                                    <i class="{{ $action['icon'] }} me-1"></i>
                                @endif
                                {{ $action['text'] }}
                            </a>
                        @else
                            <button type="button" 
                                    class="btn {{ $action['class'] ?? 'btn-primary' }} btn-sm"
                                    @if(isset($action['onclick']))
                                        onclick="{{ $action['onclick'] }}"
                                    @endif>
                                @if(isset($action['icon']))
                                    <i class="{{ $action['icon'] }} me-1"></i>
                                @endif
                                {{ $action['text'] }}
                            </button>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ============================================
            2. FILTER SECTION - Filtre Bölümü
            ============================================ --}}
        @if(!empty($filters))
        <div class="table-page-filters table-page-fade-in-delay-1">
            <div class="filter-header">
                <h6>
                    <i class="bx bx-filter me-2"></i>
                    Filtreler
                </h6>
                <small>Veri arama ve filtreleme</small>
            </div>
            <div class="filter-body">
                {{ $filters }}
            </div>
        </div>
        @endif

        {{-- ============================================
            3. SUMMARY CARDS - Özet Kartları
            ============================================ --}}
        @if(!empty($summary))
        <div class="table-page-summary table-page-fade-in-delay-2">
            <div class="summary-cards">
                @foreach($summary as $card)
                <div class="summary-card">
                    <div class="card-icon {{ $card['type'] ?? 'primary' }}">
                        <i class="{{ $card['icon'] ?? 'bx-grid-alt' }}"></i>
                    </div>
                    <div class="card-value {{ $card['valueClass'] ?? '' }}">
                        {{ $card['value'] ?? '0' }}
                    </div>
                    <div class="card-label">
                        {{ $card['label'] ?? 'Label' }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================================
            4. DETAILED TOTALS - Detaylı Toplamlar
            ============================================ --}}
        @if(!empty($totals))
        <div class="table-page-totals table-page-fade-in-delay-2">
            <div class="totals-header">
                <h6>
                    <i class="bx bx-calculator me-2"></i>
                    Detaylı Toplamlar
                </h6>
            </div>
            <div class="totals-grid">
                @foreach($totals as $total)
                <div class="total-item">
                    <div class="total-value {{ $total['valueClass'] ?? '' }}">
                        {{ $total['value'] ?? '0' }}
                    </div>
                    <div class="total-label">
                        {{ $total['label'] ?? 'Label' }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================================
            5. DATA TABLE - Veri Tablosu
            ============================================ --}}
        <div class="table-page-table table-page-fade-in-delay-3">
            @if($loading)
                <div class="table-page-loading">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-primary mt-2">Veriler yükleniyor...</p>
                </div>
            @elseif($empty)
                <div class="table-page-empty">
                    <i class="bx bx-data"></i>
                    <h4 class="text-muted mb-3">Veri Bulunamadı</h4>
                    <p class="text-muted">{{ $emptyMessage }}</p>
                </div>
            @else
                {{ $table }}
            @endif
        </div>

        {{-- ============================================
            6. PAGINATION - Sayfalama
            ============================================ --}}
        @if($pagination && $pagination['last_page'] > 1)
        <div class="table-page-pagination table-page-fade-in-delay-3">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    {{-- Previous Page --}}
                    <li class="page-item {{ $pagination['current_page'] <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="#" 
                           @if($pagination['current_page'] > 1)
                               onclick="changePage({{ $pagination['current_page'] - 1 }})"
                           @endif>
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    {{-- Page Numbers --}}
                    @php
                        $current = $pagination['current_page'];
                        $last = $pagination['last_page'];
                        $delta = 2;
                        $range = [];
                        $rangeWithDots = [];

                        for ($i = max(2, $current - $delta); $i <= min($last - 1, $current + $delta); $i++) {
                            $range[] = $i;
                        }

                        if ($current - $delta > 2) {
                            $rangeWithDots = [1, '...'];
                        } else {
                            $rangeWithDots = [1];
                        }

                        $rangeWithDots = array_merge($rangeWithDots, $range);

                        if ($current + $delta < $last - 1) {
                            $rangeWithDots = array_merge($rangeWithDots, ['...', $last]);
                        } else {
                            $rangeWithDots[] = $last;
                        }
                    @endphp

                    @foreach($rangeWithDots as $page)
                        <li class="page-item {{ $page === $current ? 'active' : '' }}">
                            @if($page !== '...')
                                <a class="page-link" href="#" onclick="changePage({{ $page }})">
                                    {{ $page }}
                                </a>
                            @else
                                <span class="page-link">...</span>
                            @endif
                        </li>
                    @endforeach

                    {{-- Next Page --}}
                    <li class="page-item {{ $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' }}">
                        <a class="page-link" href="#" 
                           @if($pagination['current_page'] < $pagination['last_page'])
                               onclick="changePage({{ $pagination['current_page'] + 1 }})"
                           @endif>
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Pagination Info --}}
            <div class="pagination-info">
                <small class="text-muted">
                    {{ $pagination['from'] }}-{{ $pagination['to'] }} / {{ $pagination['total'] }} kayıt
                </small>
            </div>
        </div>
        @endif

    </div>
</div>
