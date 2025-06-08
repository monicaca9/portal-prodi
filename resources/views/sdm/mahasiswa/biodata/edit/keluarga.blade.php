<?php
$data_kk_ayah = [];
$data_kk_ibu = [];
if (!is_null($mhs->id_kk_ayah)) {
    if ($mhs->id_kk==0) {
        $data_kk_ayah[] = "NULL";
    } else {
        $data_kk_ayah = explode(', ',$mhs->id_kk_ayah);
    }
}
if (!is_null($mhs->id_kk_ibu)) {
    if ($mhs->id_kk==0) {
        $data_kk_ibu[] = "NULL";
    } else {
        $data_kk_ibu = explode(', ',$mhs->id_kk_ibu);
    }
}
?>
<form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
    @csrf
    @method('PUT')
    <input type="hidden" name="kode" value="keluarga">
    <div class="row">
        <div class="col-sm-6">
            <h5>Ayah</h5>
            {!! FormInputText('nm_ayah','Nama Ayah','text',$mhs->nm_ayah,['required'=>true,'column'=>4]) !!}
            {!! FormInputText('nik_ayah','NIK Ayah','text',$mhs->nik_ayah,['required'=>true,'column'=>4]) !!}
            {!! FormInputText('tgl_lahir_ayah','Tanggal Lahir Ayah','text',$mhs->tgl_lahir_ayah,['required'=>true,'column'=>4,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
            {!! FormInputSelect('id_pendidikan_ayah','Pend. Terakhir Ayah',true,true,$jenjang,$mhs->id_pendidikan_ayah,'',['column'=>4]) !!}
            {!! FormInputSelect('id_pekerjaan_ayah','Pekerjaan Ayah',true,true,$pekerjaan,$mhs->id_pekerjaan_ayah,'',['column'=>4]) !!}
            {!! FormInputSelect('id_penghasilan_ayah','Range Penghasilan Ayah',true,true,$penghasilan,$mhs->id_penghasilan_ayah,'',['column'=>4]) !!}
            <div id="form_kk_ayah">
                <div class="form-group row">
                    <label for="kk_ayah" class="col-sm-4 col-form-label">Kebutuhan Khusus Ayah <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        @foreach($kebutuhan_khusus AS $each_kk_ayah)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $each_kk_ayah->id_kk }}" name="list_kk_ayah[]" id="{{ $each_kk_ayah->id_kk }}_ayah" {{ (in_array($each_kk_ayah->kode,$data_kk_ayah)?'checked':'') }}>
                                <label class="form-check-label" for="{{ $each_kk_ayah->id_kk }}_ayah">
                                    {{ $each_kk_ayah->nm_kk }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix hidden-md-up"></div>
        <div class="col-sm-6">
            <h5>Ibu</h5>
            {!! FormInputText('nm_ibu_kandung','Nama Ibu Kandung','text',$mhs->nm_ibu_kandung,['required'=>true,'column'=>4]) !!}
            {!! FormInputText('nik_ibu','NIK Ibu','text',$mhs->nik_ibu,['required'=>true,'column'=>4]) !!}
            {!! FormInputText('tgl_lahir_ibu','Tanggal Lahir Ibu','text',$mhs->tgl_lahir_ibu,['required'=>true,'column'=>4,'properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
            {!! FormInputSelect('id_pendidikan_ibu','Pend. Terakhir Ibu',true,true,$jenjang,$mhs->id_pendidikan_ibu,'',['column'=>4]) !!}
            {!! FormInputSelect('id_pekerjaan_ibu','Pekerjaan Ibu',true,true,$pekerjaan,$mhs->id_pekerjaan_ibu,'',['column'=>4]) !!}
            {!! FormInputSelect('id_penghasilan_ibu','Range Penghasilan Ibu',true,true,$penghasilan,$mhs->id_penghasilan_ibu,'',['column'=>4]) !!}
            <div id="form_kk_ibu">
                <div class="form-group row">
                    <label for="kk_ibu" class="col-sm-4 col-form-label">Kebutuhan Khusus Ibu <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        @foreach($kebutuhan_khusus AS $each_kk_ibu)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $each_kk_ibu->id_kk }}" name="list_kk_ibu[]" id="{{ $each_kk_ibu->id_kk }}_ibu" {{ (in_array($each_kk_ibu->kode,$data_kk_ibu)?'checked':'') }}>
                                <label class="form-check-label" for="{{ $each_kk_ibu->id_kk }}_ibu">
                                    {{ $each_kk_ibu->nm_kk }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    {!! FormInputSelect('id_jns_tinggal','Tinggal di?',true,true,$jenis_tinggal,$mhs->id_jns_tinggal) !!}
    <hr>
    <button type="submit" class="btn btn-success btn-flat btn-block">SIMPAN</button>
    <hr>
    <div class="clearfix">
        <a href="{{ route('biodata.ubah') }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
        @if(!is_null($mhs->id_jns_tinggal))
            <div class="pull-right">
                <a href="{{ url(route('biodata.ubah').'?tab=wali') }}" class="btn btn-flat btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></a>
            </div>
        @endif
    </div>
</form>
