@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Daftar Beasiswa {{ $periode->nm_periode_beasiswa }}</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <table class="table table-striped">
                {!! tableRow('Nama Periode',$periode->nm_periode_beasiswa) !!}
                {!! tableRow('Rincian',$periode->ket_beasiswa) !!}
                <tr>
                    <td>Dokumen Pendukung</td>
                    <td>:</td>
                    <td>
                        <ul>
                            @foreach($dokumen_pendukung AS $each_dok_dukung)
                                <li><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok_dukung->id_dok)) }}" class="link-muted" target="_blank">{{ $each_dok_dukung->nm_dok }}</a></li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            </table>
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
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Dokumen Pendukung</h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="mt-2">
                        @if($periode->wkt_mulai<=currDateTime() && $periode->wkt_berakhir>=currDateTime())
                            <div class="alert alert-success">
                                Waktu pendaftaran dari {{ tglWaktuIndonesia($periode->wkt_mulai) }} sampai {{ tglWaktuIndonesia($periode->wkt_berakhir) }}
                            </div>
                        @else
                            <div class="alert alert-danger">
                                Waktu pendaftaran sudah berakhir sejak {{ tglWaktuIndonesia($periode->wkt_berakhir) }}
                            </div>
                        @endif
                    </div>
                    <div class="mt-2">
                        <div class="alert alert-info">
                            <i class="text-danger">*</i>) adalah dokumen yang harus diisi/ada/diunggah.<br>
                            Semua dokumen diunggah dalam format (pdf).<br>
                            Maksimal unggah per file adalah 2MB.
                        </div>
                    </div>
                    <hr>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Dokumen yang dibutuhkan</th>
                            <th>File dokumen</th>
                            <th>Status</th>
                            @if(is_null($data->waktu_diajukan))
                                <th>Aksi</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($syarat AS $no=> $each_syarat)
                            <?php
                            $dok = \DB::SELECT("
                                        SELECT
                                            dok_daftar_beasiswa.id_dok_daftar_beasiswa,tdok.id_dok,
                                            tdok.nm_dok,tdok.file_dok,tdok.file_name,tdok.media_type
                                        FROM beasiswa.dok_daftar_beasiswa
                                        JOIN dok.dokumen AS tdok ON tdok.id_dok = dok_daftar_beasiswa.id_dok
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
                                                <li><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="link-muted" target="_blank"><i class="fas fa-download"></i> {{ $each_dok->nm_dok }}</a>
                                                    @if(is_null($data->waktu_diajukan))
                                                        {!! buttonDeleteMultipleId('daftar_beasiswa.detail.delete',[Crypt::encrypt($data->id_daftar_beasiswa),Crypt::encrypt($each_dok->id_dok_daftar_beasiswa)],'Hapus Dokumen '.$each_dok->nm_dok) !!}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-danger">--Belum diunggah--</span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($dok)>0)
                                        <span class="text-success">--Sudah diunggah--</span>
                                    @else
                                        <span class="text-danger">--Belum diunggah--</span>
                                    @endif
                                </td>
                                @if(is_null($data->waktu_diajukan))
                                    <td>
                                        <a href="{{ route('daftar_beasiswa.detail.tambah',[Crypt::encrypt($data->id_daftar_beasiswa),Crypt::encrypt($each_syarat->id_syarat_beasiswa)]) }}" class="btn btn-flat btn-primary btn-xs"><i class="fas fa-upload"></i></a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if(!is_null($data->waktu_diajukan))
                <?php
                $validasi = \DB::SELECT("
                    SELECT tver.*, tpeng.nm_pengguna
                    FROM beasiswa.ver_daftar_beasiswa AS tver
                    LEFT JOIN man_akses.role_pengguna AS trole ON trole.id_role_pengguna = tver.id_role_pengguna
                    LEFT JOIN man_akses.pengguna AS tpeng ON tpeng.id_pengguna = trole.id_pengguna
                    WHERE tver.soft_delete=0
                    AND tver.id_daftar_beasiswa ='".$data->id_daftar_beasiswa."'
                    ORDER BY tver.level_ver ASC
                ");
                ?>
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-list-alt"></i> Validasi Pengajuan Beasiswa</h3>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu Pengajuan</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                                <th>Waktu Verifikasi</th>
                                <th>Komentar</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($validasi AS $no_val=>$each_validasi)
                                <tr>
                                    <td>{{ $no_val+1 }}</td>
                                    <td>{{ tglWaktuIndonesia($data->waktu_diajukan) }}</td>
                                    <td>{{ config('mp.data_master.level_verifikasi.'.$each_validasi->level_ver) }}</td>
                                    <td>{{ config('mp.data_master.status_periksa.'.$each_validasi->status_periksa) }}</td>
                                    <td>
                                        @if(!is_null($each_validasi->wkt_selesai_ver))
                                            {{ tglWaktuIndonesia($each_validasi->wkt_selesai_ver) }}
                                        @endif
                                    </td>
                                    <td>{{ $each_validasi->ket_periksa }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            {!! buttonBack(route('daftar_beasiswa')) !!}
            <div class="pull-right">
                @if(is_null($data->waktu_diajukan))
                    @if($periode->wkt_mulai<=currDateTime() && $periode->wkt_berakhir>=currDateTime())
                        <form action="{{ route('daftar_beasiswa.detail.update',Crypt::encrypt($data->id_daftar_beasiswa)) }}" class="validasi_form" style="display: inline;" method="POST">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-xs btn-primary btn-flat btn-validasi">
                                <i class="fa fa-check"></i> Kirim Formulir Pendaftaran
                            </button>
                        </form>
                        @push('js')
                            <script>
                                $(document).ready(function () {
                                    $('button.btn-validasi').on('click', function(e){
                                        e.preventDefault();
                                        var self = $(this);
                                        swal({
                                            title               : "Apakah anda yakin kirim berkas formulir anda?",
                                            text                : "Jika sudah terkirim maka anda tidak bisa mengubah/menambahkan datanya kembali",
                                            icon                : "warning",
                                            buttons: {
                                                cancel: {
                                                    text: "Batal",
                                                    value: null,
                                                    closeModal: true,
                                                    visible: true,
                                                },
                                                text: {
                                                    text: "Ya, Kirimkan!",
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
                    @else
                        <button type="button" class="btn btn-danger btn-flat btn-sm" disabled><i class="fa fa-close"></i> Masa pendaftaran sudah habis</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
