{!! FormInputSelect('id_smt','Periode Semester',true,true,$semester) !!}
{!! FormInputSelect('id_jenj_didik','Jenjang Beasiswa',true,true,$jenjang) !!}
{!! FormInputSelect('id_jns_beasiswa','Jenis Beasiswa',true,true,$jenis) !!}
{!! FormInputText('nm_periode_beasiswa','Nama Periode','text',null,['require'=>true]) !!}
{!! FormInputTextareaCKeditor('ket_beasiswa','Rincian Beasiswa',true,null) !!}
{!! FormInputText('wkt_mulai','Waktu Mulai','text',null,['required'=>true,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
{!! FormInputText('wkt_berakhir','Waktu Berakhir','text',null,['required'=>true,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
{!! FormInputText('jmlh_terima','Jumlah Terima','number',null,['require'=>true]) !!}
{!! FormInputSelect('a_wawancara','Apakah dengan Wawancara?',true,false,[0=>'Tidak',1=>'Ya']) !!}
{!! FormInputSelect('a_tes','Apakah dengan Tes?',true,false,[0=>'Tidak',1=>'Ya']) !!}
