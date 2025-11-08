<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Önce seller oluşturalım
        $seller1 = Seller::create([
            'company_id' => 1,
            'name' => 'Ana Mağaza',
            'phone' => '+90 212 555 1001',
            'is_status' => 1,
            'can_see_stock' => 1,
            'can_see_cost_price' => 1,
            'can_see_base_cost_price' => 1,
            'can_see_sale_price' => 1,
        ]);

        $seller2 = Seller::create([
            'company_id' => 1,
            'name' => 'Şube Mağaza',
            'phone' => '+90 216 555 1002',
            'is_status' => 1,
            'can_see_stock' => 1,
            'can_see_cost_price' => 0,
            'can_see_base_cost_price' => 0,
            'can_see_sale_price' => 1,
        ]);

        // Admin kullanıcı
        $admin = User::create([
            'name' => 'Admin Kullanıcı',
            'email' => 'admin@phportal.com',
            'password' => Hash::make('123456'),
            'company_id' => 1,
            'seller_id' => $seller1->id,
            'is_status' => 1,
            'position' => '1', // Admin
            'personel' => 1,
            'salary' => 25000.00,
        ]);

        // Mağaza müdürü
        $manager = User::create([
            'name' => 'Mağaza Müdürü',
            'email' => 'manager@phportal.com',
            'password' => Hash::make('123456'),
            'company_id' => 1,
            'seller_id' => $seller1->id,
            'is_status' => 1,
            'position' => '2', // Manager
            'personel' => 1,
            'salary' => 18000.00,
        ]);

        // Satış danışmanı
        $sales = User::create([
            'name' => 'Satış Danışmanı',
            'email' => 'sales@phportal.com',
            'password' => Hash::make('123456'),
            'company_id' => 1,
            'seller_id' => $seller2->id,
            'is_status' => 1,
            'position' => '2',
            'personel' => 1,
            'salary' => 12000.00,
        ]);

        // Demo şirket için kullanıcı
        $seller3 = Seller::create([
            'company_id' => 2,
            'name' => 'Demo Mağaza',
            'phone' => '+90 216 555 2001',
            'is_status' => 1,
            'can_see_stock' => 1,
            'can_see_cost_price' => 1,
            'can_see_base_cost_price' => 1,
            'can_see_sale_price' => 1,
        ]);

        $demoUser = User::create([
            'name' => 'Demo Kullanıcı',
            'email' => 'demo@demo.com',
            'password' => Hash::make('123456'),
            'company_id' => 2,
            'seller_id' => $seller3->id,
            'is_status' => 1,
            'position' => '1',
            'personel' => 1,
            'salary' => 15000.00,
        ]);

        // Seller'ların user_id'sini güncelle
        $seller1->update(['user_id' => $admin->id]);
        $seller2->update(['user_id' => $manager->id]);
        $seller3->update(['user_id' => $demoUser->id]);
    }
}