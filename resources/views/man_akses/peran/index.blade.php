@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-users"></i> DAFTAR PERAN</h3>
            <div class="card-tools">
                {!! buttonAdd('manajemen_akses.peran.tambah','Tambah Peran') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>ID PERAN</th>
                        <th>NAMA PERAN</th>
                        <th>DETAIL</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_data)
                        <tr>
                            <td>{{ $each_data->id_peran }}</td>
                            <td>{{ $each_data->nm_peran }}</td>
                            <td><a href="{{ route('manajemen_akses.peran.detail',Crypt::encrypt($each_data->id_peran)) }}" class="btn btn-flat btn-xs btn-info">Detail Peran</a></td>
                            <td>
                                @if($each_data->id_peran>=3000)
                                    {!! buttonEdit('manajemen_akses.peran.ubah',Crypt::encrypt($each_data->id_peran),'Ubah Peran') !!}
                                    {!! buttonDelete('manajemen_akses.peran.delete',Crypt::encrypt($each_data->id_peran),'Hapus Peran') !!}
                                @else
                                    <button class="btn btn-flat btn-info btn-xs btn-block" disabled>SYNC PDDIKTI</button>
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
