@extends('template.default')
@include('__partial.datatable_yajra')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas ion-md-school"></i> DAFTAR SEKOLAH</h3>
            <div class="card-tools">
                {!! buttonAdd('data_master.daftar_sekolah.tambah','Tambah Data Sekolah Baru') !!}
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA SEKOLAH</th>
                        <th>NISN</th>
                        <th>PROVINSI</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
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
                ajax: '{!! route('data_master.daftar_sekolah.detail') !!}',
                columns: [
                    { data: 'rownum', name: 'rownum', searchable: false },
                    { data: 'nm_slta', name: 't1.nm_slta' },
                    { data: 'nisn', name: 't1.nisn' },
                    { data: 'nm_wil', name: 't2.nm_wil' },
                    { data: 'status', name: 'status', searchable: false, orderable: false },
                    { data: 'aksi', name: 'aksi', searchable: false, orderable: false },
                ]
            });
        });
    </script>
@endpush
