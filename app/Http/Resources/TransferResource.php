<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'type_text' => $this->type == 'phone' ? 'TELEFON' : 'DİĞER',
            'main_seller' => [
                'id' => $this->main_seller_id,
                'name' => $this->seller($this->main_seller_id)?->name ?? 'Bilinmiyor',
            ],
            'delivery_seller' => [
                'id' => $this->delivery_seller_id,
                'name' => $this->seller($this->delivery_seller_id)?->name ?? 'Bilinmiyor',
            ],
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user($this->user_id)?->name ?? 'Bilinmiyor',
            ],
            'confirm_user' => $this->comfirm_id ? [
                'id' => $this->comfirm_id,
                'name' => $this->user($this->comfirm_id)?->name ?? 'Bilinmiyor',
            ] : null,
            'status' => [
                'code' => $this->is_status,
                'text' => $this->getStatusText(),
                'color' => $this->getStatusColor(),
            ],
            'serial_list' => $this->serial_list,
            'serial_count' => is_array($this->serial_list) ? count($this->serial_list) : 0,
            'description' => $this->description,
            'confirm_date' => $this->comfirm_date,
            'created_at' => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d-m-Y H:i:s'),
            'can_edit' => $this->is_status == 1,
            'can_approve' => $this->canApprove(),
            'can_reject' => $this->canReject(),
            'can_complete' => $this->canComplete(),
        ];
    }

    /**
     * Get status text
     */
    private function getStatusText(): string
    {
        $statuses = [
            1 => 'Beklemede',
            2 => 'Onaylandı',
            3 => 'Tamamlandı',
            4 => 'Reddedildi',
        ];

        return $statuses[$this->is_status] ?? 'Bilinmiyor';
    }

    /**
     * Get status color
     */
    private function getStatusColor(): string
    {
        $colors = [
            1 => 'primary',
            2 => 'success',
            3 => 'warning',
            4 => 'danger',
        ];

        return $colors[$this->is_status] ?? 'secondary';
    }

    /**
     * Check if can approve
     */
    private function canApprove(): bool
    {
        return $this->is_status == 1;
    }

    /**
     * Check if can reject
     */
    private function canReject(): bool
    {
        return in_array($this->is_status, [1, 2]);
    }

    /**
     * Check if can complete
     */
    private function canComplete(): bool
    {
        return $this->is_status == 2;
    }
}
