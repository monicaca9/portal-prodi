@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Detail Ajuan Beasiswa {{ $periode->nm_periode_beasiswa }}</h3>
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
                                <?php $foto = DB::table('dok.large_object')->where('id_blob',$profil->id_blob)->first(); ?>
                                <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                            @endif
                        </div>
                        <div class="col-sm-9">
                            <table class="table table-striped">
                                <tbody>
                                {!! tableRow('Nama Lengkap',$profil->nm_pd) !!}
                                {!! tableRow('NPM',$profil->nim) !!}
                                {!! tableRow('Homebase',$profil->prodi) !!}
                                {{--                        {!! tableRow('IPK Terakhir',3.44) !!}--}}
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
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Dokumen Pendukung</h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Dokumen yang dibutuhkan</th>
                            <th>File dokumen</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($syarat AS $no=> $each_syarat)
                            <?php
                            $dok = \DB::SELECT("
                                        SELECT
                                            dok_daftar_beasiswa.id_dok_daftar_beasiswa,tdok.id_dok,
                                            tdok.nm_dok,tdok.wkt_unggah,tdok.file_dok,tdok.file_name,tdok.media_type,
                                            tjns.nm_jns_dok
                                        FROM beasiswa.dok_daftar_beasiswa
                                        JOIN dok.dokumen AS tdok ON tdok.id_dok = dok_daftar_beasiswa.id_dok
                                        JOIN ref.jenis_dokumen AS tjns ON tjns.id_jns_dok = tdok.id_jns_dok
                                        WHERE dok_daftar_beasiswa.soft_delete=0
                                            AND dok_daftar_beasiswa.id_syarat_beasiswa='".$each_syarat->id_syarat_beasiswa."'
                                            AND dok_daftar_beasiswa.id_daftar_beasiswa='".$data->id_daftar_beasiswa."'
                                        ORDER BY dok_daftar_beasiswa.tgl_create ASC
                                    ");
                            ?>
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_syarat->nm_syarat }}</td>
                                <td>
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
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Detail Ajuan</h3>
                </div>
                <div class="card-body" style="margin: 0; padding: 0">
                    <table class="table table-striped">
                        <tbody>
                        {!! tableRow('Status Ajuan','<span class="badge badge-info">'.config('mp.data_master.status_periksa.'.$data->status_periksa).' oleh '.config('mp.data_master.level_verifikasi.'.$data->level_ver).'</span>') !!}
                        @if($data->status_periksa!='N')
                            {!! tableRow('Waktu Verifikasi',tglWaktuIndonesia($data->wkt_selesai_ver)) !!}
                            {!! tableRow('Verifikator',$data->nm_verifikator) !!}
                            {!! tableRow('Keterangan',$data->ket_periksa) !!}
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if($data->status_periksa=='N')
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title"><i class="fas fa-check-square"></i> Validasi Ajuan Beasiswa</h3>
                    </div>
                    <form class="validasi_form" action="{{ route('validasi.pengajuan_beasiswa.update',Crypt::encrypt($data->id_ver_daftar_beasiswa)) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="nm_verifikator" value="{{ auth()->user()->nm_pengguna }}">
                        {!! FormInputSelect('status_periksa','Status Periksa',true,false,config('mp.data_master.status_periksa'),$data->status_periksa) !!}
                        {!! FormInputTextarea('ket_periksa','Keterangan',true,$data->ket_periksa) !!}
                    </div>
                    <div class="card-footer">
                        {!! buttonBack(route('validasi.pengajuan_beasiswa')) !!}
                        <div class="pull-right">
                            <button type="submit" class="btn btn-flat btn-primary tombol_validasi"><i class="fa fa-save"></i> Simpan Validasi</button>
                        </div>
                    </div>
                    </form>
                </div>

                @push('js')
                    <script>
                        $(document).ready(function () {
                            $('button.tombol_validasi').on('click', function(e){
                                e.preventDefault();
                                var self = $(this);
                                swal({
                                    title               : "Lakukan Validasi Ajuan?",
                                    text                : "Jika sudah divalidasi maka anda tidak bisa mengubah datanya kembali",
                                    icon                : "warning",
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
                                    dangerMode         : true,
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
        @if($data->status_periksa!='N')
            <div class="card-footer">
                {!! buttonBack(route('validasi.pengajuan_beasiswa')) !!}
            </div>
        @endif
    </div>
@endsection
