@extends('template.default')
@if(isset($app_kode))
    @include('__partial.datatable')
@endif

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR MENU</h3>
            @if(isset($app_kode))
            <div class="card-tools">
                @if(check_akses('manajemen_akses.menu.tambah'))
                    <a href="{{ (route('manajemen_akses.menu.tambah')).'?app_kode='.$app_kode }}" class="btn btn-primary btn-sm btn-flat" data-toggle="tooltip" data-placement="top" title="Tambah Menu">
                        <i class="fa fa-plus"></i> Tambah Menu</a>
                @endif
            </div>
            @endif
        </div><!-- /.card-header -->
        <div class="card-body">
            <form action="" class="form-horizontal form-inside">
            {!! FormInputSelect('app_kode','Menu dari Aplikasi',true,true,$aplikasi, (isset($app_kode)?$app_kode:null)) !!}
            </form>
            @if(isset($app_kode))
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
                                    {!! buttonEdit('manajemen_akses.menu.ubah',Crypt::encrypt($each_data->id_menu),'Ubah Menu') !!}
                                    {!! buttonDelete('manajemen_akses.menu.delete',Crypt::encrypt($each_data->id_menu),'Hapus Menu') !!}
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
            $('#app_kode').on('change',function() {
                var role = $(this).val();
                if(role!='') {
                    $('form.form-inside').submit();
                } else {
                    window.location.href = "{{ route('manajemen_akses.menu') }}";
                }
            });
        })
    </script>
@endpush
