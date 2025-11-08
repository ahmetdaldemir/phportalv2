<?php

namespace App\Console\Commands;

use App\Services\LlmService;
use Illuminate\Console\Command;

/**
 * LLM Learn Command
 * 
 * Projeyi LLM modeline öğretir.
 */
class LlmLearnCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'llm:learn {--samples= : Comma-separated list of sample files}';

    /**
     * The console command description.
     */
    protected $description = 'Teach the LLM about the project structure and patterns';

    /**
     * Execute the console command.
     */
    public function handle(LlmService $llmService): int
    {
        $this->info("Starting project learning process...");
        $this->newLine();

        // Proje yapısını topla
        $this->line("📊 Collecting project structure...");
        $projectStructure = $llmService->collectProjectStructure();
        $this->info("✓ Project structure collected");

        // Kod örneklerini topla
        $codeSamples = null;
        if ($samplesOption = $this->option('samples')) {
            $files = explode(',', $samplesOption);
            $files = array_map('trim', $files);
            
            $this->line("📝 Collecting code samples...");
            $codeSamples = $llmService->collectCodeSamples($files);
            $this->info("✓ Collected " . count($codeSamples) . " code samples");
        } else {
            // Varsayılan örnek dosyalar
            $defaultSamples = [
                app_path('Http/Controllers/HomeController.php'),
                app_path('Services/LlmService.php'),
                app_path('Models/User.php'),
            ];
            
            $existingSamples = array_filter($defaultSamples, 'file_exists');
            
            if (!empty($existingSamples)) {
                $this->line("📝 Collecting default code samples...");
                $codeSamples = $llmService->collectCodeSamples($existingSamples);
                $this->info("✓ Collected " . count($codeSamples) . " code samples");
            }
        }

        // Dokümantasyon topla
        $documentation = null;
        $readmePath = base_path('README.md');
        if (file_exists($readmePath)) {
            $this->line("📚 Loading documentation...");
            $documentation = file_get_contents($readmePath);
            $this->info("✓ Documentation loaded");
        }

        $this->newLine();
        $this->line("🧠 Teaching LLM about the project...");
        $this->line("This may take a few minutes...");
        $this->newLine();

        // Progress bar
        $bar = $this->output->createProgressBar(100);
        $bar->start();

        // LLM'e öğret
        $result = $llmService->learnProject(
            projectStructure: $projectStructure,
            codeSamples: $codeSamples,
            documentation: $documentation
        );

        $bar->finish();
        $this->newLine(2);

        if (!$result['success']) {
            $this->error("Learning failed: " . ($result['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info("=== Learning Results ===");
        $this->newLine();
        
        if (isset($result['data']['response'])) {
            $this->line($result['data']['response']);
        }

        $this->newLine();
        $this->info("✓ Project learning completed successfully!");
        $this->line("The LLM now has knowledge about your project structure and patterns.");

        return Command::SUCCESS;
    }
}

