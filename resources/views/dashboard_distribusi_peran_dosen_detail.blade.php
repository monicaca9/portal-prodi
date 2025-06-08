@extends('template.default')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Detail Data {{ $data_seminar_prodi->nm_jns_seminar }} - {{ $profil->nm_pd }}</h3>
    </div>
    <div class="card-body">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Biodata Mahasiswa</h3>
            </div>
            <div class="card-body" style="margin: 0;padding: 0">
                <div class="row">
                    <div class="col-sm-3 text-center mt-4">
                        @if(is_null($profil->id_blob))
                        <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                        @else
                        <?php $foto = DB::table('dok.large_object')->where('id_blob', $profil->id_blob)->first(); ?>
                        <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                        @endif
                    </div>
                    <div class="col-sm-9">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Lengkap',$profil->nm_pd) !!}
                                {!! tableRow('NPM',$profil->nim) !!}
                                {!! tableRow('Homebase',$profil->prodi) !!}
                                {!! tableRow('IPK Terakhir',$profil->ipk.' (Semester: '.$profil->nm_smt.')') !!}
                                {!! tableRow('Tempat/Tanggal Lahir',($profil->tmpt_lahir.', '.tglIndonesia($profil->tgl_lahir))) !!}
                                {!! tableRow('No. HP',$profil->tlpn_hp) !!}
                                {!! tableRow('Status',$profil->nm_stat_mhs) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Detail Pembimbing dan Penguji</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
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
                                        AND peran.a_aktif=1
                                        AND peran.a_ganti=0
                                        AND peran.id_jns_seminar=" . $data_seminar_prodi->id_jns_seminar . "
                                        AND peran.id_reg_pd = '" . $data_reg_pd->id_reg_pd . "'
                                        ORDER BY peran.peran ASC, peran.urutan ASC
                                    ");
                        ?>
                        @if(count($cari_pembimbing)>0)
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
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($data_seminar_prodi->id_jns_seminar == 5 )
        @if(!empty($jangka_wkt_ta) && isset($jangka_wkt_ta->tgl_mulai))
        @php
        $sisa_hari = $jangka_wkt_ta->sisa_hari;
        $total_hari = max($jangka_wkt_ta->total_hari, 1);
        $tgl_selesai = $jangka_wkt_ta->tgl_selesai;

        if (isset($tgl_selesai)) {
        $hari_selesai = (strtotime($jangka_wkt_ta->tgl_batas_penyusunan) - strtotime($tgl_selesai)) / (60 * 60 * 24);
        $progress = min(max((1 - ($hari_selesai / $total_hari)) * 100, 0), 100);

        $warna = $hari_selesai >= 0 ? 'bg-success' : 'bg-danger';
        } else {
        $progress = min(max((1 - ($sisa_hari / $total_hari)) * 100, 0), 100);
        $warna = $sisa_hari > 5 ? 'bg-success' : ($sisa_hari > 0 ? 'bg-warning' : 'bg-danger');
        }
        @endphp

        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Sisa Waktu Penyusunan Tugas Akhir </h3>
            </div>
            <div class="card-body">
                <div class="progress">
                    <div class="progress-bar {{ $warna }} progress-bar-striped"
                        role="progressbar"
                        style="width: {{ $progress }}%;"
                        aria-valuenow="{{ $progress }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>
                <p class="mt-2">
                    @if ($tgl_selesai)
                    <span>
                        Tugas akhir selesai pada {{tglIndonesia($tgl_selesai) }}.
                    </span>
                    @elseif ($sisa_hari >= 0)
                    Tugas akhir Anda belum terselesaikan, tersisa {{ $sisa_hari }} hari lagi.
                    @else
                    <span>
                        Tugas akhir sudah melewati batas selama {{ abs($sisa_hari) }} hari.
                    </span>
                    @endif
                </p>
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Detail Data Seminar {{ $data_seminar_prodi->nm_jns_seminar }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Judul Tugas Akhir ', $data_tugas_akhir->firstWhere('judul_akt_mhs', '!=', null)->judul_akt_mhs ?? '-') !!}
                        @if (!empty($jangka_wkt_ta) && isset($jangka_wkt_ta->tgl_batas_penyusunan))
                        {!! tableRow('Tenggat Waktu Penyusunan TA ', tglIndonesia($jangka_wkt_ta->tgl_batas_penyusunan)) !!}
                        @else
                        {!! tableRow('Tenggat Waktu Penyusunan TA ', '-') !!}
                        @endif
                        @foreach($data_tugas_akhir as $each_data_ta)
                        {!! tableRow('Tanggal Seminar ' . $each_data_ta->nm_jns_seminar, tglIndonesia($each_data_ta->tgl_mulai)) !!}
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Detail Data Seminar {{ $data_seminar_prodi->nm_jns_seminar }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Judul',$data_daftar_seminar->judul_akt_mhs) !!}
                        {!! tableRow('Hari Seminar', config('mp.data_master.hari')[$data_daftar_seminar->hari] ?? '-') !!}
                        {!! tableRow('Tanggal Seminar',tglIndonesia($data_daftar_seminar->tgl_mulai)) !!}
                        {!! tableRow('Waktu Seminar', config('mp.data_master.waktu') [$data_daftar_seminar->waktu] ?? '-') !!}
                        {!! tableRow('Tempat Seminar',$data_daftar_seminar->nm_ruang . ' - ('.$data_daftar_seminar->nm_gedung.')') !!}
                    </tbody>
                </table>

            </div>
        </div>
        @endif
    </div>
    <div class="card-footer">
        {!! buttonBack(url()->previous()) !!}
    </div>
</div>
@endsection