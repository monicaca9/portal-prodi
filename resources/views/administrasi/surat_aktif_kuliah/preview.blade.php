@extends('template.default')

@section('content')
    @php
        $showContentHeader = false;

    @endphp
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card ">
            <iframe src="{{ route('administrasi.surat_aktif_kuliah.preview-pdf', ['id' => Crypt::encrypt($data->id)]) }}"
                style="min-width:70vh; min-height:80vh; height:100%; border:none;"></iframe>
            <div class="card-footer">
                <a href="{{ route('administrasi.surat_aktif_kuliah.detail', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                <div class="pull-right">
                    <a href="{{ route('administrasi.surat_aktif_kuliah.edit', ['id' => Crypt::encrypt($data->id)]) }}"
                        class="btn btn-warning btn-flat
                        {{ $data->status !== 'dibuat' ? 'disabled aria-disabled=true tabindex=-1' : '' }}">
                        <i class="fa fa-pencil"></i> Ubah Data
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection