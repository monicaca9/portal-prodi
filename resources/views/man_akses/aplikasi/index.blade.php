@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR MENU</h3>
            <div class="card-tools">
                {!! buttonAdd('manajemen_akses.aplikasi.tambah','Tambah Aplikasi') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA APLIKASI</th>
                        <th>KETERANGAN</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_aplikasi }}</td>
                            <td>{{ $each_data->ket_aplikasi }}</td>
                            <td>
                                {!! buttonEdit('manajemen_akses.aplikasi.ubah',Crypt::encrypt($each_data->id_aplikasi),'Ubah Aplikasi') !!}
                                {!! buttonDelete('manajemen_akses.aplikasi.delete',Crypt::encrypt($each_data->id_aplikasi),'Hapus Aplikasi') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
