{!! FormInputSelect('id_thn_ajaran','Tahun Ajaran',true,true,$tahun_ajaran) !!}
{!! FormInputSelect('smt','Semester',true,true,config('mp.data_master.smt')) !!}
{!! FormInputSelect('a_periode_aktif','Status',true,true,[0=>'TIDAK AKTIF',1=>'AKTIF']) !!}
{!! FormInputText('tgl_mulai','Tanggal Mulai','text',null,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
{!! FormInputText('tgl_selesai','Tanggal Selesai','text',null,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
