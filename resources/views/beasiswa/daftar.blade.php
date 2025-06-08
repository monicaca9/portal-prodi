@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt"></i> DAFTAR BEASISWA PPA</h3>
        </div><!-- /.card-header -->
        <div class="card-body" style="margin: 0;padding: 0">
            <div class="row">
                <div class="col-sm-3 text-center mt-4">
                    <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                </div>
                <div class="col-sm-9">
                    <table class="table table-striped">
                        <tbody>
                        {!! tableRow('Nama Lengkap',$profil->nm_pd) !!}
                        {!! tableRow('NPM',$profil->nim) !!}
                        {!! tableRow('Homebase',$profil->nm_lemb) !!}
                        {!! tableRow('IPK Terakhir',3.44) !!}
                        {!! tableRow('Tempat/Tanggal Lahir',($profil->tmpt_lahir.', '.tglIndonesia($profil->tgl_lahir))) !!}
                        {!! tableRow('No. HP',$profil->tlpn_hp) !!}
                        {!! tableRow('Status','Aktif') !!}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Dokumen Pendukung</h3>
                    </div><!-- /.card-header -->
                    <div class="card-body" style="margin: 0;padding: 0">
                        <div class="container mt-2">
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
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>Transkrip Terakhir</td>
                                <td><span class="text-danger">--Belum diunggah--</span></td>
                                <td><span class="text-danger">--Belum diunggah--</span></td>
                                <td><a href="" class="btn btn-flat btn-primary btn-sm"><i class="fas fa-upload"></i></a></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Formulir Pendaftaran<i class="text-danger">*</i></td>
                                <td><span class="text-danger">--Belum diunggah--</span></td>
                                <td><span class="text-danger">--Belum diunggah--</span></td>
                                <td><a href="" class="btn btn-flat btn-primary btn-sm"><i class="fas fa-upload"></i></a></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Surat tidak mengambil beasiswa lain<i class="text-danger">*</i></td>
                                <td><a href="" class="btn btn-sm btn-info btn-flat"><i class="fas fa-download"></i> file_surat_ket_beasiswa.pdf</a></td>
                                <td><span class="text-success">--Sudah diunggah--</span></td>
                                <td><a href="" class="btn btn-flat btn-warning btn-sm"><i class="fas fa-upload"></i></a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('daftar_beasiswa')) !!}
            <div class="pull-right">
                <a href="" class="btn btn-success btn-flat btn-sm"><i class="fa fa-check"></i> Kirim Formulir Pendaftaran</a>
            </div>
        </div>
    </div>
@endsection
