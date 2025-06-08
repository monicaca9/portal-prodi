@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> Daftar Konsentrasi Prodi {{$prodi->prodi}} </h3>
        <div class="card-tools">
            {!! buttonAdd('konsentrasi_prodi.tambah','Tambah Konsentrasi Prodi') !!}
        </div>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama Konsentrasi Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data AS $no=> $each_data)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $each_data->nm_konsentrasi_prodi }}</td>
                        <td>
                            {!! buttonEdit('konsentrasi_prodi.ubah',Crypt::encrypt($each_data->id_konsentrasi_prodi),'Ubah Konsentrasi Prodi') !!}
                            {!! buttonDelete('konsentrasi_prodi.delete',Crypt::encrypt($each_data->id_konsentrasi_prodi),'Hapus Konsentrasi Prodi') !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection