{!! FormInputText('nm_pengguna','Nama','text',$data->nm_pengguna,['required'=>true]) !!}
{!! FormInputText('username','E-mail','text',$data->username,['required'=>true]) !!}
{!! FormInputSelect('jenis_kelamin','Jenis Kelamin',true,false,['L'=>'Laki-laki','P'=>'Perempuan'],$data->jenis_kelamin) !!}
{!! FormInputText('password','Password','password',null) !!}
{!! FormInputText('password_confirmation','Konfirmasi Password','password',null) !!}

@push('form_tambahan')
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-users"></i> Peran Pengguna</h3></div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Peran</th>
                    <th>Unit Organisasi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($daftar_peran AS $each_daftar_peran)
                    <tr>
                        <td>{{ $each_daftar_peran->nm_peran }}</td>
                        <td>{{ $each_daftar_peran->nm_lemb }}</td>
                        <td>{!! buttonDelete('manajemen_akses.pengguna.delete_peran',Crypt::encrypt($each_daftar_peran->id_role_pengguna),'Hapus Peran '.$each_daftar_peran->nm_peran) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{route('manajemen_akses.pengguna.simpan_peran_pengguna')}}" class="form-horizontal">
            @csrf
            <div class="card-body">
                <input type="hidden" name="id_pengguna" value="{{ $data->id_pengguna }}">
                {!! FormInputSelect('id_organisasi','Unit Organisasi',true,true,$unit) !!}
                {!! FormInputSelect('id_peran','Peran',true,true,$peran) !!}
                {!! FormInputText('sk_penugasan','Nomor SK','text',null) !!}
                {!! FormInputText('tgl_sk_penugasan','Tanggal SK','date',null) !!}
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-plus"></i> Tambahkan Peran</button>
                </div>
            </div>
        </form>
    </div>
@endpush
