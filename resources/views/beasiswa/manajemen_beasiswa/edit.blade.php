{!! FormInputSelect('id_smt','Periode Semester',true,true,$semester,$data->id_smt) !!}
{!! FormInputSelect('id_jenj_didik','Jenjang Beasiswa',true,true,$jenjang,$data->id_jenj_didik) !!}
{!! FormInputSelect('id_jns_beasiswa','Jenis Beasiswa',true,true,$jenis,$data->id_jns_beasiswa) !!}
{!! FormInputText('nm_periode_beasiswa','Nama Periode','text',$data->nm_periode_beasiswa,['require'=>true]) !!}
{!! FormInputTextareaCKeditor('ket_beasiswa','Rincian Beasiswa',true,$data->ket_beasiswa) !!}
{!! FormInputText('wkt_mulai','Waktu Mulai','text',$data->wkt_mulai,['required'=>true,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
{!! FormInputText('wkt_berakhir','Waktu Berakhir','text',$data->wkt_berakhir,['required'=>true,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
{!! FormInputText('jmlh_terima','Jumlah Terima','number',$data->jmlh_terima,['require'=>true]) !!}
{!! FormInputSelect('a_wawancara','Apakah dengan Wawancara?',true,false,[0=>'Tidak',1=>'Ya'],$data->a_wawancara) !!}
{!! FormInputSelect('a_tes','Apakah dengan Tes?',true,false,[0=>'Tidak',1=>'Ya'],$data->a_tes) !!}
