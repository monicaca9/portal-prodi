@extends('template.default')
@if(isset($peran_pilih))
    @include('__partial.datatable')
@endif

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR HAK MENU PERAN</h3>
            @if(isset($peran_pilih))
                <div class="card-tools">
                    @if(check_akses('manajemen_akses.hak_akses.tambah'))
                        <a href="{{ (route('manajemen_akses.hak_akses.tambah')).'?peran_pilih='.$peran_pilih }}" class="btn btn-primary btn-sm btn-flat" data-toggle="tooltip" data-placement="top" title="Tambah Menu">
                            <i class="fa fa-plus"></i> Tambah Hak Akses Menu</a>
                    @endif
                </div>
            @endif
        </div><!-- /.card-header -->
        <div class="card-body">
            <form action="" class="form-horizontal form-inside">
                {!! FormInputSelect('peran_pilih','Hak Menu dari Peran',true,true,$list_peran,(isset($peran_pilih)?$peran_pilih:null)) !!}
            </form>
            @if(isset($peran_pilih))
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA MENU</th>
                            <th>NAMA FILE</th>
                            <th>AKSI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data AS $no=>$each_data)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_data->nm_menu }}</td>
                                <td>{{ $each_data->nm_file }}</td>
                                <td>
                                    {!! buttonEdit('manajemen_akses.hak_akses.ubah',Crypt::encrypt($each_data->id_menu),'Ubah Hak Menu') !!}
                                    {!! buttonDelete('manajemen_akses.hak_akses.delete',Crypt::encrypt($each_data->id_menu),'Hapus Hak Menu') !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            $('#peran_pilih').on('change',function() {
                var role = $(this).val();
                if(role!='') {
                    $('form.form-inside').submit();
                } else {
                    window.location.href = "{{ route('manajemen_akses.hak_akses') }}";
                }
            });
        })
    </script>
@endpush
