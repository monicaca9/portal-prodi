@extends('template.default')


@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"> </i> Sisa Waktu Pentusunan TA - {{ $profil->nm_pd.' ('.$profil->nim.')' }}</h3>
    </div>

    <div class="card-body">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Biodata Mahasiswa</h3>
            </div>
            <div class="card-body" style="margin: 0;padding: 0">
                <div class="row">
                    <div class="col-sm-3 text-center mt-4">
                        @if (is_null($profil->id_blob))
                        <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                        @else
                        <?php $foto = DB::table('dok.large_object')
                            ->where('id_blob', $profil->id_blob)
                            ->first();
                        ?>
                        <img src="data:{{ $foto->mime_type }};base64,{{ stream_get_contents($foto->blob_content) }}"
                            width="200" alt="foto">
                        @endif
                    </div>
                    <!-- end of div class="col-sm-3 text-center mt-4 -->

                    <div class="col-sm-9">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Lengkap', $profil->nm_pd)!!}
                                {!! tableRow('NPM', $profil->nim)!!}
                                {!! tableRow('Program Studi', $profil->prodi)!!}
                                {!! tableRow('IPK Terakhir', $profil->ipk . ' (Semester: ' . $profil->nm_smt . ')') !!}
                                {!! tableRow('Tempat/Tanggal Lahir', $profil->tmpt_lahir . ', ' . tglIndonesia($profil->tgl_lahir)) !!}
                                {!! tableRow('No. HP', $profil->tlpn_hp)!!}
                                {!! tableRow('Status', $profil->nm_stat_mhs)!!}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        @if(!is_null($jangka_wkt_ta))
        @php
        $sisa_hari = $jangka_wkt_ta->sisa_hari;
        $total_hari = max($jangka_wkt_ta->total_hari, 1); // Hindari pembagian nol
        $tgl_selesai = $jangka_wkt_ta->tgl_selesai; // Ambil tanggal selesai

        if ($tgl_selesai) {
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
                <h3 class="card-title">Detail Data Tugas Akhir</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Judul Tugas Akhir ', $data_tugas_akhir->firstWhere('judul_akt_mhs', '!=', null)->judul_akt_mhs ?? 'Judul tidak ditemukan') !!}
                        {!! tableRow('Tenggat Waktu Penyusunan TA ', tglIndonesia($jangka_wkt_ta->tgl_batas_penyusunan??null)) !!}
                        @foreach($data_tugas_akhir as $each_data_ta)
                        {!! tableRow('Tanggal Seminar ' . $each_data_ta->nm_jns_seminar, tglIndonesia($each_data_ta->tgl_mulai??null)) !!}
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection