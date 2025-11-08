<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'code' => 'CUST001',
            'fullname' => 'Ahmet Yılmaz',
            'firstname' => 'Ahmet',
            'lastname' => 'Yılmaz',
            'tc' => '12345678901',
            'iban' => 'TR330006100519786457841326',
            'phone1' => '+90 532 123 4567',
            'phone2' => '+90 212 555 1234',
            'address' => 'Atatürk Mahallesi, Cumhuriyet Sokak No:15 Beşiktaş/İstanbul',
            'city' => 'İstanbul',
            'district' => 'Beşiktaş',
            'email' => 'ahmet.yilmaz@email.com',
            'note' => 'VIP müşteri',
            'type' => 'customer',
            'company_id' => 1,
            'seller_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'is_danger' => 0,
        ]);

        Customer::create([
            'code' => 'CUST002',
            'fullname' => 'Fatma Demir',
            'firstname' => 'Fatma',
            'lastname' => 'Demir',
            'tc' => '98765432109',
            'iban' => 'TR330006100519786457841327',
            'phone1' => '+90 533 987 6543',
            'phone2' => null,
            'address' => 'Çamlıca Mahallesi, Gül Sokak No:8 Üsküdar/İstanbul',
            'city' => 'İstanbul',
            'district' => 'Üsküdar',
            'email' => 'fatma.demir@email.com',
            'note' => 'Düzenli müşteri',
            'type' => 'customer',
            'company_id' => 1,
            'seller_id' => 2,
            'user_id' => 2,
            'is_status' => 1,
            'is_danger' => 0,
        ]);

        Customer::create([
            'code' => 'CUST003',
            'fullname' => 'Mehmet Kaya',
            'firstname' => 'Mehmet',
            'lastname' => 'Kaya',
            'tc' => '11223344556',
            'iban' => 'TR330006100519786457841328',
            'phone1' => '+90 534 555 7788',
            'phone2' => '+90 216 444 9900',
            'address' => 'Fenerbahçe Mahallesi, Spor Sokak No:23 Kadıköy/İstanbul',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'email' => 'mehmet.kaya@email.com',
            'note' => 'Toptan alım yapar',
            'type' => 'account',
            'company_id' => 1,
            'seller_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'is_danger' => 0,
        ]);

        Customer::create([
            'code' => 'CUST004',
            'fullname' => 'Ayşe Özkan',
            'firstname' => 'Ayşe',
            'lastname' => 'Özkan',
            'tc' => '77889900112',
            'iban' => 'TR330006100519786457841329',
            'phone1' => '+90 535 111 2233',
            'phone2' => null,
            'address' => 'Bostancı Mahallesi, Deniz Sokak No:5 Kadıköy/İstanbul',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'email' => 'ayse.ozkan@email.com',
            'note' => 'Online müşteri',
            'type' => 'sitecustomer',
            'company_id' => 1,
            'seller_id' => 2,
            'user_id' => 3,
            'is_status' => 1,
            'is_danger' => 0,
        ]);

        Customer::create([
            'code' => 'CUST005',
            'fullname' => 'Ali Çelik',
            'firstname' => 'Ali',
            'lastname' => 'Çelik',
            'tc' => '33445566778',
            'iban' => null,
            'phone1' => '+90 536 777 8899',
            'phone2' => null,
            'address' => 'Maltepe Mahallesi, Barış Sokak No:12 Maltepe/İstanbul',
            'city' => 'İstanbul',
            'district' => 'Maltepe',
            'email' => 'ali.celik@email.com',
            'note' => 'Teknik servis müşterisi',
            'type' => 'customer',
            'company_id' => 1,
            'seller_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'is_danger' => 1, // Problemli müşteri
        ]);
    }
}