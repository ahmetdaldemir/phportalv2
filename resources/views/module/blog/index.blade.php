@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bloglar /</span> Blog listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('blog.create')}}" class="btn btn-primary float-end">Yeni Blog Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Marka Adı</th>
                        <th>Kayıt Tarihi</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @if($blogs)
                    @foreach($blogs as $blog)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$blog->title}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$blog->created_at}}</span></td>

                            <td>
                                <a href="{{route('blog.delete',['id' => $blog->id])}}"
                                   onclick="return confirm('Silmek istediğinizden eminmisiniz?')"  class="btn btn-icon btn-danger">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('blog.edit',['id' => $blog->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>

                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
