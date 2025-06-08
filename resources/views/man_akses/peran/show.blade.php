@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-users"></i> DETAIL PERAN</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                {!! tableRow('ID Peran',$data->id_peran) !!}
                {!! tableRow('Nama Peran',$data->nm_peran) !!}
                {!! tableRow('Apakah Perlu SK?',($data->a_perlu_sk==1?'YA':'TIDAK')) !!}
                </tbody>
            </table>
            <hr>
            <h5>DAFTAR MENU</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>PARENT MENU</th>
                        <th>NAMA MENU</th>
                        <th>NAMA FILE</th>
                        <th>APLIKASI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($menu AS $no => $each_menu)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_menu->parent_menu }}</td>
                            <td>{{ $each_menu->nm_menu }}</td>
                            <td>{{ $each_menu->nm_file }}</td>
                            <td>{{ $each_menu->nm_aplikasi }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('manajemen_akses.peran')) !!}
        </div>
    </div>
@endsection
