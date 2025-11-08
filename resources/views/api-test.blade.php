<!DOCTYPE html>
<html>
<head>
    <title>API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="company-id" content="{{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 1 }}">
</head>
<body>
    <h1>API Test Sayfası</h1>
    
    <div id="results">
        <h2>StockCards API Test:</h2>
        <div id="stockcards-result">Yükleniyor...</div>
    </div>

    <script>
        async function testStockCardsAPI() {
            try {
                const response = await fetch('/api/stockcards?category_id=&brand_id=&seller_id=&color_id=&search=&page=1', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    document.getElementById('stockcards-result').innerHTML = 
                        `✅ Başarılı! ${data.data ? data.data.length : data.length} stok kartı yüklendi`;
                    console.log('StockCards data:', data);
                } else {
                    document.getElementById('stockcards-result').innerHTML = 
                        `❌ Hata: ${data.error || 'Bilinmeyen hata'}`;
                    console.error('API Error:', data);
                }
            } catch (error) {
                document.getElementById('stockcards-result').innerHTML = 
                    `❌ Hata: ${error.message}`;
                console.error('Fetch Error:', error);
            }
        }
        
        // Test when page loads
        window.onload = testStockCardsAPI;
    </script>
</body>
</html>
