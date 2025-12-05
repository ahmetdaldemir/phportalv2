<!-- Professional Page Header -->
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="me-3">
                <i class="bx {{ $icon }} display-4 text-white"></i>
            </div>
            <div>
                <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600; color: white;">
                    <i class="bx {{ $icon }} me-2"></i>
                    {{ strtoupper($title) }}
                </h2>
                <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">{{ $description }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($count !== null)
                <span class="badge bg-light text-dark align-self-center">{{ $count }} Kayıt</span>
            @endif
            @if($createRoute)
                <a href="{{ $createRoute }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i>
                    Yeni Ekle
                </a>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>