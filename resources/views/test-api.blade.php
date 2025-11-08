<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>API Test Sayfası</h1>
    
    <div id="results">
        <h2>Direct API Test Sonuçları:</h2>
        <div id="categories-result">Kategoriler: Yükleniyor...</div>
        <div id="brands-result">Markalar: Yükleniyor...</div>
        <div id="sellers-result">Şubeler: Yükleniyor...</div>
        <div id="colors-result">Renkler: Yükleniyor...</div>
        <div id="stockcards-result">Stok Kartları: Yükleniyor...</div>
        
        <h2>Cache Test Sonuçları:</h2>
        <div id="categories-cache-result">Kategoriler (Cache): Yükleniyor...</div>
        <div id="brands-cache-result">Markalar (Cache): Yükleniyor...</div>
        <div id="sellers-cache-result">Şubeler (Cache): Yükleniyor...</div>
        <div id="colors-cache-result">Renkler (Cache): Yükleniyor...</div>
        
        <h2>Cache Durumu:</h2>
        <div id="cache-status">Yükleniyor...</div>
    </div>

    <script>
        // CSRF token ayarı
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Test fonksiyonları
        async function testAPI(endpoint, resultId) {
            try {
                const response = await fetch(`/api/${endpoint}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    document.getElementById(resultId).innerHTML = 
                        `${endpoint}: ✅ Başarılı (${Array.isArray(data) ? data.length : 'N/A'} kayıt)`;
                } else {
                    document.getElementById(resultId).innerHTML = 
                        `${endpoint}: ❌ Hata - ${data.error || 'Bilinmeyen hata'}`;
                }
            } catch (error) {
                document.getElementById(resultId).innerHTML = 
                    `${endpoint}: ❌ Hata - ${error.message}`;
            }
        }

        // Cache test fonksiyonu
        async function testCache(endpoint, resultId) {
            try {
                if (window.localStorageManager) {
                    const data = await window.localStorageManager.getOrFetch(endpoint, `/api/${endpoint}`);
                    const cacheStatus = window.localStorageManager.getCacheStatus()[endpoint];
                    const isFresh = window.localStorageManager.isCacheFresh(endpoint);
                    
                    document.getElementById(resultId).innerHTML = 
                        `${endpoint} (Cache): ✅ ${data.length} kayıt | Fresh: ${isFresh ? '✅' : '❌'} | Company: ${cacheStatus.companyId}`;
                } else {
                    document.getElementById(resultId).innerHTML = 
                        `${endpoint} (Cache): ❌ LocalStorage Manager bulunamadı`;
                }
            } catch (error) {
                document.getElementById(resultId).innerHTML = 
                    `${endpoint} (Cache): ❌ Hata - ${error.message}`;
            }
        }
        
        // Tüm API'leri test et
        window.onload = function() {
            // Direct API tests
            testAPI('categories', 'categories-result');
            testAPI('brands', 'brands-result');
            testAPI('sellers', 'sellers-result');
            testAPI('colors', 'colors-result');
            testAPI('stockcards', 'stockcards-result');
            
            // Cache tests (after a short delay to allow cache initialization)
            setTimeout(() => {
                testCache('categories', 'categories-cache-result');
                testCache('brands', 'brands-cache-result');
                testCache('sellers', 'sellers-cache-result');
                testCache('colors', 'colors-cache-result');
                
                // Show cache status
                if (window.localStorageManager) {
                    const status = window.localStorageManager.getCacheStatus();
                    let statusHtml = '<table border="1" style="border-collapse: collapse; width: 100%;">';
                    statusHtml += '<tr><th>Key</th><th>Exists</th><th>Count</th><th>Fresh</th><th>Company ID</th></tr>';
                    
                    Object.entries(status).forEach(([key, info]) => {
                        const isFresh = window.localStorageManager.isCacheFresh(key);
                        statusHtml += `<tr>
                            <td>${key}</td>
                            <td>${info.exists ? '✅' : '❌'}</td>
                            <td>${info.count}</td>
                            <td>${isFresh ? '✅' : '❌'}</td>
                            <td>${info.companyId}</td>
                        </tr>`;
                    });
                    statusHtml += '</table>';
                    document.getElementById('cache-status').innerHTML = statusHtml;
                }
            }, 1000);
        };
    </script>
</body>
</html>
