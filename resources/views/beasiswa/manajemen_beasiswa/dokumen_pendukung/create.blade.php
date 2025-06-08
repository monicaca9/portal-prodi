<div class="alert alert-info">
    Maksimal dokumen 1 MB, dengan format PDF
</div>
{!! FormInputText('nm_dok','Nama Dokumen','text',null,['required'=>true]) !!}
{!! FormInputSelect('id_jns_dok','Jenis Dokumen',true,true,$jenis_dok) !!}
{!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf']) !!}
{!! FormInputText('url','URL','text',null) !!}
{!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
