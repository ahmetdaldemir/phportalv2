@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kart /</span> @if(isset($stockcards))
                {{$stockcards->name}}
            @endif</h4>
        <div id="app">
            <form action="{{route('stockcard.store')}}" method="post" class="needs-validation" novalidate @submit="submitForm">
                @csrf
                <input type="hidden" name="id" @if(isset($stockcards)) value="{{$stockcards->id}}" @endif />
                
                <!-- Temel Bilgiler Kartı -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-package me-2"></i>Stok Kart Bilgileri
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Stok Adı -->
                            <div class="col-xl-6 col-lg-6 col-md-12">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bx bx-tag me-1 text-primary"></i>Stok Adı
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="name" 
                                           v-model="formData.name"
                                           @keydown="handleKeydown"
                                           @click="handleClickOutside"
                                           name="name" 
                                           placeholder="Stok adını giriniz..." 
                                           autocomplete="off" 
                                           required>
                                    
                                    <!-- Loading Spinner -->
                                    <div v-if="loading.stockSearch" class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Aranıyor...</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Autocomplete Suggestions -->
                                    <div v-show="showSuggestions" 
                                         id="name-suggestions" 
                                         class="autocomplete-suggestions">
                                        <div v-if="loading.stockSearch" class="autocomplete-loading">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            Aranıyor...
                                        </div>
                                        <div v-else-if="stockSuggestions.length === 0" class="autocomplete-no-results">
                                            Sonuç bulunamadı
                                        </div>
                                        <div v-else>
                                            <div v-for="(suggestion, index) in stockSuggestions" 
                                                 :key="'suggestion-' + index"
                                                 :class="['autocomplete-suggestion', { 'active': selectedSuggestionIndex === index }]"
                                                 @click="selectSuggestion(index)">
                                                @{{ suggestion }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">Mevcut stoklardan seçebilir veya yeni stok adı girebilirsiniz</div>
                            </div>

                            <!-- Barkod -->
                            <div class="col-xl-3 col-lg-3 col-md-6">
                                <label for="barcode" class="form-label fw-semibold">
                                    <i class="bx bx-barcode me-1 text-success"></i>Barkod
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="barcode"
                                       v-model="formData.barcode"
                                       name="barcode" 
                                       placeholder="Barkod giriniz...">
                            </div>

                            <!-- Stok Takibi -->
                            <div class="col-xl-3 col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bx bx-trending-up me-1 text-warning"></i>Stok Takibi
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               v-model="formData.tracking"
                                               name="tracking" 
                                               id="flexSwitchCheckChecked" 
                                               role="switch">
                                        <label class="form-check-label ms-2" for="flexSwitchCheckChecked">
                                            Takip Et
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
                <!-- Stok Ayarları Kartı -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-cog me-2"></i>Stok Ayarları
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Sol Kolon -->
                            <div class="col-lg-6">
                                <!-- Stok Takip Miktarı -->
                                <div class="mb-4">
                                    <label for="tracking_quantity" class="form-label fw-semibold">
                                        <i class="bx bx-hash me-1 text-info"></i>Stok Takip Miktarı
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="tracking_quantity"
                                           v-model="formData.tracking_quantity"
                                           name="tracking_quantity" 
                                           placeholder="Minimum stok miktarı...">
                                    <div class="form-text">Stok bu miktarın altına düştüğünde uyarı verilir</div>
                                </div>

                                <!-- Kategori -->
                                <div class="mb-4">
                                    <label for="category_id" class="form-label fw-semibold">
                                        <i class="bx bx-category me-1 text-primary"></i>Kategori
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" 
                                            v-model="formData.category_id"
                                            name="category_id" 
                                            id="category_id" 
                                            required>
                                        <option value="">Kategori seçiniz...</option>
                                        <option v-for="category in categories" 
                                                :key="category.id" 
                                                :value="category.id">
                                            @{{ category.path }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Sağ Kolon -->
                            <div class="col-lg-6">
                                <!-- Marka -->
                                <div class="mb-4">
                                    <label for="brand_id" class="form-label fw-semibold">
                                        <i class="bx bx-purchase-tag me-1 text-success"></i>Marka
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="brand_id" 
                                            id="brand_id" 
                                            v-model="formData.brand_id"
                                            @change="getVersion"
                                            class="form-select form-select-lg" 
                                            required>
                                        <option value="">Marka seçiniz...</option>
                                        <option v-for="brand in brands" 
                                                :key="brand.id" 
                                                :value="brand.id">
                                            @{{ brand.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Model -->
                                <div class="mb-4">
                                    <label for="version_id" class="form-label fw-semibold">
                                        <i class="bx bx-mobile me-1 text-warning"></i>Model
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="version_id[]" 
                                            id="version_id" 
                                            v-model="formData.version_id"
                                            class="form-select form-select-lg" 
                                            required 
                                            multiple>
                                        <option value="">Model seçiniz...</option>
                                        <option v-for="version in versions" 
                                                :key="version.id" 
                                                :value="version.id">
                                            @{{ version.name }}
                                        </option>
                                    </select>
                                    <div class="form-text">Birden fazla model seçebilirsiniz</div>
                                </div>

                                <!-- Birim -->
                                <div class="mb-4">
                                    <label for="unit_id" class="form-label fw-semibold">
                                        <i class="bx bx-ruler me-1 text-secondary"></i>Birim
                                    </label>
                                    <select name="unit_id" 
                                            id="unit_id" 
                                            v-model="formData.unit_id"
                                            class="form-select form-select-lg">
                                        <option v-for="(value, key) in units" 
                                                :key="key" 
                                                :value="key">
                                            @{{ value }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kaydet Butonu -->
                <div class="d-flex justify-content-end gap-3 mb-4">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-lg px-4"
                            @click="formData = {}">
                        <i class="bx bx-x me-2"></i>İptal
                    </button>
                    <button type="submit" 
                            class="btn btn-primary btn-lg px-5"
                            :disabled="loading.brands || loading.versions || loading.categories">
                        <i class="bx bx-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
        <hr class="my-5">
    </div>
    <style>
        /* Modern Form Stilleri */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }

        .form-control-lg, .form-select-lg {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 16px;
            padding: 12px 16px;
        }

        .form-control-lg:focus, .form-select-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            border: none;
            padding: 20px 24px;
        }

        .card-body {
            padding: 24px;
        }

        .btn-lg {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        .form-switch-lg .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        .form-switch-lg .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 16px;
            }
            
            .form-control-lg, .form-select-lg {
                font-size: 14px;
                padding: 10px 12px;
            }
        }

        .controls {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #fff;
            z-index: 1;
            padding: 6px 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        /* Autocomplete stilleri - Modern tasarım */
        .autocomplete-suggestions {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #e1e5e9;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            max-height: 250px;
            overflow-y: auto;
            z-index: 9999;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            margin-bottom: 4px;
        }

        .autocomplete-suggestion {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
            font-size: 14px;
            color: #2d3748;
            position: relative;
        }

        .autocomplete-suggestion:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(4px);
        }

        .autocomplete-suggestion.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(4px);
        }

        .autocomplete-suggestion:last-child {
            border-bottom: none;
            border-radius: 0 0 8px 8px;
        }

        .autocomplete-suggestion:first-child {
            border-radius: 0;
        }

        /* Scrollbar stilleri */
        .autocomplete-suggestions::-webkit-scrollbar {
            width: 6px;
        }

        .autocomplete-suggestions::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .autocomplete-suggestions::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .autocomplete-suggestions::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Input focus efekti */
        #name:focus + .autocomplete-suggestions {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Loading animasyonu */
        .autocomplete-loading {
            padding: 12px 16px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        .autocomplete-loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
            margin-left: 8px;
        }

        .autocomplete-no-results {
            padding: 12px 16px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            border-bottom: 1px solid #f1f3f4;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive tasarım */
        @media (max-width: 768px) {
            .autocomplete-suggestions {
                max-height: 200px;
                font-size: 13px;
            }
            
            .autocomplete-suggestion {
                padding: 10px 12px;
            }
        }

        button {
            border: 0px;
            color: #e13300;
            margin: 4px;
            padding: 4px 12px;
            cursor: pointer;
            background: transparent;
        }

        button.active,
        button.active:hover {
            background: #e13300;
            color: #fff;
        }

        button:hover {
            background: #efefef;
        }

        input[type=checkbox] {
            vertical-align: middle !important;
        }

        h1 {
            font-size: 3em;
            font-weight: lighter;
            color: #fff;
            text-align: center;
            display: block;
            padding: 40px 0px;
            margin-top: 40px;
        }

        .tree {
            margin: 2% auto;
            width: 80%;
        }

        .tree ul {
            margin: 4px auto;
            margin-left: 6px;
            border-left: 1px dashed #dfdfdf;
        }


        .tree li {
            padding: 12px 18px;
            cursor: pointer;
            vertical-align: middle;
            background: #fff;
        }

        .tree li:first-child {
            border-radius: 3px 3px 0 0;
        }

        .tree li:last-child {
            border-radius: 0 0 3px 3px;
        }

        .tree .active,
        .active li {
            background: #efefef;
        }

        .tree label {
            cursor: pointer;
        }

        .tree input[type=checkbox] {
            margin: -2px 6px 0 0px;
        }

        .has > label {
            color: #000;
        }

        .tree .total {
            color: #e13300;
        }
    </style>
@endsection

@section('custom-js')
    <script>
        "use strict";
        !function () {
            var e = document.querySelectorAll(".invoice-item-price"),
                t = document.querySelectorAll(".invoice-item-qty"), n = document.querySelectorAll(".date-picker");
            e && e.forEach(function (e) {
                new Cleave(e, {delimiter: "", numeral: !0})
            }), t && t.forEach(function (e) {
                new Cleave(e, {delimiter: "", numeral: !0})
            }), n && n.forEach(function (e) {
                e.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD-MM-YYYY'
                    }
                })
            })
        }(), $(function () {
            var n, o, a, i, l, r, e = $(".btn-apply-changes"), t = $(".source-item"), c = {
                "App Design": "Designed UI kit & app pages.",
                "App Customization": "Customization & Bug Fixes.",
                "ABC Template": "Bootstrap 4 admin template.",
                "App Development": "Native App Development."
            };

            function p(e, t) {
                e.closest(".repeater-wrapper").find(t).text(e.val())
            }

            $(document).on("click", ".tax-select", function (e) {
                e.stopPropagation()
            }), e.length && $(document).on("click", ".btn-apply-changes", function (e) {
                var t = $(this);
                l = t.closest(".dropdown-menu").find("#taxInput1"), r = t.closest(".dropdown-menu").find("#taxInput2"), i = t.closest(".dropdown-menu").find("#discountInput"), o = t.closest(".repeater-wrapper").find(".tax-1"), a = t.closest(".repeater-wrapper").find(".tax-2"), n = $(".discount"), null !== l.val() && p(l, o), null !== r.val() && p(r, a), i.val().length && t.closest(".repeater-wrapper").find(n).text(i.val() + "%")
            }), t.length && (t.on("submit", function (e) {
                e.preventDefault()
            }), t.repeater({
                show: function () {
                    $(this).slideDown(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (e) {
                        return new bootstrap.Tooltip(e)
                    })
                }, hide: function (e) {
                    $(this).slideUp()
                }
            })), $(document).on("change", ".item-details", function () {
                var e = $(this), t = c[e.val()];
                e.next("textarea").length ? e.next("textarea").val(t) : e.after('<textarea class="form-control" rows="2">' + t + "</textarea>")
            })
        });
    </script>
<script>
    $(document).on('click', '.tree label', function(e) {
        $(this).next('ul').fadeToggle();
        e.stopPropagation();
    });

    $(document).on('change', '.tree input[type=checkbox]', function(e) {
        $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
        $(this).parentsUntil('.tree').children("input[type='checkbox']").prop('checked', this.checked);
        e.stopPropagation();
    });

    $(document).on('click', 'button', function(e) {
        switch ($(this).text()) {
            case 'Collepsed':
                $('.tree ul').fadeOut();
                break;
            case 'Expanded':
                $('.tree ul').fadeIn();
                break;
            case 'Checked All':
                $(".tree input[type='checkbox']").prop('checked', true);
                break;
            case 'Unchek All':
                $(".tree input[type='checkbox']").prop('checked', false);
                break;
            default:
        }
    });
</script>

<script>
    // AJAX ile verileri yükle - performans optimizasyonu
    $(document).ready(function() {
        // Markaları yükle
        $.ajax({
            url: '/stockcard/brands-ajax',
            method: 'GET',
            success: function(data) {
                var select = $('#brand_id');
                select.empty();
                select.append('<option value="">Seçiniz</option>');
                
                $.each(data, function(index, brand) {
                    select.append('<option value="' + brand.id + '">' + brand.name + '</option>');
                });
            },
            error: function() {
                console.log('Markalar yüklenemedi');
            }
        });

        // Kategorileri yükle
        $.ajax({
            url: '/stockcard/categories-ajax',
            method: 'GET',
            success: function(data) {
                var select = $('select[name="category_id"]');
                select.empty();
                select.append('<option value="">Seçiniz</option>');
                
                $.each(data, function(index, category) {
                    select.append('<option value="' + category.id + '">' + category.path + '</option>');
                });
            },
            error: function() {
                console.log('Kategoriler yüklenemedi');
            }
        });

        // Versiyonları yükle
        $.ajax({
            url: '/stockcard/versions-ajax',
            method: 'GET',
            success: function(data) {
                var select = $('#version_id');
                select.empty();
                select.append('<option value="">Seçiniz</option>');
                
                $.each(data, function(index, version) {
                    select.append('<option value="' + version.id + '">' + version.name + '</option>');
                });
            },
            error: function() {
                console.log('Versiyonlar yüklenemedi');
            }
        });

        // Autocomplete fonksiyonu
        let currentRequest = null;
        let selectedIndex = -1;
        let suggestions = [];

        $('#name').on('input', function() {
            const query = $(this).val();
            const suggestionsDiv = $('#name-suggestions');
            
            if (query.length < 2) {
                suggestionsDiv.hide();
                return;
            }

            // Loading göster
            showLoading();

            // Önceki isteği iptal et
            if (currentRequest) {
                currentRequest.abort();
            }

            // Yeni istek gönder
            currentRequest = $.ajax({
                url: '/stockcard/stock-names-ajax',
                method: 'GET',
                data: { q: query },
                success: function(data) {
                    suggestions = data;
                    selectedIndex = -1;
                    displaySuggestions(data);
                },
                error: function() {
                    console.log('Autocomplete verisi yüklenemedi');
                    suggestionsDiv.hide();
                }
            });
        });

        function showLoading() {
            const suggestionsDiv = $('#name-suggestions');
            suggestionsDiv.html('<div class="autocomplete-loading">Aranıyor...</div>');
            suggestionsDiv.show();
        }

        function displaySuggestions(data) {
            const suggestionsDiv = $('#name-suggestions');
            suggestionsDiv.empty();

            if (data.length === 0) {
                suggestionsDiv.hide();
                return;
            }

            data.forEach(function(item, index) {
                const suggestion = $('<div class="autocomplete-suggestion" data-index="' + index + '">' + item + '</div>');
                suggestionsDiv.append(suggestion);
            });

            suggestionsDiv.show();
        }

        // Klavye navigasyonu
        $('#name').on('keydown', function(e) {
            const suggestionsDiv = $('#name-suggestions');
            
            if (!suggestionsDiv.is(':visible')) return;

            switch(e.keyCode) {
                case 38: // Yukarı ok
                    e.preventDefault();
                    if (selectedIndex > 0) {
                        selectedIndex--;
                        updateSelection();
                    }
                    break;
                case 40: // Aşağı ok
                    e.preventDefault();
                    if (selectedIndex < suggestions.length - 1) {
                        selectedIndex++;
                        updateSelection();
                    }
                    break;
                case 13: // Enter
                    e.preventDefault();
                    if (selectedIndex >= 0) {
                        $('#name').val(suggestions[selectedIndex]);
                        suggestionsDiv.hide();
                    }
                    break;
                case 27: // Escape
                    suggestionsDiv.hide();
                    selectedIndex = -1;
                    break;
            }
        });

        function updateSelection() {
            $('.autocomplete-suggestion').removeClass('active');
            $('.autocomplete-suggestion[data-index="' + selectedIndex + '"]').addClass('active');
        }

        // Mouse ile seçim
        $(document).on('click', '.autocomplete-suggestion', function() {
            const index = $(this).data('index');
            $('#name').val(suggestions[index]);
            $('#name-suggestions').hide();
        });

        // Dışarı tıklama
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#name, #name-suggestions').length) {
                $('#name-suggestions').hide();
            }
        });
    });
</script>

<!-- Vue.js ile modern form yönetimi -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    // Vue.js yüklenme kontrolü
    if (typeof Vue === 'undefined') {
        console.error('Vue.js yüklenemedi!');
    }
    
    const { createApp } = Vue;
    
    createApp({
        data() {
            return {
                // Form verileri
                formData: {
                    name: '',
                    barcode: '',
                    tracking: false,
                    tracking_quantity: '',
                    category_id: '',
                    brand_id: '',
                    version_id: [],
                    unit_id: ''
                },
                
                // AJAX verileri
                brands: [],
                versions: [],
                categories: [],
                units: [],
                
                // Autocomplete
                stockSuggestions: [],
                selectedSuggestionIndex: -1,
                isSearching: false,
                showSuggestions: false,
                
                // Loading durumları
                loading: {
                    brands: false,
                    versions: false,
                    categories: false,
                    stockSearch: false
                }
            }
        },
        
        mounted() {
            this.loadInitialData();
        },
        
        methods: {
            // İlk veri yükleme
            async loadInitialData() {
                await Promise.all([
                    this.loadBrands(),
                    this.loadVersions(),
                    this.loadCategories(),
                    this.loadUnits()
                ]);
            },
            
            // Markaları yükle
            async loadBrands() {
                this.loading.brands = true;
                try {
                    const response = await fetch('/stockcard/brands-ajax');
                    this.brands = await response.json();
                } catch (error) {
                    console.error('Markalar yüklenemedi:', error);
                } finally {
                    this.loading.brands = false;
                }
            },
            
            // Versiyonları yükle
            async loadVersions() {
                this.loading.versions = true;
                try {
                    const response = await fetch('/stockcard/versions-ajax');
                    this.versions = await response.json();
                } catch (error) {
                    console.error('Versiyonlar yüklenemedi:', error);
                } finally {
                    this.loading.versions = false;
                }
            },
            
            // Kategorileri yükle
            async loadCategories() {
                this.loading.categories = true;
                try {
                    const response = await fetch('/stockcard/categories-ajax');
                    this.categories = await response.json();
                } catch (error) {
                    console.error('Kategoriler yüklenemedi:', error);
                } finally {
                    this.loading.categories = false;
                }
            },
            
            // Birimleri yükle
            async loadUnits() {
                // Units verisi zaten mevcut
                this.units = @json($units ?? []);
            },
            
            // Stok adı arama
            async searchStockNames() {
                if (!this.formData.name || this.formData.name.length < 2) {
                    this.showSuggestions = false;
                    this.stockSuggestions = [];
                    return;
                }
                
                this.loading.stockSearch = true;
                this.showSuggestions = true;
                
                try {
                    const response = await fetch(`/stockcard/stock-names-ajax?q=${encodeURIComponent(this.formData.name)}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    // Ensure data is array
                    this.stockSuggestions = Array.isArray(data) ? data : [];
                    this.selectedSuggestionIndex = -1;
                } catch (error) {
                    console.error('Autocomplete verisi yüklenemedi:', error);
                    this.stockSuggestions = [];
                    this.showSuggestions = false;
                } finally {
                    this.loading.stockSearch = false;
                }
            },
            
            // Suggestion seçimi
            selectSuggestion(index) {
                if (this.stockSuggestions[index]) {
                    this.formData.name = this.stockSuggestions[index];
                    this.showSuggestions = false;
                    this.selectedSuggestionIndex = -1;
                }
            },
            
            // Klavye navigasyonu
            handleKeydown(event) {
                if (!this.showSuggestions) return;
                
                switch(event.key) {
                    case 'ArrowUp':
                        event.preventDefault();
                        if (this.selectedSuggestionIndex > 0) {
                            this.selectedSuggestionIndex--;
                        }
                        break;
                    case 'ArrowDown':
                        event.preventDefault();
                        if (this.selectedSuggestionIndex < this.stockSuggestions.length - 1) {
                            this.selectedSuggestionIndex++;
                        }
                        break;
                    case 'Enter':
                        event.preventDefault();
                        if (this.selectedSuggestionIndex >= 0) {
                            this.selectSuggestion(this.selectedSuggestionIndex);
                        }
                        break;
                    case 'Escape':
                        this.showSuggestions = false;
                        this.selectedSuggestionIndex = -1;
                        break;
                }
            },
            
            // Dışarı tıklama
            handleClickOutside(event) {
                if (!event.target.closest('#name') && !event.target.closest('#name-suggestions')) {
                    this.showSuggestions = false;
                }
            },
            
            // Form gönderimi
            submitForm() {
                // Form validasyonu burada yapılabilir
                console.log('Form gönderiliyor:', this.formData);
            },
            
            // Marka değiştiğinde versiyonları yükle
            async getVersion() {
                if (!this.formData.brand_id) {
                    this.versions = [];
                    return;
                }
                
                this.loading.versions = true;
                try {
                    const response = await fetch(`/stockcard/versions-ajax?brand_id=${this.formData.brand_id}`);
                    this.versions = await response.json();
                } catch (error) {
                    console.error('Versiyonlar yüklenemedi:', error);
                } finally {
                    this.loading.versions = false;
                }
            }
        },
        
        watch: {
            'formData.name': {
                handler: 'searchStockNames',
                debounce: 300
            }
        }
    }).mount('#app');
</script>
@endsection
