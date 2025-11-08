<?php

namespace App\Http\Controllers;

use App\Services\LlmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * LLM Controller
 * 
 * Bu controller LLM servisi ile etkileşim sağlar.
 * Kod analizi, bug fix ve proje öğrenimi işlemlerini yönetir.
 */
class LlmController extends Controller
{
    public function __construct(
        private LlmService $llmService
    ) {}

    /**
     * Kod analizi yap
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'file_path' => 'nullable|string',
            'context' => 'nullable|array',
            'model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->llmService->analyzeCode(
                code: $request->input('code'),
                filePath: $request->input('file_path'),
                context: $request->input('context'),
                model: $request->input('model')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('LLM analyze code error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while analyzing code'
            ], 500);
        }
    }

    /**
     * Bug düzelt
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixBug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'error_message' => 'nullable|string',
            'file_path' => 'nullable|string',
            'context' => 'nullable|array',
            'model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->llmService->fixBug(
                code: $request->input('code'),
                errorMessage: $request->input('error_message'),
                filePath: $request->input('file_path'),
                context: $request->input('context'),
                model: $request->input('model')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('LLM fix bug error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while fixing bug'
            ], 500);
        }
    }

    /**
     * Proje öğrenimi başlat
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_structure' => 'nullable|array',
            'code_samples_paths' => 'nullable|array',
            'documentation' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Proje yapısını topla
            $projectStructure = $request->input('project_structure') 
                ?? $this->llmService->collectProjectStructure();

            // Kod örneklerini topla
            $codeSamples = null;
            if ($request->has('code_samples_paths')) {
                $paths = $request->input('code_samples_paths');
                $codeSamples = $this->llmService->collectCodeSamples($paths);
            }

            // Dokümantasyon
            $documentation = $request->input('documentation');
            if (!$documentation && file_exists(base_path('README.md'))) {
                $documentation = file_get_contents(base_path('README.md'));
            }

            $result = $this->llmService->learnProject(
                projectStructure: $projectStructure,
                codeSamples: $codeSamples,
                documentation: $documentation,
                model: $request->input('model')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('LLM learn project error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while learning project'
            ], 500);
        }
    }

    /**
     * Sohbet
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'conversation_history' => 'nullable|array',
            'model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->llmService->chat(
                message: $request->input('message'),
                conversationHistory: $request->input('conversation_history'),
                model: $request->input('model')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('LLM chat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred during chat'
            ], 500);
        }
    }

    /**
     * Sağlık kontrolü
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck()
    {
        try {
            $result = $this->llmService->healthCheck();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mevcut modelleri listele
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listModels()
    {
        try {
            $result = $this->llmService->listModels();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

