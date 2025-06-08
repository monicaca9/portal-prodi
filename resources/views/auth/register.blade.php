@extends('auth.layout')
@section('title', 'Pastikan email yang dimasukkan valid!')
@section('content')
    <form action="{{ route('auth.do_register') }}" method="post">
        {!! csrf_field() !!}
        <label for="nim">NIM/NPM</label>
        <div class="input-group mb-3">
            <input type="number" name="nim" id="nim" class="form-control {{($errors->has('nim')?" is-invalid":"")}}" placeholder="Tulis NIM/NPM anda" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-barcode"></span>
                </div>
            </div>
            @if($errors->has('nim'))
                <span class="invalid-feedback">{{ $errors->first('nim') }}</span>
            @endif
        </div>
        <label for="nim">Email</label>
        <div class="input-group mb-3">
            <input type="email" name="email" id="email" class="form-control {{($errors->has('email')?" is-invalid":"")}}" placeholder="Tulis Email anda" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @if($errors->has('email'))
                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="row">
            <button type="submit" class="btn btn-info btn-block">Daftar Akun</button>
        </div>
        <hr>
        Sudah memiliki akun?
        <div class="row mt-2">
            <a  href="{{ route('login') }}" class="btn btn-outline-primary btn-block">Login</a>
        </div>
    </form>
@endsection
