@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')
@include('__partial.date')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> Daftar Dokumen Pendaftaran Seminar {{ $data->SeminarProdi->jenisSeminar->nm_jns_seminar }}</h3>
    </div>
    <div class="card-body" style="margin: 0;padding: 0">
        <table class="table table-striped">
            <tbody>
                {!! tableRow('Nama Syarat',$syarat->syarat->nm_syarat_seminar) !!}
                {!! tableRow('Keterangan Syarat',$syarat->syarat->keterangan) !!}
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"> <i class="fas fa-th-list"></i> Contoh Format Dokumen Syarat Seminar</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>File</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($dokumen_syarat_seminar AS $no=>$each_dok)
                            <tr>
                                <td>{{$no+1 }}</td>
                                <td>{{$each_dok->nm_dok }}</td>
                                <td><a href="{{ route('dokumen.preview', Crypt::encrypt($each_dok->id_dok))}}" target ="_blank" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
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
        <hr>
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
                                @if($data->status_validasi==0 && $cari_dok_syarat->stat_ajuan==0)
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumen AS $no_dok=>$each_dok)
                            <tr>
                                <td>{{ $no_dok+1 }}</td>
                                <td>{{ $each_dok->nm_dok }}</td>
                                <td>{{ $each_dok->nm_jns_dok }}</td>
                                <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah) }}</td>
                                <td><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" target="_blank" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
                                @if($data->status_validasi==0 && $cari_dok_syarat->stat_ajuan==0)
                                <td>
                                    {!! buttonDeleteMultipleId('pendaftaran_seminar.detail.daftar_dokumen.hapus',[Crypt::encrypt($data->id_daftar_seminar),Crypt::encrypt($syarat->id_list_syarat),Crypt::encrypt($each_dok->id_dok_syarat_daftar)],'Hapus Dokumen') !!}
                                </td>
                                @endif
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
    </div>
    @if(count($validasi)>0)
    <div class="card-body">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-th-list"></i> Riwayat Verifikasi</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Verifikator</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Waktu verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($validasi AS $no_val=>$each_validasi)
                            <tr>
                                <td>{{ $no_val+1 }}</td>
                                <td>{{ $each_validasi->nm_verifikator }}</td>
                                <td>{{ config('mp.data_master.status_periksa.'.$each_validasi->status_periksa) }}</td>
                                <td>{{ $each_validasi->ket_periksa }}</td>
                                <td>{{ tglWaktuIndonesia($each_validasi->wkt_selesai_ver) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">--Tidak ada data--</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($data->status_validasi==0 && $cari_dok_syarat->stat_ajuan==0)
    <hr>
    <div class="card-body">
        <div class="card">
            <form action="{{ route('pendaftaran_seminar.detail.daftar_dokumen.simpan',[Crypt::encrypt($data->id_daftar_seminar),Crypt::encrypt($syarat->id_list_syarat)]) }}" enctype="multipart/form-data" method="post">
                @csrf
                <div class="card-header bg-black">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen</h3>
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

                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary float-right" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    <div class="card-footer">
        {!! buttonBack(route('pendaftaran_seminar.detail',Crypt::encrypt($data->id_daftar_seminar))) !!}
    </div>
</div>
@endsection