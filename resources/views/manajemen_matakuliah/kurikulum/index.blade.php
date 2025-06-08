@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas ion-ios-list"></i> KURIKULUM</h3>
            <div class="card-tools">
                {!! buttonAdd('kurikulum.tambah','Tambah Kurikulum Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>KURIKULUM</th>
                        <th>&Sigma; MATAKULIAH</th>
                        <th>SEMESTER AWAL DIGUNAKAN</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_kurikulum }}</td>
                            <td>{{ count($each_data->matkul).' Matakuliah' }}</td>
                            <td>{{ $each_data->semester->nm_smt }}</td>
                            <td>
                                @if($each_data->a_digunakan==1)
                                    <a href="{{ route('kurikulum.ubah_aktif',Crypt::encrypt($each_data->id_smt)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                @else
                                    <a href="{{ route('kurikulum.ubah_aktif',Crypt::encrypt($each_data->id_smt)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                @endif
                            </td>
                            <td>
                                {!! buttonEdit('kurikulum.ubah',$each_data->id_smt,'Ubah Semester') !!}
                                {!! buttonDelete('kurikulum.delete',$each_data->id_smt,'Hapus Semester') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
