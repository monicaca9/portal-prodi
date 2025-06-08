@extends('template.default')
@include('__partial.datatable')


@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"> </i> Detail Mahasiswa - {{ $profil->nm_pd.' ('.$profil->nim.')' }}</h3>
    </div>

    <div class="card-body">

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
                        {!! tableRow('Nama Lengkap', $profil->nm_pd)!!}
                        {!! tableRow('NPM', $profil->nim)!!}
                        {!! tableRow('Program Studi', $profil->prodi)!!}
                        {!! tableRow('IPK Terakhir', $profil->ipk . ' (Semester: ' . $profil->nm_smt . ')') !!}
                        {!! tableRow('Tempat/Tanggal Lahir', $profil->tmpt_lahir . ', ' . tglIndonesia($profil->tgl_lahir)) !!}
                        {!! tableRow('No. HP', $profil->tlpn_hp)!!}
                        {!! tableRow('Status', $profil->nm_stat_mhs)!!}
                        {!! tableRow('Konsentrasi Prodi', $data_konsentrasi_pd->nm_konsentrasi_prodi ?? '-')!!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('sdm.mahasiswa')) !!}
    </div>
</div>
</div>
@endsection