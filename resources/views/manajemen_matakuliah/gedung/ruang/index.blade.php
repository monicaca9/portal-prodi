@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building"></i> Ruang {{ $gedung->nm_gedung.' - '.$gedung->fakultas->nm_lemb }}</h3>
            <div class="card-tools">
                {!! buttonAddMultipleId('gedung_ruang.detail_ruang.tambah',[Crypt::encrypt($gedung->id_gedung)],'Tambah Ruang Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>KODE RUANG</th>
                        <th>NAMA RUANG</th>
                        <th>KAPASITAS RUANG</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->kode_ruang }}</td>
                            <td>{{ $each_data->nm_ruang }}</td>
                            <td>{{ $each_data->kapasitas_ruang }}</td>
                            <td>
                                {!! buttonEdit('gedung_ruang.detail_ruang.ubah',[Crypt::encrypt($gedung->id_gedung),Crypt::encrypt($each_data->id_ruang)],'Ubah Ruang') !!}
                                {!! buttonDelete('gedung_ruang.detail_ruang.delete',[Crypt::encrypt($gedung->id_gedung),Crypt::encrypt($each_data->id_ruang)],'Hapus Ruang') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('gedung_ruang')) !!}
        </div>
    </div>
@endsection
