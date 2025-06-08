@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')
@include('__partial.date')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.js" integrity="sha256-rPPF6R+AH/Gilj2aC00ZAuB2EKmnEjXlEWx5MkAp7bw=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.css" integrity="sha256-ejA/z0dc7D+StbJL/0HAnRG/Xae3yS2gzg0OAnIURC4=" crossorigin="anonymous">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var datakelender = [
            <?php
            foreach ($jadwal_seminar as $item) {
            ?> {
                    "title": "<?= $item['title']; ?>",
                    "jam": "<?= $item['jam']; ?>",
                    "start": new Date("<?= $item['start']; ?>").toISOString(),
                    "end": new Date("<?= $item['end']; ?>").toISOString(),
                    "pembimbing_penguji": "<?= addslashes($item['pembimbing_penguji']); ?>"
                },
            <?php
            }
            ?>
        ];

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 550,
            eventClick: function(info) {
                var eventObj = info.event;
                var stringTitle = eventObj.title.split(',');

                Swal.fire({
                    title: stringTitle[0],
                    html: " Seminar " + stringTitle[1] + "<br> Gedung :" + stringTitle[2] + " <br> Ruang :" + stringTitle[3] +
                        "<br>Jam : " + eventObj.extendedProps.jam +
                        "<br>" + eventObj.extendedProps.pembimbing_penguji,
                    showCloseButton: true,
                });
            },
            events: datakelender
        });

        calendar.render();
    });
</script>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> Pendaftaran Seminar
            {{ $data->SeminarProdi->jenisSeminar->nm_jns_seminar }}
        </h3>
    </div>
    <div class="card-body">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Biodata Pendaftar</h3>
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
                    <div class="col-sm-9">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Lengkap', $profil->nm_pd) !!}
                                {!! tableRow('NPM', $profil->nim) !!}
                                {!! tableRow('Homebase', $profil->prodi) !!}
                                {!! tableRow('IPK Terakhir', $profil->ipk . ' (Semester: ' . $profil->nm_smt . ')') !!}
                                {!! tableRow('Tempat/Tanggal Lahir', $profil->tmpt_lahir . ', ' . tglIndonesia($profil->tgl_lahir)) !!}
                                {!! tableRow('No. HP', $profil->tlpn_hp) !!}
                                {!! tableRow('Status', $profil->nm_stat_mhs) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if (in_array($data->status_validasi, [0, 3]))
        <form action="{{ route('pendaftaran_seminar.ubah', Crypt::encrypt($data->id_daftar_seminar)) }}"
            method="POST">
            @csrf
            @method('PUT')
            @endif
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Tanggal</h3>
                </div>
                <div class="card-body">
                    <div class="response"></div>

                    <div id='calendar'></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Seminar</h3>
                </div>
                <div class="card-body">
                       <h5>Info Pembimbing dan Penguji Seminar</h5>
                       <table class="table table-striped">
                        <tbody>
                            <?php
                            $jns_seminar = \DB::table('ref.jenis_seminar AS induk_jns')
                                ->join('ref.jenis_seminar AS jns', 'jns.id_induk_jns_seminar', '=', 'induk_jns.id_jns_seminar')
                                ->join('kpta.seminar_prodi AS seminar', 'jns.id_jns_seminar', '=', 'seminar.id_jns_seminar')
                                ->whereNull('jns.expired_date')
                                ->whereNull('induk_jns.expired_date')
                                ->where('seminar.soft_delete', 0)
                                ->where('seminar.id_seminar_prodi', $seminar->id_seminar_prodi)
                                ->select('induk_jns.id_jns_seminar', 'induk_jns.nm_jns_seminar')
                                ->groupBy('induk_jns.id_jns_seminar', 'induk_jns.nm_jns_seminar')
                                ->orderBy('induk_jns.a_tugas_akhir', 'ASC')
                                ->first();

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
                                        AND peran.id_jns_seminar= $jns_seminar->id_jns_seminar
                                        AND peran.id_reg_pd = '" . $data->id_reg_pd . "'
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

                            @if($data->status_validasi != 1)
                            {!! tableRow('Judul', $data->judul_akt_mhs) !!}
                            @endif
                        </tbody>
                    </table>
                    <hr>
                    @if ($data->status_validasi == 0)
                    <input type="hidden" name="awal" value="1">
                    {!! FormInputText('judul_akt_mhs', 'Judul', 'text', $data->judul_akt_mhs, ['required' => true, 'properties' => ['minlength' => 10, 'maxlength' => 500]]) !!}
                    @if ($data->SeminarProdi->jenisSeminar->a_tugas_akhir == 0)
                    <?php

                    $dosen_pembimbing_non_ta = DB::table('kpta.peran_dosen_pendaftar')
                        ->join('kpta.peran_seminar AS peran', 'peran.id_peran_seminar', '=', 'kpta.peran_dosen_pendaftar.id_peran_seminar')
                        ->where('peran.soft_delete', 0)
                        ->where('peran.a_aktif', 1)
                        ->where('peran.a_ganti', 0)
                        ->where('peran.id_reg_pd', $profil->id_reg_pd)
                        ->where('peran.peran', 6)
                        ->where('kpta.peran_dosen_pendaftar.id_daftar_seminar', $data->id_daftar_seminar)
                        ->where('peran.id_jns_seminar', $data->SeminarProdi->jenisSeminar->id_jns_seminar)
                        ->where('kpta.peran_dosen_pendaftar.soft_delete', 0)
                        ->first();

                    ?>
                    {!! FormInputText('nm_pemb_lapangan', 'Nama Pembimbing Lapangan', 'text', is_null($dosen_pembimbing_non_ta) ? null : $dosen_pembimbing_non_ta->nm_pemb_lapangan, ['required' => true]) !!}
                    {!! FormInputText('jabatan', 'Jabatan Pembimbing Lapangan', 'text', is_null($dosen_pembimbing_non_ta) ? null : $dosen_pembimbing_non_ta->jabatan, ['required' => true]) !!}
                    {!! FormInputText('lokasi', 'Lokasi Pembimbing Lapangan', 'text', is_null($dosen_pembimbing_non_ta) ? null : $dosen_pembimbing_non_ta->lokasi, ['required' => true]) !!}
                    @endif
                   
                    @elseif ($data->status_validasi == 3 && $nilai_akhir_seminar->a_valid == 0)
                    <hr>
                    <input type="hidden" name="awal" value="2">
                    {!! FormInputSelect('hari', 'Hari Seminar', true, true, config('mp.data_master.hari'), $data->hari) !!}
                    {!! FormInputText('tgl_mulai', 'Tanggal Seminar', 'text', $data->tgl_mulai, ['required' => true, 'placeholder' => 'Tuliskan tanggal seminar akan dilakukan', 'properties' => ['autocomplete' => 'off'], 'readonly' => true]) !!}
                    {!! FormInputSelect('waktu', 'Waktu Seminar', true, true, config('mp.data_master.waktu'), $data->waktu) !!}

                    {{-- @foreach ($gedung_ruang as $item)
                                    {{$item}}
                    @endforeach --}}
                    <div class="form-group row">
                        <label for="ruang" class="col-sm-2 col-form-label">Gedung - Ruang<span style="color:red;">
                                *</span></label>
                        <div class="col-sm-10">
                            <select name="id_ruang" id="ruang" class="form-control" required>
                                <option value="">-Pilih-</option>
                                @foreach ($gedung_ruang as $item)
                                @if ($item['id_ruang'] == $data->id_ruang)
                                <option value="{{ $item['id_ruang'] }}" selected>{{ $item['nm_gedung'] }} -
                                    {{ $item['nm_ruang'] }}
                                </option>
                                @else
                                <option value="{{ $item['id_ruang'] }}">{{ $item['nm_gedung'] }} -
                                    {{ $item['nm_ruang'] }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            </option>
                            </select>
                        </div>
                    </div>
                    @endif
                 
                    @if (in_array($data->status_validasi, [0, 3]))
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary float-right" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                    @endif
                </div>
            </div>
            @if (in_array($data->status_validasi, [0, 3]))
        </form>
        @endif

        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-file-archive"></i> Dokumen Pendukung</h3>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    <div class="alert alert-info">
                        <i class="text-danger">*</i> adalah dokumen yang harus diisi/ada/diunggah.<br>
                        Semua dokumen diunggah dalam format (pdf).<br>
                        Maksimal unggah per file adalah 1MB.
                    </div>
                </div>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Dokumen yang dibutuhkan</th>
                            <th>File dokumen</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($syarat as $no => $each_syarat)
                        <tr>
                            <td style="vertical-align: middle">{{ $no + 1 }}</td>
                            <td style="vertical-align: middle; max-width: 300px; word-wrap: break-word;">
                                {{ $each_syarat->nm_syarat_seminar . (!is_null($each_syarat->keterangan) ? ' (' . $each_syarat->keterangan . ')' : null) }}
                            </td>
                            <td>
                                @if (is_null($each_syarat->jmlh_dok) || $each_syarat->jmlh_dok == 0)
                                <a href="{{ route('pendaftaran_seminar.detail.daftar_dokumen', [Crypt::encrypt($data->id_daftar_seminar), Crypt::encrypt($each_syarat->id_list_syarat)]) }}"
                                    class="btn btn-xs btn-flat btn-danger">Belum Unggah Dokumen</a>
                                @else
                                <a href="{{ route('pendaftaran_seminar.detail.daftar_dokumen', [Crypt::encrypt($data->id_daftar_seminar), Crypt::encrypt($each_syarat->id_list_syarat)]) }}"
                                    class="btn btn-xs btn-flat btn-info">{{ $each_syarat->jmlh_dok }}
                                    Dokumen</a>
                                @endif
                            </td>
                            <!-- <td style="vertical-align: middle; text-align: center">
                                        @if (is_null($each_syarat->stat_ajuan) || $each_syarat->stat_ajuan == 0)
                                            <i class="text-warning fa fa-warning" style="font-size:30px">
                                            @elseif($each_syarat->stat_ajuan==1)
                                                <i class="text-info fa fa-share-square" style="font-size:20px"></i>
                                            @elseif($each_syarat->stat_ajuan==2)
                                                <i class="text-success fa fa-share-square" style="font-size:20px"></i>
                                            @elseif($each_syarat->stat_ajuan==3)
                                                <i class="text-success fa fa-check" style="font-size:20px"></i>
                                            @elseif($each_syarat->stat_ajuan==4)
                                                <i class="text-danger fa fa-close" style="font-size:20px"></i>
                                            @else
                                                <i class="text-danger fa fa-exclamation" style="font-size:20px"></i>
                                        @endif
                                    </td> -->
                            @if (is_null($each_syarat->stat_ajuan) || $each_syarat->stat_ajuan == 0)
                            <td>Belum diajukan</td>
                            @else
                            <td>{{ config('mp.data_master.status_periksa.'.$each_syarat->status_periksa) }}</td>
                            @endif
                            <td>
                                {{ $each_syarat->ket_periksa }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- <div class="mt-2">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Status Pengajuan</h3>
                            </div>
                            <div class="card-body">
                                <p>
                                    <i class="text-success fa fa-check" style="font-size:20px"></i>= Ajuan disetujui<br>
                                    <i class="text-danger fa fa-close" style="font-size:20px"></i>= Ajuan ditolak<br>
                                    <i class="text-danger fa fa-exclamation" style="font-size:20px"></i>= Ajuan
                                    ditangguhkan<br>
                                    <i class="text-info fa fa-share-square" style="font-size:20px"></i>= Ajuan diajukan dan
                                    menunggu validasi<br>
                                    <i class="text-success fa fa-share-square" style="font-size:20px"></i>= Ajuan Diserahkan ke Kaprodi dan
                                    menunggu validasi<br>
                                    <i class="text-warning fa fa-warning" style="font-size:20px"></i>= Belum diajukan
                                </p>
                            </div>
                        </div>
                    </div> -->
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Detail Ajuan Seminar</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Status Ajuan','<span class="badge badge-info">'.config('mp.data_master.status_ajuan_daftar_seminar.'.$data->status_validasi).'</span>') !!}
                        {!! tableRow('Nama Validator', $data->nm_validator) !!}
                        {!! tableRow('Keterangan Validasi', $data->ket_periksa) !!}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {!! buttonBack(route('pendaftaran_seminar')) !!}
            <div class="pull-right">
                @if ($data->status_validasi == 0)
                <form action="{{ route('pendaftaran_seminar.update', Crypt::encrypt($data->id_daftar_seminar)) }}"
                    class="validasi_form" style="display: inline;" method="POST">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-xs btn-success btn-flat btn-validasi"><i class="fa fa-check"></i> Ajukan
                        Pendaftaran</button>
                </form>
                @push('js')
                <script>
                    $(document).ready(function() {
                        $('button.btn-validasi').on('click', function(e) {
                            e.preventDefault();
                            var self = $(this);
                            swal({
                                title: "Apakah anda yakin mengajukan pendaftaran?",
                                text: "Jika sudah terkirim maka anda tidak bisa mengubah/menambahkan datanya kembali",
                                icon: "warning",
                                buttons: {
                                    cancel: {
                                        text: "Batal",
                                        value: null,
                                        closeModal: true,
                                        visible: true,
                                    },
                                    text: {
                                        text: "Ya, Kirimkan!",
                                        value: true,
                                        visible: true,
                                        closeModal: false,
                                    }
                                },
                                dangerMode: true,
                            }).then((willDelete) => {
                                if (willDelete) {
                                    self.parents(".validasi_form").submit();
                                }
                            })
                        });
                    })
                </script>
                @endpush
                @elseif(in_array($data->status_validasi,[4,5]))
                <form action="{{ route('pendaftaran_seminar.delete', Crypt::encrypt($data->id_daftar_seminar)) }}"
                    class="validasi_form" style="display: inline;" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-danger btn-flat btn-validasi"><i class="fa fa-refresh"></i> Tarik
                        Kembali dan Perbaiki Pendaftaran</button>
                </form>
                @push('js')
                <script>
                    $(document).ready(function() {
                        $('button.btn-validasi').on('click', function(e) {
                            e.preventDefault();
                            var self = $(this);
                            swal({
                                title: "Apakah anda yakin ingin menarik ajuan untuk diperbaiki?",
                                text: "Jika sudah yakin, silahkan klik tombol setuju dan lakukan perbaikan data hanya pada bagian yang tidak valid",
                                icon: "warning",
                                buttons: {
                                    cancel: {
                                        text: "Batal",
                                        value: null,
                                        closeModal: true,
                                        visible: true,
                                    },
                                    text: {
                                        text: "Setuju!",
                                        value: true,
                                        visible: true,
                                        closeModal: false,
                                    }
                                },
                                dangerMode: true,
                            }).then((willDelete) => {
                                if (willDelete) {
                                    self.parents(".validasi_form").submit();
                                }
                            })
                        });
                    })
                </script>
                @endpush
                @endif
            </div>
        </div>
    </div>
    @endsection