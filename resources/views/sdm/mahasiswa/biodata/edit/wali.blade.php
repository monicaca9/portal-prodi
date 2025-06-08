@if($mhs->id_jns_tinggal==2)
<form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
    @csrf
    @method('PUT')
    <input type="hidden" name="kode" value="wali">
    {!! FormInputText('nm_wali','Nama Wali','text',$mhs->nm_wali,['required'=>true]) !!}
    {!! FormInputText('tgl_lahir_wali','Tanggal Lahir Wali','text',$mhs->tgl_lahir_wali,['required'=>true,'readonly'=>true,'properties'=>['autocomplete'=>'off']]) !!}
    {!! FormInputSelect('id_pekerjaan_wali','Pekerjaan Wali',true,true,$pekerjaan,$mhs->id_pekerjaan_wali) !!}
    {!! FormInputSelect('id_penghasilan_wali','Range Penghasilan Wali',true,true,$penghasilan,$mhs->id_penghasilan_wali) !!}
    {!! FormInputSelect('id_pendidikan_wali','Pend. Terakhir Wali',true,true,$jenjang,$mhs->id_pendidikan_wali) !!}
    <hr>
    <button type="submit" class="btn btn-success btn-flat btn-block">SIMPAN</button>
    <hr>
    <div class="clearfix">
        <a href="{{ url(route('biodata.ubah').'?tab=keluarga') }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
        @if(!is_null($mhs->nm_wali) )
            <div class="pull-right">
                <a href="{{ url(route('biodata.ubah').'?tab=lainnya') }}" class="btn btn-flat btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></a>
            </div>
        @endif
    </div>
</form>
@else
    <div class="alert alert-info">
        Anda tidak tinggal bersama Wali<br>Silahkan lanjut ke proses selanjutnya dengan klik tombol selanjutnya
    </div>
    <hr>
    <div class="clearfix">
        <a href="{{ url(route('biodata.ubah').'?tab=keluarga') }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
        @if(!is_null($mhs->nm_ibu_kandung) )
            <div class="pull-right">
                <a href="{{ url(route('biodata.ubah').'?tab=lainnya') }}" class="btn btn-flat btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></a>
            </div>
        @endif
    </div>
@endif
