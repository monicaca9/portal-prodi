@include('__partial.date')

{!! FormInputSelect('id_smt','Semester mulai berlaku',true,true,$smt,$data->id_smt) !!}
{!! FormInputText('no_sk','No. SK','text',$data->no_sk,['required'=>true]) !!}
{!! FormInputText('tgl_sk','Tanggal SK','text',$data->tgl_sk,['required'=>true,'placeholder'=>'Tuliskan tanggal disahkan SK','properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
{!! FormInputSelect('ubah_file','Ubah File SK?',true,false,config('mp.data_master.status_ubah_file')) !!}
<div class="card">
    <div class="card-header bg-black">
        <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen SK</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            Maksimal dokumen 1 MB, dengan format PDF
        </div>
        {!! FormInputText('nm_dok','Nama Dokumen','text',null) !!}
        {!! FormInputSelect('id_jns_dok','Jenis Dokumen',false,true,$jenis_dok) !!}
        {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf']]) !!}
        {!! FormInputText('url','URL','text',null) !!}
        {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
    </div>
</div>
