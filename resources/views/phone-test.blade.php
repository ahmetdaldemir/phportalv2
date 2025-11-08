<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Module Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="company-id" content="{{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 1 }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Phone Module Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>API Test</h5>
                    </div>
                    <div class="card-body">
                        <button onclick="testPhoneAPI()" class="btn btn-primary">Test Phone API</button>
                        <button onclick="testStaticData()" class="btn btn-success">Test Static Data</button>
                        <button onclick="testVersions()" class="btn btn-info">Test Versions</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Cache Test</h5>
                    </div>
                    <div class="card-body">
                        <button onclick="testCache()" class="btn btn-warning">Test Cache</button>
                        <button onclick="clearCache()" class="btn btn-danger">Clear Cache</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Results</h5>
                    </div>
                    <div class="card-body">
                        <pre id="results" style="max-height: 400px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Navigation</h5>
                    </div>
                    <div class="card-body">
                        <a href="/phone" class="btn btn-primary">Phone Page (Vue.js)</a>
                        <a href="/phone-test" class="btn btn-success">Test Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        // Axios CSRF token ayarı
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function log(message, data = null) {
            const results = document.getElementById('results');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = `[${timestamp}] ${message}\n${data ? JSON.stringify(data, null, 2) + '\n' : ''}`;
            results.textContent += logEntry;
            results.scrollTop = results.scrollHeight;
        }
        
        async function testPhoneAPI() {
            try {
                log('Testing Phone API...');
                const response = await axios.get('/api/phones');
                log('Phone API Response:', response.data);
            } catch (error) {
                log('Phone API Error:', error.response?.data || error.message);
            }
        }
        
        async function testStaticData() {
            try {
                log('Testing Static Data APIs...');
                
                const [brands, colors, sellers] = await Promise.all([
                    axios.get('/api/brands'),
                    axios.get('/api/colors'),
                    axios.get('/api/sellers')
                ]);
                
                log('Brands:', brands.data);
                log('Colors:', colors.data);
                log('Sellers:', sellers.data);
            } catch (error) {
                log('Static Data Error:', error.response?.data || error.message);
            }
        }
        
        async function testVersions() {
            try {
                log('Testing Versions API...');
                const response = await axios.get('/api/versions', {
                    params: { brand_id: 1 }
                });
                log('Versions Response:', response.data);
            } catch (error) {
                log('Versions Error:', error.response?.data || error.message);
            }
        }
        
        async function testCache() {
            try {
                log('Testing Cache...');
                
                if (window.localStorageManager) {
                    const status = window.localStorageManager.getCacheStatus();
                    log('Cache Status:', status);
                    
                    const brands = await window.localStorageManager.getBrands();
                    const colors = await window.localStorageManager.getColors();
                    const sellers = await window.localStorageManager.getSellers();
                    
                    log('Cached Brands:', brands);
                    log('Cached Colors:', colors);
                    log('Cached Sellers:', sellers);
                } else {
                    log('LocalStorage Manager not available');
                }
            } catch (error) {
                log('Cache Error:', error.message);
            }
        }
        
        function clearCache() {
            try {
                log('Clearing Cache...');
                if (window.localStorageManager) {
                    window.localStorageManager.clearAll();
                    log('Cache cleared successfully');
                } else {
                    log('LocalStorage Manager not available');
                }
            } catch (error) {
                log('Clear Cache Error:', error.message);
            }
        }
        
        // Auto-test on page load
        document.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, starting auto-tests...');
            setTimeout(() => {
                testCache();
            }, 1000);
        });
    </script>
</body>
</html>
