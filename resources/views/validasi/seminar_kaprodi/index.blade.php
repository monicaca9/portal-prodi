@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header bg-dark">
        <h3 class="card-title"><i class="fa fa-list"></i> Validasi Pengajuan Seminar Mahasiswa</h3>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Diserahkan'?'active':null) }}" href="{{ url(route('validasi.pengajuan_seminar_kaprodi')) }}">Diserahkan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Disetujui'?'active':null) }}" href="{{ url(route('validasi.pengajuan_seminar_kaprodi').'?status=Disetujui') }}">Disetujui</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditolak'?'active':null) }}" href="{{ url(route('validasi.pengajuan_seminar_kaprodi').'?status=Ditolak') }}">Ditolak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditangguhkan'?'active':null) }}" href="{{ url(route('validasi.pengajuan_seminar_kaprodi').'?status=Ditangguhkan') }}">Ditangguhkan</a>
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
                            @if($status=='Diserahkan')
                            <th>Tanggal Ajuan</th>
                            <th class="text-center">Umur Ajuan<br>(Hari)</th>
                            @elseif($status=='Disetujui')
                            <th>No.Ba Seminar</th>
                            <th>Status Terbaru</th>
                            @else
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
                            @if($status=='Diserahkan')
                            <td>{{ tglWaktuIndonesia($each_data->waktu_diajukan) }}</td>
                            <td class="text-center">{{ $each_data->umur_ajuan }}</td>
                            @elseif($status=='Disetujui')
                            <td>{{ $each_data->no_ba_seminar ?? '-' }}</td>
                            <td class="text-left">{{ config('mp.data_master.status_ajuan_daftar_seminar.'.$each_data->status_validasi) }}</td>
                            @else 
                            {{-- <td>{{ tglWaktuIndonesia($each_data->wkt_selesai_ver) }}</td>--}}
                            <td class="text-left">{{ config('mp.data_master.status_ajuan_daftar_seminar.'.$each_data->status_validasi) }}</td>
                            {{-- <td>{{ $each_data->ket_periksa }}</td>--}}
                            @endif
                            <td>
                                @if($status!='Diserahkan')
                                <a href="{{ route('validasi.pengajuan_seminar_kaprodi.detail',Crypt::encrypt($each_data->id_daftar_seminar)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                                @else
                                <a href="{{ route('validasi.pengajuan_seminar_kaprodi.detail',Crypt::encrypt($each_data->id_daftar_seminar)) }}" class="btn btn-flat btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Validasi Ajuan"><i class="fa fa-check-square-o"></i></a>
                                @endif
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