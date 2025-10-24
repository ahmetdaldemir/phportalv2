@extends('layouts.admin')

@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea, #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            #printableArea table {
                border: solid #000 !important;
                border-width: 1px 0 0 1px !important;
            }
            th, td {
                border: solid #000 !important;
                border-width: 0 1px 1px 0 !important;
            }
        }

        .transfer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .info-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
            font-weight: 600;
        }

        .product-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .product-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 12px;
        }

        .product-serial {
            font-family: 'Courier New', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .product-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 0.875rem;
            color: #1e293b;
            font-weight: 500;
        }

        .action-buttons {
            position: sticky;
            top: 20px;
        }

        .btn-action {
            width: 100%;
            margin-bottom: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .transfer-summary {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="transfer-header p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bx bx-transfer me-3" style="font-size: 2rem;"></i>
                        <div>
                            <h2 class="mb-1 text-white">Transfer Detayı</h2>
                            <p class="mb-0 text-white-50">Transfer #{{$transfer->number}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="status-badge status-{{$transfer->is_status == 1 ? 'pending' : ($transfer->is_status == 2 ? 'approved' : ($transfer->is_status == 3 ? 'completed' : 'rejected'))}}">
                        {{\App\Models\Transfer::STATUS[$transfer->is_status] ?? 'Bilinmiyor'}}
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div id="printableArea" class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <!-- Transfer Summary -->
                <div class="transfer-summary">
                    <h5 class="mb-3">Transfer Özeti</h5>
                    <div class="summary-grid">
                        <div class="info-card">
                            <div class="info-label">Gönderici Bayi</div>
                            <div class="info-value">{{\App\Models\Seller::find($transfer->main_seller_id)->name ?? 'N/A'}}</div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Alıcı Bayi</div>
                            <div class="info-value">{{\App\Models\Seller::find($transfer->delivery_seller_id)->name ?? 'N/A'}}</div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Oluşturma Tarihi</div>
                            <div class="info-value">{{$transfer->created_at->format('d.m.Y H:i')}}</div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Personel</div>
                            <div class="info-value">{{$transfer->user->name ?? 'N/A'}}</div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Transfer Edilen Ürünler</h5>
                    </div>
                    <div class="card-body">
                        @if($transfer->serial_list && count($transfer->detail) > 0)
                            @foreach($transfer->detail as $index => $value)
                                <div class="product-card">
                                    <div class="product-header">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">#{{$index + 1}}</span>
                                            <span class="product-serial">{{$value['serial']}}</span>
                                        </div>
                                    </div>
                                    <div class="product-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Ürün Adı</div>
                                            <div class="detail-value">{{$value['name']}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Kategori</div>
                                            <div class="detail-value">{{$value['category']}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Marka</div>
                                            <div class="detail-value">{{$value['brand'] ?? 'Bulunamadı'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Model</div>
                                            <div class="detail-value">
                                                <?php
                                                $as = json_decode($value['version'], JSON_UNESCAPED_UNICODE);
                                                if (is_array($as) && count($as) > 0) {
                                                    echo $as[0];
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Renk</div>
                                            <div class="detail-value">{{$value['color']}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Adet</div>
                                            <div class="detail-value">{{$value['quantity'] ?? 1}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <h5>Ürün Bulunamadı</h5>
                                <p>Bu transfer için ürün bilgisi bulunmamaktadır.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes Section -->
                @if($transfer->description)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Notlar</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{$transfer->description}}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Sidebar -->
            <div class="col-xl-3 col-md-4 col-12">
                <div class="action-buttons">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">İşlemler</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-primary btn-action" onclick="printDiv()">
                                <i class="bx bx-printer me-2"></i>Yazdır
                            </button>
                            <a href="{{route('transfer.index')}}" class="btn btn-outline-secondary btn-action">
                                <i class="bx bx-arrow-back me-2"></i>Geri Dön
                            </a>
                            <a href="{{route('transfer.edit', ['id' => $transfer->id])}}" class="btn btn-outline-warning btn-action">
                                <i class="bx bx-edit me-2"></i>Düzenle
                            </a>
                        </div>
                    </div>

                    <!-- Transfer Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Transfer Bilgileri</h6>
                        </div>
                        <div class="card-body">
                            <div class="info-card">
                                <div class="info-label">Transfer Türü</div>
                                <div class="info-value">{{ucfirst($transfer->type)}}</div>
                            </div>
                            @if($transfer->comfirm_date)
                                <div class="info-card">
                                    <div class="info-label">Onay Tarihi</div>
                                    <div class="info-value">{{$transfer->comfirm_date}}</div>
                                </div>
                            @endif
                            @if($transfer->confirm_user)
                                <div class="info-card">
                                    <div class="info-label">Onaylayan</div>
                                    <div class="info-value">{{$transfer->confirm_user->name}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    function printDiv(){
        var divName = 'printableArea';
        var printContents = document.getElementById(divName).innerHTML;
        w = window.open();
        w.document.write(printContents);
        w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
        w.document.close(); // necessary for IE >= 10
        w.focus(); // necessary for IE >= 10
    }
 </script>
