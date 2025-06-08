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
                        <th>KODE SUMBER DANA</th>
                        <th>NAMA SUMBER DANA</th>
                        <th>BLOCKGRANT?</th>
                        <th>BEASISWA?</th>
                        <th>PENELITIAN?</th>
                        <th>UNIT USAHA?</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $each_data->id_sumber_dana }}</td>
                            <td>{{ $each_data->nm_sumber_dana }}</td>
                            <td>{!! $each_data->u_blockgrant==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>{!! $each_data->u_beasiswa==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>{!! $each_data->u_lit==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>{!! $each_data->u_unit_usaha==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>
                                {!! buttonEdit($route_default.'.ubah',Crypt::encrypt($each_data->id_sumber_dana),'Ubah '.$title_default) !!}
                                {!! buttonDelete($route_default.'.delete',Crypt::encrypt($each_data->id_sumber_dana),'Hapus '.$title_default) !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
