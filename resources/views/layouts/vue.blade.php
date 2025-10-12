<!DOCTYPE html>
<html lang="tr" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'PHP Portal')</title>
    
    <meta name="description" content=""/>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/daterangepicker/daterangepicker.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/buttons.bootstrap5.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.checkboxes.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/responsive.bootstrap5.css') }}"/>

    <!-- Custom CSS -->
    @yield('custom-css')
</head>

<body>
    <!-- Internet Connection Check -->
    <div id="internet-check"></div>
    <div id="internet-not-available" style="display: none; width: 100%; height: fit-content;">
        <img src="{{ asset('img/not-connection.jpg') }}" style="max-width: 100%;" />
    </div>

    <!-- Vue App Mount Point -->
    <div id="app">
        <!-- Loading spinner while Vue app loads -->
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker-init.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Internet Connection Check Script -->
    <script>
        window.addEventListener('load', function() {
            var internetCheckDiv = document.getElementById('internet-check');
            var notAvailableDiv = document.getElementById('internet-not-available');
            var appDiv = document.getElementById('app');

            function checkInternetConnection() {
                if (navigator.onLine) {
                    internetCheckDiv.style.display = 'block';
                    notAvailableDiv.style.display = 'none';
                    appDiv.style.display = 'block';
                    console.log('İnternet bağlantısı var.');
                } else {
                    internetCheckDiv.style.display = 'none';
                    notAvailableDiv.style.display = 'block';
                    appDiv.style.display = 'none';
                    console.log('İnternet bağlantısı yok.');
                }
            }

            checkInternetConnection();
            window.addEventListener('online', checkInternetConnection);
            window.addEventListener('offline', checkInternetConnection);
        });
    </script>

    <!-- Global Variables for Vue -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: @json(auth()->user()),
            permissions: @json(auth()->user() ? auth()->user()->getAllPermissions()->pluck('name') : []),
            roles: @json(auth()->user() ? auth()->user()->getRoleNames() : []),
            company: @json(auth()->user() ? auth()->user()->company : null),
            seller: @json(auth()->user() ? auth()->user()->seller : null)
        };
    </script>

    <!-- Vite for Vue - Manual Asset Loading -->
    @if(app()->environment('local'))
        <!-- Development - Vite Dev Server -->
        <script type="module" src="http://localhost:5180/@vite/client"></script>
        <script type="module" src="http://localhost:5180/resources/js/app.ts"></script>
    @else
        <!-- Production - Built Assets -->
        <link rel="stylesheet" href="{{ asset('build/assets/app-CtoP1VY-.css') }}">
        <script type="module" src="{{ asset('build/assets/app-DQJI4Sj7.js') }}"></script>
    @endif

    <!-- Custom JS -->
    @yield('custom-js')
</body>
</html>
