@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR SEMINAR PRODI - {{ $prodi->prodi }}</h3>
        <div class="card-tools">
            {!! buttonAdd('daftar_seminar_prodi.create','Tambah Daftar Seminar Prodi') !!}
        </div>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" id="table-data">
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">Nama Jenis Seminar</th>
                        <!-- <th rowspan="2">Prodi</th> -->
                        <th colspan="2" class="text-center">Jumlah</th>
                        <th rowspan="2" class="text-center">Urutan</th>
                        <th rowspan="2">Syarat</th>
                        <th rowspan="2"> Kategori Nilai</th>
                        <th rowspan="2"> Distribusi Nilai (%)</th>
                        <th rowspan="2">Aksi</th>
                    </tr>
                    <tr>
                        <th>Pembimbing</th>
                        <th>Penguji</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data AS $no=> $each_data)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $each_data->nm_jns_seminar }}</td>
                        <!-- <td>{{ $each_data->prodi }}</td> -->
                        <td class="text-center">{{ $each_data->jmlh_pembimbing }}</td>
                        <td class="text-center">{{ $each_data->jmlh_penguji }}</td>
                        <td class="text-center">{{ $each_data->urutan }}</td>
                        <td class="text-center">
                            <a href="{{ route('daftar_seminar_prodi.detail',Crypt::encrypt($each_data->id_seminar_prodi)) }}" class="btn btn-flat btn-info btn-xs">{{ $each_data->total_syarat.' Syarat' }}</a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('daftar_seminar_prodi.detail_kategori',Crypt::encrypt($each_data->id_seminar_prodi)) }}" class="btn btn-flat btn-info btn-xs">{{ $each_data->total_kategori_nilai.' Kategori' }}</a>
                        </td>
                        <td class="text-center">
                            @php
                            $distribusiNilai = DB::SELECT("
                            SELECT * FROM distribusi_nilai 
                            WHERE id_seminar_prodi = '" . $each_data->id_seminar_prodi. "' 
                            AND soft_delete = 0
                            ");
                            @endphp

                            @if ($distribusiNilai)
                            <a href="{{ route('daftar_seminar_prodi.detail_distribusi_nilai', Crypt::encrypt($each_data->id_seminar_prodi)) }}"
                                class="btn btn-flat btn-xs btn-warning">
                                Ubah Data
                            </a>
                            @else
                            <a href="{{ route('daftar_seminar_prodi.detail_distribusi_nilai', Crypt::encrypt($each_data->id_seminar_prodi)) }}"
                                class="btn btn-flat btn-xs btn-primary">
                                Tambah Data
                            </a>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-between align-items-center">
                                {!! buttonEdit('daftar_seminar_prodi.ubah',Crypt::encrypt($each_data->id_seminar_prodi),'Ubah Seminar Prodi') !!}
                                {!! buttonDelete('daftar_seminar_prodi.delete',Crypt::encrypt($each_data->id_seminar_prodi),'Hapus Seminar Prodi') !!}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection