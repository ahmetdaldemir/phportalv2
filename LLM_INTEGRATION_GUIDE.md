# PHPortal LLM Entegrasyonu Kullanım Kılavuzu

Bu kılavuz, PHPortal projesine entegre edilen LLM (Large Language Model) sisteminin nasıl kullanılacağını açıklar.

## 📋 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Kurulum](#kurulum)
3. [Kullanım](#kullanım)
4. [Örnekler](#örnekler)
5. [API Referansı](#api-referansı)
6. [Sorun Giderme](#sorun-giderme)

## 🔍 Genel Bakış

LLM entegrasyonu üç ana bileşenden oluşur:

1. **FastAPI Servisi** (`/llm_api/`) - Python tabanlı API servisi
2. **Laravel LlmService** (`/app/Services/LlmService.php`) - Laravel entegrasyon servisi
3. **Ollama Model** (`/ollama/`) - Özel eğitilmiş PHPortal asistan modeli

### Mimari

```
Laravel App
    ↓
LlmService.php
    ↓
FastAPI (llm_api/)
    ↓
Ollama + phportal-assistant model
```

## 🚀 Kurulum

### 1. Ollama Kurulumu

#### Windows
```powershell
# Ollama'yı indir ve kur: https://ollama.ai/download
# Kurulum sonrası:
cd ollama
.\setup-model.ps1
```

#### Linux/Mac
```bash
# Ollama'yı yükle
curl -fsSL https://ollama.ai/install.sh | sh

# Model kurmak için:
cd ollama
chmod +x setup-model.sh
./setup-model.sh
```

### 2. FastAPI Servisi Kurulumu

```bash
cd llm_api

# Python sanal ortamı oluştur
python -m venv venv

# Windows
venv\Scripts\activate

# Linux/Mac
source venv/bin/activate

# Gereksinimleri yükle
pip install -r requirements.txt

# Servisi başlat
python main.py
```

### 3. Laravel Yapılandırması

`config/services.php` dosyasına ekle:

```php
'llm' => [
    'api_url' => env('LLM_API_URL', 'http://localhost:8000'),
    'default_model' => env('LLM_DEFAULT_MODEL', 'phportal-assistant'),
    'timeout' => env('LLM_TIMEOUT', 300),
],
```

`.env` dosyasına ekle:

```env
LLM_API_URL=http://localhost:8000
LLM_DEFAULT_MODEL=phportal-assistant
LLM_TIMEOUT=300
```

## 💻 Kullanım

### Laravel'den Kullanım

#### 1. Kod Analizi

```php
use App\Services\LlmService;

$llmService = new LlmService();

$result = $llmService->analyzeCode(
    code: $sourceCode,
    filePath: 'app/Http/Controllers/UserController.php',
    context: [
        'type' => 'controller',
        'concerns' => ['performance', 'security']
    ]
);

if ($result['success']) {
    $analysis = $result['data']['response'];
    $suggestions = $result['data']['suggestions'];
    
    // Analiz sonuçlarını kullan
    foreach ($suggestions as $suggestion) {
        echo "- " . $suggestion . "\n";
    }
}
```

#### 2. Bug Düzeltme

```php
$result = $llmService->fixBug(
    code: $buggyCode,
    errorMessage: 'Call to undefined method',
    filePath: 'app/Services/PaymentService.php',
    context: [
        'error_line' => 45,
        'stack_trace' => $stackTrace
    ]
);

if ($result['success']) {
    $fixedCode = $result['data']['fixed_code'];
    $explanation = $result['data']['response'];
    
    // Düzeltilmiş kodu kaydet veya göster
    file_put_contents('fixed_code.php', $fixedCode);
}
```

#### 3. Proje Öğrenimi

```php
// Proje yapısını topla
$projectStructure = $llmService->collectProjectStructure();

// Örnek kodları topla
$codeSamples = $llmService->collectCodeSamples([
    app_path('Http/Controllers/HomeController.php'),
    app_path('Services/StockCardService.php'),
    app_path('Repositories/InvoiceRepository.php'),
]);

// Dokümantasyonu hazırla
$documentation = file_get_contents(base_path('README.md'));

// LLM'e öğret
$result = $llmService->learnProject(
    projectStructure: $projectStructure,
    codeSamples: $codeSamples,
    documentation: $documentation
);

if ($result['success']) {
    Log::info('Project learning completed', $result['data']);
}
```

#### 4. Sohbet

```php
$conversationHistory = [
    ['role' => 'user', 'content' => 'Bu controller nasıl iyileştirilebilir?'],
    ['role' => 'assistant', 'content' => 'Repository pattern kullanabilirsiniz...'],
];

$result = $llmService->chat(
    message: 'Peki service layer nasıl olmalı?',
    conversationHistory: $conversationHistory
);

if ($result['success']) {
    $response = $result['data']['response'];
    // Cevabı göster
}
```

### Controller Örneği

```php
<?php

namespace App\Http\Controllers;

use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LlmController extends Controller
{
    public function __construct(
        private LlmService $llmService
    ) {}

    /**
     * Kod analizi yap
     */
    public function analyzeCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'file_path' => 'nullable|string',
            'context' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->llmService->analyzeCode(
            code: $request->input('code'),
            filePath: $request->input('file_path'),
            context: $request->input('context')
        );

        return response()->json($result);
    }

    /**
     * Bug düzelt
     */
    public function fixBug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'error_message' => 'nullable|string',
            'file_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->llmService->fixBug(
            code: $request->input('code'),
            errorMessage: $request->input('error_message'),
            filePath: $request->input('file_path')
        );

        return response()->json($result);
    }

    /**
     * Sağlık kontrolü
     */
    public function healthCheck()
    {
        $result = $this->llmService->healthCheck();
        return response()->json($result);
    }
}
```

### Route Tanımları

```php
// routes/api.php veya routes/web.php

Route::prefix('llm')->middleware(['auth'])->group(function () {
    Route::post('/analyze-code', [LlmController::class, 'analyzeCode']);
    Route::post('/fix-bug', [LlmController::class, 'fixBug']);
    Route::get('/health', [LlmController::class, 'healthCheck']);
});
```

## 📝 Örnekler

### Örnek 1: Performans Analizi

```php
$code = <<<'PHP'
public function getUsers()
{
    $users = User::all();
    foreach ($users as $user) {
        $user->posts; // N+1 problem
    }
    return $users;
}
PHP;

$result = $llmService->analyzeCode(
    code: $code,
    context: ['focus' => 'performance']
);

// Çıktı:
// "N+1 query problemi tespit edildi. Eager loading kullanın:
// User::with('posts')->get()"
```

### Örnek 2: Güvenlik Kontrolü

```php
$code = <<<'PHP'
public function search(Request $request)
{
    $query = $request->input('q');
    $results = DB::select("SELECT * FROM products WHERE name LIKE '%$query%'");
    return view('search', compact('results'));
}
PHP;

$result = $llmService->analyzeCode(
    code: $code,
    context: ['focus' => 'security']
);

// Çıktı:
// "SQL Injection açığı var! Eloquent veya prepared statements kullanın."
```

### Örnek 3: Otomatik Bug Fix

```php
$buggyCode = <<<'PHP'
public function store(Request $request)
{
    $data = $request->all();
    Product::create($data); // Mass assignment vulnerability
    return redirect()->back();
}
PHP;

$result = $llmService->fixBug(
    code: $buggyCode,
    errorMessage: 'Mass Assignment Exception'
);

// fixed_code içerisinde düzeltilmiş kod:
/*
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
    ]);
    
    Product::create($validated);
    return redirect()->back()->with('success', 'Product created');
}
*/
```

## 🔌 API Referansı

### LlmService Metodları

#### `healthCheck(): array`
LLM servisinin durumunu kontrol eder.

#### `analyzeCode(string $code, ?string $filePath, ?array $context, ?string $model): array`
Kod analizi yapar.

#### `fixBug(string $code, ?string $errorMessage, ?string $filePath, ?array $context, ?string $model): array`
Bug düzeltir.

#### `learnProject(array $projectStructure, ?array $codeSamples, ?string $documentation, ?string $model): array`
Proje hakkında bilgi öğrenir.

#### `chat(string $message, ?array $conversationHistory, ?string $model): array`
Sohbet eder.

#### `listModels(): array`
Mevcut modelleri listeler.

## 🔧 Sorun Giderme

### LLM servisi bağlanamıyor

**Semptom**: `Connection refused` hatası

**Çözüm**:
```bash
# FastAPI servisini başlat
cd llm_api
python main.py
```

### Ollama bulunamadı

**Semptom**: `Ollama API error`

**Çözüm**:
```bash
# Ollama servisini başlat
ollama serve

# Farklı terminal penceresinde model çalıştır
ollama run phportal-assistant
```

### Model yavaş çalışıyor

**Çözüm**:
1. Daha küçük model kullan (`codellama:7b`)
2. GPU desteği aktif mi kontrol et
3. Context boyutunu azalt (Modelfile'da)

### Timeout hatası

**Çözüm**:
`.env` dosyasında timeout süresini artır:
```env
LLM_TIMEOUT=600
```

## 🎯 En İyi Uygulamalar

1. **Cache Kullanımı**: Benzer sorguları cache'le
2. **Async İşleme**: Uzun işlemleri queue'ya at
3. **Error Handling**: Her zaman error kontrolü yap
4. **Logging**: Tüm LLM çağrılarını logla
5. **Rate Limiting**: API çağrılarını sınırla

## 📚 İleri Seviye Kullanım

### Command Line Interface

Artisan command oluştur:

```php
<?php

namespace App\Console\Commands;

use App\Services\LlmService;
use Illuminate\Console\Command;

class AnalyzeCodeCommand extends Command
{
    protected $signature = 'llm:analyze {file}';
    protected $description = 'Analyze code file with LLM';

    public function handle(LlmService $llmService)
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $code = file_get_contents($file);
        
        $this->info("Analyzing $file...");
        
        $result = $llmService->analyzeCode($code, $file);
        
        if ($result['success']) {
            $this->info("\nAnalysis Results:");
            $this->line($result['data']['response']);
        } else {
            $this->error("Analysis failed: " . $result['error']);
        }
        
        return 0;
    }
}
```

Kullanım:
```bash
php artisan llm:analyze app/Http/Controllers/UserController.php
```

## 🤝 Katkıda Bulunma

Model iyileştirmeleri için `ollama/Modelfile` dosyasını güncelleyin ve modeli yeniden oluşturun.

## 📄 Lisans

Bu entegrasyon PHPortal projesinin bir parçasıdır.

---

**Not**: LLM servisi geliştirme ortamında kullanılmak üzere tasarlanmıştır. Production ortamında kullanmadan önce güvenlik ve performans testleri yapın.

