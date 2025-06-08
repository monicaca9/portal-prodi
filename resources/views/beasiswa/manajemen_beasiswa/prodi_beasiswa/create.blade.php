<input type="hidden" name="id_periode_beasiswa" value="{{ $param_form }}">
{!! FormInputSelect('id_sms','Program Studi',true,true,$prodi) !!}
{!! FormInputText('kuota_terima_beasiswa','Kuota','number',0,['required'=>true,'properties'=>['min'=>0]]) !!}
