@extends('template.default')
@include('__partial.select2')

@section('content')
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title"><i class="fa fa-list-alt"></i> Daftar Pengajuan Beasiswa</h3>
        </div>
        <form method="POST">@csrf
            <div class="card-body">
                {!! FormInputSelect('periode','Pilih Periode',true, true, $list_periode, session()->get('periode')) !!}
            </div>
        </form>
    </div>

    @if(session()->has('periode'))
    @include('__partial.datatable')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> Daftar Pengajuan Beasiswa - {{ $periode->nm_periode_beasiswa }}</h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link {{ ($status=='Diajukan'?'active':null) }}" href="{{ route('validasi.pengajuan_beasiswa') }}">Diajukan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status=='Disetujui'?'active':null) }}" href="{{ url(route('validasi.pengajuan_beasiswa').'?status=Disetujui') }}">Disetujui</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($status=='Ditolak'?'active':null) }}" href="{{ url(route('validasi.pengajuan_beasiswa').'?status=Ditolak') }}">Ditolak</a>
                </li>
            </ul>
            <div class="mt-4">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table-data">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal Ajuan</th>
                            <th class="text-center">Umur Ajuan<br>(Hari)</th>
                            @if($status!='Diajukan')
                                <th>Tanggal Verifikasi</th>
                                <th>Status Terbaru</th>
                                <th>Keterangan</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data AS $no=> $each_data)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{!! $each_data->nm_pd.'<br>('.$each_data->nim.')' !!}</td>
                                <td>{{ tglWaktuIndonesia($each_data->waktu_diajukan) }}</td>
                                <td class="text-center">{{ $each_data->umur_ajuan }}</td>
                                @if($status!='Diajukan')
                                    <td>{{ tglWaktuIndonesia($each_data->wkt_selesai_ver) }}</td>
                                    <td class="text-center">{{ config('mp.data_master.status_periksa.'.$each_data->status_periksa) }}</td>
                                    <td>{{ $each_data->ket_periksa }}</td>
                                @endif
                                <td>
                                    @if($status!='Diajukan')
                                        <a href="{{ route('validasi.pengajuan_beasiswa.detail',Crypt::encrypt($each_data->id_ver_daftar_beasiswa)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                                    @else
                                        <a href="{{ route('validasi.pengajuan_beasiswa.detail',Crypt::encrypt($each_data->id_ver_daftar_beasiswa)) }}" class="btn btn-flat btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Validasi Ajuan"><i class="fa fa-check-square-o"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $('#periode').on('change',function () {
                this.form.submit();
            });
        });
    </script>
@endpush
