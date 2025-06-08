@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> Riwayat Pengajaran</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <form action="" class="form-horizontal form-inside">
                {!! FormInputSelect('periode_smt','Periode',$detail_smt,(isset($smt)?$smt:null)) !!}
            </form>
            <hr>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th>Kelas</th>
                        <th>SKS</th>
                        <th>Jenis MK</th>
                        <th>AKSI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data AS $no=>$each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{{ $each_data->kode_mk }}</td>
                            <td>{{ $each_data->nm_mk }}</td>
                            <td>{{ $each_data->nm_kls }}</td>
                            <td>{{ $each_data->sks_mk }}</td>
                            <td>{{ config('mp.data_master.jenis_matkul.'.$each_data->jns_mk) }}</td>
                            <td>
                                {!! buttonShow('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($each_data->id_mk),'RPS MK '.$each_data->nm_mk) !!}
                                
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
            $('#periode_smt').on('change',function() {
                var role = $(this).val();
                if(role!='') {
                    $('form.form-inside').submit();
                } else {
                    window.location.href = "{{ route('pelaksanaan_pendidikan.pengajaran') }}";
                }
            });
        })
    </script>
@endpush
