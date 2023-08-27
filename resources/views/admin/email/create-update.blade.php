@extends('layouts.admin')
@section('css')
    <link href="{{asset('assets/admin/plugins/summernote/summernote-lite.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    @php
    if (isset($theme))
        {
        $name = $theme->name;
        $theme_type = $theme->getRawOriginal('theme_type');
        $process = $theme->getRawOriginal('process');
        $status = $theme->status;
        $body = json_decode($theme->body);
        $logo = '';
        $logo_alt = '';
        $logo_title = '';

        $reset_password_image = '';
        $reset_password_image_alt = '';
        $reset_password_image_title = '';

        $title = '';
        $description = '';
        $button_text = '';

        $custom = false;
        if ($theme_type == 1)
            {
                $custom = true;
            }
        else if ($theme_type == 2)
            {
                $logo = $body->logo;
                $logo_alt = $body->logo_alt;
                $logo_title = $body->logo_title;

                $reset_password_image = $body->reset_password_image;
                $reset_password_image_alt = $body->reset_password_image_alt;
                $reset_password_image_title = $body->reset_password_image_title;

                $title = $body->title;
                $description = $body->description;
                $button_text = $body->button_text;
            }
        }
    else
        {
            $theme = null;
        }
    @endphp
    <x-bootstrap.card>
        <x-slot:header>
            <h2 class="card-title">Şablon {{isset($theme) ? 'Güncəllə' : 'Əlavə Et'}}</h2>
        </x-slot:header>
        <x-slot:body>
            <form action="{{ $theme ? route('admin.email-themes.edit') : '' }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($theme)
                    <input type="hidden" name="id" value="{{ $theme->id }}">
                @endif
                <div class="theme-select">
                    <div class="row">
                        <x-errors.display-error></x-errors.display-error>

                        <div class="col-md-4">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Şablon Adı" value="{{ $theme ? $name : '' }}">
                        </div>

                        <div class="col-md-4">
                            <select name="theme_type" id="theme-type" class="form-control" {{ $theme ? 'disabled' : '' }}>
                                <option value="{{ null }}">Şablon Tipini Seçin</option>
                                <option value="1" {{ $theme && $theme_type==1 ? 'selected' : '' }}>Özüm Email Şablon Hazırlamaq İstəyirəm</option>
                                <option value="2" {{ $theme && $theme_type==2 ? 'selected' : '' }}>Şifrə Sıfırlama Maili</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select name="process" id="process" class="form-control" {{ $theme ? 'disabled' : '' }}>
                                <option value="{{ null }}">Əməliyyat Seçin</option>
                                <option value="1" {{ $theme && $process==1 ? 'selected' : '' }}>Email Doğrulama</option>
                                <option value="2" {{ $theme && $process==2 ? 'selected' : '' }}>Şifrə Sıfırlama</option>
                                <option value="3" {{ $theme && $process==3 ? 'selected' : '' }}>Şifrə Sıfırlama Əməliyyatı Bitdikdən Sonra Göndəriləcək Email</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="content mt-5">
                    <div class="custom-content {{ $theme && $custom ? '' : 'd-none' }}">
                        <div class="row">
                            <div class="col-12">
                                <h5>Öz Kontentinizi Hazırlaya Bilərsiniz</h5>
                                <hr>
                                <h6>İstifadə Edə Biləcəyiniz Sahələr</h6>
                                <p>
                                    {link} , {username} , {useremail}
                                </p>
                            </div>

                            <div class="col-12 mt-5">
                                <textarea class="form-control" name="custom_content" id="custom_content" cols="30" rows="10">{!! $theme && $theme_type == 1 ? $body : "" !!}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="password-reset-mail {{ $theme && !$custom ? '' : 'd-none' }}">
                        <div class="row">
                            <div class="col-12">
                                <h5>Şifrə Sıfırlama Maili Sahələrini Doldura Bilərsiniz</h5>
                                <hr>
                            </div>

                            <div class="col-6 mt-4">
                                <a href="javascript:void(0)" class="btn btn-warning btn-sm w-100" id="btnAddLogoImage" data-input="logo" data-preview="imgLogo">
                                    Logo Şəkli
                                </a>
                                <input type="hidden" name="passwordResetMail[logo]" id="logo" value="{{ $theme ? $logo : '' }}">
                            </div>
                            <div class="col-6 mt-4" id="imgLogo">
                                <img src="{{ $theme ? $logo : '' }}" alt="" height="50" id="imgLogo2">
                            </div>

                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[logo_alt]" id="logo_alt" class="form-control" placeholder="Logo Alt Attribute" value="{{ $theme ? $logo_alt : '' }}">
                            </div>
                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[logo_title]" id="logo_title" class="form-control" placeholder="Logo Title Attribute" value="{{ $theme ? $logo_title : '' }}">
                            </div>

                            <div class="col-6 mt-4">
                                <a href="javascript:void(0)" class="btn btn-warning btn-sm w-100" id="btnAddResetPasswordImage" data-input="resetPasswordImage" data-preview="resetPassword">
                                    Reset Password Şəkli
                                </a>
                                <input type="hidden" name="passwordResetMail[reset_password_image]" id="resetPasswordImage" value="{{ $theme ? $reset_password_image : '' }}">
                            </div>
                            <div class="col-6 mt-4" id="resetPassword">
                                <img src="{{ $theme ? $reset_password_image : '' }}" alt="" height="50" id="passwordResetMail[imgResetPassword]">
                            </div>

                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[reset_password_image_alt]" id="reset_password_image_alt" class="form-control" placeholder="Reset Password Alt Attribute" value="{{ $theme ? $reset_password_image_alt : '' }}">
                            </div>
                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[reset_password_image_title]" id="reset_password_image_title" class="form-control" placeholder="Reset Password Title Attribute" value="{{ $theme ? $reset_password_image_title : '' }}">
                            </div>

                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[title]" id="title" class="form-control" placeholder="Başlığ" value="{{ $theme ? $title : '' }}">
                            </div>
                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[description]" id="description" class="form-control" placeholder="Açığlama" value="{{ $theme ? $description : '' }}">
                            </div>

                            <div class="col-6 mt-4">
                                <input type="text" name="passwordResetMail[button_text]" id="button_text" class="form-control" placeholder="Şifrə Sıfırlama Düyməsində Nə Yazılısın?" value="{{ $theme ? $button_text : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check {{ $theme ? '' : 'd-none' }} theme-status mt-4">
                                <input class="form-check-input" type="checkbox" value="1" name="status"
                                       id="status" {{ $theme && $status ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Şablon Aktiv Olaraq Qalsınmı?
                                </label>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <hr>
                            <button class="btn btn-success w-50">SAXLA</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-slot:body>
    </x-bootstrap.card>
@endsection

@section('js')
    <script src="{{asset('assets/admin/plugins/summernote/summernote-lite.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/pages/text-editor.js')}}"></script>
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
    <script>
        $('#theme-type').change(function (){
            let val = $(this).val();

            switch (val){
                case "1":
                    $('.custom-content').removeClass('d-none');
                    $('.theme-status').removeClass('d-none');
                    $('.password-reset-mail').addClass('d-none');
                    $('#process').val(null).change();
                    $('#process').removeAttr('readonly').removeAttr('style');
                    break;
                case "2":
                    $('.custom-content').addClass('d-none');
                    $('.theme-status').removeClass('d-none');
                    $('.password-reset-mail').removeClass('d-none');
                    $('#process').val(2).change();
                    $('#process').attr('readonly', true).attr('style', 'pointer-events: none;')
                    break;
                default:
                    $('.custom-content').addClass('d-none');
                    $('.password-reset-mail').addClass('d-none');
                    $('.theme-status').addClass('d-none');
                    $('#process').val(null).change();
                    $('#process').removeAttr('readonly').removeAttr('style');
                    break;
            }
        });

        $('#btnAddLogoImage').filemanager();
        $('#btnAddResetPasswordImage').filemanager();
    </script>
@endsection
