<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>QR Kod Yazdır</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <style>
        body {
            font-family: 'Public Sans', Arial, sans-serif;
            background: #f5f5f5;
            color: #1f2a37;
            margin: 0;
            padding: 24px;
        }

        .qr-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 18px;
        }

        .qr-card {
            display: flex;
            gap: 14px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            padding: 16px;
            position: relative;
            overflow: hidden;
        }

        .qr-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(99, 102, 241, 0.08);
            pointer-events: none;
        }

        .qr-card__logo {
            width: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid rgba(15, 23, 42, 0.08);
            padding-right: 12px;
        }

        .qr-card__logo img {
            max-width: 105px;
            object-fit: contain;
            rotate: -90deg;
        }

        .qr-card__body {
            flex: 1;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .qr-card__qr canvas {
            width: 100px !important;
            height: 100px !important;
        }

        .qr-card__details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .qr-card__title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .qr-card__meta {
            font-size: 13px;
            color: #6b7280;
        }

        .qr-card__price {
            margin-top: 4px;
            font-weight: 600;
            color: #4f46e5;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .qr-card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="qr-wrapper">
        @foreach ($items as $item)
            @php
                $qrPayload = implode("\n", array_filter([
                    'Seri: ' . ($item['serial_number'] ?? ''),
                    'Ürün: ' . ($item['stock_name'] ?? ''),
                    'Kategori: ' . ($item['category_name'] ?? ''),
                    'Marka: ' . ($item['brand_name'] ?? ''),
                    !empty($item['model_name']) ? 'Model: ' . $item['model_name'] : null,
                    'Fiyat: ' . number_format($item['sale_price'] ?? 0, 2) . ' ₺',
                ]));

                $qrUrl = url('/') . '?sale_serial=' . urlencode($item['serial_number'] ?? '');
                if (!empty($item['stock_id'])) {
                    $qrUrl .= '&sale_stock=' . urlencode($item['stock_id']);
                }
            @endphp
            <div class="qr-card">
                <div class="qr-card__logo">
                    <img src="https://phportal.net/img/147836.png" alt="Logo">
                </div>
                <div class="qr-card__body">
                    <div class="qr-card__qr">
                        <canvas class="qr-canvas" data-qr="{{ e($qrUrl) }}"></canvas>
                    </div>
                    <div class="qr-card__details">
                        <div class="qr-card__title">{{ $item['stock_name'] ?? 'Ürün Adı' }}</div>
                        <div class="qr-card__meta">
                            {{ $item['brand_name'] ?? '' }}
                            @if(!empty($item['model_name']))
                                · {{ $item['model_name'] }}
                            @endif
                        </div>
                        <div class="qr-card__meta">Kategori: {{ $item['category_name'] ?? 'Belirtilmedi' }}</div>
                        <div class="qr-card__price">{{ number_format($item['sale_price'] ?? 0, 2) }} ₺</div>
                        <div class="qr-card__meta text-muted" style="font-size: 12px;">Seri: {{ $item['serial_number'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        document.querySelectorAll('.qr-canvas').forEach(canvas => {
            const value = canvas.dataset.qr || '';
            new QRious({
                element: canvas,
                value,
                size: 50,
                level: 'M'
            });
        });
    </script>
</body>
</html>
