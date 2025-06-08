@include('__partial.date')

<input type="hidden" name="id_sms" value="{{ session()->get('login.peran.id_organisasi') }}">
{!! FormInputSelect('id_smt','Semester mulai berlaku',true,true,$smt) !!}
{!! FormInputText('no_sk','No. SK','text',null,['required'=>true]) !!}
{!! FormInputText('tgl_sk','Tanggal SK','text',null,['required'=>true,'placeholder'=>'Tuliskan tanggal disahkan SK','properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
<div class="card">
    <div class="card-header bg-black">
        <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen SK</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            Maksimal dokumen 1 MB, dengan format PDF
        </div>
        {!! FormInputText('nm_dok','Nama Dokumen','text',null,['required'=>true]) !!}
        {!! FormInputSelect('id_jns_dok','Jenis Dokumen',true,true,$jenis_dok) !!}
        {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf']]) !!}
        {!! FormInputText('url','URL','text',null) !!}
        {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
    </div>
</div>
