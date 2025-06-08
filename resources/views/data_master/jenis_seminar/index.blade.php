@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-circle-o"></i> {{ $title_default }}</h3>
            <div class="card-tools">
                {!! buttonAdd($route_default.'.tambah','Tambah '.$title_default.' Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>KATEGORI SEMINAR</th>
                        <th>NAMA JENIS SEMINAR</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ is_null($each_data->id_induk_jns_seminar)?null:$each_data->induk_jenis->nm_jns_seminar }}</td>
                            <td>{{ $each_data->nm_jns_seminar }}</td>
                            <td>
                                {!! buttonEdit($route_default.'.ubah',Crypt::encrypt($each_data->id_jns_seminar),'Ubah '.$title_default) !!}
                                {!! buttonDelete($route_default.'.delete',Crypt::encrypt($each_data->id_jns_seminar),'Hapus '.$title_default) !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
