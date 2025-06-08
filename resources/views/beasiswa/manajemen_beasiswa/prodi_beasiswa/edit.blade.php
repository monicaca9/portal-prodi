{!! FormInputSelect('id_sms','Program Studi',true,true,$prodi,$data->id_sms) !!}
{!! FormInputText('kuota_terima_beasiswa','Kuota','number',$data->kuota_terima_beasiswa,['required'=>true,'properties'=>['min'=>0]]) !!}
