<input type='hidden' name='id_sp' value='{{$id_sp}}'>
{!! FormInputSelect('id_jns_seminar','Jenis Seminar',true,true,$jenis_seminar) !!}
{!! FormInputSelect('id_jenj_didik','Jenjang Pendidikan',true,true,$jenj_didik) !!}
{!! FormInputText('durasi_penyusunan','Durasi Penyusunan (Bulan)','number',null) !!}
{!! FormInputText('durasi_perpanjangan','Durasi Perpanjangan (Bulan)','number',null) !!}
