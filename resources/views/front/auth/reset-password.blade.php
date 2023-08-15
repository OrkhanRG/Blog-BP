@extends('layouts.front')

@section('css')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-bootstrap.card>
                <x-slot:header>
                    Şifrəni Sıfırla
                </x-slot:header>
                <x-slot:body>
                    <form action="{{isset($token) ? route('passwordResetToken', ['token' => $token]) : route('passwordReset') }}" method="POST" class="login-form">
                        @csrf
                        <div class="row">
                            <x-errors.display-error></x-errors.display-error>
                            @isset($token)
                                <div class="col-md-12 mt-2">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Şifrə">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Şifrə Təkrar">
                                </div>
                            @else
                                <div class="col-md-12 mt-2">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                                </div>
                            @endisset
                            <div class="col-md-12 mt-2">
                                <hr class="my-4">
                                <button class="btn btn-success btn-sm w-100" type="submit">Şifrəni Sıfırla</button>
                            </div>
                        </div>
                    </form>
                </x-slot:body>
            </x-bootstrap.card>
        </div>
    </div>
@endsection

@section('js')
    @include('sweetalert::alert')
@endsection
