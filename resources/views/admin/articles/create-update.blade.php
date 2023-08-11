@extends('layouts.admin')
@section('title')
    Məqalə {{isset($article) ? 'Güncəllə' : 'Əlavə Et'}}
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2 class="card-title">Məqalə {{isset($article) ? 'Güncəllə' : 'Əlavə Et'}}</h2>
        </x-slot:header>
        <x-slot:body>
            <div class="example-container">
                <div class="example-content">
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <div class="alert alert-danger">{{$error}}</div>
                        @endforeach
                    @endif
                    <form
                        action="{{isset($article) ? route('article.edit',['id' => $article->id]) : route('article.create')}}"
                        method="POST"
                        id="articleForm"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="title" class="form-label">Məqalə Başlığı</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm"
                               placeholder="Məqalə Başlığı"
                               name="title"
                               id="title"
                               required
                               value="{{isset($article) ? $article->title : ''}}"
                        >
                        {{--@if($errors->has('name')) {{$errors->first('name')}} @endif--}}
                        <label for="slug" class="form-label">Məqalə Slug</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm"
                               placeholder="Məqalə Slug"
                               name="slug"
                               id="slug"
                               value="{{isset($article) ? $article->slug : ''}}"
                        >
                        <label for="tags" class="form-label">Teqlər</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered"
                               placeholder="Teqlər "
                               name="tags"
                               value="{{isset($article) ? $article->tags : ''}}"
                               id="tags"
                        >
                        <div class="form-text m-b-sm">Hər bir teqi vergüllə ayıraraq yazın</div>

                        <label for="category_id" class="form-label">Kateqoriya</label>
                        <select
                            class="form-select form-control form-control-solid-bordered m-b-sm"
                            name="category_id"
                            id="category_id"
                        >
                            <option value="{{null}}">Kateqoriya Seçimi</option>
                            @foreach($categories as $item)
                                <option
                                    value="{{$item->id}}" {{isset($article) && $article->category_id == $item->id ? 'selected' : ''}}>
                                    {{$item->name}}
                                </option>
                            @endforeach
                        </select>

                        <label for="summernote" class="form-label">Məzmun</label>
                        <textarea class="m-b-sm" name="body" id="summernote">{{isset($article) ? $article->body : ''}}</textarea>

                        <label for="seo_keywords" class="form-label m-t-sm">Seo Keywords</label>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm"
                            name="seo_keywords"
                            id="seo_keywords"
                            cols="30"
                            rows="5"
                            placeholder="Seo Keywords"
                            style="resize: none;">{{isset($article) ? $article->seo_keywords : ''}}</textarea>
                        <label for="seo_description" class="form-label ">Seo Description</label>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm"
                            name="seo_description"
                            id="seo_description"
                            cols="30"
                            rows="5"
                            placeholder="Seo Description"
                            style="resize: none;">{{isset($article) ? $article->seo_description : ''}}</textarea>

                        <label for="publish_date" class="form-label ">Yayınlanma Tarixi</label>
                        <input class="form-control form-control-solid-bordered flatpickr2 m-b-sm"
                               type="text"
                               id="publish_date"
                               name="publish_date"
                               value="{{isset($article) ? $article->publish_date : ''}}"
                               placeholder="Nə vaxt yayınlansın?">

                        <label for="image" class="form-label ">Məqalə Şəkli</label>
                        <input type="file" name="image" id="image" class="form-control"
                               accept="image/png, image/jpeg, image/jpg ">
                        <div class="form-text m-b-sm">Məqalə Şəkli Maksimum 2mb olmalıdır</div>

                        @if(isset($article) && $article->image)
                            <img src="{{asset($article->image)}}" alt="" class="img-fluid" style="max-height: 200px">
                        @endif

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="status"
                                   id="statsu" {{isset($article) && $article->status ? 'checked' : ''}}>
                            <label class="form-check-label" for="statsu">
                                Məqalə saytda aktiv olaraq görünsün?
                            </label>
                        </div>

                        <hr>
                        <div class="col-6 mx-auto mt-2">
                            <button type="button"
                                    id="btnSave"
                                    class="btn btn-success btn-rounded w-100">{{isset($article) ? 'Güncəllə' : 'Əlavə Et'}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-slot:body>
    </x-bootstrap.card>
@endsection

@section('js')
    <script src="{{asset('assets/admin/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/datepickers.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/summernote/summernote-lite.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/text-editor.js')}}"></script>

    <script>
        $("#publish_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    </script>

    <script>
        let title = $('#title');
        let tags = $('#tags');
        let category_id = $('#category_id');

        $(document).ready(function () {
            $('#btnSave').click(function (){
                if (title.val().trim() === '' || title.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Məqalə adı boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else if (tags.val().trim().length < 3)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Teqlər boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else if (category_id.val().trim() === '' || category_id.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Kateqoriya boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else
                {
                    $('#articleForm').submit();
                }
            });
        });
    </script>


@endsection
