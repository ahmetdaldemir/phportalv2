@extends('layouts.admin')

@section('content')
    <x-table-page
        :title="'STOK KARTLARI'"
        :subtitle="'Stok kartları yönetimi ve takibi'"
        :icon="'bx-package'"
        :actions="[
            [
                'type' => 'link',
                'text' => 'YENİ STOK KARTI',
                'icon' => 'bx-plus',
                'class' => 'btn-primary',
                'url' => route('stockcard.create')
            ],
            [
                'type' => 'button',
                'text' => 'Excel',
                'icon' => 'bx-download',
                'class' => 'btn-success',
                'onclick' => 'exportStockCards()'
            ]
        ]"
    >
        {{-- FILTERS --}}
        <x-slot name="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bx bx-search"></i> Stok Adı
                    </label>
                    <input type="text" class="filter-input" placeholder="Stok adı ara...">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bx bx-tag"></i> Marka
                    </label>
                    <select class="filter-select">
                        <option value="">Tüm Markalar</option>
                        <option value="1">Samsung</option>
                        <option value="2">Apple</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bx bx-category"></i> Kategori
                    </label>
                    <select class="filter-select">
                        <option value="">Tüm Kategoriler</option>
                        <option value="1">Telefon</option>
                        <option value="2">Tablet</option>
                    </select>
                </div>
                
                <div class="filter-group auto">
                    <label class="filter-label">
                        <i class="bx bx-search"></i> Ara
                    </label>
                    <button type="button" class="filter-button primary">
                        <i class="bx bx-search me-1"></i>
                        Ara
                    </button>
                </div>
            </div>
        </x-slot>

        {{-- SUMMARY --}}
        <x-slot name="summary">
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-icon primary">
                        <i class="bx bx-package"></i>
                    </div>
                    <div class="card-value">150</div>
                    <div class="card-label">Toplam Stok</div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon success">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <div class="card-value">120</div>
                    <div class="card-label">Aktif Stok</div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon warning">
                        <i class="bx bx-x-circle"></i>
                    </div>
                    <div class="card-value">30</div>
                    <div class="card-label">Pasif Stok</div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon info">
                        <i class="bx bx-trending-up"></i>
                    </div>
                    <div class="card-value">25.000,00 ₺</div>
                    <div class="card-label">Toplam Değer</div>
                </div>
            </div>
        </x-slot>

        {{-- TABLE --}}
        <x-slot name="table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">
                                <input type="checkbox" class="form-check-input">
                            </th>
                            <th style="width: 20%;">
                                <i class="bx bx-package me-1"></i>
                                STOK ADI
                            </th>
                            <th style="width: 15%;">
                                <i class="bx bx-tag me-1"></i>
                                MARKA
                            </th>
                            <th style="width: 15%;">
                                <i class="bx bx-mobile me-1"></i>
                                MODEL
                            </th>
                            <th style="width: 10%;">
                                <i class="bx bx-barcode me-1"></i>
                                BARKOD
                            </th>
                            <th style="width: 10%;" class="text-center">
                                <i class="bx bx-package me-1"></i>
                                STOK
                            </th>
                            <th style="width: 12%;" class="text-end">
                                <i class="bx bx-money me-1"></i>
                                FİYAT
                            </th>
                            <th style="width: 8%;" class="text-center">
                                <i class="bx bx-cog me-1"></i>
                                İŞLEMLER
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input">
                            </td>
                            <td>
                                <div class="fw-bold">iPhone 14 Pro</div>
                                <small class="text-muted">#SK001</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">Apple</span>
                            </td>
                            <td>iPhone 14 Pro</td>
                            <td>
                                <code>1234567890</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">15</span>
                            </td>
                            <td class="text-end">
                                <span class="price-display fw-bold text-success">45.000,00 ₺</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1">
                                    <button class="btn btn-xs btn-outline-primary" title="Düzenle">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input">
                            </td>
                            <td>
                                <div class="fw-bold">Samsung Galaxy S23</div>
                                <small class="text-muted">#SK002</small>
                            </td>
                            <td>
                                <span class="badge bg-info">Samsung</span>
                            </td>
                            <td>Galaxy S23</td>
                            <td>
                                <code>0987654321</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">5</span>
                            </td>
                            <td class="text-end">
                                <span class="price-display fw-bold text-success">35.000,00 ₺</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1">
                                    <button class="btn btn-xs btn-outline-primary" title="Düzenle">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end">Toplam</td>
                            <td class="text-end">
                                <span class="price-display fw-bold">80.000,00 ₺</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-slot>
    </x-table-page>
@endsection
