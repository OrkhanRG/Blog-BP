@extends('layouts.admin')
@section('title')
    User {{isset($user) ? 'Güncəllə' : 'Əlavə Et'}}
@endsection

@section('css')
    <link href="{{asset('assets/admin/plugins/flatpickr/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <x-bootstrap.card>
        <x-slot:header>
            <h2 class="card-title">User {{isset($user) ? 'Güncəllə' : 'Əlavə Et'}}</h2>
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
                        action="{{isset($user) ? route('users.edit',['user' => $user->username]) : route('users.create')}}"
                        method="POST"
                        id="userForm"
                        enctype="multipart/form-data">
                        @csrf
                        <label for="username" class="form-label">İstifadəçi Adı</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('username'))
                               border-danger
                               @endif
                               "
                               placeholder="İstifadəçi Adı"
                               name="username"
                               id="username"
                               required
                               value="{{isset($user) ? $user->username : old('username')}}"
                        >
                        @if($errors->has('username'))
                            {{$errors->first('username')}}
                        @endif
                        <label for="password" class="form-label">İstifadəçi Şifrəsi</label>
                        <input type="password"
                               class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('password'))
                               border-danger
                               @endif
                               "
                               placeholder="İstifadəçi Şifrəsi"
                               name="password"
                               id="password"
                               required
                               value=""
                        >
                        @if($errors->has('password'))
                            {{$errors->first('password')}}
                        @endif
                        <label for="name" class="form-label">Ad Soyad</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered m-b-sm
                               @if($errors->has('name'))
                               border-danger
                               @endif
                               "
                               placeholder="Ad Soyad"
                               name="name"
                               id="name"
                               value="{{isset($user) ? $user->name : old('name')}}"
                        >
                        <label for="email" class="form-label">Email</label>
                        <input type="text"
                               class="form-control form-control-solid-bordered"
                               placeholder="Istifadəçi Email"
                               name="email"
                               value="{{isset($user) ? $user->email : old('email')}}"
                               id="email"
                        >

                        <label for="about" class="form-label">Haqqında</label>
                        <textarea class="m-b-sm" name="about" id="about">{{isset($user) ? $user->about : old('about')}}</textarea>

                        <div class="row mt-5">
                            <div class="col-8">
                                <label for="image" class="form-label m-b-sm">Istifadəçi Şəkli</label>
                                <select name="image" id="image" class="form-control">
                                    <option value="{{ null }}">Şəkil seçin</option>
                                    <option value="/assets/images/user-image/profile1.png" {{ isset($user) && $user->image == "/assets/images/user-image/profile1.png" ? "selected" : (old('image') == "/assets/images/user-image/profile1.png" ? "selected" : "") }}>Profile 1</option>
                                    <option value="/assets/images/user-image/profile2.png" {{ isset($user) && $user->image == "/assets/images/user-image/profile2.png" ? "selected" : (old('image') == "/assets/images/user-image/profile2.png" ? "selected" : "") }}>Profile 2</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <img src="{{isset($user) ? asset($user->image) : old('image')}}" id="profileImage" alt=""
                                     class="img-fluid" style="max-height: 80px">
                            </div>
                        </div>

                        <div class="form-check mt-5">
                            <input class="form-check-input" type="checkbox" value="1" name="is_admin"
                                   id="is_admin" {{isset($user) && $user->is_admin ? 'checked' : (old('is_admin') ? 'checked' : '')}}>
                            <label class="form-check-label" for="is_admin">
                                İstifadəçi admin'di ?
                            </label>
                        </div>

                        <div class="form-check mt-5">
                            <input class="form-check-input" type="checkbox" value="1" name="status"
                                   id="status" {{isset($user) && $user->status ? 'checked' : (old('status') ? 'checked' : '')}}>
                            <label class="form-check-label" for="status">
                                İstifadəçi aktiv olsunmu?
                            </label>
                        </div>

                        <hr>
                        <div class="col-6 mx-auto mt-2">
                            <button type="button"
                                    id="btnSave"
                                    class="btn btn-success btn-rounded w-100"
                            >
                                {{isset($user) ? 'Güncəllə' : 'Əlavə Et'}}
                            </button>
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
        let username = $('#username');
        let email = $('#email');
        let name = $('#name');


        $(document).ready(function () {
            $('#btnSave').click(function (){
                if (username.val().trim() === '' || username.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'İstifadəçi adı boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else if (email.val().trim() === '' || email.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Email boş keçilə bilməz',
                        icon: 'error',
                    });
                }
                else if (name.val().trim() === '' || name.val().trim() == null)
                {
                    Swal.fire({
                        title: 'Xəta!',
                        confirmButtonText: 'yaxşı',
                        text: 'Ad Soyad boş keçilə bilməz',
                        icon: 'error',
                    });
                }

                else
                {
                    $('#userForm').submit();
                }
            });

            $('#image').change(function (){
               $('#profileImage').attr("src", $(this).val());
            });
        });
    </script>


@endsection
