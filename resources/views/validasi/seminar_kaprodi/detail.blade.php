@extends('template.default')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> Detail Ajuan Seminar {{ $seminar->jenisSeminar->nm_jns_seminar }} - {{ $profil->nm_pd }}</h3>
    </div>
    <div class="card-body">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Biodata Pendaftar</h3>
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
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Seminar</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Jenis Seminar',$seminar->jenisSeminar->nm_jns_seminar) !!}
                        {!! tableRow('Judul',$data->judul_akt_mhs) !!}
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
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Dokumen Pendukung Seminar {{ $seminar->jenisSeminar->nm_jns_seminar }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Dokumen yang dibutuhkan</th>
                            <th>Status Verifikasi</th>
                            <th>Komentar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($syarat AS $no=> $each_syarat)
                        <?php
                        $dok = \DB::SELECT("
                                        SELECT list.id_dok_syarat_daftar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
                                        FROM dok.dok_syarat_daftar AS list
                                        JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
                                        JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
                                        WHERE list.soft_delete=0
                                        AND list.id_list_syarat_daftar='" . $each_syarat->id_list_syarat_daftar . "'
                                    ");

                        ?>
                        <tr>
                            <td>{{ $no+1 }}</td>
                            <td>
                                <strong>{{ $each_syarat->nm_syarat_seminar }}</strong>
                                <br>Dokumen:
                                @if(count($dok)>0)
                                <ul style="padding-left: 20px;">
                                    @foreach($dok AS $each_dok)
                                    <li>
                                        <a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="link-muted" target="_blank"><i class="fas fa-download"></i> <strong>{{ $each_dok->nm_dok }} ({{ $each_dok->nm_jns_dok }})</strong> Diunggah pada {{ tglWaktuIndonesia($each_dok->wkt_unggah) }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-danger">--Belum diunggah--</span>
                                @endif
                            </td>
                            @if($data->status_validasi=='2')
                            <form action="{{ route('validasi.pengajuan_seminar_kaprodi.ubah',Crypt::encrypt($data->id_daftar_seminar)) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_ver_ajuan" value="{{ $each_syarat->id_ver_ajuan }}">
                                <td>
                                    <select name="status_periksa" id="status_periksa" class="form-control" required>
                                        @foreach(config('mp.data_master.status_periksa') AS $key_status=>$each_status)
                                        <option value="{{ $key_status }}" {{ ($key_status==$each_syarat->status_periksa)?'selected':null }}>{{ $each_status }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="ket_periksa" placeholder="Tuliskan komentar" value="{{ $each_syarat->ket_periksa }}"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="submit" class="btn btn-primary btn-flat btn-sm">Simpan</button>
                                        <a href="{{ route('validasi.pengajuan_seminar_kaprodi.detail.daftar_riwayat_verifikasi', [Crypt::encrypt($data->id_daftar_seminar), Crypt::encrypt($each_syarat->id_list_syarat)]) }}"
                                            class="btn btn-info btn-flat btn-sm"
                                            style="margin-left: 5px;"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Detail Data Validasi">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </form>
                            @else
                            <td>{{ config('mp.data_master.status_periksa.'.$each_syarat->status_periksa) }}</td>
                            <td>{{ $each_syarat->ket_periksa }}</td>
                            <td>
                                <a href="{{ route('validasi.pengajuan_seminar_kaprodi.detail.daftar_riwayat_verifikasi', [Crypt::encrypt($data->id_daftar_seminar), Crypt::encrypt($each_syarat->id_list_syarat)]) }}"
                                    class="btn btn-info btn-flat btn-sm"
                                    style="margin-left: 5px;"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Detail Data Validasi">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->status_validasi=='2')
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-check-square"></i> Validasi Ajuan Seminar</h3>
            </div>
            <form class="validasi_form" action="{{ route('validasi.pengajuan_seminar_kaprodi.update',Crypt::encrypt($data->id_daftar_seminar)) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <input type="hidden" name="nm_verifikator" value="{{ auth()->user()->nm_pengguna }}">
                    {!! FormInputSelect('status_validasi','Status Periksa',true,false,config('mp.data_master.status_ajuan_seminar_kaprodi'),$data->status_validasi) !!}
                    {!! FormInputText('ket_periksa','Keterangan Periksa','text',null) !!}
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-flat btn-primary tombol_validasi"><i class="fa fa-save"></i> Simpan Validasi</button>
                    </div>
                </div>
            </form>
        </div>

        @push('js')
        <script>
            $(document).ready(function() {
                $('button.tombol_validasi').on('click', function(e) {
                    e.preventDefault();
                    var self = $(this);
                    swal({
                        title: "Lakukan Validasi Ajuan?",
                        text: "Jika sudah divalidasi maka anda tidak bisa mengubah datanya kembali",
                        icon: "warning",
                        buttons: {
                            cancel: {
                                text: "Batal",
                                value: null,
                                closeModal: true,
                                visible: true,
                            },
                            text: {
                                text: "Ya, Validasi Ajuan!",
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
                        @if(!is_null($no_ba_seminar))
                        {!! tableRow('No. Berita Acara Seminar', $no_ba_seminar->no_ba_daftar_seminar .'/'. $no_ba_seminar->kode_ba_daftar_seminar ) !!}
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        {!! buttonBack(route(('validasi.pengajuan_seminar_kaprodi'))) !!}
    </div>
</div>
@endsection