@extends('layouts.admin')

@section('content')
    @php
        $customer = optional($invoice->account);
        $personel = optional($invoice->staff)->name ?? 'Personel Yok';
        $detailCollection = collect($invoice->detail ?? [])->map(function ($detail) {
            $item = is_array($detail) ? (object)$detail : $detail;
            $item->quantity = (float)($item->quantity ?? 0);
            $item->cost_price = (float)($item->cost_price ?? 0);
            $item->base_cost_price = (float)($item->base_cost_price ?? 0);
            $item->sale_price = (float)($item->sale_price ?? 0);
            return $item;
        });
        $summary = [
            'itemCount' => $detailCollection->count(),
            'quantity' => $detailCollection->sum('quantity'),
            'totalCost' => $detailCollection->sum('cost_price'),
            'totalSupport' => $detailCollection->sum('base_cost_price'),
            'totalSale' => $detailCollection->sum('sale_price'),
        ];
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y" id="invoiceShow">
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-4">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold">Fatura Numarası</small>
                            <h3 class="mb-1">#{{ $invoice->number }}</h3>
                            <p class="mb-0 text-muted">Oluşturma Tarihi: <strong>{{ $invoice->create_date }}</strong></p>
                        </div>
                        <div class="d-flex gap-3 flex-wrap align-items-center">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold">Müşteri</small>
                                <p class="mb-0">{{ $customer->fullname ?? 'Genel Cari' }}</p>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold">Personel</small>
                                <p class="mb-0">{{ $personel }}</p>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold">Toplam</small>
                                <h4 class="mb-0 text-primary">{{ number_format($summary['totalSale'],2,',','.') }} ₺</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card info-card bg-primary-subtle border-0 h-100">
                            <div class="card-body">
                                <small class="text-muted text-uppercase">Kalem</small>
                                <h4 class="mb-0">{{ $summary['itemCount'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card info-card bg-success-subtle border-0 h-100">
                            <div class="card-body">
                                <small class="text-muted text-uppercase">Adet</small>
                                <h4 class="mb-0">{{ $summary['quantity'] }}</h4>
                            </div>
                        </div>
                    </div>
                    @role('admin')
                    <div class="col-md-3 col-sm-6">
                        <div class="card info-card bg-warning-subtle border-0 h-100">
                            <div class="card-body">
                                <small class="text-muted text-uppercase">Toplam Alış</small>
                                <h4 class="mb-0">{{ number_format($summary['totalCost'],2,',','.') }} ₺</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card info-card bg-info-subtle border-0 h-100">
                            <div class="card-body">
                                <small class="text-muted text-uppercase">Destekli Fiyat</small>
                                <h4 class="mb-0">{{ number_format($summary['totalSupport'],2,',','.') }} ₺</h4>
                            </div>
                        </div>
                    </div>
                    @endrole
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Fatura Kalemleri</h5>
                            <small class="text-muted">{{ $summary['itemCount'] }} ürün listeleniyor</small>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" target="_blank" href="#">
                            <i class="bx bx-printer me-1"></i> Yazdır
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th>Ürün Bilgisi</th>
                                    <th>Seri / Barkod</th>
                                    <th class="text-end">Adet</th>
                                    @role('admin')
                                    <th class="text-end">Alış</th>
                                    <th class="text-end">Destekli</th>
                                    @endrole
                                    <th class="text-end">Satış</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($detailCollection as $itemData)
                                    @php
                                        $stock = $itemData->stock ?? null;
                                        $brand = $stock->brand->name ?? ($itemData->brand_name ?? '—');
                                        $versions = ($stock && method_exists($stock, 'version'))
                                            ? collect(json_decode($stock->version(), true))->implode(', ')
                                            : ($itemData->model_name ?? '');
                                        $category = $itemData->category_name ?? '';
                                        $baseName = $stock->name ?? ($itemData->stock_name ?? 'Ürün');
                                        $productName = $itemData->product_summary
                                            ?? collect([$baseName, $brand, $versions, $category])
                                                ->filter(fn ($value) => filled($value))
                                                ->implode(' | ');
                                        $quantity = $itemData->quantity ?? 1;
                                        $costPrice = $itemData->cost_price ?? 0;
                                        $baseCost = $itemData->base_cost_price ?? 0;
                                        $salePrice = $itemData->sale_price ?? 0;
                                        $serialNumber = $itemData->serial_number ?? ($itemData->imei ?? '—');
                                        $barcodeNumber = $itemData->barcode ?? '';
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $productName }}</td>
                                        <td>
                                            <div>{{ $serialNumber }}</div>
                                            <small class="text-muted">{{ $barcodeNumber }}</small>
                                        </td>
                                        <td class="text-end">{{ $quantity }}</td>
                                        @role('admin')
                                        <td class="text-end">{{ number_format($costPrice,2,',','.') }} ₺</td>
                                        <td class="text-end">{{ number_format($baseCost,2,',','.') }} ₺</td>
                                        @endrole
                                        <td class="text-end fw-semibold">{{ number_format($salePrice,2,',','.') }} ₺</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Fatura kalemi bulunamadı.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                                <tfoot class="bg-light">
                                <tr>
                                    <td colspan="@role('admin')4 @else 2 @endrole" class="text-start fw-semibold">Toplam</td>
                                    <td class="text-end fw-semibold">{{ $summary['quantity'] }}</td>
                                    @role('admin')
                                    <td class="text-end fw-semibold">{{ number_format($summary['totalCost'],2,',','.') }} ₺</td>
                                    <td class="text-end fw-semibold">{{ number_format($summary['totalSupport'],2,',','.') }} ₺</td>
                                    @endrole
                                    <td class="text-end fw-semibold text-primary">{{ number_format($summary['totalSale'],2,',','.') }} ₺</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0">Müşteri Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Ad Soyad:</strong> {{ $customer->fullname ?? 'Genel Cari' }}</p>
                        <p class="mb-1"><strong>Telefon:</strong> {{ $customer->phone1 ?? '—' }}</p>
                        <p class="mb-0"><strong>Email:</strong> {{ $customer->email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0">Notlar</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ $invoice->description ?? 'Not bulunmuyor.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
