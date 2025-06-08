@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-tag"></i> Anggota Tim Penjaminan Mutu Program Studi {{ $data->prodi->nm_lemb }}</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th>Status Keanggotaan</th>
                        <th>Status Keaktifan</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($anggota AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->dosen->nm_sdm.' ('.$each_data->dosen->nidn.')' }}</td>
                            <td>
                                @if($each_data->a_ketua==1)
                                    <a href="{{ route('tim_penjamin_mutu.anggota.ubah_ketua',[Crypt::encrypt($data->id_tpmps),Crypt::encrypt($each_data->id_ang_tpmps)]) }}" class="btn btn-flat btn-xs btn-success" data-toggle="tooltip" data-placement="top" data-title="Ubah menjadi Anggota?">Ketua</a>
                                @else
                                    <a href="{{ route('tim_penjamin_mutu.anggota.ubah_ketua',[Crypt::encrypt($data->id_tpmps),Crypt::encrypt($each_data->id_ang_tpmps)]) }}" class="btn btn-flat btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-title="Ubah menjadi Ketua?">Anggota</a>
                                @endif
                            </td>
                            <td>
                                @if($each_data->a_aktif==1)
                                    <a href="{{ route('tim_penjamin_mutu.anggota.ubah_status',[Crypt::encrypt($data->id_tpmps),Crypt::encrypt($each_data->id_ang_tpmps)]) }}" class="btn btn-flat btn-xs btn-success" data-toggle="tooltip" data-placement="top" data-title="Ubah menjadi Tidak Aktif?">Aktif</a>
                                @else
                                    <a href="{{ route('tim_penjamin_mutu.anggota.ubah_status',[Crypt::encrypt($data->id_tpmps),Crypt::encrypt($each_data->id_ang_tpmps)]) }}" class="btn btn-flat btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-title="Ubah menjadi Aktif?">Tidak Aktif</a>
                                @endif
                            </td>
                            <td>
                                {!! buttonDeleteMultipleId('tim_penjamin_mutu.anggota.delete',[Crypt::encrypt($data->id_tpmps),Crypt::encrypt($each_data->id_ang_tpmps)],'Hapus Anggota Peer Group') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="card-body">
                <div class="card">
                    <form action="{{ route('tim_penjamin_mutu.anggota.simpan',Crypt::encrypt($data->id_tpmps)) }}" method="post">
                        @csrf
                        <div class="card-header bg-black">
                            <h3 class="card-title"><i class="fas fa-th-list"></i> Tambah Anggota</h3>
                            <div class="card-tools">
                                <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id_tpmps" value="{{ $data->id_tpmps }}">
                            {!! FormInputSelect('id_sdm','Anggota',true,true,$kelompok_anggota) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('tim_penjamin_mutu')) !!}
        </div>
    </div>
@endsection
