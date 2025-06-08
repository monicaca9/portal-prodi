@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> Daftar Riwayat Seminar Mahasiswa Prodi {{$prodi->prodi}}</h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama</th>
                        <th>Jenis Seminar</th>
                        <th>Status Terbaru</th>
                        <th>Nilai Seminar</th>
                        <th class="text-center">Entri Nilai Seminar  <br> (SIAKAD)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data AS $no=> $each_data)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{!! $each_data->nm_pd.'<br>('.$each_data->nim.')' !!}</td>
                        <td>{{ $each_data->nm_jns_seminar }}</td>
                        <td class="text-center">{{ config('mp.data_master.status_ajuan_admin.'.$each_data->stat_ajuan) }}</td>
                        <td class="text-center">{{ number_format($each_data->nilai_seminar,2) }}</td>
                        <td class="text-center">
                            @if(isset($each_data->nilai_angka) && !is_null($each_data->nilai_angka))
                            <i class="text-success fa fa-check"></i>
                            @else
                            <i class="text-danger fa fa-close"></i>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('validasi.riwayat_seminar_kaprodi.detail',Crypt::encrypt($each_data->id_ver_ajuan)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                            <a href="{{ route('validasi.riwayat_seminar.beritaacara',Crypt::encrypt($each_data->id_ajuan_pdm_seminar)) }}" href="" target="_blank" class="btn btn-flat btn-xs btn-primary"><i class="fas fa-print"></i> Berita Acara</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection