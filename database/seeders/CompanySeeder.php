<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run()
    {
        Company::create([
            'name' => 'PhPortal Ana Şirket',
            'phone' => '+90 212 555 0001',
            'authorized' => 'Ahmet Yönetici',
            'web_sites' => 'https://phportal.com',
            'commercial_registration_number' => '123456789',
            'tax_number' => '1234567890',
            'tax_office' => 'Beşiktaş Vergi Dairesi',
            'mersis_number' => '0123456789012345',
            'company_name' => 'PhPortal Teknoloji A.Ş.',
            'email' => 'info@phportal.com',
            'address' => 'Levent Mahallesi, Teknoloji Sokak No:1 Beşiktaş/İstanbul',
            'postal_code' => '34394',
            'city' => 34, // İstanbul
            'district' => 1, // Beşiktaş
            'country' => 'Türkiye',
            'country_code' => 'TR',
            'is_status' => 1,
        ]);

        Company::create([
            'name' => 'Demo Şirket',
            'phone' => '+90 216 555 0002',
            'authorized' => 'Mehmet Demo',
            'web_sites' => 'https://demo.com',
            'commercial_registration_number' => '987654321',
            'tax_number' => '0987654321',
            'tax_office' => 'Kadıköy Vergi Dairesi',
            'mersis_number' => '9876543210987654',
            'company_name' => 'Demo Ticaret Ltd. Şti.',
            'email' => 'info@demo.com',
            'address' => 'Kadıköy Mahallesi, Demo Sokak No:5 Kadıköy/İstanbul',
            'postal_code' => '34710',
            'city' => 34, // İstanbul
            'district' => 2, // Kadıköy
            'country' => 'Türkiye',
            'country_code' => 'TR',
            'is_status' => 1,
        ]);
    }
}