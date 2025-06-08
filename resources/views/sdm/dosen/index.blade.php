@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-user"></i> DAFTAR DOSEN</h3>
            <div class="card-tools">
                {{--                {!! buttonAdd('manajemen_akses.peran.tambah','Tambah Peran') !!}--}}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIDN</th>
                        <th>NIP</th>
                        <th>Ikatan Kerja</th>
                        <th>Status Kepegawaian</th>
                        <th>Asal Prodi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_sdm }}</td>
                            <td>{{ $each_data->nidn }}</td>
                            <td>{{ $each_data->nip }}</td>
                            <td>{{ $each_data->nm_ikatan_kerja }}</td>
                            <td>{{ $each_data->nm_stat_pegawai }}</td>
                            <td>{{ $each_data->nm_prodi }}</td>
                            <td>{{ $each_data->nm_stat_aktif }}</td>
                            <td>
                                <span class="badge badge-info">{{ 'Terakhir sync '.tglWaktuIndonesia($each_data->last_sync) }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
