<!DOCTYPE html>

<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>PHONE HOSPİTAL</title>

    <meta name="description" content=""/>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon/favicon.ico')}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/boxicons.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}"/>

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/buttons.bootstrap5.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.checkboxes.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/responsive.bootstrap5.css')}}"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-message-box@3.2.2/dist/messagebox.min.css"/>
    
    <!-- PROJECT BASE CSS - UNIFIED STYLING -->
    <link rel="stylesheet" href="{{asset('assets/css/project-base.css')}}?v={{time()}}"/>
     <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>
    
    <!-- Vue.js 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Global Vue.js Setup -->
    <script>
        // Global Store for Common Modules
        window.GlobalStore = {
            // Data cache
            cache: {
                sellers: [],
                categories: [],
                warehouses: [],
                colors: [],
                brands: [],
                versions: [],
                reasons: [],
                customers: [],
                cities: [],
                towns: [],
                currencies: [],
                safes: [],
                users: []
            },
            
            // Loading states
            loading: {
                sellers: false,
                categories: false,
                warehouses: false,
                colors: false,
                brands: false,
                versions: false,
                reasons: false,
                customers: false,
                cities: false,
                towns: false,
                currencies: false,
                safes: false,
                users: false
            },
            
            // Cache timestamps
            cacheTimestamps: {},
            cacheDuration: 5 * 60 * 1000, // 5 minutes
            
            // Check cache validity
            isCacheValid(key) {
                const timestamp = this.cacheTimestamps[key];
                if (!timestamp) return false;
                return (Date.now() - timestamp) < this.cacheDuration;
            },
            
            // Generic fetch method
            async fetchData(endpoint, cacheKey) {
                if (this.cache[cacheKey] && this.cache[cacheKey].length > 0 && this.isCacheValid(cacheKey)) {
                    return this.cache[cacheKey];
                }
                
                this.loading[cacheKey] = true;
                try {
                    const response = await fetch(endpoint, {
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    this.cache[cacheKey] = data;
                    this.cacheTimestamps[cacheKey] = Date.now();
                    return data;
                } catch (error) {
                    console.error(`Error loading ${cacheKey}:`, error);
                    return [];
                } finally {
                    this.loading[cacheKey] = false;
                }
            },
            
            // Specific data loaders
            async getSellers() { return await this.fetchData('/api/common/sellers', 'sellers'); },
            async getCategories() { return await this.fetchData('/api/common/categories', 'categories'); },
            async getWarehouses() { return await this.fetchData('/api/common/warehouses', 'warehouses'); },
            async getColors() { return await this.fetchData('/api/common/colors', 'colors'); },
            async getBrands() { return await this.fetchData('/api/common/brands', 'brands'); },
            async getReasons() { return await this.fetchData('/api/common/reasons', 'reasons'); },
            async getCustomers(type = null) { 
                const endpoint = type ? `/api/common/customers?type=${type}` : '/api/common/customers';
                const cacheKey = type ? `customers_${type}` : 'customers';
                return await this.fetchData(endpoint, cacheKey);
            },
            async getCities() { return await this.fetchData('/api/common/cities', 'cities'); },
            async getCurrencies() { return await this.fetchData('/api/common/currencies', 'currencies'); },
            async getSafes() { return await this.fetchData('/api/common/safes', 'safes'); },
            async getUsers() { return await this.fetchData('/api/common/users', 'users'); },
            
            // Get versions by brand
            async getVersions(brandId = null) {
                const endpoint = brandId ? `/api/common/versions?brand_id=${brandId}` : '/api/common/versions';
                const cacheKey = brandId ? `versions_brand_${brandId}` : 'versions';
                return await this.fetchData(endpoint, cacheKey);
            },
            
            // Get towns by city
            async getTowns(cityId) {
                const endpoint = `/api/common/towns?city_id=${cityId}`;
                const cacheKey = `towns_city_${cityId}`;
                return await this.fetchData(endpoint, cacheKey);
            }
        };

        // Global Vue mixins and utilities
        window.VueGlobalMixin = {
            data() {
                return {
                    globalStore: window.GlobalStore
                };
            },
            computed: {
                globalSellers() { return this.globalStore.cache.sellers; },
                globalCategories() { return this.globalStore.cache.categories; },
                globalParentCategories() { return this.globalCategories.filter(c => c.parent_id == 0 || !c.parent_id); },
                globalWarehouses() { return this.globalStore.cache.warehouses; },
                globalColors() { return this.globalStore.cache.colors; },
                globalBrands() { return this.globalStore.cache.brands; },
                globalVersions() { return this.globalStore.cache.versions; },
                globalReasons() { return this.globalStore.cache.reasons; },
                globalCustomers() { return this.globalStore.cache.customers; },
                globalAccountCustomers() { return this.globalCustomers.filter(c => c.type === 'account'); },
                globalRegularCustomers() { return this.globalCustomers.filter(c => c.type === 'customer'); },
                globalCities() { return this.globalStore.cache.cities; },
                globalTowns() { return this.globalStore.cache.towns; },
                globalCurrencies() { return this.globalStore.cache.currencies; },
                globalSafes() { return this.globalStore.cache.safes; },
                globalUsers() { return this.globalStore.cache.users; }
            },
            methods: {
                // Load common data modules
                async loadCommonData(modules = ['sellers', 'categories', 'warehouses', 'colors', 'brands', 'reasons']) {
                    const promises = modules.map(module => {
                        switch(module) {
                            case 'sellers': return this.globalStore.getSellers();
                            case 'categories': return this.globalStore.getCategories();
                            case 'warehouses': return this.globalStore.getWarehouses();
                            case 'colors': return this.globalStore.getColors();
                            case 'brands': return this.globalStore.getBrands();
                            case 'versions': return this.globalStore.getVersions();
                            case 'reasons': return this.globalStore.getReasons();
                            case 'customers': return this.globalStore.getCustomers();
                            case 'cities': return this.globalStore.getCities();
                            case 'currencies': return this.globalStore.getCurrencies();
                            case 'safes': return this.globalStore.getSafes();
                            case 'users': return this.globalStore.getUsers();
                        }
                    });
                    
                    await Promise.all(promises.filter(p => p));
                },
                
                // Utility methods
                formatCurrency(amount) {
                    return new Intl.NumberFormat('tr-TR', {
                        style: 'currency',
                        currency: 'TRY'
                    }).format(amount || 0);
                },
                formatDate(date) {
                    return new Date(date).toLocaleDateString('tr-TR');
                },
                async apiCall(url, options = {}) {
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                ...options.headers
                            },
                            ...options
                        });
                        return await response.json();
                    } catch (error) {
                        console.error('API Call Error:', error);
                        throw error;
                    }
                },
                
                // Helper methods
                getVersionsByBrand(brandId) {
                    return this.globalVersions.filter(v => v.brand_id == brandId);
                },
                getTownsByCity(cityId) {
                    return this.globalTowns.filter(t => t.city_id == cityId);
                },
                findFromCache(type, id) {
                    const cache = this.globalStore.cache[type];
                    return cache ? cache.find(item => item.id == id) : null;
                }
            }
        };
    </script>
    <script src="{{asset('assets/js/config.js')}}"></script>


    <script>

        window.addEventListener('load', function() {
            var internetCheckDiv = document.getElementById('internet-check');
            var notAvailableDiv = document.getElementById('internet-not-available');
            var pageScroll = document.getElementById('wrapperId');

            function checkInternetConnection() {
                if (navigator.onLine) {
                    internetCheckDiv.style.display = 'block';
                    notAvailableDiv.style.display = 'none';
                    pageScroll.style.display = 'block';
                    console.log('İnternet bağlantısı var.');
                } else {
                    internetCheckDiv.style.display = 'none';
                    notAvailableDiv.style.display = 'block';
                    pageScroll.style.display = 'none';

                    console.log('İnternet bağlantısı yok.');
                }
            }

            checkInternetConnection();

            window.addEventListener('online', checkInternetConnection);
            window.addEventListener('offline', checkInternetConnection);
        });

    </script>
    @yield('custom-css')

</head>

<body>

<div id="internet-check"></div>

<div id="internet-not-available" style="display: none;    width: 100%;height: fit-content;">
    <img src="{{asset('img/not-connection.jpg')}}" style="max-width: 100%;" />
</div>

<div class="layout-wrapper layout-content-navbar" id="wrapperId">
    <div class="layout-container">
@include('layouts.components.aside')
        <div class="layout-page">
            <nav
                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar"
            >
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <!-- Search -->
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                            <i class="bx bx-search fs-4 lh-0"></i>
                            <input
                                type="text"
                                class="form-control border-0 shadow-none"
                                placeholder="Arama..."
                                aria-label="Arama..."
                            />
                        </div>
                    </div>
                    <!-- /Search -->

                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        @role(['Depo Sorumlusu','super-admin'])
                        <li class="nav-item lh-1 me-3">
                            <a target="_blank" href="{{route("calculation.index")}}">Muhasebe</a>
                        </li>
                        @endrole

                        <!-- Place this tag where you want the button to render. -->
                        <li class="nav-item lh-1 me-3">
                            <a
                                class="github-button"
                                href="https://github.com/themeselection/sneat-html-admin-template-free"
                                data-icon="octicon-star"
                                data-size="large"
                                data-show-count="true"
                                aria-label="Star themeselection/sneat-html-admin-template-free on GitHub"
                            >{{\Carbon\Carbon::now()}}</a
                            >
                        </li>

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                               data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <i class="bx bx-user  w-px-40 h-auto rounded-circle" style="font-size: 35px;
    background: blueviolet;
    color: #fff;
    text-align: center;"></i>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <i class="bx bx-user  w-px-40 h-auto rounded-circle" style="font-size: 35px;
    background: blueviolet;
    color: #fff;
    text-align: center;"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-semibold d-block">{{\Illuminate\Support\Facades\Auth::user()->name}}</span>
                                                <small class="text-muted">{{\Illuminate\Support\Facades\Auth::user()->getRoleNames()}}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <!-- li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-user me-2"></i>
                                        <span class="align-middle">My Profile</span>
                                    </a>
                                </li -->
                                @role('super-admin')
                                <li>
                                    <a class="dropdown-item" href="{{route('settings.index')}}">
                                        <i class="bx bx-cog me-2"></i>
                                        <span class="align-middle">Settings</span>
                                    </a>
                                </li>
                                @endrole

                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{route('logout')}}">
                                        <i class="bx bx-power-off me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </nav>
            <div class="content-wrapper">
                @yield('content')
                @include('layouts.components.footer')

                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('assets/vendor/js/menu.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
 <script src="{{asset('assets/vendor/libs/i18n.js')}}"></script>

<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('assets/js/daterangepicker-init.js')}}"></script>
<script src="{{asset('assets/vendor/libs/tagify/tagify.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-message-box@3.2.2/dist/messagebox.min.js"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover();
    });
</script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

@yield('custom-js')

<script src="{{asset('assets/js/main.js')}}"></script>
@if(request()->routeIs('dashboard'))
<script src="{{asset('assets/js/custom.js')}}?rand=<?=rand(9,9999999)?>"></script>
@endif
<script src="{{asset('assets/js/forms-selects.js')}}"></script>


<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}"/>

@if(request()->routeIs('dashboard'))
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/js/docs.js')}}"></script>
<script src="{{asset('assets/vendor/js/charts-apex.js')}}"></script>
<script src="{{asset('assets/js/dashboard.js')}}"></script>
@endif


<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
