@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title"><i class="fa fa-list"></i> Validasi Pengajuan RPS Ketua Program Studi - {{ $prodi->prodi }}</h3>
        </div>
        <div class="card-body">
            @if(count($cari_role)>0)
                <div class="alert alert-info">
                    Anda adalah Admin Prodi
                </div>
                
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link {{ ($status=='Diajukan'?'active':null) }}" href="{{ route('validasi.rps_admin_prodi') }}">Diajukan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($status=='Disetujui'?'active':null) }}" href="{{ url(route('validasi.rps_admin_prodi').'?status=Disahkan') }}">Disahkan</a>
                    </li>
            
                </ul>
                <div class="mt-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="table-data">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dosen Pengaju</th>
                                <th>Mata Kuliah</th>
                                <th>Jenis MK</th>
                                <th>SKS MK</th>
                                <th>Waktu Validasi</th>
                                @if($status!='Disahkan')
                                <th>Status Validasi</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data AS $no=> $each_data)
                                <tr>
                                    <td>{{ $no+1 }}</td>
                                    <td>{!! $each_data->nm_sdm !!}</td>
                                    <td>{{ $each_data->nm_mk }}</td>
                                    <td>{{ config('mp.data_master.jenis_matkul.'.$each_data->jns_mk) }}</td>
                                    <td>{{ $each_data->sks_mk }}</td>
                                    <td>{{ tglWaktuIndonesia($each_data->wkt_ajuan) }}</td>
                                    @if($status!='Disahkan')
                                    <td class="text-center">{!! '<span class="badge badge-info">'.config('mp.data_master.status_periksa.'.$each_data->status_periksa).' oleh '.config('mp.data_master.level_verifikasi_prodi.'.$each_data->level_ver).'</span>' !!}</td>
                                    @endif
                                    <td>
                                        @if($status!='Diajukan')
                                        <a href="{{ route('pelaksanaan_pendidikan.pengajaran.pd_show',Crypt::encrypt($each_data->id_mk)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                                        @else
                                        <form class="validasi_form" action="{{ route('validasi.rps_admin_prodi.sah',Crypt::encrypt($each_data->id_ver_ajuan)) }}" method="POST">
                                        <a href="{{ route('validasi.rps_tpmps.detail',Crypt::encrypt($each_data->id_ver_ajuan)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" value="Y">
                                        <button type="submit" class="btn btn-flat btn-sm btn-warning"><i class="fas fa-check-square"></i></button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">
                    Maaf, anda tidak memiliki hak akses ini karena anda bukan termasuk Peer Group pada Program Studi {{ $prodi->prodi }}
                </div>
            @endif
        </div>
    </div>
@endsection
