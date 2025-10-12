@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="table-responsive text-nowrap">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table border-top">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>id</th>
                            <th>Title</th>
                            <th>Fiyat</th>
                            <th>Kategori</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <hr class="my-5">
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kategoriler</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="siteTechnicalServiceCategoryForm" action="{{route('site.technical_service_category.store')}}" method="post" enctype="multipart/form-data">
                   @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Ana Kategori:</label>
                            <select name="category" id="category" class="form-select">
                                <option value="phone">Telefon</option>
                                <option value="watch">Saat</option>
                                <option value="ipad'">Ipad</option>
                                <option value="mac">Mac</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Teknik Servis Başlığı:</label>
                            <input type="text" class="form-control"  name="title" id="title">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Teknik Servis Kisa Aciklama:</label>
                            <input type="text" class="form-control"  name="sort_description" id="sort_description">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Teknik Servis Fiyat:</label>
                            <input type="text" class="form-control"  id="price"  name="price">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Teknik Servis Açıklama:</label>
                            <textarea class="form-control"  name="description" rows="2" id="description"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.tiny.cloud/1/oj6zyoqfb6eqi7142vqs78p5k23x3vdo28svzv867z9cd3fu/tinymce/6/tinymce.min.js"  referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->

    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
    </script>
    <script>
        app.directive('loading', function () {
            return {
                restrict: 'E',
                replace: true,
                template: '<p><img src="img/loading.gif"/></p>', // Define a template where the image will be initially loaded while waiting for the ajax request to complete
                link: function (scope, element, attr) {
                    scope.$watch('loading', function (val) {
                        val = val ? $(element).show() : $(element).hide();  // Show or Hide the loading image
                    });
                }
            }
        }).directive('ngConfirmClick', [
            function () {
                return {
                    link: function (scope, element, attr) {
                        var msg = attr.ngConfirmClick || "Are you sure?";
                        var clickAction = attr.confirmedClick;
                        element.bind('click', function (event) {
                            if (window.confirm(msg)) {
                                scope.$eval(clickAction)
                            }
                        });
                    }
                };
            }]).directive('tinymce', function () {
            return {
                require: 'ngModel',
                link: function (scope, element, attrs, ngModel) {
                    tinymce.init({
                        selector: '#' + attrs.id,
                        setup: function (editor) {
                            editor.on('change', function () {
                                ngModel.$setViewValue(editor.getContent());
                            });
                        }
                    });
                }
            };
        }).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {




        });
    </script>
    <script>
        $(function () {
            'use strict';

            var dt_basic_table = $('.datatables-basic');
            // DataTable with buttons
            // --------------------------------------------------------------------

            if (dt_basic_table.length) {
                var dt_basic = dt_basic_table.DataTable({
                    ajax: '{{route('site.technical_service_category.get')}}',
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'id' },
                        { data: 'title' },
                        { data: 'price' },
                        { data: 'category' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'dt-control',
                            orderable: false,
                            responsivePriority: 2,
                            searchable: false,
                            targets: 0,
                            visible: false,
                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },
                        {
                            // For Checkboxes
                            targets: 1,
                            orderable: false,
                            responsivePriority: 3,
                            searchable: false,
                            checkboxes: true,
                            render: function () {
                                return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                            },
                            checkboxes: {
                                selectAllRender: '<input type="checkbox" class="form-check-input">'
                            }
                        },
                        {
                            targets: 2,
                            searchable: false,
                            visible: false,
                            className: 'control',
                            responsivePriority: 4,

                            render: function (data, type, full, meta) {
                                return '';
                            }
                        },

                        {
                            responsivePriority: 1,
                            targets: 3
                        },
                        {
                            // Label
                            targets: -2,
                            render: function (data, type, full, meta) {
                                var $status_number = full['category'];
                                var $status = {
                                    'phone': {title: 'phone', class: 'bg-label-primary'},
                                    'watch': {title: 'watch', class: ' bg-label-success'},
                                };
                                if (typeof $status[$status_number] === 'undefined') {
                                    return data;
                                }
                                return (
                                    '<span class="badge rounded-pill ' +
                                    $status[$status_number].class +
                                    '">' +
                                    $status[$status_number].title +
                                    '</span>'
                                );
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<div class="d-inline-block">' +
                                    '<a href="javascript:;" class="btn btn-sm text-primary btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></a>' +
                                    '<ul class="dropdown-menu dropdown-menu-end">' +
                                    '<li><a href="javascript:;" class="dropdown-item">Details</a></li>' +
                                    '<li><a href="javascript:;" class="dropdown-item">Archive</a></li>' +
                                    '<div class="dropdown-divider"></div>' +
                                    '<li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></li>' +
                                    '</ul>' +
                                    '</div>' +
                                    '<a href="javascript:;" data-id="'+full['id']+'"  class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bxs-edit"></i></a>'
                                );
                            }
                        }
                    ],
                    order: [[2, 'desc']],
                    dom:
                        '<"card-header"<"head-label text-center"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 5,
                    lengthMenu: [7, 10, 25, 50, 75, 100],
                    buttons: [
                        {
                            extend: 'collection',
                            className: 'btn btn-label-primary dropdown-toggle me-2',
                            text: '<i class="bx bx-show me-1"></i>Export',
                            buttons: [
                                {
                                    extend: 'print',
                                    text: '<i class="bx bx-printer me-1" ></i>Print',
                                    className: 'dropdown-item',
                                    exportOptions: {columns: [3, 4, 5, 6, 7]}
                                },
                                {
                                    extend: 'csv',
                                    text: '<i class="bx bx-file me-1" ></i>Csv',
                                    className: 'dropdown-item',
                                    exportOptions: {columns: [3, 4, 5, 6, 7]}
                                },
                                {
                                    extend: 'excel',
                                    text: 'Excel',
                                    className: 'dropdown-item',
                                    exportOptions: {columns: [3, 4, 5, 6, 7]}
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="bx bxs-file-pdf me-1"></i>Pdf',
                                    className: 'dropdown-item',
                                    exportOptions: {columns: [3, 4, 5, 6, 7]}
                                },
                                {
                                    extend: 'copy',
                                    text: '<i class="bx bx-copy me-1" ></i>Copy',
                                    className: 'dropdown-item',
                                    exportOptions: {columns: [3, 4, 5, 6, 7]}
                                }
                            ]
                        },
                        {
                            text: '<i class="bx bx-plus me-1"></i> <span class="d-none d-lg-inline-block">Add New Record</span>',
                            className: 'create-new btn btn-primary',
                            action: function (e, node, config) {
                                $('#exampleModal').modal('show')
                            }
                        }
                    ],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function (row) {
                                    var data = row.data();
                                    return 'Details of ' + data['title'];
                                }
                            }),
                            target: 'dt-control',
                            type: 'column',
                            renderer: function (api, rowIdx, columns) {
                                console.log(columns);
                                var data = $.map(columns, function (col, i) {
                                    return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                        ? '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +

                                        '</tr>'
                                        : '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });
                $('div.head-label').html('<h5 class="card-title mb-0">DataTable with Buttons</h5>');
            }
        });


    </script>

    <script>
        $(document).on("click",".item-edit",function () {
            var id = $(this).data("id");
            var modalForm = $("#exampleModal");
            modalForm.modal("show");
            var form = $(this);
            var actionUrl = '{{route('site.technical_service_category.edit')}}?id='+id+'';
            $.ajax({
                type: "GET",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {

                    modalForm.find("#id").val(data.id);
                    modalForm.find("select#category").val(data.category).trigger("change");
                    modalForm.find("#title").val(data.title);
                    modalForm.find("#price").val(data.price);
                 //   modalForm.find("textarea#description").val(data.description);
                    tinyMCE.activeEditor.setContent(data.description);

                    $("textarea#description option[value='Gateway 2']").prop('selected', true);

                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: "Hata Var",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                 }
            });
        })
    </script>
@endsection
