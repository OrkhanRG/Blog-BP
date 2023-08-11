@extends('layouts.front')

@section('css')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-bootstrap.card>
                <x-slot:header>
                    Daxil Ol
                </x-slot:header>
                <x-slot:body>
                    <form action="{{ route('login') }}" method="POST" class="login-form">
                        <div class="row">
                            @csrf
                            <div class="col-md-12 mt-2">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="col-md-12 mt-2">
                                <input type="password" name="password" id="password" class="form-control"
                                       placeholder="Şifrə">
                                <hr class="my-4">
                            </div>

                            <div class="col-md-12 social-media-register">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('socialLogin', ['driver' => 'google']) }}">
                                        <i class="fa fa-google fa-2x me-3"></i>
                                    </a>
                                    <a href="">
                                        <i class="fa fa-facebook fa-2x me-3"></i>
                                    </a>
                                    <a href="">
                                        <i class="fa fa-twitter fa-2x me-3"></i>
                                    </a>
                                    <a href="">
                                        <i class="fa fa-github fa-2x me-3"></i>
                                    </a>

                                </div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <hr class="my-4">
                                <button class="btn btn-success btn-sm w-100" type="submit">Daxil Ol</button>
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
