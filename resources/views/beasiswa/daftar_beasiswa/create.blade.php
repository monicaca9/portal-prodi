@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Upload Dokumen Beasiswa {{ $periode->nm_periode_beasiswa }}</h3>
        </div>
        <form action="{{ route('daftar_beasiswa.detail.simpan',[Crypt::encrypt($data->id_daftar_beasiswa),Crypt::encrypt($syarat->id_syarat_beasiswa)]) }}" enctype="multipart/form-data" method="post">
            @csrf
        <div class="card-body" style="margin: 0;padding: 0">
            <table class="table table-striped">
                <tbody>
                {!! tableRow('Syarat Dokumen',$syarat->nm_syarat) !!}
                </tbody>
            </table>
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
        <div class="card-footer">
            {!! buttonBack(route('daftar_beasiswa.detail',Crypt::encrypt($data->id_daftar_beasiswa))) !!}
            <div class="pull-right">
                <button class="btn btn-primary btn-flat" type="submit"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </div>
        </form>
    </div>
@endsection
