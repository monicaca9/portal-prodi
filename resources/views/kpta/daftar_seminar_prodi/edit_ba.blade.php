@extends('template.default')

@section('content')
<div class="row">
    <section class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-pencil"></i> Ubah Nomor Berita Acara</h3>
            </div>
            <form action="{{ route('daftar_seminar_prodi.update_ba', Crypt::encrypt($data_ba->id_no_ba)) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    {!! FormInputText('nm_akt_ba', 'Nama Berita Acara (Kegiatan)', 'text', $data_ba->nm_akt_ba, ['required' => false, 'placeholder' => 'Contoh: Seminar Skripsi']) !!}
                    {!! FormInputText('no_ba_awal', 'No. Awal Berita Acara', 'number', $data_ba->no_ba_awal, ['required' => false, 'placeholder' => 'Contoh: 001']) !!}
                    {!! FormInputText('kode_ba', 'Kode Dokumen Berita Acara', 'text', $data_ba->kode_ba, ['required' => false, 'placeholder' => 'Contoh: SKR-2024']) !!}
                </div>

                <div class="card-footer">
                    <a href="{{ route('daftar_seminar_prodi.ubah', ['id' => Crypt::encrypt($data_seminar_prodi->id_seminar_prodi)]) }}" class="btn btn-default btn-flat">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-warning pull-right btn-flat">
                        <i class="fa fa-pencil"></i> UBAH
                    </button>
                </div>
            </form>
        </div>
</div>
@endsection