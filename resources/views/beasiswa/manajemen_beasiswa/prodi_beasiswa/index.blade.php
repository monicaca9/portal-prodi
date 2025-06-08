@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-building"></i> Daftar Prodi Pilihan - {{ $periode->nm_periode_beasiswa }}</h3>
            <div class="card-tools">
                @if(check_akses('manajemen_beasiswa.detail.prodi_beasiswa.tambah'))
                <a href="{{ route('manajemen_beasiswa.detail.prodi_beasiswa.tambah',Crypt::encrypt($periode->id_periode_beasiswa)) }}" class="btn btn-primary btn-sm btn-flat" data-toggle="tooltip" data-placement="top" title="Tambah Pilihan Prodi">
                    <i class="fa fa-plus"></i> Tambah Pilihan Prodi</a>
                @endif
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA PRODI</th>
                        <th>KUOTA</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_daftar_prodi)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_daftar_prodi->nm_prodi }}</td>
                            <td>{{ $each_daftar_prodi->kuota_terima_beasiswa }}</td>
                            <td>
                                {!! buttonEditMultipleId('manajemen_beasiswa.detail.prodi_beasiswa.ubah',[Crypt::encrypt($periode->id_periode_beasiswa),Crypt::encrypt($each_daftar_prodi->id_prodi_beasiswa)],'Edit Pilihan Prodi') !!}
                                {!! buttonDeleteMultipleId('manajemen_beasiswa.detail.prodi_beasiswa.delete',[Crypt::encrypt($periode->id_periode_beasiswa),Crypt::encrypt($each_daftar_prodi->id_prodi_beasiswa)],'Hapus Pilihan Prodi') !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('manajemen_beasiswa.detail',Crypt::encrypt($periode->id_periode_beasiswa))) !!}
        </div>
    </div>
@endsection
