@extends('template.default')
@include('__partial.datatable_yajra')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-user"></i> DAFTAR MAHASISWA</h3>
            <div class="card-tools">
{{--                {!! buttonAdd('manajemen_akses.peran.tambah','Tambah Peran') !!}--}}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Angkatan</th>
                        <th>NIM</th>
                        <th>Asal Prodi</th>
                        <th>IPK</th>
                        <th>Total SKS</th>
                        <th>Status</th>
                        <th>Status Sinkronisasi</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            $('#table-data').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('sdm.mahasiswa.table') !!}',
                columns: [
                    { data: 'rownum', name: 'rownum', searchable: false },
                    { data: 'nm_pd', name: 't1.nm_pd' },
                    { data: 'id_thn_ajaran', name: 't7.id_thn_ajaran' },
                    { data: 'nim', name: 't2.nim' },
                    { data: 'prodi', name: 'prodi' },
                    { data: 'ipk', name: 't8.ipk' },
                    { data: 'total_sks', name: 't8.total_sks' },
                    { data: 'status', name: 'status', searchable: false, orderable: false },
                    { data: 'sync', name: 'sync', searchable: false, orderable: false },
                    { data: 'aksi', name: 'aksi', searchable: false, orderable: false },
                ]
            });
        });
    </script>
@endpush
