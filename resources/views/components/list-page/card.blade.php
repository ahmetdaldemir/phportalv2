<!-- Professional Card -->
<div class="professional-card">
    @if($title || $badge)
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex align-items-center justify-content-between">
                @if($title)
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2 text-primary"></i>
                        {{ $title }}
                    </h5>
                @endif
                @if($badge)
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">{{ $badge }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="card-body p-0">
        {{ $slot }}
    </div>
</div>