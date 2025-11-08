<?php

namespace App\Console\Commands;

use App\Services\LlmService;
use Illuminate\Console\Command;

/**
 * LLM Analyze Command
 * 
 * Dosyadaki kodu analiz eder ve öneriler sunar.
 */
class LlmAnalyzeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'llm:analyze {file} {--focus= : Analysis focus (performance, security, quality)}';

    /**
     * The console command description.
     */
    protected $description = 'Analyze code file with LLM';

    /**
     * Execute the console command.
     */
    public function handle(LlmService $llmService): int
    {
        $file = $this->argument('file');
        
        // Dosya yolu kontrolü
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return Command::FAILURE;
        }

        $code = file_get_contents($file);
        
        // Bağlam oluştur
        $context = [];
        if ($focus = $this->option('focus')) {
            $context['focus'] = $focus;
        }

        $this->info("Analyzing $file...");
        $this->newLine();

        // Progress bar göster
        $bar = $this->output->createProgressBar(3);
        $bar->start();

        $bar->advance();

        // Analiz yap
        $result = $llmService->analyzeCode($code, $file, $context);

        $bar->advance();

        if (!$result['success']) {
            $bar->finish();
            $this->newLine(2);
            $this->error("Analysis failed: " . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);

        // Sonuçları göster
        $this->info("=== Analysis Results ===");
        $this->newLine();
        $this->line($result['data']['response'] ?? 'No response');
        
        if (isset($result['data']['suggestions']) && count($result['data']['suggestions']) > 0) {
            $this->newLine();
            $this->info("=== Suggestions ===");
            foreach ($result['data']['suggestions'] as $suggestion) {
                if (!empty(trim($suggestion))) {
                    $this->line("• " . trim($suggestion));
                }
            }
        }

        $this->newLine();
        $this->info("Analysis completed successfully!");

        return Command::SUCCESS;
    }
}

