<!DOCTYPE html>
<html>
<head>
    <title>Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="company-id" content="{{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 1 }}">
</head>
<body>
    <h1>Debug Test Sayfası</h1>
    
    <div id="auth-test">
        <h2>Auth Test:</h2>
        <p>Kullanıcı: {{ \Illuminate\Support\Facades\Auth::user()->name ?? 'Anonim' }}</p>
        <p>Şirket ID: {{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 'Yok' }}</p>
        <p>CSRF Token: {{ csrf_token() }}</p>
    </div>
    
    <div id="cache-test">
        <h2>Cache Test:</h2>
        <div id="cache-status">Yükleniyor...</div>
    </div>

    <script>
        console.log('Debug test sayfası yüklendi');
        
        // Auth test
        const companyId = document.querySelector('meta[name="company-id"]').getAttribute('content');
        console.log('Company ID from meta:', companyId);
        
        // Cache test
        if (window.localStorageManager) {
            console.log('LocalStorage Manager bulundu');
            
            // Test cache
            window.localStorageManager.initializeCache().then(() => {
                const status = window.localStorageManager.getCacheStatus();
                console.log('Cache status:', status);
                
                let html = '<h3>Cache Durumu:</h3>';
                html += '<table border="1" style="border-collapse: collapse;">';
                html += '<tr><th>Key</th><th>Exists</th><th>Count</th><th>Company ID</th></tr>';
                
                Object.entries(status).forEach(([key, info]) => {
                    html += `<tr>
                        <td>${key}</td>
                        <td>${info.exists ? '✅' : '❌'}</td>
                        <td>${info.count}</td>
                        <td>${info.companyId}</td>
                    </tr>`;
                });
                html += '</table>';
                
                document.getElementById('cache-status').innerHTML = html;
            });
        } else {
            console.log('LocalStorage Manager bulunamadı');
            document.getElementById('cache-status').innerHTML = '❌ LocalStorage Manager bulunamadı';
        }
    </script>
</body>
</html>
