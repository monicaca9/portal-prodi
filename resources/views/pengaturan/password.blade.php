@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-key"></i> Ubah Password</h3></div>
        <form method="POST" action="{{route('password.update',Crypt::encrypt(auth()->user()->id_pengguna))}}" class="form-horizontal">
            @csrf
            @method('PUT')
            <div class="card-body">
                {!! FormInputText('pass_lama','Password Lama','password',null,['required'=>true]) !!}
                {!! FormInputText('password','Password Baru','password',null,['required'=>true]) !!}
                {!! FormInputText('password_confirmation','Konfirmasi Password Baru','password',null,['required'=>true]) !!}
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-send"></i> KIRIM EMAIL TESTING</button>
                </div>
            </div>
        </form>
    </div>
@endsection
