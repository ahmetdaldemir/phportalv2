<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTransferStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Transfer $transfer;
    protected int $newStatus;
    protected ?User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Transfer $transfer, int $newStatus, ?User $user = null)
    {
        $this->transfer = $transfer;
        $this->newStatus = $newStatus;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Transfer durumu güncelleniyor', [
                'transfer_id' => $this->transfer->id,
                'old_status' => $this->transfer->is_status,
                'new_status' => $this->newStatus,
                'user_id' => $this->user?->id,
            ]);

            // Transfer durumunu güncelle
            $this->transfer->update([
                'is_status' => $this->newStatus,
                'comfirm_id' => $this->user?->id,
                'comfirm_date' => now(),
            ]);

            // Duruma göre ek işlemler
            $this->processStatusSpecificActions();

            Log::info('Transfer durumu başarıyla güncellendi', [
                'transfer_id' => $this->transfer->id,
                'new_status' => $this->newStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Transfer durumu güncellenirken hata oluştu', [
                'transfer_id' => $this->transfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Duruma özel işlemler
     */
    private function processStatusSpecificActions(): void
    {
        switch ($this->newStatus) {
            case 2: // Onaylandı
                $this->processApproval();
                break;
            case 3: // Tamamlandı
                $this->processCompletion();
                break;
            case 4: // Reddedildi
                $this->processRejection();
                break;
        }
    }

    /**
     * Onaylama işlemleri
     */
    private function processApproval(): void
    {
        // Onaylama işlemleri burada yapılabilir
        // Örneğin: Email gönderme, bildirim gönderme, vs.
        Log::info('Transfer onaylandı', [
            'transfer_id' => $this->transfer->id,
            'approved_by' => $this->user?->id,
        ]);
    }

    /**
     * Tamamlama işlemleri
     */
    private function processCompletion(): void
    {
        // Tamamlama işlemleri burada yapılabilir
        // Örneğin: Stok güncelleme, rapor oluşturma, vs.
        Log::info('Transfer tamamlandı', [
            'transfer_id' => $this->transfer->id,
            'completed_by' => $this->user?->id,
        ]);
    }

    /**
     * Reddetme işlemleri
     */
    private function processRejection(): void
    {
        // Reddetme işlemleri burada yapılabilir
        // Örneğin: Email gönderme, bildirim gönderme, vs.
        Log::info('Transfer reddedildi', [
            'transfer_id' => $this->transfer->id,
            'rejected_by' => $this->user?->id,
        ]);
    }

    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Transfer durumu güncelleme job\'u başarısız oldu', [
            'transfer_id' => $this->transfer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
