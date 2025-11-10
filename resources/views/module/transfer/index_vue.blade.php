@extends('layouts.admin')

@section('content')
<div id="app" class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Sevkler /</span> Sevk Yönetimi
    </h4>

    <div class="card">
        @if($errors->any())
            <div class="card-header">
                <div class="alert alert-warning">
                    <h4>{{$errors->first()}}</h4>
                </div>
            </div>
        @endif

        @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sevk Listesi</h5>
            <a href="{{route('transfer.create')}}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Yeni Sevk Ekle
            </a>
        </div>
        @endrole

        <div class="card-body">
            <!-- Vue.js DataTable Component -->
            <transfer-data-table 
                :api-url="'/api/transfers'"
                :sellers="{{ json_encode($sellers) }}"
            ></transfer-data-table>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<!-- Vue.js App Script -->
<script src="{{ mix('js/app.js') }}"></script>

<!-- Toast Notifications -->
<script>
// Global toast function for Vue components
window.$toast = {
    success: function(message) {
        // You can integrate with your preferred toast library
        console.log('Success:', message);
        // Example: Swal.fire('Başarılı!', message, 'success');
    },
    error: function(message) {
        console.log('Error:', message);
        // Example: Swal.fire('Hata!', message, 'error');
    }
};
</script>
@endsection

@section('custom-css')
<style>
/* Custom styles for Transfer DataTable */
.transfer-datatable .table {
    margin-bottom: 0;
}

.transfer-datatable .form-control {
    border-radius: 6px;
}

.transfer-datatable .btn {
    border-radius: 6px;
}

.transfer-datatable .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.transfer-datatable .pagination {
    margin-bottom: 0;
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .transfer-datatable .table-responsive {
        font-size: 0.875rem;
    }
    
    .transfer-datatable .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Card improvements */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

/* Avatar styles */
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 12px;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 10px;
}

/* Status badges */
.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

/* Table improvements */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    background-color: #f8f9fa;
}

/* Modal improvements */
.modal-content {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Form improvements */
.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Button improvements */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Pagination improvements */
.pagination .page-link {
    border-radius: 0.375rem;
    margin: 0 0.125rem;
    border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Stats cards */
.card.bg-primary {
    background: linear-gradient(45deg, #0d6efd, #0b5ed7) !important;
}

.card.bg-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800) !important;
}

.card.bg-success {
    background: linear-gradient(45deg, #198754, #157347) !important;
}

.card.bg-danger {
    background: linear-gradient(45deg, #dc3545, #bb2d3b) !important;
}

/* Animation for loading */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.transfer-datatable {
    animation: fadeIn 0.3s ease-in-out;
}

/* Empty state styling */
.text-muted {
    color: #6c757d !important;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem;
    }
}
</style>
@endsection
