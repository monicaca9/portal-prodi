{!! FormInputSelect('id_sumber_dana','Sumber Dana',true,true,$sumber_dana,$data->id_sumber_dana) !!}
{!! FormInputText('nm_jns_beasiswa','Nama Jenis Beasiswa','text',$data->nm_jns_beasiswa,['required'=>true]) !!}
{!! FormInputSelect('u_pd','Untuk Peserta Didik?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_pd) !!}
{!! FormInputSelect('u_ptk','Untuk Dosen/Tendik?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_ptk) !!}
{!! FormInputSelect('u_non_ca','Untuk Non CA?',true,false,[0=>'Tidak',1=>'Ya'],$data->u_non_ca) !!}
