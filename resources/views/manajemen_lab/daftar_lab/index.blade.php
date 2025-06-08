@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-flask"></i> DAFTAR LABORATORIUM</h3>
            <div class="card-tools">
                @if(check_akses('manajemen_lab.daftar_lab.tambah'))
                    <a href="{{ route('manajemen_lab.daftar_lab.tambah') }}" class="btn btn-primary btn-sm btn-flat" data-toggle="tooltip" data-placement="top" title="Tambah Laboratorium">
                        <i class="fa fa-plus"></i> Tambah Laboratorium</a>
                @endif
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>UNIT ORGANISASI</th>
                        <th>NAMA LABORATORIUM</th>
                        <th>NAMA GEDUNG</th>
                        <th>ALAMAT GEDUNG</th>
                        <th>KETERANGAN</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_lemb }}</td>
                            <td>{{ $each_data->nm_lab }}</td>
                            <td>{{ $each_data->nm_gedung }}</td>
                            <td>{{ $each_data->alamat_gedung }}</td>
                            <td>{{ $each_data->ket }}</td>
                            <td>
                                {!! buttonEdit('manajemen_lab.daftar_lab.ubah',Crypt::encrypt($each_data->id_lab),'Ubah Laboratorium') !!}
                                {!! buttonDelete('manajemen_lab.daftar_lab.delete',Crypt::encrypt($each_data->id_lab),'Hapus Laboratorium') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
