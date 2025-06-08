@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-percent"></i> Manajemen Beasiswa</h3>
            <div class="card-tools">
                {!! buttonAdd('manajemen_beasiswa.tambah','Tambah Pembukaan Beasiswa') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>SEMESTER BEASISWA</th>
                        <th>JENIS BEASISWA</th>
                        <th>NAMA BEASISWA</th>
                        <th>STATUS</th>
                        <th>WAKTU PERIODE</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->Semester->nm_smt }}</td>
                            <td>{{ $each_data->JenisBeasiswa->nm_jns_beasiswa }}</td>
                            <td>{{ $each_data->JenjangPendidikan->nm_jenj_didik. ' - '.$each_data->nm_periode_beasiswa }}</td>
                            <td>
                                @if($each_data->a_aktif==1)
                                    <a href="{{ route('manajemen_beasiswa.ubah_aktif',Crypt::encrypt($each_data->id_periode_beasiswa)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                @else
                                    <a href="{{ route('manajemen_beasiswa.ubah_aktif',Crypt::encrypt($each_data->id_periode_beasiswa)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                @endif
                            </td>
                            <td>{{ tglIndonesia($each_data->wkt_mulai).' - '.tglIndonesia($each_data->wkt_berakhir) }}</td>
                            <td>
                                {!! buttonShow('manajemen_beasiswa.detail',Crypt::encrypt($each_data->id_periode_beasiswa),'Rincian Pembukaan Beasiswa') !!}
                                {!! buttonEdit('manajemen_beasiswa.ubah',Crypt::encrypt($each_data->id_periode_beasiswa),'Ubah Pembukaan Beasiswa') !!}
                                {!! buttonDelete('manajemen_beasiswa.delete',Crypt::encrypt($each_data->id_periode_beasiswa),'Hapus Pembukaan Beasiswa') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
