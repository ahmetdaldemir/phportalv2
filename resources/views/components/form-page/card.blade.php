@props([
    'title' => 'Form Bilgileri',
    'icon' => 'bx-edit'
])

<div class="form-card">
    <div class="form-card-header">
        <h5 class="card-title mb-0">
            <i class="{{ $icon }} me-2"></i>
            {{ $title }}
        </h5>
    </div>
    <div class="form-card-body">
        {{ $slot }}
    </div>
</div>
