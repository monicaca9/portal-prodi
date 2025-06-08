@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-sign-in"></i> JENIS PENERIMAAN</h3>
            <div class="card-tools">
                {!! buttonAdd('jenis_penerimaan.tambah','Tambah Jenis Penerimaan Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA JENIS PENERIMAAN</th>
                        <th>SINGKATAN</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_jalur_masuk }}</td>
                            <td>{{ $each_data->singkat_jalur }}</td>
                            <td>
                                {!! buttonEdit('jenis_penerimaan.ubah',Crypt::encrypt($each_data->id_jalur_masuk),'Ubah Jenis Penerimaan') !!}
                                {!! buttonDelete('jenis_penerimaan.delete',Crypt::encrypt($each_data->id_jalur_masuk),'Hapus Jenis Penerimaan') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
