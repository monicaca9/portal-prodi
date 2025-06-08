@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header ">
        <h3 class="card-title"><i class="fa fa-plus"></i> Tambah Daftar Seminar Baru</h3>
    </div>
    <div class="card-body">
        <form action="{{route('daftar_seminar_prodi.update', Crypt::encrypt($data->id_seminar_prodi))}}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Data Seminar Baru</h3>
                </div>
                <div class="card-body">
                    {!! FormInputSelect('id_jns_seminar','Jenis Seminar',true,true,$jenis_seminar,$data->id_jns_seminar) !!}
                    {!! FormInputSelect('jmlh_pembimbing','Jmlh Pembimbing',true,true,$urutan,$data->jmlh_pembimbing) !!}
                    {!! FormInputSelect('jmlh_penguji','Jmlh Penguji',true,true,$urutan,$data->jmlh_penguji) !!}
                    {!! FormInputSelect('urutan','Urutan Seminar',true,true,$urutan,$data->urutan) !!}
                    {!! FormInputSelect('id_mk','Matkul Seminar',true,true,$id_mk, $data->id_mk) !!}

                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary float-right" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"><i class="fas fa-th-list"></i> Penomoran Berita Acara Seminar</h3>
            </div>
            @if(is_null($data_nomor_ba_seminar))
            <form action="{{ route('daftar_seminar_prodi.simpan_ba', Crypt::encrypt($data->id_seminar_prodi)) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="alert alert-info">
                        Apakah nomor berita acara seminar {{ $data->jenisSeminar->nm_jns_seminar }} ini, kelanjutan dari nomor berita acara yang lain?
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="pilihan_no_ba" id="ba_lanjutan" value="lanjutan" checked>
                                    <label class="form-check-label" for="ba_lanjutan">Ya, gunakan nomor sebelumnya</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="pilihan_no_ba" id="ba_baru" value="baru">
                                    <label class="form-check-label" for="ba_baru">Tidak, buat nomor baru</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-old" style="display: block;">
                        {!! FormInputSelect('id_no_ba', 'Pilih Lanjutan Nomor Berita Acara', true, true, $list_nm_ba) !!}
                    </div>

                    <div class="form-new" style="display: none;">
                        <input type="hidden" name="id_sms" value="{{ $id_sms }}">
                        {!! FormInputText('nm_akt_ba', 'Nama Berita Acara (Kegiatan)', 'text', null, ['required' => true, 'placeholder' => 'Contoh: Seminar Skripsi']) !!}
                        {!! FormInputText('no_ba_awal', 'No. Awal Berita Acara', 'number', null, [ 'placeholder' => 'Contoh: 1']) !!}
                        {!! FormInputText('kode_ba', 'Kode Dokumen Berita Acara', 'text', null, [ 'placeholder' => 'Contoh: SKRIPSI-BA-2024']) !!}
                    </div>
                </div>

                <div class="card-footer">
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary float-right" type="submit">
                            <i class="fa fa-save"></i> SIMPAN
                        </button>
                    </div>
                </div>
            </form>
            @else
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            {!! tableRow('Nama Berita Acara (Kegiatan)',$data_nomor_ba_seminar->nomorBa->nm_akt_ba) !!}
                            {!! tableRow('No. Awal Berita Acara',$data_nomor_ba_seminar->nomorBa->no_ba_awal) !!}
                            {!! tableRow('Kode Dokumen Berita Acara',$data_nomor_ba_seminar->nomorBa->kode_ba) !!}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('daftar_seminar_prodi.ubah_ba', ['id' => Crypt::encrypt($data->id_seminar_prodi)]) }}"
                    class="btn btn-xs btn-warning btn-flat float-right">
                    <i class="fa fa-pencil"></i> UBAH
                </a>
                <form action="{{ route('daftar_seminar_prodi.delete_ba_seminar', ['id' => Crypt::encrypt($data_nomor_ba_seminar->id_no_ba_seminar)])  }}" class="delete_form" style="display: inline;" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-danger btn-flat btn-delete float-right float-right mr-2" data-toggle="tooltip" data-placement="top">
                        <i class="fa fa-trash-o"></i> HAPUS
                    </button>
                </form>
            </div>
            @endif
        </div>
        <div class="card-footer">
            {!! buttonBack(route('daftar_seminar_prodi')) !!}
        </div>
    </div>

    @endsection

    @push('js')
    <script>
        $(function() {
            if ($('#ba_lanjutan').prop("checked")) {
                $('.form-old').show();
                $('.form-new').hide();
                $('#id_no_ba').prop('required', true);
                $('#nm_akt_ba, #no_ba_awal, #kode_ba').prop('required', false);
            } else {
                $('.form-old').hide();
                $('.form-new').show();
                $('#id_no_ba').prop('required', false);
                $('#nm_akt_ba, #no_ba_awal, #kode_ba').prop('required', true);
            }


            $('input[name="pilihan_no_ba"]').change(function() {
                if ($('#ba_lanjutan').prop("checked")) {
                    $('.form-old').show();
                    $('.form-new').hide();
                    $('#id_no_ba').prop('required', true);
                    $('#nm_akt_ba, #no_ba_awal, #kode_ba').prop('required', false);
                } else {
                    $('.form-old').hide();
                    $('.form-new').show();
                    $('#id_no_ba').prop('required', false);
                    $('#nm_akt_ba, #no_ba_awal, #kode_ba').prop('required', true);
                }
            });
        });
    </script>
    @endpush