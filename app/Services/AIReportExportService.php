<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AIReportExportService
{
    /**
     * PDF olarak AI raporu oluştur
     */
    public function exportToPDF($analysis, $companyName = 'Şirket')
    {
        try {
            $data = [
                'analysis' => $analysis,
                'companyName' => $companyName,
                'generatedAt' => Carbon::now()->format('d.m.Y H:i'),
                'title' => 'AI Stok Analiz Raporu'
            ];
            
            $pdf = PDF::loadView('reports.ai-analysis-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('ai-stok-analizi-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('PDF export hatası: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Excel formatında export
     */
    public function exportToExcel($analysis)
    {
        try {
            $filename = 'ai-stok-analizi-' . date('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($analysis) {
                $file = fopen('php://output', 'w');
                
                // BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Özet Bilgiler
                fputcsv($file, ['AI STOK ANALİZ RAPORU'], ';');
                fputcsv($file, ['Oluşturulma: ' . ($analysis['generated_at'] ?? date('Y-m-d H:i:s'))], ';');
                fputcsv($file, [], ';');
                
                // Summary
                if (isset($analysis['summary'])) {
                    fputcsv($file, ['ÖZET BİLGİLER'], ';');
                    fputcsv($file, ['Toplam Ürün', $analysis['summary']['total_products'] ?? 0], ';');
                    fputcsv($file, ['Ort. Devir Hızı (gün)', $analysis['summary']['avg_turnover_rate'] ?? 0], ';');
                    fputcsv($file, ['Toplam Gelir', $analysis['summary']['total_revenue'] ?? 0], ';');
                    fputcsv($file, ['Sağlık Skoru', $analysis['score'] ?? 0], ';');
                    fputcsv($file, [], ';');
                }
                
                // Predictions
                if (isset($analysis['predictions']) && is_array($analysis['predictions'])) {
                    fputcsv($file, ['TAHMİNLER'], ';');
                    fputcsv($file, ['Ürün', 'Mevcut Stok', 'Günlük Satış', 'Gelecek Hafta Tahmini', 'Gelecek Ay Tahmini', 'Tükenme (Gün)', 'Öneri'], ';');
                    
                    foreach ($analysis['predictions'] as $pred) {
                        fputcsv($file, [
                            $pred['stock_name'] ?? '',
                            $pred['current_stock'] ?? 0,
                            $pred['daily_sales_rate'] ?? 0,
                            $pred['projected_next_week_sales'] ?? 0,
                            $pred['projected_next_month_sales'] ?? 0,
                            $pred['days_until_stockout'] ?? 0,
                            $pred['recommendation'] ?? ''
                        ], ';');
                    }
                    fputcsv($file, [], ';');
                }
                
                // Pricing Opportunities
                if (isset($analysis['pricing']['opportunities']) && is_array($analysis['pricing']['opportunities'])) {
                    fputcsv($file, ['FİYAT OPTİMİZASYONU ÖNERİLERİ'], ';');
                    fputcsv($file, ['Ürün', 'Mevcut Fiyat', 'Öneri', 'Değişim %', 'Önerilen Fiyat', 'Sebep', 'Öncelik'], ';');
                    
                    foreach ($analysis['pricing']['opportunities'] as $opp) {
                        $suggestionText = $opp['suggestion'] === 'price_increase' ? 'Fiyat Artır' : 
                                        ($opp['suggestion'] === 'price_decrease' ? 'Fiyat Düşür' : 'Koru');
                        
                        fputcsv($file, [
                            $opp['stock_name'] ?? '',
                            $opp['current_price'] ?? 0,
                            $suggestionText,
                            $opp['suggested_change_percent'] ?? 0,
                            $opp['suggested_price'] ?? 0,
                            $opp['reason'] ?? '',
                            $opp['priority'] ?? ''
                        ], ';');
                    }
                    fputcsv($file, [], ';');
                }
                
                // Seasonality
                if (isset($analysis['seasonality']['has_data']) && $analysis['seasonality']['has_data']) {
                    fputcsv($file, ['SEZONALİTE ANALİZİ'], ';');
                    
                    if (isset($analysis['seasonality']['seasonal_performance'])) {
                        fputcsv($file, ['Mevsim', 'Ort. Satış', 'Ort. Gelir', 'Performans (%)'], ';');
                        
                        $seasonNames = [
                            'winter' => 'Kış',
                            'spring' => 'İlkbahar',
                            'summer' => 'Yaz',
                            'autumn' => 'Sonbahar'
                        ];
                        
                        foreach ($analysis['seasonality']['seasonal_performance'] as $season => $data) {
                            fputcsv($file, [
                                $seasonNames[$season] ?? $season,
                                $data['avg_sales'] ?? 0,
                                $data['avg_revenue'] ?? 0,
                                $data['performance_vs_avg'] ?? 0
                            ], ';');
                        }
                    }
                    fputcsv($file, [], ';');
                }
                
                // Insights
                if (isset($analysis['insights']) && is_array($analysis['insights'])) {
                    fputcsv($file, ['ÖNEMLİ İÇGÖRÜLER'], ';');
                    fputcsv($file, ['Tip', 'Başlık', 'Mesaj', 'Aksiyon'], ';');
                    
                    foreach ($analysis['insights'] as $insight) {
                        fputcsv($file, [
                            $insight['type'] ?? '',
                            $insight['title'] ?? '',
                            $insight['message'] ?? '',
                            $insight['action'] ?? ''
                        ], ';');
                    }
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Excel export hatası: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * JSON formatında export
     */
    public function exportToJSON($analysis)
    {
        try {
            $filename = 'ai-stok-analizi-' . date('Y-m-d') . '.json';
            
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ];
            
            return response()->json($analysis, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('JSON export hatası: ' . $e->getMessage());
            throw $e;
        }
    }
}

