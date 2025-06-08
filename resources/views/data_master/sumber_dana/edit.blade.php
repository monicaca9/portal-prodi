{!! FormInputText('nm_sumber_dana','Nama Sumber Dana','text',$data->nm_sumber_dana,['required'=>true]) !!}
{!! FormInputSelect('u_blockgrant','Untuk Blockgrant?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_blockgrant) !!}
{!! FormInputSelect('u_beasiswa','Untuk Beasiswa?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_beasiswa) !!}
{!! FormInputSelect('u_lit','Untuk Penelitian?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_lit) !!}
{!! FormInputSelect('u_unit_usaha','Untuk Unit Usaha?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_unit_usaha) !!}
