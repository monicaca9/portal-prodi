@extends('auth.layout')
@section('title', 'Pastikan email yang dimasukkan valid!')
@section('content')
    <form action="{{ route('auth.do_claim') }}" method="post">
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

        <div class="row">
            <button type="submit" class="btn btn-info btn-block">Klaim npm</button>
        </div>
    </form>
@endsection
