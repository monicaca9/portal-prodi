@extends('template.default')
@include('__partial.datatable')

@section('content')
<div class="card">
    <div class="card-header bg-dark">
        <h3 class="card-title"><i class="fa fa-list"></i> Daftar Riwayat Ajuan Seminar</h3>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Diajukan'?'active':null) }}" href="{{ route('pendaftaran_seminar.daftar_ajuan_riwayat') }}">Diajukan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Disetujui'?'active':null) }}" href="{{ url(route('pendaftaran_seminar.daftar_ajuan_riwayat').'?status=Disetujui') }}">Disetujui</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditolak'?'active':null) }}" href="{{ url(route('pendaftaran_seminar.daftar_ajuan_riwayat').'?status=Ditolak') }}">Ditolak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($status=='Ditangguhkan'?'active':null) }}" href="{{ url(route('pendaftaran_seminar.daftar_ajuan_riwayat').'?status=Ditangguhkan') }}">Ditangguhkan</a>
            </li>
        </ul>
        <div class="mt-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Seminar</th>
                            <th>Tanggal Ajuan</th>
                            <th class="text-center">Umur Ajuan<br>(Hari)</th>
                            @if($status!='Diajukan')
                            <th>Tanggal Verifikasi</th>
                            <th>Status Terbaru</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data AS $no=> $each_data)
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>{!! $each_data->nm_jns_seminar !!}</td>
                            <td>{{ tglWaktuIndonesia($each_data->wkt_ajuan) }}</td>
                            <td class="text-center">{{ $each_data->umur_ajuan }}</td>
                            @if($status!='Diajukan')
                            <td>{{ tglWaktuIndonesia($each_data->wkt_selesai_ver) }}</td>
                            <td class="text-center">{{ config('mp.data_master.status_ajuan.'.$each_data->stat_ajuan) }}</td>

                            @endif
                            <td>
                                <a href="{{ route('pendaftaran_seminar.detail_riwayat',Crypt::encrypt($each_data->id_ajuan_pdm_seminar)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('pendaftaran_seminar')) !!}
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#periode').on('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush