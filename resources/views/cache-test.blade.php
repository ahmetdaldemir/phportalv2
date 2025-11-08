<!DOCTYPE html>
<html>
<head>
    <title>Cache Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="company-id" content="{{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 1 }}">
</head>
<body>
    <h1>Cache Test Sayfası</h1>
    
    <div id="results">
        <h2>Cache Test Sonuçları:</h2>
        <div id="cache-status">Yükleniyor...</div>
        <div id="api-results">API Sonuçları: Yükleniyor...</div>
    </div>

    <script>
        async function testCache() {
            const results = document.getElementById('results');
            
            try {
                // Test cache initialization
                if (window.localStorageManager) {
                    console.log('LocalStorage Manager bulundu');
                    
                    // Initialize cache
                    await window.localStorageManager.initializeCache();
                    
                    // Test individual endpoints
                    const brands = await window.localStorageManager.getBrands();
                    const sellers = await window.localStorageManager.getSellers();
                    const colors = await window.localStorageManager.getColors();
                    
                    // Show results
                    let html = '<h3>Cache Durumu:</h3>';
                    html += '<ul>';
                    html += `<li>Markalar: ${brands.length} kayıt</li>`;
                    html += `<li>Şubeler: ${sellers.length} kayıt</li>`;
                    html += `<li>Renkler: ${colors.length} kayıt</li>`;
                    html += '</ul>';
                    
                    // Show cache status
                    const status = window.localStorageManager.getCacheStatus();
                    html += '<h3>Cache Detayları:</h3>';
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
                    
                } else {
                    document.getElementById('cache-status').innerHTML = '❌ LocalStorage Manager bulunamadı';
                }
                
            } catch (error) {
                console.error('Cache test error:', error);
                document.getElementById('cache-status').innerHTML = `❌ Hata: ${error.message}`;
            }
        }
        
        // Run test when page loads
        window.onload = testCache;
    </script>
</body>
</html>
