@extends('template.default')
@if(isset($angkatan))
@include('__partial.datatable')
@endif

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> Distribusi Dosen Pembimbing dan Penguji Mahasiswa - {{ $prodi->prodi }}</h3>
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
            {!! FormInputSelect('angkatan','Angkatan',true,true,$list_angkatan,(isset($angkatan)?$angkatan:null)) !!}
        </form>
        @if(isset($angkatan))
        <hr>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="table-data">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama/NPM Mahasiswa</th>
                        <th>IPK</th>
                        <th>Jumlah SKS</th>
                        @foreach($jenis_seminar AS $judul_jns_seminar)
                        <th>{{ $judul_jns_seminar->nm_jns_seminar }}</th>
                        @endforeach
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_peserta AS $no=>$each_data)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $each_data->nm_pd }}</td>
                        <td>{{ $each_data->ipk }}</td>
                        <td>{{ !is_null($each_data->total_sks)?$each_data->total_sks.' SKS':null }}</td>
                        @foreach($jenis_seminar AS $each_jns_seminar)
                        <?php
                        $cari_pembimbing = \DB::SELECT("
                                        SELECT
                                            peran.id_peran_seminar,
                                            CASE WHEN tsdm.nm_sdm IS NOT NULL THEN CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') END AS nm_dosen,
                                            peran.peran,
                                            peran.urutan,
                                            peran.nm_pembimbing_luar_kampus,
                                            peran.nm_penguji_luar_kampus,
                                            CONCAT(peran.nm_pemb_lapangan,' (',peran.jabatan,')') AS nm_pemb_lapangan
                                        FROM kpta.peran_seminar AS peran
                                        LEFT JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm=peran.id_sdm
                                        WHERE peran.soft_delete=0
                                        AND peran.id_jns_seminar=" . $each_jns_seminar->id_jns_seminar . "
                                        AND peran.id_reg_pd = '" . $each_data->id_reg_pd . "'
                                        AND peran.a_ganti=0
                                        AND peran.a_aktif=1
                                        ORDER BY peran.peran ASC, peran.urutan ASC
                                    ");

                        ?>
                        @if(count($cari_pembimbing)>0)
                        <td style="margin: 0;padding: 0">
                            <table>
                                <tbody>
                                    @foreach($cari_pembimbing AS $each_pembimbing)
                                    {!! tableRow(
                                    config('mp.data_master.peran_seminar.'.$each_pembimbing->peran)
                                    . (!is_null($each_pembimbing->urutan) ? ' ke-'.$each_pembimbing->urutan : ''),
                                    is_null($each_pembimbing->nm_dosen)
                                    ? (!empty($each_pembimbing->nm_penguji_luar_kampus)
                                    ? $each_pembimbing->nm_penguji_luar_kampus
                                    : (!empty($each_pembimbing->nm_pembimbing_luar_kampus)
                                    ? $each_pembimbing->nm_pembimbing_luar_kampus
                                    : $each_pembimbing->nm_pemb_lapangan))
                                    : $each_pembimbing->nm_dosen
                                    ) !!}
                                    @endforeach

                                    @if($each_data->status_terbaru=='AKTIF')
                                    <tr>
                                        <td colspan="3"><a href="{{ route('distribusi_dosen_mahasiswa.ubah',Crypt::encrypt(['id_pd'=>$each_data->id_pd,'id_jns_seminar'=>$each_jns_seminar->id_jns_seminar])) }}" class="btn btn-flat btn-xs btn-warning btn-block">Ubah data</a></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </td>
                        @else
                        <td>
                            @if($each_data->status_terbaru=='AKTIF')
                            <a href="{{ route('distribusi_dosen_mahasiswa.ubah',Crypt::encrypt(['id_pd'=>$each_data->id_pd,'id_jns_seminar'=>$each_jns_seminar->id_jns_seminar])) }}" class="btn btn-flat btn-xs btn-primary">Tambah data</a>
                            @else
                            <span class="badge badge-info">--Tidak ada data--</span>
                            @endif
                        </td>
                        @endif
                        @endforeach
                        <td>
                            @if(!is_null($each_data->ipk))
                            @if($each_data->status_terbaru=='AKTIF')
                            <button type="button" class="btn btn-xs btn-flat btn-primary" disabled>{{ $each_data->status_terbaru }}</button>
                            @elseif($each_data->status_terbaru=='Lulus')
                            <button type="button" class="btn btn-xs btn-flat btn-success" disabled>Sudah Lulus</button>
                            @else
                            <button type="button" class="btn btn-xs btn-flat btn-danger" disabled>Bukan mahasiswa aktif</button>
                            @endif
                            @else
                            @if($each_data->status_terbaru=='AKTIF')
                            <button type="button" class="btn btn-xs btn-flat btn-danger" disabled>Harus dikeluarkan</button>
                            @else
                            <button type="button" class="btn btn-xs btn-flat btn-danger" disabled>Bukan mahasiswa aktif</button>
                            @endif
                            @endif
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
    $(function() {
        $('#angkatan').on('change', function() {
            var role = $(this).val();
            if (role != '') {
                $('form.form-inside').submit();
            } else {
                window.location.href = "{{ route('distribusi_dosen_mahasiswa') }}";
            }
        });
    })
</script>
@endpush