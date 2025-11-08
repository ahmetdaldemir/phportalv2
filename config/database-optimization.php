<?php

/**
 * Database Performance Optimization Configuration
 * Bu config dosyası database timeout ve performans ayarlarını optimize eder
 */

return [
    
    /*
    |--------------------------------------------------------------------------
    | Database Query Timeout Settings
    |--------------------------------------------------------------------------
    | Remote database için timeout değerlerini artır
    */
    'timeouts' => [
        'connection_timeout' => 60,  // 60 saniye connection timeout
        'read_timeout' => 120,       // 120 saniye read timeout
        'write_timeout' => 120,      // 120 saniye write timeout
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Limits
    |--------------------------------------------------------------------------
    | Büyük query'lerin önüne geçmek için limitler
    */
    'limits' => [
        'max_results_per_page' => 50,
        'max_whereIn_items' => 50,
        'max_eager_load_relations' => 5,
        'max_stockcard_list' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    | Database performans ayarları
    */
    'performance' => [
        'enable_query_cache' => true,
        'cache_ttl' => 300, // 5 dakika
        'enable_connection_pooling' => true,
        'persistent_connection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings  
    |--------------------------------------------------------------------------
    | Production'da query logging'i kapat
    */
    'debug' => [
        'log_slow_queries' => true,
        'slow_query_threshold' => 2000, // 2 saniye
        'enable_query_log' => env('DB_QUERY_LOG', false),
    ]
];
