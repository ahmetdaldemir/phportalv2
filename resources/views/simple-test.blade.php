<!DOCTYPE html>
<html>
<head>
    <title>Simple Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="company-id" content="{{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 1 }}">
</head>
<body>
    <h1>Simple Test Sayfası</h1>
    
    <div>
        <p>Kullanıcı: {{ \Illuminate\Support\Facades\Auth::user()->name ?? 'Anonim' }}</p>
        <p>Şirket ID: {{ \Illuminate\Support\Facades\Auth::user()->company_id ?? 'Yok' }}</p>
        <p>CSRF Token: {{ csrf_token() }}</p>
    </div>
    
    <script>
        console.log('Sayfa yüklendi');
        console.log('Company ID:', document.querySelector('meta[name="company-id"]').getAttribute('content'));
    </script>
</body>
</html>
