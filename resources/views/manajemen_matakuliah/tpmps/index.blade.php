@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-tag"></i> Tim Penjaminan Mutu</h3>
            <div class="card-tools">
                {!! buttonAdd('tim_penjamin_mutu.tambah','Tambah Tim Penjaminan Mutu Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tim Penjaminan Mutu</th>
                        <th>Semester mulai berlaku</th>
                        <th>Bukti</th>
                        <th>Anggota</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->nm_prodi }}</td>
                            <td>{{ $each_data->nm_smt }}</td>
                            <td>
                                <b>No. SK:</b> {{ $each_data->no_sk }}
                                <br>
                                <b>Tgl. SK:</b> {{ tglIndonesia($each_data->tgl_sk) }}
                                <br>
                                <a href="{{ route('dokumen.preview',Crypt::encrypt($each_data->id_dok)) }}" class="btn btn-info btn-xs btn-flat">Download SK</a>
                            </td>
                            <td>
                                <a href="{{ route('tim_penjamin_mutu.anggota',Crypt::encrypt($each_data->id_tpmps)) }}" class="btn btn-flat btn-info btn-sm btn-block"><i class="fas fa-folder-plus"></i> Tambah Anggota</a>
                                @if(!is_null($each_data->nm_anggota))
                                    <br>
                                    <?php $anggota = explode('; ',$each_data->nm_anggota); ?>
                                    <ul>
                                        @foreach($anggota AS $each_anggota)
                                        <li>{{ $each_anggota }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                @if($each_data->a_aktif==1)
                                    <a href="{{ route('tim_penjamin_mutu.ubah_aktif',Crypt::encrypt($each_data->id_tpmps)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                @else
                                    <a href="{{ route('tim_penjamin_mutu.ubah_aktif',Crypt::encrypt($each_data->id_tpmps)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                @endif
                            </td>
                            <td>
                                {!! buttonEdit('tim_penjamin_mutu.ubah',Crypt::encrypt($each_data->id_tpmps),'Ubah Tim Penjaminan Mutu') !!}
                                {!! buttonDelete('tim_penjamin_mutu.delete',Crypt::encrypt($each_data->id_tpmps),'Hapus Tim Penjaminan Mutu') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
