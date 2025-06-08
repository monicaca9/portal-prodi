@extends('template.default')
@include('__partial.icheck')
{{--@include('__partial.date')--}}
@include('__partial.ckeditor')
@include('__partial.datetime')
@include('__partial.select2')
@include('__partial.date')


@section('content')
<div class="row">
    <section class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-pencil"></i> Distribusi Dosen {{ $jenis_seminar->nm_jns_seminar }}</h3>
            </div>

            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        {!! tableRow('Nama Peserta Didik',$pd->nm_pd) !!}
                        {!! tableRow('NPM',$pd->nim) !!}
                        {!! tableRow('Angkatan',$pd->angkatan) !!}
                        {!! tableRow('IPK',!is_null($pd->ipk)?($pd->ipk.' (Terakhir '.$pd->nm_smt.')'):null) !!}
                    </tbody>
                </table>
                <hr>
                @for ($pembimbing = 1; $pembimbing <= $seminar->jmlh_pembimbing; $pembimbing++)
                    <?php
                    $dosen_pembimbing_unila = DB::table('kpta.peran_seminar AS peran')
                        ->join('pdrd.sdm AS tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
                        ->join('pdrd.reg_ptk AS tr', 'tr.id_sdm', '=', 'tsdm.id_sdm')
                        ->join('pdrd.sms AS tprodi', function ($join) {
                            $join->on('tprodi.id_sms', '=', 'tr.id_sms')
                                ->where('tprodi.soft_delete', 0);
                        })
                        ->join('ref.jenjang_pendidikan AS tjenj', 'tjenj.id_jenj_didik', '=', 'tprodi.id_jenj_didik')
                        ->select(
                            'tsdm.nm_sdm',
                            'tsdm.nidn',
                            'tprodi.nm_lemb',
                            'tjenj.nm_jenj_didik',
                            'peran.id_sdm',
                        )
                        ->where('peran.id_reg_pd', $pd->id_reg_pd)
                        ->where('peran.soft_delete', 0)
                        ->where('peran.peran', 1)
                        ->where('peran.id_jns_seminar', $jenis_seminar->id_jns_seminar)
                        ->where('peran.urutan', $pembimbing)
                        ->where('peran.a_aktif', 1)
                        ->where('peran.a_ganti', 0)
                        ->where('tsdm.soft_delete', 0)
                        ->where('tsdm.id_stat_aktif', 1)
                        ->where('tsdm.id_jns_sdm', 12)
                        ->first();

                    $dosen_luar_unila = DB::table('kpta.peran_seminar AS peran')
                        ->select('peran.nm_pembimbing_luar_kampus')
                        ->where('peran.id_reg_pd', $pd->id_reg_pd)
                        ->where('peran.soft_delete', 0)
                        ->where('peran.peran', 1)
                        ->where('peran.a_aktif', 1)
                        ->where('peran.a_ganti', 0)
                        ->where('peran.id_jns_seminar', $jenis_seminar->id_jns_seminar)
                        ->where('peran.urutan', $pembimbing)
                        ->first();
                    ?>

                    @if (is_null($dosen_pembimbing_unila)&&is_null($dosen_luar_unila))
                    <form action="{{ route('distribusi_dosen_mahasiswa.update', Crypt::encrypt($data_awal)) }}" enctype="multipart/form-data" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header bg-success">
                                <h3 class="card-title">
                                    <i class="fa fa-list"></i> {{ 'Pembimbing ' . $pembimbing }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2">Pembimbing Dari Luar Unila</label>
                                    <div class="col-sm-10">
                                        <div class="form-check form-inline">
                                            <input type="checkbox" class="form-check-input a_input_baru_pembimbing"
                                                name="a_input_baru_pembimbing[{{ $pembimbing }}]"
                                                id="a_input_baru_pembimbing_{{ $pembimbing }}"
                                                value="1"
                                                @if ($dosen_luar_unila && !is_null($dosen_luar_unila->nm_pembimbing_luar_kampus)) checked @endif>
                                            <label class="form-check-label" for="a_input_baru_pembimbing_{{ $pembimbing }}">YA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-old">
                                    {!! FormInputSelect('pembimbing['.$pembimbing.']', 'Pembimbing '.$pembimbing, true, true, $dosen, $dosen_pembimbing_unila->id_sdm??null) !!}
                                </div>
                                <div class="form-new">
                                    <div class="form-new">
                                        {!! FormInputText('nm_pembimbing_luar_kampus['.$pembimbing.']', 'Pembimbing '.$pembimbing, 'text', $dosen_luar_unila->nm_pembimbing_luar_kampus??null,['required'=>true]) !!}
                                    </div>
                                </div>
                                <div class="card-tools">
                                    <button class="btn btn-xs btn-primary float-right" type="submit">
                                        <i class="fa fa-save"></i> SIMPAN
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">
                                <i class="fa fa-list"></i> {{ 'Pembimbing ' . $pembimbing }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2">Ganti Pembimbing</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-inline">
                                        <input type="checkbox" class="form-check-input a_input_baru"
                                            name="a_input_baru[{{ $pembimbing }}]"
                                            id="a_input_baru_{{ $pembimbing }}"
                                            value="1">
                                        <label class="form-check-label" for="a_input_baru_{{ $pembimbing }}">YA</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-old">
                                <table class="table">
                                    <tbody>
                                        @if(!is_null($dosen_pembimbing_unila))
                                        {!! tableRow('Pembimbing Ke-'. $pembimbing, $dosen_pembimbing_unila->nm_sdm . ' (' . $dosen_pembimbing_unila->nidn . ') - ' . $dosen_pembimbing_unila->nm_lemb . ' (' . $dosen_pembimbing_unila->nm_jenj_didik . ')') !!}
                                        @elseif(!is_null($dosen_luar_unila))
                                        {!! tableRow('Pembimbing Ke-'. $pembimbing , $dosen_luar_unila->nm_pembimbing_luar_kampus. ' (Pembimbing berasal dari luar kampus)') !!}
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-new" style="display: none;">
                                <form action="{{ route('distribusi_dosen_mahasiswa.update', Crypt::encrypt($data_awal)) }}" enctype="multipart/form-data" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group row">
                                        <label class="col-sm-2">Pembimbing Dari Luar Unila</label>
                                        <div class="col-sm-10">
                                            <div class="form-check form-inline">
                                                <input type="checkbox" class="form-check-input a_ganti_pembimbing_baru"
                                                    name="a_ganti_pembimbing_baru[{{ $pembimbing }}]"
                                                    id="a_ganti_pembimbing_baru_{{ $pembimbing }}"
                                                    value="1"
                                                    @if ($dosen_luar_unila && !is_null($dosen_luar_unila->nm_pembimbing_luar_kampus)) checked @endif>
                                                <label class="form-check-label" for="a_ganti_pembimbing_baru_{{ $pembimbing }}">YA</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-old-pembimbing" style="display: none;">
                                        {!! FormInputSelect('pembimbing['.$pembimbing.']', 'Pembimbing '.$pembimbing, true, true, $dosen, $dosen_pembimbing_unila->id_sdm ?? null) !!}
                                    </div>
                                    <div class="form-new-pembimbing" style="display: none;">
                                        {!! FormInputText('nm_pembimbing_luar_kampus['.$pembimbing.']', 'Pembimbing '.$pembimbing, 'text', $dosen_luar_unila->nm_pembimbing_luar_kampus ?? null, ['required'=>true]) !!}
                                    </div>
                                    {!! FormInputText('alasan_ganti['.$pembimbing.']', 'Alasan Ganti ', 'text', ) !!}
                                    <div class="card-tools">
                                        <button class="btn btn-xs btn-warning btn-flat float-right" type="submit">
                                            <i class="fa fa-pencil"></i> UBAH
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endfor

                    @for($penguji=1;$penguji<=$seminar->jmlh_penguji;$penguji++)
                        <?php
                        $dosen_penguji_unila = DB::table('kpta.peran_seminar AS peran')
                            ->join('pdrd.sdm AS tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
                            ->join('pdrd.reg_ptk AS tr', 'tr.id_sdm', '=', 'tsdm.id_sdm')
                            ->join('pdrd.sms AS tprodi', function ($join) {
                                $join->on('tprodi.id_sms', '=', 'tr.id_sms')
                                    ->where('tprodi.soft_delete', 0);
                            })
                            ->join('ref.jenjang_pendidikan AS tjenj', 'tjenj.id_jenj_didik', '=', 'tprodi.id_jenj_didik')
                            ->select(
                                'tsdm.nm_sdm',
                                'tsdm.nidn',
                                'tprodi.nm_lemb',
                                'tjenj.nm_jenj_didik',
                                'peran.id_sdm',
                            )
                            ->where('peran.id_reg_pd', $pd->id_reg_pd)
                            ->where('peran.soft_delete', 0)
                            ->where('peran.peran', 2)
                            ->where('peran.id_jns_seminar', $jenis_seminar->id_jns_seminar)
                            ->where('peran.urutan', $penguji)
                            ->where('peran.a_aktif', 1)
                            ->where('peran.a_ganti', 0)
                            ->where('tsdm.soft_delete', 0)
                            ->where('tsdm.id_stat_aktif', 1)
                            ->where('tsdm.id_jns_sdm', 12)
                            ->first();

                        $dosen_luar_unila = DB::table('kpta.peran_seminar AS peran')
                            ->select('peran.nm_penguji_luar_kampus')
                            ->where('peran.id_reg_pd', $pd->id_reg_pd)
                            ->where('peran.soft_delete', 0)
                            ->where('peran.peran', 2)
                            ->where('peran.a_aktif', 1)
                            ->where('peran.a_ganti', 0)
                            ->where('peran.id_jns_seminar', $jenis_seminar->id_jns_seminar)
                            ->where('peran.urutan', $penguji)
                            ->first();
                        ?>

                        @if(is_null($dosen_penguji_unila)&&is_null($dosen_luar_unila))
                        <form action="{{ route('distribusi_dosen_mahasiswa.update', Crypt::encrypt($data_awal)) }}" enctype="multipart/form-data" method="post">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header bg-dark">
                                    <h3 class="card-title">
                                        <i class="fa fa-list"></i> {{ 'Penguji ' . $penguji }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-2">Penguji Dari Luar Unila</label>
                                        <div class="col-sm-10">
                                            <div class="form-check form-inline">
                                                <input type="checkbox" class="form-check-input a_input_baru_penguji"
                                                    name="a_input_baru_penguji[{{ $penguji }}]"
                                                    id="a_input_baru_penguji_{{ $penguji }}"
                                                    value="1"
                                                    @if ($dosen_luar_unila && !is_null($dosen_luar_unila->nm_penguji_luar_kampus)) checked @endif>
                                                <label class="form-check-label" for="a_input_baru_penguji_{{ $penguji }}">YA</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-old">
                                        {!! FormInputSelect('penguji['.$penguji.']', 'Penguji '.$penguji, true, true, $dosen, $dosen_penguji_unila->id_sdm??null) !!}
                                    </div>

                                    <div class="form-new">
                                        <div class="form-new">
                                            {!! FormInputText('nm_penguji_luar_kampus['.$penguji.']', 'Penguji '.$penguji, 'text', $dosen_luar_unila->nm_penguji_luar_kampus ?? null, ['required'=>true]) !!}
                                        </div>
                                    </div>
                                    <div class="card-tools">
                                        <button class="btn btn-xs btn-primary float-right" type="submit">
                                            <i class="fa fa-save"></i> SIMPAN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @else
                        <div class="card">
                            <div class="card-header bg-dark">
                                <h3 class="card-title">
                                    <i class="fa fa-list"></i> {{ 'Penguji ' . $penguji }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2">Ganti Penguji</label>
                                    <div class="col-sm-10">
                                        <div class="form-check form-inline">
                                            <input type="checkbox" class="form-check-input a_input_baru"
                                                name="a_input_baru[{{ $penguji }}]"
                                                id="a_input_baru_{{ $penguji }}"
                                                value="1">
                                            <label class="form-check-label" for="a_input_baru_{{ $penguji }}">YA</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-old">
                                    <table class="table">
                                        <tbody>
                                            @if(!is_null($dosen_penguji_unila))
                                            {!! tableRow('Nama Penguji Ke-'. $penguji, $dosen_penguji_unila->nm_sdm . ' (' . $dosen_penguji_unila->nidn . ') - ' . $dosen_penguji_unila->nm_lemb . ' (' . $dosen_penguji_unila->nm_jenj_didik . ')') !!}

                                            @elseif(!is_null($dosen_luar_unila))
                                            {!! tableRow('Nama Penguji Ke-'. $penguji , $dosen_luar_unila->nm_penguji_luar_kampus. ' (Penguji berasal dari luar kampus)') !!}
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-new" style="display: none;">
                                    <form action="{{ route('distribusi_dosen_mahasiswa.update', Crypt::encrypt($data_awal)) }}" enctype="multipart/form-data" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group row">
                                            <label class="col-sm-2">Penguji Dari Luar Unila</label>
                                            <div class="col-sm-10">
                                                <div class="form-check form-inline">
                                                    <input type="checkbox" class="form-check-input a_ganti_penguji_baru"
                                                        name="a_ganti_penguji_baru[{{ $penguji }}]"
                                                        id="a_ganti_penguji_baru_{{ $penguji }}"
                                                        value="1"
                                                        @if ($dosen_luar_unila && !is_null($dosen_luar_unila->nm_penguji_luar_kampus)) checked @endif>
                                                    <label class="form-check-label" for="a_ganti_penguji_baru_{{ $penguji }}">YA</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-old-penguji" style="display: none;">
                                            {!! FormInputSelect('penguji['.$penguji.']', 'Penguji '.$penguji, true, true, $dosen, $dosen_penguji_unila->id_sdm ?? null) !!}
                                        </div>
                                        <div class="form-new-penguji" style="display: none;">
                                            {!! FormInputText('nm_penguji_luar_kampus['.$penguji.']', 'Penguji '.$penguji, 'text', $dosen_luar_unila->nm_penguji_luar_kampus ?? null, ['required'=>true]) !!}
                                        </div>
                                        {!! FormInputText('alasan_ganti['.$penguji.']', 'Alasan Ganti ', 'text', ) !!}
                                        <div class="card-tools">
                                            <button class="btn btn-xs btn-warning btn-flat float-right" type="submit">
                                                <i class="fa fa-pencil"></i> UBAH
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endfor

                        @if($jenis_seminar->a_tugas_akhir==0)
                        <?php
                        $dosen_pembimbing_non_ta = DB::table('kpta.peran_seminar AS peran')
                            ->where('peran.id_reg_pd', $pd->id_reg_pd)
                            ->where('peran.soft_delete', 0)
                            ->where('peran.a_aktif', 1)
                            ->where('peran.a_ganti', 0)
                            ->where('peran.peran', 6)
                            ->where('peran.id_jns_seminar', $jenis_seminar->id_jns_seminar)
                            ->first();
                        ?>
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Pemb. Lapangan',is_null($dosen_pembimbing_non_ta)?null:$dosen_pembimbing_non_ta->nm_pemb_lapangan) !!}
                                {!! tableRow('Jabatan Pemb. Lapangan',is_null($dosen_pembimbing_non_ta)?null:$dosen_pembimbing_non_ta->jabatan) !!}
                                {!! tableRow('Lokasi Pemb. Lapangan',is_null($dosen_pembimbing_non_ta)?null:$dosen_pembimbing_non_ta->lokasi) !!}
                            </tbody>
                        </table>
                        @endif

                        <form action="{{route('distribusi_dosen_mahasiswa.simpan')}}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id_rincian_peran_seminar" value="{{ $rincian_peran_seminar->id_rincian_peran_seminar }}">
                            <div class="card">
                                <div class="card-header bg-success">
                                    <h3 class="card-title"><i class="fas fa-history"></i> Detail Data SK Distribusi dosen</h3>
                                </div>
                                <div class="card-body">
                                    @if($jenis_seminar->a_tugas_akhir==1)
                                    {!! FormInputText('judul_akt_mhs', 'Judul Usul Penelitian', 'text', $rincian_peran_seminar->judul_akt_mhs??null)!!}
                                    @endif
                                    {!! FormInputText('sk_tugas', 'No.SK Tugas', 'text', $rincian_peran_seminar->sk_tugas??null)!!}
                                    {!! FormInputText('tgl_sk_tugas', 'Tanggal SK', 'text', $rincian_peran_seminar->tgl_sk_tugas??null, ['placeholder' => 'Tuliskan tanggal disahkan SK', 'properties' => ['autocomplete' => 'off'], 'readonly' => true]) !!}
                                    {!! FormInputText ('keterangan','Keterangan', 'text', $rincian_peran_seminar->keterangan??null) !!}

                                    <div class="card-tools">
                                        <button class="btn btn-xs btn-primary float-right" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="card-footer">
                            {!! buttonBack(route('distribusi_dosen_mahasiswa', ['angkatan' => $pd->angkatan])) !!}
                        </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script>
    $(function() {
        $('.a_input_baru').each(function() {
            const checkbox = $(this);
            const card = checkbox.closest('.card');

            cek_atrr(card, checkbox.prop("checked"));

            checkbox.click(function() {
                cek_atrr(card, $(this).prop("checked"));
            });
        });

        $('.a_input_baru_pembimbing').each(function() {
            const checkbox = $(this);
            const card = checkbox.closest('.card');

            cek_atrr_pembimbing(card, checkbox.prop("checked"));

            checkbox.click(function() {
                cek_atrr_pembimbing(card, $(this).prop("checked"));
            });
        });

        $('.a_input_baru_penguji').each(function() {
            const checkbox = $(this);
            const card = checkbox.closest('.card');

            cek_atrr_penguji(card, checkbox.prop("checked"));

            checkbox.click(function() {
                cek_atrr_penguji(card, $(this).prop("checked"));
            });
        });

        $('.a_ganti_pembimbing_baru').each(function() {
            const checkbox = $(this);
            const card = checkbox.closest('.card');

            cek_atrr_ganti_pembimbing(card, checkbox.prop("checked"));

            checkbox.click(function() {
                cek_atrr_ganti_pembimbing(card, $(this).prop("checked"));
            });
        });

        $('.a_ganti_penguji_baru').each(function() {
            const checkbox = $(this);
            const card = checkbox.closest('.card');

            cek_atrr_ganti_penguji(card, checkbox.prop("checked"));

            checkbox.click(function() {
                cek_atrr_ganti_penguji(card, $(this).prop("checked"));
            });
        });

        function cek_atrr_pembimbing(card, checkbox_val) {
            if (checkbox_val) {
                card.find('.form-old').hide();
                card.find('.form-new').show();
                card.find('[name^="nm_pembimbing_luar_kampus"]').prop('required', true);
                card.find('[name^="pembimbing"]').prop('required', false);
            } else {
                card.find('.form-old').show();
                card.find('.form-new').hide();
                card.find('[name^="pembimbing"]').prop('required', true);
                card.find('[name^="nm_pembimbing_luar_kampus"]').prop('required', false);
            }
        }

        function cek_atrr_ganti_pembimbing(card, checkbox_val) {
            if (checkbox_val) {
                card.find('.form-old-pembimbing').hide();
                card.find('.form-new-pembimbing').show();
                card.find('[name^="pembimbing"]').prop('required', false);
                card.find('[name^="nm_pembimbing_luar_kampus"]').prop('required', true);
            } else {
                card.find('.form-old-pembimbing').show();
                card.find('.form-new-pembimbing').hide();
                card.find('[name^="pembimbing"]').prop('required', true);
                card.find('[name^="nm_pembimbing_luar_kampus"]').prop('required', false);
            }
        }

        function cek_atrr_ganti_penguji(card, checkbox_val) {
            if (checkbox_val) {
                card.find('.form-old-penguji').hide();
                card.find('.form-new-penguji').show();
                card.find('[name^="penguji"]').prop('required', false);
                card.find('[name^="nm_penguji_luar_kampus"]').prop('required', true);
            } else {
                card.find('.form-old-penguji').show();
                card.find('.form-new-penguji').hide();
                card.find('[name^="penguji"]').prop('required', true);
                card.find('[name^="nm_penguji_luar_kampus"]').prop('required', false);
            }
        }

        function cek_atrr_penguji(card, checkbox_val) {
            if (checkbox_val) {
                card.find('.form-old').hide();
                card.find('.form-new').show();
                card.find('[name^="penguji"]').prop('required', false);
                card.find('[name^="nm_penguji_luar_kampus"]').prop('required', true);
            } else {
                card.find('.form-old').show();
                card.find('.form-new').hide();
                card.find('[name^="penguji"]').prop('required', true);
                card.find('[name^="nm_penguji_luar_kampus"]').prop('required', false);
            }
        }

        function cek_atrr(card, checkbox_val) {
            if (checkbox_val) {
                card.find('.form-old').hide();
                card.find('.form-new').show();
            } else {
                card.find('.form-old').show();
                card.find('.form-new').hide();
            }
        }
    });
</script>
@endpush