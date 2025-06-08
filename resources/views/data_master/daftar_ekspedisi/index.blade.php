@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas ion-md-time"></i> DAFTAR EKSPEDISI</h3>
            <div class="card-tools">
                {!! buttonAdd('data_master.daftar_ekspedisi.tambah','Tambah Ekspedisi Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA EKSPEDISI</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_ekspedisi }}</td>
                            <td>
                                {!! buttonEdit('data_master.daftar_ekspedisi.ubah',$each_data->id_ekspedisi,'Ubah Ekspedisi') !!}
                                {!! buttonDelete('data_master.daftar_ekspedisi.delete',$each_data->id_ekspedisi,'Hapus Ekspedisi') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
