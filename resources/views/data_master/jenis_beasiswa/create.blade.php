{!! FormInputText('id_jns_beasiswa','Kode Jenis Beasiswa','number',null,['required'=>true]) !!}
{!! FormInputSelect('id_sumber_dana','Sumber Dana',true,true,$sumber_dana) !!}
{!! FormInputText('nm_jns_beasiswa','Nama Jenis Beasiswa','text',null,['required'=>true]) !!}
{!! FormInputSelect('u_pd','Untuk Peserta Didik?',true,false,[0=>'Tidak',1=>'Ya']) !!}
{!! FormInputSelect('u_ptk','Untuk Dosen/Tendik?',true,false,[0=>'Tidak',1=>'Ya']) !!}
{!! FormInputSelect('u_non_ca','Untuk Non CA?',true,false,[0=>'Tidak',1=>'Ya']) !!}
