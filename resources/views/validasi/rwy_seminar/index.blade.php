@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header bg-dark">
        <h3 class="card-title"><i class="fa fa-list"></i> Validasi Riwayat Seminar Mahasiswa</h3>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Diajukan'?'active':null) }}" href="{{ route('validasi.riwayat_seminar') }}">Diajukan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Disetujui'?'active':null) }}" href="{{ url(route('validasi.riwayat_seminar').'?status=Disetujui') }}">Disetujui</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditolak'?'active':null) }}" href="{{ url(route('validasi.riwayat_seminar').'?status=Ditolak') }}">Ditolak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditangguhkan'?'active':null) }}" href="{{ url(route('validasi.riwayat_seminar').'?status=Ditangguhkan') }}">Ditangguhkan</a>
            </li>
        </ul>
        <div class="mt-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Asal Prodi</th>
                            <th>Jenis Seminar</th>
                            @if($status=='Diajukan')
                            <th>Tanggal Ajuan</th>
                            <th class="text-center">Umur Ajuan<br>(Hari)</th>
                            @endif
                            @if($status!='Diajukan')
                            <th>Status Terbaru</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data AS $no=> $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{!! $each_data->nm_pd.'<br>('.$each_data->nim.')' !!}</td>
                            <td>{{ $each_data->asal_prodi }}</td>
                            <td>{{ $each_data->nm_jns_seminar }}</td>
                            @if($status=='Diajukan')
                            <td>{{ tglIndonesiaShort($each_data->wkt_ajuan) }}</td>
                            <td class="text-center">{{ $each_data->umur_ajuan }}</td>
                            @endif
                            @if($status!='Diajukan')
                            {{-- <td>{{ tglIndonesiaShort($each_data->wkt_selesai_ver) }}</td>--}}
                            <td class="text-center">{{ config('mp.data_master.status_ajuan_admin.'.$each_data->stat_ajuan) }}</td>
                            {{-- <td>{{ $each_data->ket_periksa }}</td>--}}
                            @endif
                            <td>
                                @if($status!='Diajukan')
                                <a href="{{ route('validasi.riwayat_seminar.detail',Crypt::encrypt($each_data->id_ver_ajuan)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                                @else
                                <a href="{{ route('validasi.riwayat_seminar.detail',Crypt::encrypt($each_data->id_ver_ajuan)) }}" class="btn btn-flat btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Validasi Ajuan"><i class="fa fa-check-square-o"></i></a>
                                @endif
                                <a href="{{ route('validasi.riwayat_seminar.beritaacara',Crypt::encrypt($each_data->id_ajuan_pdm_seminar)) }}" href="" target="_blank" class="btn btn-flat btn-xs btn-primary"><i class="fas fa-print"></i> Berita Acara</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection