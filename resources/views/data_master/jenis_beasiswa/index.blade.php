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
                        <th>KODE JENIS BEASISWA</th>
                        <th>SUMBER DANA</th>
                        <th>NAMA JENIS BEASISWA</th>
                        <th>PESERTA DIDIK?</th>
                        <th>DOSEN/TENDIK?</th>
                        <th>NON CA?</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $each_data->id_jns_beasiswa }}</td>
                            <td>{{ $each_data->SumberDana->nm_sumber_dana }}</td>
                            <td>{{ $each_data->nm_jns_beasiswa }}</td>
                            <td>{!! $each_data->u_pd==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>{!! $each_data->u_ptk==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>{!! $each_data->u_non_ca==1?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-close text-danger"></i>' !!}</td>
                            <td>
                                {!! buttonEdit($route_default.'.ubah',Crypt::encrypt($each_data->id_jns_beasiswa),'Ubah '.$title_default) !!}
                                {!! buttonDelete($route_default.'.delete',Crypt::encrypt($each_data->id_jns_beasiswa),'Hapus '.$title_default) !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
