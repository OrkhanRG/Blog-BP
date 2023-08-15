@extends('layouts.admin')
@section('title')
    Parametrlər
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2 class="card-title">Parametrlər</h2>
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
                        action="{{route('settings')}}"
                        method="POST"
                        id="settingsForm"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="telegram_links" class="form-label ">Telegram Linki</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('telegram_links'))
                                    border-danger
                               @endif
                               "
                               placeholder="Telegram Linki"
                               name="telegram_links"
                               id="telegram_links"
                               value="{{isset($settings) ? $settings->telegram_links : ''}}"
                        >
                        <label for="header_text" class="form-label ">Header mətni</label>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('header_text'))
                                    border-danger
                               @endif
                               "
                            name="header_text"
                            id="header_text"
                            cols="30"
                            rows="5"
                            placeholder="Header mətni">{!! isset($settings) ? $settings->header_text : '' !!}</textarea>

                        @if($errors->has('header_text'))
                            {{$errors->first('header_text')}}
                        @endif

                        <label for="footer_text" class="form-label mt-3 ">Footer mətni</label>
                        <textarea
                            class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('header_text'))
                                    border-danger
                               @endif
                               "
                            name="footer_text"
                            id="footer_text"
                            cols="30"
                            rows="5"
                            placeholder="Footer mətni">{!! isset($settings) ? $settings->footer_text : '' !!}</textarea>

                        @if($errors->has('footer_text'))
                            {{$errors->first('footer_text')}}
                        @endif

                        <label for="logo" class="form-label ">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control"
                               accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text m-b-sm">Logo Üçün Şəkil Maksimum 2mb olmalıdır</div>

                        @if(isset($settings) && $settings->logo)
                            <img src="{{asset($settings->logo)}}" alt="" class="img-fluid" style="max-height: 200px">
                        @endif
                        <hr>

                        <label for="category_default_image" class="form-label ">Kateqoriya Default Şəkil</label>
                        <input type="file" name="category_default_image" id="category_default_image"
                               class="form-control"
                               accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text m-b-sm">Kateqoriya Default Şəkli Maksimum 2mb olmalıdır</div>

                        @if(isset($settings) && $settings->category_default_image)
                            <img src="{{asset($settings->category_default_image)}}" alt="" class="img-fluid"
                                 style="max-height: 200px">
                        @endif
                        <hr>

                        <label for="article_default_image" class="form-label ">Məqalə Default Şəkil</label>
                        <input type="file" name="article_default_image" id="article_default_image" class="form-control"
                               accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text m-b-sm">Məqalə Default Şəkli Maksimum 2mb olmalıdır</div>

                        @if(isset($settings) && $settings->article_default_image)
                            <img src="{{asset($settings->article_default_image)}}" alt="" class="img-fluid"
                                 style="max-height: 200px">
                        @endif
                        <hr>

                        <label for="reset_password_image" class="form-label ">Şifrə Sıfırlama Mail Default Şəkli</label>
                        <input type="file" name="reset_password_image" id="reset_password_image" class="form-control"
                               accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text m-b-sm">Şifrə Sıfırlama Mail Default Şəkli Maksimum 2mb olmalıdır</div>

                        @if(isset($settings) && $settings->reset_password_image)
                            <img src="{{asset($settings->reset_password_image)}}" alt="" class="img-fluid"
                                 style="max-height: 200px">
                        @endif
                        <hr>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   name="feature_categories_is_active"
                                   id="feature_categories_is_active" {{isset($settings) && $settings->feature_categories_is_active ? 'checked' : ''}}>
                            <label class="form-check-label" for="feature_categories_is_active">
                                Önə Çıxarılan Kateqoriyalar Anasəhifədə Görünsün?
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="video_is_active"
                                   id="video_is_active" {{isset($settings) && $settings->video_is_active ? 'checked' : ''}}>
                            <label class="form-check-label" for="video_is_active">
                                Videolar Sidebarda Görünsün?
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="author_is_active"
                                   id="author_is_active" {{isset($settings) && $settings->author_is_active ? 'checked' : ''}}>
                            <label class="form-check-label" for="author_is_active">
                                Yazıçılar Sidebarda Görünsün?
                            </label>
                        </div>

                        <hr>
                        <div class="col-6 mx-auto mt-2">
                            <button type="button"
                                    id="btnSave"
                                    class="btn btn-success btn-rounded w-100">Saxla
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </x-slot:body>
    </x-bootstrap.card>
@endsection

@section('js')
    <script src="{{asset('assets/admin/plugins/summernote/summernote-lite.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/text-editor.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#btnSave').click(function () {
                // console.log($("#logo"));
                let logoCheckStaus = imageCheck($("#logo"));
                let category_default_imageStatus = imageCheck($("#category_default_image"));
                let article_default_imageStatus = imageCheck($("#article_default_image"));
                let reset_password_imageStatus = imageCheck($("#reset_password_image"));

                if (!logoCheckStaus || !category_default_imageStatus || !article_default_imageStatus || !reset_password_imageStatus)
                {
                    return false;
                }
                else
                {
                    $('#settingsForm').submit();
                }
            });
        });
    </script>
@endsection
