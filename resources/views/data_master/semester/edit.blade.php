{!! FormInputText('nm_smt','Nama Semester','text',$data->nm_smt,['required'=>true,'placeholder'=>'Contoh: 2019/2020 Ganjil','readonly'=>true]) !!}
{!! FormInputSelect('a_periode_aktif','Status',true,true,[0=>'TIDAK AKTIF',1=>'AKTIF'],$data->a_periode_aktif) !!}
{!! FormInputText('tgl_mulai','Tanggal Mulai','text',$data->tgl_mulai,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
{!! FormInputText('tgl_selesai','Tanggal Selesai','text',$data->tgl_selesai,['required'=>true,'properties'=>['autocomplete'=>'off']]) !!}
