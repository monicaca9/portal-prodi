{!! FormInputText('nm_thn_ajaran','Nama Tahun Ajaran','text',$data->nm_thn_ajaran,['required'=>true,'readonly'=>true]) !!}
{!! FormInputSelect('a_periode_aktif','Status',true,true,[0=>'TIDAK AKTIF',1=>'AKTIF'],$data->a_periode_aktif) !!}
{!! FormInputText('tgl_mulai','Tanggal Mulai','text',$data->tgl_mulai,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
{!! FormInputText('tgl_selesai','Tanggal Selesai','text',$data->tgl_selesai,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
