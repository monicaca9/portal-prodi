{!! FormInputSelect('id_induk_jns_seminar','Kategori Seminar',false,true,$jenis_seminar) !!}
{!! FormInputText('nm_jns_seminar','Nama Jenis Seminar','text',null,['required'=>true]) !!}
{!! FormInputText('urutan','Urutan Seminar','number',1,['required'=>true]) !!}
{!! FormInputSelect('a_tugas_akhir','Apakah TA?',true,false,config('mp.data_master.tipe_boolean')) !!}
{!! FormInputSelect('a_seminar','Apakah Tipe Seminar?',true,false,config('mp.data_master.tipe_boolean')) !!}
