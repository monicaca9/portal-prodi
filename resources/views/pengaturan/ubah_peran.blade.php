@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-key"></i> Ubah Peran</h3></div>
        <form method="POST" action="{{route('ubah_peran.simpan')}}" class="form-horizontal">
            @csrf
            <div class="form-group mt-3">
                <div class="col-sm-12">
                    <select name="id_role_pengguna" id="id_role_pengguna" class="form-control">
                        @foreach($daftar_peran AS $each_daftar_peran)
                            <option value="{{ $each_daftar_peran->id_role_pengguna }}" {{ $each_daftar_peran->id_role_pengguna==session()->get('login.peran.id_role_pengguna')?'selected':'' }}>{{ $each_daftar_peran->nm_peran.' ('.$each_daftar_peran->nm_lemb.')' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-forward"></i> Ubah Peran</button>
                </div>
            </div>
        </form>
    </div>
@endsection
