@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-users"></i> DAFTAR PENGGUNA</h3>
            <div class="card-tools">
                {!! buttonAdd('manajemen_akses.pengguna.tambah','Tambah Penguna') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA PENGGUNA</th>
                        <th>USERNAME</th>
                        <th>UNIT</th>
                        <th>PERAN</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_pengguna }}</td>
                            <td>{{ $each_data->username }}</td>
                            <td>{{ $each_data->nm_lemb }}</td>
                            <td>{{ $each_data->nm_peran }}</td>
                            <td>
                                @if($each_data->id_peran!=3)
                                    @if($each_data->a_aktif==1)
                                        <a href="{{ route('manajemen_akses.pengguna.ubah_aktif',Crypt::encrypt($each_data->id_pengguna)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                    @else
                                        <a href="{{ route('manajemen_akses.pengguna.ubah_aktif',Crypt::encrypt($each_data->id_pengguna)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                    @endif
                                @else
                                    <button class="btn btn-flat btn-info btn-sm btn-block" disabled>SYNC PDDIKTI</button>
                                @endif
                            </td>
                            <td>
                                @if($each_data->id_peran!=3)
                                    {!! buttonEdit('manajemen_akses.pengguna.ubah',Crypt::encrypt($each_data->id_pengguna),'Ubah Pengguna') !!}
                                    {!! buttonDelete('manajemen_akses.pengguna.delete',Crypt::encrypt($each_data->id_pengguna),'Hapus Pengguna') !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
