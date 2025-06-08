@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users"></i> Bidang Peer Group {{ $data->nm_peer_group }}</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Bidang</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bidang AS $no => $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->kelbid->nm_kel_bidang }}</td>
                            <td>
                                {!! buttonDeleteMultipleId('peer_group.bidang.delete',[Crypt::encrypt($data->id_peer_group),Crypt::encrypt($each_data->id_bidang_peer)],'Hapus Bidang Peer Group') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="card-body">
                <div class="card">
                    <form action="{{ route('peer_group.bidang.simpan',Crypt::encrypt($data->id_peer_group)) }}" method="post">
                        @csrf
                        <div class="card-header bg-black">
                            <h3 class="card-title"><i class="fas fa-th-list"></i> Tambah Bidang</h3>
                            <div class="card-tools">
                                <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id_peer_group" value="{{ $data->id_peer_group }}">
                            {!! FormInputSelect('id_kel_bidang','Kelompok Bidang',true,true,$kelompok_bidang) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('peer_group')) !!}
        </div>
    </div>
@endsection
