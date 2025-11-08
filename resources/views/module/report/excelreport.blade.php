@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR</span></h4>

        <div class="card">
            <div class="card-body">
                <form action="{{route('report.print')}}" id="stockSearch"  method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8 fv-plugins-icon-container">
                            <label class="form-label" for="formValidationName">Başlangıç - Bitiş Tarihi</label>
                            <div class="input-group input-daterange">
                                <input type="text" class="form-control" name="date1" autocomplete="off">
                                <div class="input-group-addon">to</div>
                                <input type="text" class="form-control" name="date2" autocomplete="off">
                            </div>
                        </div>
                    <div class="col-4 mt-4" style="    margin-top: 2.7rem !important;margin-bottom: 0rem;">
                        <label></label>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>
            </div>
            <div class="card-header">

            </div>
            <div class="card-body">

            </div>
        </div>
        <hr class="my-5">
    </div>

@endsection
<style>
    table > :not(caption) > * > * {
        padding: 0.225rem 0.5rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        font-size: 12px;
    }
    .position-relative {
        width: 100%;
        position: relative !important;
    }
</style>
@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.css')}}"/>

@endsection
@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script src="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/js/daterangepicker-init.js')}}"></script>
    <script>
        $('.input-daterange input').each(function() {
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });
        });
    </script>
@endsection
