@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR JANGKA WAKTU PENYUSUNAN </h3>
        <div class="card-tools">
            {!! buttonAdd('seminar_prodi.jangka_waktu_penyusunan.tambah','Tambah Jangka Waktu Penyusunan') !!}
        </div>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Jenjang Pendidikan</th>
                        <th>Jenis Seminar</th>
                        <th>Durasi Penyusunan</th>
                        <th>Durasi Perpanjangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data AS $no=> $each_data)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $each_data->nm_jenj_didik }}</td>
                        <td>{{ $each_data->nm_jns_seminar }}</td>
                        <td>{{ $each_data->durasi_penyusunan }} Bulan</td>
                        <td>{{ $each_data->durasi_perpanjangan }} Bulan</td>
                        <td>
                            {!! buttonEdit('seminar_prodi.jangka_waktu_penyusunan.ubah',Crypt::encrypt($each_data->id_jangka_wkt),'Ubah Seminar Prodi') !!}
                            {!! buttonDelete('seminar_prodi.jangka_waktu_penyusunan.delete',Crypt::encrypt($each_data->id_jangka_wkt),'Hapus Seminar Prodi') !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection