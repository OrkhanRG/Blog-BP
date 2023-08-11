@extends('layouts.admin')
@section('title')
    Kateqoriya {{isset($category) ? 'Güncəllə' : 'Əlavə Et'}}
@endsection

@section('css')
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2 class="card-title">Məqalə {{isset($category) ? 'Güncəllə' : 'Əlavə Et'}}</h2>
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
                        action="{{isset($category) ? route('categories.edit',['id' => $category->id]) : route('category.create')}}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="categoryForm">
                        @csrf
                        <label for="color">Kateqoriya Rəngi</label>
                        <input type="color" name="color" id="color" class="form-control m-b-sm" value="{{isset($category) ? $category->color : ''}}">
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm"
                               aria-describedby="solidBoderedInputExample"
                               placeholder="Kateqoriya Adı"
                               name="name"
                               id="name"
                               required
                               value="{{isset($category) ? $category->name : ''}}"
                        >
                        {{--@if($errors->has('name')) {{$errors->first('name')}} @endif--}}
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm"
                               aria-describedby="solidBoderedInputExample"
                               placeholder="Kateqoriya Slug"
                               name="slug"
                               id="slug"
                               value="{{isset($category) ? $category->slug : ''}}"
                        >
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm"
                            name="description"
                            id="description"
                            cols="30"
                            rows="5"
                            placeholder="Kateqoriya Açığlaması"
                            style="resize: none;">{{isset($category) ? $category->description : ''}}</textarea>
                        <input type="number"
                               class="form-control form-control-solid-bordered m-b-sm"
                               aria-describedby="solidBoderedInputExample"
                               placeholder="Kateqoriya Sıra"
                               name="order"
                               id="order"
                               value="{{isset($category) ? $category->order : ''}}"
                        >
                        <select
                            class="form-select form-control form-control-solid-bordered m-b-sm"
                            aria-label="Üst Kateqoriya"
                            name="parent_id"
                            id="parent_id"
                        >
                            <option value="{{null}}">Üst Kateqoriya</option>
                            @foreach($categories as $item)
                                <option
                                    value="{{$item->id}}" {{isset($category) && $category->id == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm"
                            name="seo_keywords"
                            id="seo_keywords"
                            cols="30"
                            rows="5"
                            placeholder="Seo Keywords"
                            style="resize: none;">{{isset($category) ? $category->seo_keywords : ''}}</textarea>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm"
                            name="seo_description"
                            id="seo_description"
                            cols="30"
                            rows="5"
                            placeholder="Seo Description"
                            style="resize: none;">{{isset($category) ? $category->seo_description : ''}}</textarea>

                        <label for="image" class="form-label ">Kateqoriya Şəkli</label>
                        <input type="file" name="image" id="image" class="form-control"
                               accept="image/png, image/jpeg, image/jpg ">
                        <div class="form-text m-b-sm">Kateqoriya Şəkli Maksimum 2mb olmalıdır</div>

                        @if(isset($category) && $category->image)
                            <img src="{{asset($category->image)}}" alt="" class="img-fluid" style="max-height: 200px">
                        @endif

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="status"
                                   id="statsu" {{isset($category) && $category->status ? 'checked' : ''}}>
                            <label class="form-check-label" for="statsu">
                                Kateqoriya saytda görünsün?
                            </label>
                        </div>

                        <div class="form-check ">
                            <input class="form-check-input" type="checkbox" value="1" name="feature_status"
                                   id="feature_status" {{isset($category) && $category->feature_status ? 'checked' : ''}}>
                            <label class="form-check-label" for="feature_status">
                                Kateqoriya ön sırya çıxarılsınmı?
                            </label>
                        </div>
                        <hr>
                        <div class="col-6 mx-auto mt-2">
                            <button type="button"
                                    id="btnSave"
                                    class="btn btn-success btn-rounded w-100">{{isset($category) ? 'Güncəllə' : 'Əlavə Et'}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-slot:body>
    </x-bootstrap.card>
@endsection

@section('js')
    <script>
        let name = $('#name');

        $(document).ready(function () {
            $('#btnSave').click(function (){
                if (name.val().trim() === '' || name.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Kateqoriya adı boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else
                {
                    $('#categoryForm').submit();
                }
            });
        });
    </script>
@endsection
