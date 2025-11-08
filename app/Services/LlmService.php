<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * LLM Service for PHPortal
 * 
 * Bu servis Laravel uygulaması ile FastAPI LLM servisi arasında iletişim kurar.
 * Ollama modelini kullanarak kod analizi, bug fix ve proje öğrenimi yapar.
 */
class LlmService
{
    /**
     * FastAPI servis URL'i
     */
    private string $apiBaseUrl;

    /**
     * Varsayılan model
     */
    private string $defaultModel;

    /**
     * Timeout süresi (saniye)
     */
    private int $timeout;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.llm.api_url', 'http://localhost:8000');
        $this->defaultModel = config('services.llm.default_model', 'phportal-assistant');
        $this->timeout = config('services.llm.timeout', 300);
    }

    /**
     * LLM servisinin durumunu kontrol et
     * 
     * @return array
     */
    public function healthCheck(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/health");
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'healthy',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'status' => 'unhealthy',
                'error' => 'Service returned non-200 status code'
            ];
        } catch (\Exception $e) {
            Log::error('LLM Service health check failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'unreachable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kod analizi yap
     * 
     * @param string $code Analiz edilecek kod
     * @param string|null $filePath Dosya yolu (opsiyonel)
     * @param array|null $context Ek bağlam bilgisi (opsiyonel)
     * @param string|null $model Kullanılacak model (opsiyonel)
     * @return array
     */
    public function analyzeCode(
        string $code,
        ?string $filePath = null,
        ?array $context = null,
        ?string $model = null
    ): array {
        try {
            $payload = [
                'code' => $code,
                'model' => $model ?? $this->defaultModel
            ];

            if ($filePath) {
                $payload['file_path'] = $filePath;
            }

            if ($context) {
                $payload['context'] = $context;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->apiBaseUrl}/api/analyze-code", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Code analysis failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Bug düzelt
     * 
     * @param string $code Hatalı kod
     * @param string|null $errorMessage Hata mesajı (opsiyonel)
     * @param string|null $filePath Dosya yolu (opsiyonel)
     * @param array|null $context Ek bağlam bilgisi (opsiyonel)
     * @param string|null $model Kullanılacak model (opsiyonel)
     * @return array
     */
    public function fixBug(
        string $code,
        ?string $errorMessage = null,
        ?string $filePath = null,
        ?array $context = null,
        ?string $model = null
    ): array {
        try {
            $payload = [
                'code' => $code,
                'model' => $model ?? $this->defaultModel
            ];

            if ($errorMessage) {
                $payload['error_message'] = $errorMessage;
            }

            if ($filePath) {
                $payload['file_path'] = $filePath;
            }

            if ($context) {
                $payload['context'] = $context;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->apiBaseUrl}/api/fix-bug", $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the fix suggestion for future reference
                if (isset($data['fixed_code'])) {
                    $cacheKey = 'llm_fix_' . md5($code . $errorMessage);
                    Cache::put($cacheKey, $data['fixed_code'], now()->addDays(7));
                }

                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Bug fix failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Projeyi öğren
     * 
     * @param array $projectStructure Proje yapısı
     * @param array|null $codeSamples Kod örnekleri (opsiyonel)
     * @param string|null $documentation Dokümantasyon (opsiyonel)
     * @param string|null $model Kullanılacak model (opsiyonel)
     * @return array
     */
    public function learnProject(
        array $projectStructure,
        ?array $codeSamples = null,
        ?string $documentation = null,
        ?string $model = null
    ): array {
        try {
            $payload = [
                'project_structure' => $projectStructure,
                'model' => $model ?? $this->defaultModel
            ];

            if ($codeSamples) {
                $payload['code_samples'] = $codeSamples;
            }

            if ($documentation) {
                $payload['documentation'] = $documentation;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->apiBaseUrl}/api/learn-project", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Project learning failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sohbet et
     * 
     * @param string $message Mesaj
     * @param array|null $conversationHistory Konuşma geçmişi (opsiyonel)
     * @param string|null $model Kullanılacak model (opsiyonel)
     * @return array
     */
    public function chat(
        string $message,
        ?array $conversationHistory = null,
        ?string $model = null
    ): array {
        try {
            $payload = [
                'message' => $message,
                'model' => $model ?? $this->defaultModel
            ];

            if ($conversationHistory) {
                $payload['conversation_history'] = $conversationHistory;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->apiBaseUrl}/api/chat", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Chat failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mevcut modelleri listele
     * 
     * @return array
     */
    public function listModels(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiBaseUrl}/api/models");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('List models failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Proje yapısını topla
     * 
     * @return array
     */
    public function collectProjectStructure(): array
    {
        $structure = [
            'framework' => 'Laravel ' . app()->version(),
            'php_version' => PHP_VERSION,
            'directories' => [
                'controllers' => app_path('Http/Controllers'),
                'models' => app_path('Models'),
                'services' => app_path('Services'),
                'repositories' => app_path('Repositories'),
                'views' => resource_path('views'),
            ],
            'features' => [
                'authentication' => true,
                'api' => true,
                'queue' => true,
                'cache' => true,
                'events' => true,
                'jobs' => true,
            ],
            'database' => [
                'driver' => config('database.default'),
                'tables_count' => $this->getTablesCount(),
            ]
        ];

        return $structure;
    }

    /**
     * Tablo sayısını al
     * 
     * @return int
     */
    private function getTablesCount(): int
    {
        try {
            $tables = \DB::select('SHOW TABLES');
            return count($tables);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Kod örneklerini topla
     * 
     * @param array $files Dosya yolları
     * @return array
     */
    public function collectCodeSamples(array $files): array
    {
        $samples = [];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $samples[] = [
                    'path' => str_replace(base_path(), '', $file),
                    'code' => file_get_contents($file)
                ];
            }
        }

        return $samples;
    }
}

