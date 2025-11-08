<?php

/**
 * Local Development Performance Optimization
 * Bu dosyayı lokalde kullanarak performansı artırabilirsin
 */

return [
    
    /*
    |--------------------------------------------------------------------------
    | Database Query Optimization
    |--------------------------------------------------------------------------
    | Remote database bağlantısı yavaş olduğu için cache'i agresif kullan
    */
    'cache' => [
        // Cache süreleri lokalde daha uzun
        'default_ttl' => 1800, // 30 dakika
        'long_ttl' => 3600,    // 1 saat
        'short_ttl' => 600,    // 10 dakika
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Batching
    |--------------------------------------------------------------------------
    | Birden fazla query'yi tek seferde çalıştır
    */
    'query_batching' => true,

    /*
    |--------------------------------------------------------------------------
    | Eager Loading Defaults
    |--------------------------------------------------------------------------
    | Tüm relationshipları default olarak eager load et
    */
    'default_eager_load' => [
        'brand:id,name',
        'category:id,name', 
        'color:id,name',
        'seller:id,name',
        'warehouse:id,name',
        'company:id,name'
    ],

    /*
    |--------------------------------------------------------------------------
    | Local Cache Store
    |--------------------------------------------------------------------------
    | Lokalde daha hızlı cache kullan
    */
    'local_cache_driver' => 'file', // array, file, redis

    /*
    |--------------------------------------------------------------------------
    | Pagination Optimization
    |--------------------------------------------------------------------------
    | Sayfa başına daha az item göster
    */
    'pagination' => [
        'default_per_page' => 20,
        'max_per_page' => 50,
    ]
];
