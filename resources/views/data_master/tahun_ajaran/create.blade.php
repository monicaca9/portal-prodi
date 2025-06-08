{!! FormInputText('nm_thn_ajaran','Nama Tahun Ajaran','text',null,['required'=>true]) !!}
{!! FormInputSelect('a_periode_aktif','Status',true,true,[0=>'TIDAK AKTIF',1=>'AKTIF']) !!}
{!! FormInputText('tgl_mulai','Tanggal Mulai','text',null,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
{!! FormInputText('tgl_selesai','Tanggal Selesai','text',null,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
