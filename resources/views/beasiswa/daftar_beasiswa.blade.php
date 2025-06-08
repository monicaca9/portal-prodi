@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if($stat_beasiswa==0)
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Beasiswa Aktif</h5>
                    <hr>
                    Anda belum terdaftar Beasiswa manapun
                </div>
            @else
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> Beasiswa Aktif</h5>
                    <hr>
                    Anda sudah terdaftar dan diterima Beasiswa Djarum<br>
                    Terhitung mulai dari tanggal 1 Januari 2020<br>
                    Berakhir tanggal 31 Desember 2020
                </div>
                <a href="" class="btn btn-outline-danger btn-flat btn-block">Ajukan Pengunduran Diri?</a>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-check"></i> DAFTAR BEASISWA</h3>
                </div><!-- /.card-header -->
                <div class="card-body" style="margin: 0;padding: 0">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Beasiswa</th>
                            <th>Periode Pendaftaran</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($stat_beasiswa==0)
                            @foreach($beasiswa AS $no=>$each_beasiswa)
                                <tr>
                                    <td>{{ $no+1 }}</td>
                                    <td>{{ $each_beasiswa }}</td>
                                    <td>8 Agustus 2020 - 10 November 2020</td>
                                    <td><a href="" class="btn btn-sm btn-info btn-flat" data-toggle="modal" data-target="#modal-xl_{{ $no+1 }}">Lihat Persyaratan</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="bg-danger text-center">Tidak bisa mendaftar beasiswa lain</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-xl_1" aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Persyaratan Beasiswa PPA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <tbody>
                        {!! tableRow('Waktu Pendaftaran','8 Agustus 2020 - 10 November 2020') !!}
                        {!! tableRow('Waktu Seleksi Berkas','11 November 2020') !!}
                        </tbody>
                    </table>
                    <p>
                        Beasiswa Peningkatan Prestasi Akademik (PPA) adalah beasiswa yang diberikan untuk peningkatan pemeratan dan kesempatan belajar bagi mahasiswa yang mengalami kesulitan membayar biaya pendidikannya sebagai akibat krisis ekonomi, terutama bagi mahasiswa yang berprestasi akademik.<br>
                        Adapaun tujuan PPA secara umum yaitu Meningkatkan pemerataan dan kesempatan belajar bagi mahasiswa yang mengalami kesulitan membayar pendidikan. Mendorong dan mempertahankan semangat belajar mahasiswa agar mereka dapat menyelesaikan studi/pendidikan tepat waktunya. Mendorong untuk meningkatkan prestasi akademik sehingga memacu peningkatan kualitas pendidikan.
                        Syarat dari beasiswa ini adalah:
                        <ul>
                            <li>Mahasiswa Aktif</li>
                            <li>IPK >= 3,00</li>
                        </ul>
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                    <a href="{{ route('daftar_beasiswa.daftar') }}" class="btn btn-primary btn-flat">Daftar Beasiswa ini!</a>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
