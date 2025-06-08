@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> Peer Group</h3>
            <div class="card-tools">
                {!! buttonAdd('peer_group.tambah','Tambah Peer Group Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Program Studi</th>
                        <th>Nama Peer Group</th>
                        <th>Bidang</th>
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
                            <td>{{ $each_data->nm_peer_group }}</td>
                            <td>
                                <a href="{{ route('peer_group.bidang',Crypt::encrypt($each_data->id_peer_group)) }}" class="btn btn-flat btn-info btn-sm btn-block"><i class="fas fa-folder-plus"></i> Tambah Bidang</a>
                                @if(!is_null($each_data->nm_bidang))
                                    <br>
                                    <?php $bidang = explode('; ',$each_data->nm_bidang); ?>
                                    <ul>
                                        @foreach($bidang AS $each_bidang)
                                        <li>{{ $each_bidang }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('peer_group.anggota',Crypt::encrypt($each_data->id_peer_group)) }}" class="btn btn-flat btn-info btn-sm btn-block"><i class="fas fa-folder-plus"></i> Tambah Anggota</a>
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
                                    <a href="{{ route('peer_group.ubah_aktif',Crypt::encrypt($each_data->id_peer_group)) }}" class="btn btn-flat btn-success btn-sm btn-block">AKTIF</a>
                                @else
                                    <a href="{{ route('peer_group.ubah_aktif',Crypt::encrypt($each_data->id_peer_group)) }}" class="btn btn-flat btn-danger btn-sm btn-block">TIDAK AKTIF</a>
                                @endif
                            </td>
                            <td>
                                {!! buttonEdit('peer_group.ubah',Crypt::encrypt($each_data->id_peer_group),'Ubah Peer Group') !!}
                                {!! buttonDelete('peer_group.delete',Crypt::encrypt($each_data->id_peer_group),'Hapus Peer Group') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
