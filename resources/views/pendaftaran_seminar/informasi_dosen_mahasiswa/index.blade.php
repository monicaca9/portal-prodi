@extends('template.default')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> Informasi Dosen Mahasiswa - {{ $profil->nm_pd }}</h3>
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
        @foreach($dosen_mahasiswa as $id_jns_seminar => $list_dosen)
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">
                    <i class="fas fa-list-alt"></i> Jenis Seminar : {{ $list_dosen->first()->nm_jns_seminar }}
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        @foreach($list_dosen as $dosen)
                        {!! tableRow(
                        config('mp.data_master.peran_seminar.' . $dosen->peran) .
                        (!is_null($dosen->urutan) ? ' ke-' . $dosen->urutan : '') ,
                        ($dosen->nm_sdm ??
                        $dosen->nm_pembimbing_luar_kampus ??
                        $dosen->nm_penguji_luar_kampus ??
                        $dosen->nm_pemb_lapangan) .
                        (!is_null($dosen->nip) ? ' - (' . $dosen->nip  : '') .
                        (!is_null($dosen->nm_sdm) ? ' - ' . ($dosen->nm_lemb ?? '-') . ')'
                        : ' - (' . config('mp.data_master.peran_seminar.' . $dosen->peran) . ' dari luar Unila)'))
                        !!}
                        @endforeach

                    </tbody>
                </table>
                <hr>
            </div>
        </div>
        <br>
        @endforeach

        <div class="card-footer">
            {!! buttonBack(route('validasi.pengajuan_seminar')) !!}
        </div>
    </div>
    @endsection