@props([
    'title' => 'Form',
    'icon' => 'bx-file',
    'description' => '',
    'backRoute' => null
])

<div class="form-page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2>
                <i class="{{ $icon }} me-2"></i>
                {{ $title }}
            </h2>
            @if($description)
                <p>{{ $description }}</p>
            @endif
        </div>
        @if($backRoute)
            <a href="{{ $backRoute }}" class="btn btn-light btn-lg shadow-sm">
                <i class="bx bx-arrow-back me-2"></i>Geri Dön
            </a>
        @endif
    </div>
</div>
