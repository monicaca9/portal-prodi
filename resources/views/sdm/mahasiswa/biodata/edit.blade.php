@extends('template.default')
@include('__partial.date')
@include('__partial.select2')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-user"></i> PERBARUI BIODATA - {{ $mhs->nm_pd }}</h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link {{ ($tab=='biodata'?'active':null) }}" href="{{ route('biodata.ubah') }}">Biodata Diri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab=='keluarga'?'active':null) }} {{ is_null($mhs->id_wil)?'disabled':null }}" href="{{ url(route('biodata.ubah').'?tab=keluarga') }}">Data Keluarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab=='wali'?'active':null) }} {{ is_null($mhs->id_jns_tinggal)?'disabled':null }}" href="{{ url(route('biodata.ubah').'?tab=wali') }}">Data Wali</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab=='konsentrasi_prodi'?'active':null) }}" href="{{ url(route('biodata.ubah').'?tab=konsentrasi_prodi') }}">Data Konsentrasi Prodi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($tab=='lainnya'?'active':null) }} {{ is_null($mhs->nm_ibu_kandung)||($mhs->id_jns_tinggal==2&&is_null($mhs->nm_wali))?'disabled':null }}" href="{{ url(route('biodata.ubah').'?tab=lainnya') }}">Data Lainnya</a>
                </li>
            </ul>
            <div class="mt-4">
                @if($tab=='biodata')
                    @include('sdm.mahasiswa.biodata.edit.biodata')
                @elseif($tab=='keluarga')
                    @include('sdm.mahasiswa.biodata.edit.keluarga')
                @elseif($tab=='wali')
                    @include('sdm.mahasiswa.biodata.edit.wali')
                @elseif($tab=='konsentrasi_prodi')
                    @include('sdm.mahasiswa.biodata.edit.konsentrasi_prodi')
                @elseif($tab=='lainnya')
                    @include('sdm.mahasiswa.biodata.edit.lainnya')
                @endif
            </div>
        </div>
    </div>
@endsection
