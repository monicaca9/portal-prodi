@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')
@include('__partial.date')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> Daftar Riwayat Verifikasi Syarat Seminar ( {{ $syarat->syarat->nm_syarat_seminar}} )</h3>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <tbody>
                {!! tableRow('Jenis Seminar',$data->SeminarProdi->jenisSeminar->nm_jns_seminar) !!}
                {!! tableRow('Nama Syarat',$syarat->syarat->nm_syarat_seminar) !!}
                {!! tableRow('Keterangan Syarat',$syarat->syarat->keterangan) !!}
            </tbody>
        </table>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumen AS $no_dok=>$each_dok)
                            <tr>
                                <td>{{ $no_dok+1 }}</td>
                                <td>{{ $each_dok->nm_dok }}</td>
                                <td>{{ $each_dok->nm_jns_dok }}</td>
                                <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah) }}</td>
                                <td><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
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
            <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-th-list"></i> Riwayat Verifikasi</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Verifikator</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Waktu Verifikasi</th>
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
    <div class="card-footer">
        {!! buttonBack(route('validasi.pengajuan_seminar_kaprodi.detail',Crypt::encrypt($data->id_daftar_seminar))) !!}
    </div>
</div>
@endsection