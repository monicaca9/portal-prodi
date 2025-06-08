@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
    <h3 class="card-title"><i class="fa fa-list"></i> DOKUMEN SYARAT SEMINAR - {{ $data->jenisSeminar->nm_jns_seminar }} ({{ $data->prodi->nm_lemb.' ('.$data->prodi->jenjang->nm_jenj_didik.')' }})</h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tbody>
                    {!! tableRow('Nama Syarat Seminar',  $list_syarat_seminar->syarat->nm_syarat_seminar) !!}
                    {!! tableRow('Keterangan Syarat Seminar',  $list_syarat_seminar->syarat->keterangan) !!}
                </tbody>
            </table>
        </div>
<div class="card">
    <div class="card-header bg-black">
        <h3 class="card-title"><i class="fas fa-th-list"></i> Daftar Dokumen</h3>
    </div>

    <div class="card-body" style="margin: 0; padding: 0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dokumen</th>
                        <th>Jenis Dokumen</th>
                        <th>Waktu Unggah</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($dokumen AS $no=>$each_dok)
                    <tr>
                        <td>{{$no+1 }}</td>
                        <td>{{$each_dok->nm_dok }}</td>
                        <td>{{$each_dok->nm_jns_dok }}</td>
                        <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah)}}</td>
                        <td><a href="{{ route('dokumen.preview', Crypt::encrypt($each_dok->id_dok))}}" target="_blank" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
                        <td>
                            {!! buttonDeleteMultipleId('daftar_seminar_prodi.detail.daftar_dokumen.hapus',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($list_syarat_seminar->id_list_syarat),Crypt::encrypt($each_dok->id_dok_syarat_seminar)],'Hapus Dokumen') !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">--Tidak ada dokumen--</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card">
    <form action="{{ route('daftar_seminar_prodi.detail.daftar_dokumen.simpan', [Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($list_syarat_seminar->id_list_syarat)]) }}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="card-header bg-black">
            <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen</h3>
            <div class="card-tools">
                <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                Maksimal dokumen 1 MB, dengan format PDF
            </div>
            {!! FormInputText('nm_dok','Nama Dokumen','text',null,['required'=>true]) !!}
            {!! FormInputSelect('id_jns_dok','Jenis Dokumen',true,true,$jenis_dok) !!}
            {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf'], 'required'=>true]) !!}
            {!! FormInputText('url','URL','text',null) !!}
            {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
        </div>
    </form>
</div>
<a href="{{ route('daftar_seminar_prodi.detail', ['id' => Crypt::encrypt($id_seminar_prodi)]) }}" class="btn btn-default btn-flat">
    <i class="fa fa-arrow-left"></i> Kembali
</a>

@endsection