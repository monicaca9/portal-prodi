@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-percent"></i> Manajemen Beasiswa - {{ $data->nm_periode_beasiswa }}</h3>
            <div class="card-tools">
{{--                {!! buttonAdd('manajemen_beasiswa.tambah','Tambah Pembukaan Beasiswa') !!}--}}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body" style="margin: 0;padding: 0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <tbody>
                    {!! tableRow('Jenis Beasiswa',$data->JenisBeasiswa->nm_jns_beasiswa) !!}
                    {!! tableRow('Waktu Periode',tglWaktuIndonesia($data->wkt_mulai).' - '.tglWaktuIndonesia($data->wkt_berakhir)) !!}
                    {!! tableRow('Rincian',$data->ket_beasiswa) !!}
                    {!! tableRow('Apakah dengan Wawancara?',($data->a_wawancara==1?'Ya':'Tidak')) !!}
                    {!! tableRow('Apakah dengan Tes?',($data->a_tes==1?'Ya':'Tidak')) !!}
                    {!! tableRow('Status',($data->a_aktif==1?'Aktif':'Tidak Aktif')) !!}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"> Dokumen Pendukung</h3>
                    <div class="card-tools">
                        {!! buttonAddMultipleId('manajemen_beasiswa.detail.dokumen_pendukung.tambah',[Crypt::encrypt($data->id_periode_beasiswa)],'Tambah Dokumen Pendukung') !!}
                    </div>
                </div>
                <div class="card-body" style="margin: 0;padding: 0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" style="margin: 0;padding: 0">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Waktu Unggah</th>
                                <th>Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($dokumen_pendukung AS $no_dok=>$each_dok)
                                <tr>
                                    <td>{{ $no_dok+1 }}</td>
                                    <td>{{ $each_dok->nm_dok }}</td>
                                    <td>{!! tglWaktuIndonesia($each_dok->wkt_unggah) !!}</td>
                                    <td><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="btn btn-info btn-flat btn-sm" target="_blank">Lihat</a></td>
                                    <td>
                                        {!! buttonDeleteMultipleId('manajemen_beasiswa.detail.dokumen_pendukung.delete',[Crypt::encrypt($data->id_periode_beasiswa),Crypt::encrypt($each_dok->id_dok_pendukung_beasiswa)],'Hapus Jalur') !!}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" align="center">--Tidak ada dokumen pendukung--</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"> Syarat Dokumen</h3>
                    <div class="card-tools">
                        {!! buttonAddMultipleId('manajemen_beasiswa.detail.syarat_beasiswa.tambah',[Crypt::encrypt($data->id_periode_beasiswa)],'Tambah Syarat Dokumen') !!}
                    </div>
                </div>
                <div class="card-body" style="margin: 0;padding: 0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" style="margin: 0;padding: 0">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Syarat</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($syarat AS $no_syarat=>$each_syarat)
                                <tr>
                                    <td>{{ $no_syarat+1 }}</td>
                                    <td>{{ $each_syarat->nm_syarat }}</td>
                                    <td>
                                        {!! buttonEditMultipleId('manajemen_beasiswa.detail.syarat_beasiswa.ubah',[Crypt::encrypt($data->id_periode_beasiswa),Crypt::encrypt($each_syarat->id_syarat_beasiswa)],'Edit Syarat') !!}
                                        {!! buttonDeleteMultipleId('manajemen_beasiswa.detail.syarat_beasiswa.delete',[Crypt::encrypt($data->id_periode_beasiswa),Crypt::encrypt($each_syarat->id_syarat_beasiswa)],'Hapus Syarat') !!}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" align="center">--Tidak ada dokumen pendukung--</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('manajemen_beasiswa.detail.prodi_beasiswa',Crypt::encrypt($data->id_periode_beasiswa)) }}" class="link-muted">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-gradient-blue elevation-1"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Terbuka untuk</span>
                                    <span class="info-box-number">{{ $prodi }} <small>Prodi</small></span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="" class="link-muted">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-group"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Peserta Pendaftar</span>
                                    <span class="info-box-number">{{ $pendaftar }}<small>/{{ $data->jmlh_terima }}</small></span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('manajemen_beasiswa')) !!}
        </div>
    </div>
@endsection
