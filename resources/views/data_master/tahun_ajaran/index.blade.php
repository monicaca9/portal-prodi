@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas ion-md-time"></i> TAHUN AJARAN</h3>
            <div class="card-tools">
                {!! buttonAdd('data_master.tahun_ajaran.tambah','Tambah Tahun Ajaran Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA TAHUN AJARAN</th>
                        <th>STATUS</th>
                        <th>PERIODE WAKTU</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_thn_ajaran }}</td>
                            <td>
                                @if($each_data->a_periode_aktif==1)
                                    <a href="{{ route('data_master.tahun_ajaran.ubah_aktif',Crypt::encrypt($each_data->id_thn_ajaran)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                @else
                                    <a href="{{ route('data_master.tahun_ajaran.ubah_aktif',Crypt::encrypt($each_data->id_thn_ajaran)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                @endif
                            </td>
                            <td>{{ tglIndonesia($each_data->tgl_mulai).' - '.tglIndonesia($each_data->tgl_selesai) }}</td>
                            <td>
                                {!! buttonEdit('data_master.tahun_ajaran.ubah',$each_data->id_thn_ajaran,'Ubah Tahun Ajaran') !!}
                                {!! buttonDelete('data_master.tahun_ajaran.delete',$each_data->id_thn_ajaran,'Hapus Tahun Ajaran') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
