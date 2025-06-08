@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"> </i> Detail Seminar {{ $seminar->jenisSeminar->nm_jns_seminar }} - {{ $profil->nm_pd }}</h3>
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
                        {!! tableRow('Judul',$data_daftar_seminar->judul_akt_mhs) !!}
                        {!! tableRow('Hari Seminar', config('mp.data_master.hari')[$data_daftar_seminar->hari] ?? '-') !!}
                        {!! tableRow('Tanggal Seminar', tglIndonesiaShort($data_daftar_seminar->tgl_mulai) ?? '-') !!}
                        {!! tableRow('Waktu Seminar', config('mp.data_master.waktu') [$data_daftar_seminar->waktu] ?? '-') !!}
                        {!! tableRow('Tempat Seminar', ($data_daftar_seminar->nm_ruang && $data_daftar_seminar->nm_gedung) ?
                        $data_daftar_seminar->nm_ruang . ' - (' . $data_daftar_seminar->nm_gedung . ')' : '-') !!} </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-blue">
                <h3 class="card-title"> <i class="fas fa-list-alt"></i>
                    Hasil Penilaian Seminar
                </h3>
            </div>
            <div class="card-body">
                <form action="" class="form-horizontal form-inside">
                    {!! FormInputSelect('peran', 'Jabatan', false, true, $list_peran_seminar, $peran_id) !!}
                </form>
                <table class="table table-striped">
                    <tbody>
                        <?php
                        $dosen = \DB::table('kpta.peran_seminar AS peran')
                            ->leftJoin('pdrd.sdm AS tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
                            ->leftJoin('pdrd.reg_ptk AS tr', 'tr.id_sdm', '=', 'tsdm.id_sdm')
                            ->leftJoin('pdrd.sms AS tprodi', 'tprodi.id_sms', '=', 'tr.id_sms')
                            ->leftJoin('ref.jenjang_pendidikan AS tjenj', 'tjenj.id_jenj_didik', '=', 'tprodi.id_jenj_didik')
                            ->select(
                                'peran.id_peran_seminar',
                                \DB::raw("CASE WHEN tsdm.nm_sdm IS NOT NULL THEN CONCAT(tsdm.nm_sdm, ' (', tsdm.nidn, ')') END AS nm_dosen"),
                                'tsdm.nm_sdm',
                                'tsdm.nip',
                                'peran.peran',
                                'peran.urutan',
                                'peran.nm_pembimbing_luar_kampus',
                                'peran.nm_penguji_luar_kampus',
                                \DB::raw("CONCAT(peran.nm_pemb_lapangan, ' (', peran.jabatan, ')') AS nm_pemb_lapangan"),
                                \DB::raw(" CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_lemb")
                            )
                            ->where('peran.soft_delete', 0)
                            ->where('peran.a_aktif', 1)
                            ->where('peran.a_ganti', 0)
                            ->where('peran.id_peran_seminar', $data_peran_seminar->id_peran_seminar)
                            ->first();
                        ?>
                        {!! tableRow(
                        'Nama',
                        $dosen->nm_sdm ??
                        $dosen->nm_pembimbing_luar_kampus ??
                        $dosen->nm_penguji_luar_kampus ??
                        $dosen->nm_pemb_lapangan
                        ) !!}

                        {!! tableRow( 'NIP', $dosen->nip ?? ' - ') !!}
                        @if(!is_null($dosen->nm_sdm))
                        {!! tableRow( 'Asal Prodi', $dosen->nm_lemb ?? ' - ') !!}
                        @else
                        {!! tableRow( 'Asal Prodi', config('mp.data_master.peran_seminar.' . $dosen->peran) . ' berasal dari luar unila') !!}
                        @endif

                    </tbody>
                    <hr>
                </table>
                <hr>
                @if($nilai_akhir_seminar->a_valid == 0)
                <form action="{{ route('seminar_prodi.penilaian_seminar.update', Crypt::encrypt($peran_id)) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="d-flex justify-content-end mb-2">
                        <button type="submit" class="btn btn-sm btn-primary" style="margin-right: 8px;">
                            <i class="fa fa-save"></i> SIMPAN
                        </button>

                        <a href="{{ route('berita_acara.detail', Crypt::encrypt($data_daftar_seminar->id_daftar_seminar)) }}"
                            target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-print"></i> Cetak Nilai
                        </a>

                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead style="background-color: rgb(43, 69, 96); color: white;">
                                <tr>
                                    <th rowspan="2" class="align-middle">NO</th>
                                    <th rowspan="2" class="align-middle">Kategori Penilaian</th>
                                    <th colspan="3">Nilai Komponen</th>
                                    <th rowspan="2" class="align-middle">Nilai Rata-rata <br> Keseluruhan</th>
                                </tr>
                                <tr>
                                    <th>Komponen</th>
                                    <th style="width: 150px;">Nilai</th>
                                    <th>Nilai Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $last_kategori = null;
                                $rowspan_counts = array_count_values(array_column($data_nilai, 'nm_kategori_nilai'));
                                @endphp

                                @foreach($data_nilai as $index => $each_nilai)
                                <tr @if ($last_kategori !=null && $last_kategori !=$each_nilai->nm_kategori_nilai) style="border-top: 5px solid rgb(43, 69, 96);" @endif>
                                    @if ($last_kategori !== $each_nilai->nm_kategori_nilai)
                                    <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" style="text-align: midlde; vertical-align: top;">
                                        {{ $index+1 }}
                                    </td>
                                    <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" style="text-align: left; vertical-align: top;">
                                        {{ $each_nilai->nm_kategori_nilai }}
                                    </td>
                                    @endif

                                    <td style="text-align: left; vertical-align: top">{{ $each_nilai->nm_komponen_nilai }}</td>
                                    <td class="align-middle text-center">
                                        <div style="display: flex; justify-content: center; align-items: center;">
                                            <input type="number" name="skor[{{ $each_nilai->id_skor_komponen }}]"
                                                value="{{ $each_nilai->skor_komponen ?? 0 }}"
                                                class="form-control text-center"
                                                step="0.01"
                                                min="0"
                                                style="max-width: 100px;">
                                        </div>
                                    </td>

                                    @if ($last_kategori !== $each_nilai->nm_kategori_nilai)
                                    <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" class="align-middle">
                                        {{ $each_nilai->rata_rata_komponen ?? 0 }}
                                    </td>
                                    @endif

                                    @if ($index === 0)
                                    <td rowspan="{{ count($data_nilai) }}" class="align-middle">
                                        {{ $each_nilai->nilai_akhir ?? '-' }}
                                    </td>
                                    @endif
                                </tr>
                                @php $last_kategori = $each_nilai->nm_kategori_nilai; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <div class="card">

                    <form action="{{ route('seminar_prodi.penilaian_seminar.ubah', Crypt::encrypt($data_daftar_seminar->id_daftar_seminar)) }}" class="validasi_form" style="display: inline;" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-header bg-dark ">
                            <h3 class="card-title"> <i class="fas fa-list-alt"></i>
                                Validasi seminar
                            </h3>
                            <div class="card-tools">
                                <button class="btn btn-xs btn-success btn-flat btn-validasi"><i class="fa fa-save"></i> SIMPAN</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <label class="d-flex align-items-center">
                                <input type="checkbox" id="valid_nilai" class="mr-2">
                                <span><strong>Validasi Data Penilaian Seminar {{ $seminar->jenisSeminar->nm_jns_seminar }} - {{ $profil->nm_pd }}</strong></span>
                            </label>
                            <input type="hidden" name="a_valid_nilai" id="a_valid_nilai" value="0">
                        </div>

                    </form>
                </div>
                
                @elseif ($nilai_akhir_seminar->a_valid == 1)
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('berita_acara.detail', Crypt::encrypt($data_daftar_seminar->id_daftar_seminar)) }}"
                        target="_blank"
                        class="btn btn-sm btn-primary">
                        <i class="fa fa-print"></i> Cetak Nilai
                    </a>

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead style="background-color: rgb(43, 69, 96); color: white;">
                            <tr>
                                <th rowspan="2" class="align-middle">NO</th>
                                <th rowspan="2" class="align-middle">Kategori Penilaian</th>
                                <th colspan="3">Nilai Komponen</th>
                                <th rowspan="2" class="align-middle">Nilai Rata-rata <br> Keseluruhan</th>
                            </tr>
                            <tr>
                                <th>Komponen</th>
                                <th style="width: 150px;">Nilai</th>
                                <th>Nilai Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $last_kategori = null;
                            $rowspan_counts = array_count_values(array_column($data_nilai, 'nm_kategori_nilai'));
                            @endphp

                            @foreach($data_nilai as $index => $each_nilai)
                            <tr @if ($last_kategori !=null && $last_kategori !=$each_nilai->nm_kategori_nilai) style="border-top: 5px solid rgb(43, 69, 96);" @endif>
                                @if ($last_kategori !== $each_nilai->nm_kategori_nilai)
                                <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" style="text-align: midlde; vertical-align: top;">
                                    {{ $index+1 }}
                                </td>
                                <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" style="text-align: left; vertical-align: top;">
                                    {{ $each_nilai->nm_kategori_nilai }}
                                </td>
                                @endif

                                <td style="text-align: left; vertical-align: top">{{ $each_nilai->nm_komponen_nilai }}</td>
                                <td class="align-middle text-center"> {{$each_nilai->skor_komponen}}
                                </td>

                                @if ($last_kategori !== $each_nilai->nm_kategori_nilai)
                                <td rowspan="{{ $rowspan_counts[$each_nilai->nm_kategori_nilai] }}" class="align-middle">
                                    {{ $each_nilai->rata_rata_komponen ?? 0 }}
                                </td>
                                @endif

                                @if ($index === 0)
                                <td rowspan="{{ count($data_nilai) }}" class="align-middle">
                                    {{ $each_nilai->nilai_akhir ?? '-' }}
                                </td>
                                @endif
                            </tr>
                            @php $last_kategori = $each_nilai->nm_kategori_nilai; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('seminar_prodi.penilaian_seminar')) !!}
    </div>
</div>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#valid_nilai').on('change', function() {
            $('#a_valid_nilai').val(this.checked ? 1 : 0);
        });

        $('#peran').on('change', function() {
            var peranNama = $('#peran option:selected').text().trim();
            var urlParams = new URLSearchParams(window.location.search);

            if (peranNama) {
                urlParams.set('peran', peranNama);
            }
            window.location.search = urlParams.toString();
        });

        $('button.btn-validasi').on('click', function(e) {
            e.preventDefault();
            var self = $(this);
            swal({
                title: "Simpan Permanen Nilai Seminar Mahasiswa",
                text: "Jika sudah disimpan permanen maka anda tidak bisa mengubah datanya kembali",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Batal",
                        value: null,
                        closeModal: true,
                        visible: true,
                    },
                    text: {
                        text: "Ya, Simpan Permanen Nilai Seminar Mahasiswa!",
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
    });
</script>
@endpush