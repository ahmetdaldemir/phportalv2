@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Markalar /</span> @if(isset($brands)) {{$brands->name}} @endif</h4>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bx bx-purchase-tag me-2"></i>Marka Bilgileri
                </h5>
            </div>
            <form action="{{route('brand.store')}}" method="post" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id" @if(isset($brands)) value="{{$brands->id}}" @endif />
            <div class="card-body p-4">
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">
                        <i class="bx bx-purchase-tag me-1 text-success"></i>Marka Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="name"  
                           @if(isset($brands)) value="{{$brands->name}}" @endif  
                           name="name" 
                           placeholder="Marka adını giriniz..."
                           required>
                    <div class="form-text">Marka adı benzersiz olmalıdır</div>
                </div>

                <hr class="my-5">
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bx bx-x me-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bx bx-save me-2"></i>Kaydet
                    </button>
                </div>
            </div>
            </form>
        </div>
        <hr class="my-5">
    </div>
    
    <style>
        /* Modern Form Stilleri */
        .bg-gradient-success {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
        }

        .form-control-lg {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 16px;
            padding: 12px 16px;
        }

        .form-control-lg:focus {
            border-color: #00b894;
            box-shadow: 0 0 0 0.2rem rgba(0, 184, 148, 0.25);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            border: none;
            padding: 20px 24px;
        }

        .card-body {
            padding: 24px;
        }

        .btn-lg {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 184, 148, 0.3);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 16px;
            }
            
            .form-control-lg {
                font-size: 14px;
                padding: 10px 12px;
            }
        }
    </style>
@endsection
