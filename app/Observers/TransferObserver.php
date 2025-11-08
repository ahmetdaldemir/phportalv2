<?php

namespace App\Observers;

use App\Models\Transfer;
use App\Jobs\ProcessTransferStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        Log::info('Yeni transfer oluşturuldu', [
            'transfer_id' => $transfer->id,
            'user_id' => $transfer->user_id,
            'main_seller_id' => $transfer->main_seller_id,
            'delivery_seller_id' => $transfer->delivery_seller_id,
        ]);

        // Cache'i temizle
        $this->clearRelatedCache();
    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        // Durum değişikliği kontrol et
        if ($transfer->wasChanged('is_status')) {
            Log::info('Transfer durumu değişti', [
                'transfer_id' => $transfer->id,
                'old_status' => $transfer->getOriginal('is_status'),
                'new_status' => $transfer->is_status,
            ]);

            // Durum değişikliği için job dispatch et
            ProcessTransferStatus::dispatch(
                $transfer, 
                $transfer->is_status, 
                auth()->user()
            );
        }

        // Cache'i temizle
        $this->clearRelatedCache();
    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        Log::info('Transfer silindi', [
            'transfer_id' => $transfer->id,
            'deleted_by' => auth()->id(),
        ]);

        // Cache'i temizle
        $this->clearRelatedCache();
    }

    /**
     * Handle the Transfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        Log::info('Transfer geri yüklendi', [
            'transfer_id' => $transfer->id,
            'restored_by' => auth()->id(),
        ]);

        // Cache'i temizle
        $this->clearRelatedCache();
    }

    /**
     * Handle the Transfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        Log::info('Transfer kalıcı olarak silindi', [
            'transfer_id' => $transfer->id,
            'deleted_by' => auth()->id(),
        ]);

        // Cache'i temizle
        $this->clearRelatedCache();
    }

    /**
     * İlgili cache'leri temizle
     */
    private function clearRelatedCache(): void
    {
        $companyId = auth()->user()->company_id ?? 0;
        
        // Transfer ile ilgili cache'leri temizle
        Cache::forget('transfers_' . $companyId);
        Cache::forget('transfers_count_' . $companyId);
        
        // Dashboard cache'lerini temizle
        Cache::forget('dashboard_stats_' . $companyId);
        Cache::forget('dashboard_reports_' . $companyId);
    }
}
