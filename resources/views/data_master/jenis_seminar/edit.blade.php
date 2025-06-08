{!! FormInputSelect('id_induk_jns_seminar','Kategori Seminar',false,true,$jenis_seminar,$data->id_induk_jns_seminar) !!}
{!! FormInputText('nm_jns_seminar','Nama Jenis Seminar','text',$data->nm_jns_seminar,['required'=>true]) !!}
{!! FormInputText('urutan','Urutan Seminar','number',$data->urutan,['required'=>true]) !!}
{!! FormInputSelect('a_tugas_akhir','Apakah TA?',true,false,config('mp.data_master.tipe_boolean'),$data->a_tugas_akhir) !!}
{!! FormInputSelect('a_seminar','Apakah Tipe Seminar?',true,false,config('mp.data_master.tipe_boolean'),$data->a_seminar) !!}
