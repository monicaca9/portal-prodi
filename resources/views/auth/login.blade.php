@extends('auth.layout')
@section('title', 'Login Area')
@section('content')
    <form action="{{ route('auth.login') }}" method="post">
        {!! csrf_field() !!}
        <label for="username">Username</label>
        <div class="input-group mb-3">
            <input type="text" name="username" id="username" class="form-control" placeholder="Tuliskan Username/Email Anda" value="{{ old('username') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <label for="password">Password</label>
        <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="Tuliskan Password Anda">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
        <div class="row mt-2">
            <a href="{!! route('auth.login.sso') !!}" class="btn btn-success btn-block"><i class="fas fa-user-alt mr-1"></i>Sign In - SSO UNILA</a>
        </div>
        @if(config('modul_pp.module.register_mhs')==1)
            <hr>
            Belum memiliki akun?
            <div class="row mt-2">
                <a  href="{{ route('auth.register') }}" class="btn btn-outline-info btn-block">Daftar Akun</a>
            </div>
        @endif
    </form>
@endsection
