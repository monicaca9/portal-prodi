@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')
@include('__partial.date')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> Ajukan Riwayat Seminar - {{ $profil->nm_pd.' ('.$profil->nim.')' }}</h3>
    </div>
    <div class="card-body">
        @if($data->stat_ajuan==0)
        <form action="{{ route('pendaftaran_seminar.simpan_riwayat') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_ajuan_pdm_seminar" value="{{ $data->id_ajuan_pdm_seminar }}">
            @endif
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Seminar</h3>
                    @if($data->stat_ajuan==0)
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    {!! FormInputSelect('id_jns_seminar_lama','Jenis Seminar',true,true,$jenis_seminar,$data->id_jns_seminar_lama) !!}
                    {!! FormInputText('judul_seminar_baru','Judul Seminar','text',$data->judul_seminar_baru,['required'=>true]) !!}
                    {!! FormInputText('lokasi_seminar_baru','Tempat Seminar','text',$data->lokasi_seminar_baru,['required'=>true]) !!}
                    {!! FormInputText('tgl_seminar_baru','Tanggal Seminar','text',$data->tgl_seminar_baru,['required'=>true,'placeholder'=>'Tuliskan tanggal seminar akan dilakukan','properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
                    {!! FormInputText('sk_seminar_baru','No. SK Seminar','text',$data->sk_seminar_baru,['required'=>true]) !!}
                    {!! FormInputText('tgl_sk_seminar_baru','Tanggal SK Seminar','text',$data->tgl_sk_seminar_baru,['required'=>true,'placeholder'=>'Tuliskan tanggal SK seminar ','properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
                    {!! FormInputText('nilai_seminar_baru','Nilai Seminar','text',number_format($data->nilai_seminar_baru,2),['required'=>true]) !!}
                    {!! FormInputText('huruf_nilai_seminar_baru','Huruf Nilai','text',$data->huruf_nilai_seminar_baru,['required'=>true]) !!}
                </div>
            </div>
            @if($data->stat_ajuan==0)
        </form>
        @endif
        <div class="card">
            <div class="card-header bg-black">
                <h3 class="card-title"><i class="fas fa-th-list"></i> Daftar Dokumen</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Jenis Dokumen</th>
                                <th>Waktu Unggah</th>
                                <th>File</th>
                                @if($data->stat_ajuan==0)
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumen AS $no_dok=>$each_dok)
                            <tr>
                                <td>{{ $no_dok+1 }}</td>
                                <td>{{ $each_dok->nm_dok }}</td>
                                <td>{{ $each_dok->nm_jns_dok }}</td>
                                <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah) }}</td>
                                <td><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
                                @if($data->stat_ajuan==0)
                                <td>
                                    {!! buttonDelete('pendaftaran_seminar.delete_dok_riwayat',Crypt::encrypt($each_dok->id_dok_ajuan_seminar),'Hapus Dokumen') !!}
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">--Tidak ada dokumen--</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($data->stat_ajuan==0)
        <div class="card">
            <form action="{{ route('pendaftaran_seminar.simpan_dokumen') }}" enctype="multipart/form-data" method="post">
                @csrf
                <input type="hidden" name="id_ajuan_pdm_seminar" value="{{ $data->id_ajuan_pdm_seminar }}">
                <div class="card-header bg-black">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Maksimal dokumen 1 MB, dengan format PDF
                    </div>
                    {!! FormInputText('nm_dok','Nama Dokumen','text',null,['required'=>true]) !!}
                    {!! FormInputSelect('id_jns_dok','Jenis Dokumen',true,true,$jenis_dok) !!}
                    {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf']]) !!}
                    {!! FormInputText('url','URL','text',null) !!}
                    {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
                </div>
            </form>
        </div>
        @endif
    </div>
    <div class="card-footer">
        {!! buttonBack(route('pendaftaran_seminar')) !!}
        <div class="pull-right">
            @if($data->stat_ajuan==0)
            <form action="{{ route('pendaftaran_seminar.simpan_permanen_riwayat') }}" class="validasi_form" style="display: inline;" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id_ajuan_pdm_seminar" value="{{ $data->id_ajuan_pdm_seminar }}">
                <button class="btn btn-xs btn-success btn-flat btn-validasi"><i class="fa fa-check"></i> Ajukan Riwayat Seminar</button>
            </form>
            @push('js')
            <script>
                $(document).ready(function() {
                    $('button.btn-validasi').on('click', function(e) {
                        e.preventDefault();
                        var self = $(this);
                        swal({
                            title: "Apakah anda yakin mengajukan pengajuan riwayat seminar?",
                            text: "Jika sudah terkirim maka anda tidak bisa mengubah/menambahkan datanya kembali",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    text: "Batal",
                                    value: null,
                                    closeModal: true,
                                    visible: true,
                                },
                                text: {
                                    text: "Ya, saya yakin!",
                                    value: true,
                                    visible: true,
                                    closeModal: false,
                                }
                            },
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                self.parents(".validasi_form").submit();
                            }
                        })
                    });
                })
            </script>
            @endpush
            @elseif(in_array($data->status_validasi,[3,4]))
            <form action="{{ route('pendaftaran_seminar.delete',Crypt::encrypt($data->id_daftar_seminar)) }}" class="validasi_form" style="display: inline;" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-danger btn-flat btn-validasi"><i class="fa fa-refresh"></i> Tarik Kembali dan Perbaiki Pendaftaran</button>
            </form>
            @push('js')
            <script>
                $(document).ready(function() {
                    $('button.btn-validasi').on('click', function(e) {
                        e.preventDefault();
                        var self = $(this);
                        swal({
                            title: "Apakah anda yakin ingin menarik ajuan untuk diperbaiki?",
                            text: "Jika sudah yakin, silahkan klik tombol setuju dan lakukan perbaikan data hanya pada bagian yang tidak valid",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    text: "Batal",
                                    value: null,
                                    closeModal: true,
                                    visible: true,
                                },
                                text: {
                                    text: "Setuju!",
                                    value: true,
                                    visible: true,
                                    closeModal: false,
                                }
                            },
                            dangerMode: true,
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
    </div>
</div>
@endsection