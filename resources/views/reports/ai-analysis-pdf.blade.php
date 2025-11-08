<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            background: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #f5f5f5;
            padding: 8px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
            border-left: 4px solid #4CAF50;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .summary-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #f5f5f5;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .insight-box {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid;
            background: #f9f9f9;
        }
        .insight-box.success { border-color: #4CAF50; }
        .insight-box.warning { border-color: #FF9800; }
        .insight-box.danger { border-color: #f44336; }
        .insight-box.info { border-color: #2196F3; }
        .insight-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-high { background: #f44336; color: white; }
        .badge-medium { background: #FF9800; color: white; }
        .badge-low { background: #4CAF50; color: white; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>{{ $companyName }} | Oluşturulma: {{ $generatedAt }}</p>
    </div>

    <!-- Özet Bilgiler -->
    @if(isset($analysis['summary']))
    <div class="section">
        <div class="section-title">Özet Bilgiler</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Toplam Ürün</div>
                <div class="value">{{ $analysis['summary']['total_products'] ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Ort. Devir Hızı</div>
                <div class="value">{{ number_format($analysis['summary']['avg_turnover_rate'] ?? 0, 1) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Toplam Gelir</div>
                <div class="value">{{ number_format($analysis['summary']['total_revenue'] ?? 0, 0) }} ₺</div>
            </div>
            <div class="summary-item">
                <div class="label">Sağlık Skoru</div>
                <div class="value">{{ $analysis['score'] ?? 0 }}/100</div>
            </div>
        </div>
    </div>
    @endif

    <!-- İçgörüler -->
    @if(isset($analysis['insights']) && count($analysis['insights']) > 0)
    <div class="section">
        <div class="section-title">Önemli İçgörüler</div>
        @foreach($analysis['insights'] as $insight)
        <div class="insight-box {{ $insight['type'] }}">
            <div class="insight-title">{{ $insight['title'] ?? '' }}</div>
            <div>{{ $insight['message'] ?? '' }}</div>
            @if(isset($insight['action']))
            <div style="margin-top: 5px; font-style: italic; color: #666;">
                Aksiyon: {{ $insight['action'] }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Tahminler -->
    @if(isset($analysis['predictions']) && count($analysis['predictions']) > 0)
    <div class="section">
        <div class="section-title">Satış Tahminleri (İlk 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Mevcut Stok</th>
                    <th>Günlük Satış</th>
                    <th>Gelecek Hafta</th>
                    <th>Tükenme (Gün)</th>
                    <th>Öneri</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($analysis['predictions'], 0, 10) as $pred)
                <tr>
                    <td>{{ $pred['stock_name'] ?? '' }}</td>
                    <td>{{ $pred['current_stock'] ?? 0 }}</td>
                    <td>{{ number_format($pred['daily_sales_rate'] ?? 0, 2) }}</td>
                    <td>{{ $pred['projected_next_week_sales'] ?? 0 }}</td>
                    <td>{{ $pred['days_until_stockout'] ?? 0 }}</td>
                    <td style="font-size: 8px;">{{ $pred['recommendation'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Fiyat Optimizasyonu -->
    @if(isset($analysis['pricing']['opportunities']) && count($analysis['pricing']['opportunities']) > 0)
    <div class="section">
        <div class="section-title">Fiyat Optimizasyonu Önerileri (İlk 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Mevcut Fiyat</th>
                    <th>Öneri</th>
                    <th>Değişim</th>
                    <th>Yeni Fiyat</th>
                    <th>Sebep</th>
                    <th>Öncelik</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($analysis['pricing']['opportunities'], 0, 10) as $opp)
                <tr>
                    <td>{{ $opp['stock_name'] ?? '' }}</td>
                    <td>{{ number_format($opp['current_price'] ?? 0, 2) }} ₺</td>
                    <td style="font-size: 8px;">
                        @if($opp['suggestion'] === 'price_increase')
                            Artır
                        @elseif($opp['suggestion'] === 'price_decrease')
                            Düşür
                        @else
                            Koru
                        @endif
                    </td>
                    <td>{{ $opp['suggested_change_percent'] ?? 0 }}%</td>
                    <td>{{ number_format($opp['suggested_price'] ?? 0, 2) }} ₺</td>
                    <td style="font-size: 8px;">{{ $opp['reason'] ?? '' }}</td>
                    <td>
                        <span class="badge badge-{{ $opp['priority'] ?? 'low' }}">
                            {{ strtoupper($opp['priority'] ?? 'low') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Sezonalite -->
    @if(isset($analysis['seasonality']['has_data']) && $analysis['seasonality']['has_data'])
    <div class="section">
        <div class="section-title">Sezonalite Analizi</div>
        
        @if(isset($analysis['seasonality']['seasonal_performance']))
        <table>
            <thead>
                <tr>
                    <th>Mevsim</th>
                    <th>Ortalama Satış</th>
                    <th>Ortalama Gelir</th>
                    <th>Performans (%)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $seasonNames = [
                        'winter' => 'Kış',
                        'spring' => 'İlkbahar',
                        'summer' => 'Yaz',
                        'autumn' => 'Sonbahar'
                    ];
                @endphp
                @foreach($analysis['seasonality']['seasonal_performance'] as $season => $data)
                <tr>
                    <td>{{ $seasonNames[$season] ?? $season }}</td>
                    <td>{{ number_format($data['avg_sales'] ?? 0, 0) }}</td>
                    <td>{{ number_format($data['avg_revenue'] ?? 0, 2) }} ₺</td>
                    <td>{{ number_format($data['performance_vs_avg'] ?? 0, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($analysis['seasonality']['insights']))
        <div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 3px solid #2196F3;">
            <strong>İçgörüler:</strong>
            <ul style="margin-left: 20px; margin-top: 5px;">
                @foreach($analysis['seasonality']['insights'] as $insight)
                <li>{{ $insight }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <!-- Trend Analizi -->
    @if(isset($analysis['trends']))
    <div class="section">
        <div class="section-title">Trend Analizi</div>
        <table>
            <thead>
                <tr>
                    <th>Metrik</th>
                    <th>Yön</th>
                    <th>Güç</th>
                    <th>Değişim (%)</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($analysis['trends']['velocity']))
                <tr>
                    <td>Satış Hızı</td>
                    <td>{{ $analysis['trends']['velocity']['direction'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['velocity']['strength'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['velocity']['change_percent'] ?? '-' }}%</td>
                </tr>
                @endif
                @if(isset($analysis['trends']['revenue']))
                <tr>
                    <td>Gelir Trendi</td>
                    <td>{{ $analysis['trends']['revenue']['direction'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['revenue']['strength'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['revenue']['change_percent'] ?? '-' }}%</td>
                </tr>
                @endif
                @if(isset($analysis['trends']['efficiency']))
                <tr>
                    <td>Verimlilik</td>
                    <td>{{ $analysis['trends']['efficiency']['direction'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['efficiency']['strength'] ?? '-' }}</td>
                    <td>{{ $analysis['trends']['efficiency']['change_percent'] ?? '-' }}%</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        AI Stok Analiz Raporu | {{ $companyName }} | Sayfa {PAGE_NUM}
    </div>
</body>
</html>

