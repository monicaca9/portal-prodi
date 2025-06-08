{!! FormInputText('nm_pengguna','Nama','text',null,['required'=>true]) !!}
{!! FormInputText('username','E-mail','text',null,['required'=>true]) !!}
{!! FormInputText('password','Password','password',null,['required'=>true]) !!}
{!! FormInputText('password_confirmation','Konfirmasi Password','password',null,['required'=>true]) !!}
{!! FormInputSelect('jenis_kelamin','Jenis Kelamin',true,false,['L'=>'Laki-laki','P'=>'Perempuan']) !!}
{!! FormInputSelect('id_organisasi','Unit Organisasi',true,true,$unit) !!}
{!! FormInputSelect('id_peran','Peran',true,true,$peran) !!}
{!! FormInputText('sk_penugasan','Nomor SK','text',null) !!}
{!! FormInputText('tgl_sk_penugasan','Tanggal SK','date',null) !!}
