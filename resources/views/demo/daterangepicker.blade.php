@extends('layouts.vue')

@section('title', 'Date Range Picker Demo')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Date Range Picker Demo</h4>
                    <p class="card-subtitle">Modern jQuery Date Range Picker Kütüphanesi Örnekleri</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Date Range Picker -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Basit Tarih Aralığı Seçici</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tarih Aralığı Seçin</label>
                                        <input type="text" class="form-control daterangepicker-input" 
                                               placeholder="Tarih aralığı seçin..." readonly>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong>Özellikler:</strong>
                                        <ul class="mb-0">
                                            <li>Önceden tanımlı aralıklar</li>
                                            <li>Türkçe dil desteği</li>
                                            <li>Responsive tasarım</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Single Date Picker -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Tek Tarih Seçici</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tarih Seçin</label>
                                        <input type="text" class="form-control single-datepicker" 
                                               placeholder="Tarih seçin..." readonly>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong>Özellikler:</strong>
                                        <ul class="mb-0">
                                            <li>Tek tarih seçimi</li>
                                            <li>Yıl/ay dropdown'ları</li>
                                            <li>Otomatik format</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Time Range Picker -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Tarih ve Saat Aralığı Seçici</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tarih ve Saat Aralığı</label>
                                        <input type="text" class="form-control datetime-range-picker" 
                                               placeholder="Tarih ve saat aralığı seçin..." readonly>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong>Özellikler:</strong>
                                        <ul class="mb-0">
                                            <li>Saat seçimi dahil</li>
                                            <li>24 saat formatı</li>
                                            <li>30 dakika aralıklarla</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Apply Date Range Picker -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Otomatik Uygulama</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Otomatik Uygulanan Aralık</label>
                                        <input type="text" class="form-control auto-apply-daterangepicker" 
                                               placeholder="Tarih aralığı seçin..." readonly>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong>Özellikler:</strong>
                                        <ul class="mb-0">
                                            <li>Otomatik uygulama</li>
                                            <li>Uygula butonu yok</li>
                                            <li>Anında güncelleme</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Examples -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Kod Örnekleri</h5>
                                </div>
                                <div class="card-body">
                                    <h6>HTML Kullanımı:</h6>
                                    <pre><code>&lt;!-- Basit tarih aralığı --&gt;
&lt;input type="text" class="form-control daterangepicker-input" placeholder="Tarih aralığı seçin..." readonly&gt;

&lt;!-- Tek tarih seçici --&gt;
&lt;input type="text" class="form-control single-datepicker" placeholder="Tarih seçin..." readonly&gt;

&lt;!-- Tarih ve saat aralığı --&gt;
&lt;input type="text" class="form-control datetime-range-picker" placeholder="Tarih ve saat aralığı seçin..." readonly&gt;

&lt;!-- Otomatik uygulama --&gt;
&lt;input type="text" class="form-control auto-apply-daterangepicker" placeholder="Tarih aralığı seçin..." readonly&gt;</code></pre>

                                    <h6 class="mt-3">JavaScript Kullanımı:</h6>
                                    <pre><code>// Programmatik olarak tarih aralığı ayarlama
DateRangePickerUtils.setDateRange('#my-input', '2024-01-01', '2024-01-31');

// Mevcut tarih aralığını alma
var range = DateRangePickerUtils.getCurrentRange('#my-input');
console.log('Başlangıç:', range.startDate.format('DD-MM-YYYY'));
console.log('Bitiş:', range.endDate.format('DD-MM-YYYY'));

// Tarih aralığını temizleme
DateRangePickerUtils.clearDateRange('#my-input');</code></pre>

                                    <h6 class="mt-3">Event Handling:</h6>
                                    <pre><code>// Tarih aralığı uygulandığında
$(document).on('daterangepicker:applied', function(event, data) {
    console.log('Uygulanan tarih aralığı:', data.startDate, 'to', data.endDate);
});

// Tarih aralığı iptal edildiğinde
$(document).on('daterangepicker:cancelled', function(event, data) {
    console.log('Tarih aralığı iptal edildi');
});</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
$(document).ready(function() {
    // Demo için event listeners
    $(document).on('daterangepicker:applied', function(event, data) {
        console.log('Tarih aralığı uygulandı:', data.startDate.format('DD-MM-YYYY'), 'to', data.endDate.format('DD-MM-YYYY'));
        
        // SweetAlert ile bilgi göster
        Swal.fire({
            title: 'Tarih Aralığı Seçildi!',
            text: data.startDate.format('DD-MM-YYYY') + ' - ' + data.endDate.format('DD-MM-YYYY'),
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    });

    $(document).on('daterangepicker:cancelled', function(event, data) {
        console.log('Tarih aralığı iptal edildi');
    });
});
</script>
@endsection
