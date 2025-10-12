@extends('layouts.admin')

@section('content')
    <style>
        /* Professional Page Header */
        .page-header {
            background: #2c3e50;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }
        
        .page-header h4 {
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .page-header .text-muted {
            color: rgba(255,255,255,0.8) !important;
        }
        
        /* Professional Cards */
        .professional-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .professional-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Professional Table */
        .professional-table {
            margin: 0;
        }
        
        .professional-table thead th {
            background: #2c3e50;
            color: white !important;
            border: none;
            font-weight: 700;
            padding: 1rem 0.75rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        .professional-table thead th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #34495e;
        }
        
        .professional-table thead th i {
            font-size: 1rem;
            margin-right: 0.5rem;
        }
        
        .professional-tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .professional-tbody tr:hover {
            background: #ecf0f1;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .professional-tbody td {
            padding: 0.75rem;
            border: none;
            vertical-align: middle;
            font-size: 0.8rem;
            color: #495057;
        }
        
        .professional-tbody tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }
        
        /* Professional Buttons */
        .btn {
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            border: none;
            margin: 0.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
        }
        
        .btn-success {
            background: #27ae60;
            box-shadow: 0 3px 10px rgba(39, 174, 96, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            background: #2ecc71;
        }
        
        .btn-danger {
            background: #e74c3c;
            box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
            background: #c0392b;
        }
        
        .btn-warning {
            background: #f39c12;
            box-shadow: 0 3px 10px rgba(243, 156, 18, 0.3);
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
            background: #e67e22;
        }
        
        .btn-info {
            background: #3498db;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
            background: #2980b9;
        }
        
        .btn-dark {
            background: #34495e;
            box-shadow: 0 3px 10px rgba(52, 73, 94, 0.3);
        }
        
        .btn-dark:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 73, 94, 0.4);
            background: #2c3e50;
        }
        
        .btn-secondary {
            background: #95a5a6;
            box-shadow: 0 3px 10px rgba(149, 165, 166, 0.3);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
            background: #7f8c8d;
        }
        
        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .badge.bg-primary {
            background: #2c3e50 !important;
        }
        
        .badge.bg-success {
            background: #27ae60 !important;
        }
        
        .badge.bg-danger {
            background: #e74c3c !important;
        }
        
        .badge.bg-warning {
            background: #f39c12 !important;
        }
        
        .badge.bg-info {
            background: #3498db !important;
        }
        
        /* Pagination */
        .pagination {
            justify-content: center;
            margin: 2rem 0;
        }
        
        .page-link {
            border-radius: 8px;
            margin: 0 0.2rem;
            border: 2px solid #e9ecef;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .page-link:hover {
            background: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }
        
        .page-item.active .page-link {
            background: #2c3e50;
            border-color: #2c3e50;
        }
        
        /* Status Indicators */
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .status-deleted {
            background: #e74c3c;
        }
        
        .status-active {
            background: #27ae60;
        }
        
        .status-warning {
            background: #f39c12;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem;
            }
            
            .page-header h4 {
                font-size: 1.2rem;
            }
            
            .btn {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
                min-width: 30px;
                height: 30px;
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bx bx-trash display-4 text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0" style="font-size: 1.5rem; font-weight: 600;">
                        <i class="bx bx-trash me-2"></i>
                        SİLİNEN STOK KART HAREKETLERİ
                    </h4>
                    <p class="mb-0" style="font-size: 0.9rem;">Silinen stok kartları ve hareketleri yönetimi</p>
                </div>
            </div>
        </div>

        <div class="card professional-card">

            <div class="table-responsive text-nowrap">
                <table class="table professional-table">
                    <thead>
                    <tr>
                        <th><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-package me-1"></i>Stok Adı</th>
                        <th><i class="bx bx-barcode me-1"></i>SKU</th>
                        <th><i class="bx bx-barcode me-1"></i>Barkod</th>
                        <th><i class="bx bx-barcode me-1"></i>Serial</th>
                        <th><i class="bx bx-box me-1"></i>Adet</th>
                        <th><i class="bx bx-store me-1"></i>Şube</th>
                        <th><i class="bx bx-user me-1"></i>Personel</th>
                        <th><i class="bx bx-time me-1"></i>Silinme Tarihi</th>
                        <th><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 professional-tbody">
                    @foreach($stockCardMovement as $movement)
                        <tr>
                            <td>
                                <span class="status-indicator status-deleted"></span>
                                {{$movement->id}}
                            </td>
                            <td>
                                <strong>{{$movement->stock->name??"Stok Kaydı Silinmiş"}}</strong>
                                @if($movement->stock->name == null)
                                    <span class="badge bg-danger">Silinmiş</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($movement->stock->category))
                                    <span class="badge bg-info">{{$movement->categorySeperator($movement->testParent($movement->stock->category->id))}}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($movement->stock->barcode)
                                    <code>{{$movement->stock->barcode}}</code>
                                @else
                                    <span class="text-muted">Stok Kaydı Silinmiş</span>
                                @endif
                            </td>
                            <td>
                                @if($movement->serial_number)
                                    <code>{{$movement->serial_number}}</code>
                                @else
                                    <span class="text-muted">Stok Kaydı Silinmiş</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning">{{$movement->quantity}}</span>
                            </td>
                            <td>
                                @if($movement->seller->name)
                                    {{$movement->seller->name}}
                                @else
                                    <span class="text-muted">Şube Kaydı Silinmiş</span>
                                @endif
                            </td>
                            <td>
                                <i class="bx bx-user me-1"></i>
                                {{$movement->deletedUser()}}
                            </td>
                            <td>
                                <i class="bx bx-time me-1"></i>
                                <small class="text-muted">{{$movement->deleted_at}}</small>
                            </td>
                            <td>
                                <a href="#" class="btn btn-success btn-sm" title="Geri Al">
                                    <i class="bx bx-undo me-1"></i>
                                    Geri Al
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {!! $stockCardMovement->links() !!}
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
@endsection
