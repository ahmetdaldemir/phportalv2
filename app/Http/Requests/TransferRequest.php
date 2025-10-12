<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'main_seller_id' => 'required|exists:sellers,id',
            'delivery_seller_id' => 'required|exists:sellers,id',
            'serial_list' => 'required|array|min:1',
            'serial_list.*' => 'required|string|max:255',
            'type' => 'required|in:phone,other',
            'description' => 'nullable|string|max:1000',
        ];

        // Update işlemi için ek kurallar
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['is_status'] = [
                'required',
                Rule::in([1, 2, 3, 4]) // Beklemede, Onaylandı, Tamamlandı, Reddedildi
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'main_seller_id.required' => 'Gönderici bayi seçimi zorunludur.',
            'main_seller_id.exists' => 'Seçilen gönderici bayi geçerli değil.',
            'delivery_seller_id.required' => 'Alıcı bayi seçimi zorunludur.',
            'delivery_seller_id.exists' => 'Seçilen alıcı bayi geçerli değil.',
            'serial_list.required' => 'En az bir seri numarası girilmelidir.',
            'serial_list.array' => 'Seri numaraları liste formatında olmalıdır.',
            'serial_list.min' => 'En az bir seri numarası girilmelidir.',
            'serial_list.*.required' => 'Seri numarası boş olamaz.',
            'serial_list.*.string' => 'Seri numarası metin formatında olmalıdır.',
            'serial_list.*.max' => 'Seri numarası 255 karakterden uzun olamaz.',
            'type.required' => 'Transfer tipi seçimi zorunludur.',
            'type.in' => 'Geçersiz transfer tipi.',
            'description.max' => 'Açıklama 1000 karakterden uzun olamaz.',
            'is_status.required' => 'Durum seçimi zorunludur.',
            'is_status.in' => 'Geçersiz durum değeri.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'main_seller_id' => 'gönderici bayi',
            'delivery_seller_id' => 'alıcı bayi',
            'serial_list' => 'seri numaraları',
            'type' => 'transfer tipi',
            'description' => 'açıklama',
            'is_status' => 'durum',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Seri numaralarını temizle
        if ($this->has('serial_list') && is_array($this->serial_list)) {
            $this->merge([
                'serial_list' => array_filter($this->serial_list, function($value) {
                    return !empty(trim($value));
                })
            ]);
        }
    }
}
