@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Blog /</span> @if(isset($blog))
                {{$blog->name}}
            @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Blog Bilgileri</h5>
            <form action="{{route('blog.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" @if(isset($blog)) value="{{$blog->id}}" @endif />
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Adı</label>
                                <input type="text" class="form-control" id="name"
                                       @if(isset($blog)) value="{{$blog->name}}" @endif  name="title"
                                       aria-describedby="name">
                            </div>

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Açıklama</label>
                                <textarea class="form-control" name="description" rows="2" id="description"> @if(isset($blog))
                                        {{ $blog->description}}
                                    @endif</textarea>

                            </div>
                        </div>
                        <div class="col-md-3">

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Resim</label>
                                <input type="file" class="form-control" id="name"
                                       @if(isset($blog)) value="{{$blog->name}}" @endif  name="image"
                                       aria-describedby="name">
                            </div>

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Meta Title</label>
                                <input type="text" class="form-control" id="name"
                                       @if(isset($blog)) value="{{$blog->name}}" @endif  name="meta_title"
                                       aria-describedby="name">
                            </div>

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Meta Açıklama</label>
                                <input type="text" class="form-control" id="name"
                                       @if(isset($blog)) value="{{$blog->name}}" @endif  name="meta_description"
                                       aria-describedby="name">
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Blog Meta Kelimeler</label>
                                <input type="text" class="form-control" id="labels"
                                       @if(isset($blog)) value="{{$blog->labels}}" @endif  name="labels"
                                       aria-describedby="labels">
                            </div>
                        </div>
                    </div>


                    <hr class="my-5">
                    <div>
                        <button type="submit" class="btn btn-danger btn-buy-now">Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.tiny.cloud/1/oj6zyoqfb6eqi7142vqs78p5k23x3vdo28svzv867z9cd3fu/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->

    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
    </script>

@endsection


