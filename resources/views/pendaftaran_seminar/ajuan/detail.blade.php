@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')
@include('__partial.date')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> Detail Ajukan Riwayat Seminar - {{ $profil->nm_pd.' ('.$profil->nim.')' }}</h3>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Seminar</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                        {!! tableRow('Jenis Seminar',$data->jenis_seminar->nm_jns_seminar) !!}
                        {!! tableRow('Judul Seminar',$data->judul_seminar_baru) !!}
                        {!! tableRow('Tempat Seminar',$data->lokasi_seminar_baru) !!}
                        {!! tableRow('Tanggal Seminar',tglIndonesia($data->tgl_seminar_baru)) !!}
                        {!! tableRow('No. SK Seminar',$data->sk_seminar_baru) !!}
                        {!! tableRow('Tanggal SK Seminar',tglIndonesia($data->tgl_sk_seminar_baru)) !!}
                        {!! tableRow('Nilai Seminar',number_format($data->nilai_seminar_baru,2)) !!}
                        {!! tableRow('Huruf Nilai',$data->huruf_nilai_seminar_baru) !!}
                        </tbody>
                    </table>
                </div>
            </div>
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
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Daftar Riwayat Validasi</h3>
                </div>
                <div class="card-body" style="margin: 0; padding: 0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Validator</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Waktu Validasi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($validasi AS $no_val=>$each_validasi)
                                <tr>
                                    <td>{{ $no_val+1 }}</td>
                                    <td>{{ $each_validasi->nm_verifikator }}</td>
                                    <td>{{ config('mp.data_master.status_periksa.'.$each_validasi->status_periksa) }}</td>
                                    <td>{{ $each_validasi->ket_periksa }}</td>
                                    <td>{{ tglWaktuIndonesia($each_validasi->wkt_selesai_ver) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            {!! buttonBack(route('pendaftaran_seminar')) !!}
            <div class="pull-right">
                @if(in_array($data->stat_ajuan,[1,4]))
                    <form action="{{ route('pendaftaran_seminar.delete_riwayat',Crypt::encrypt($data->id_ajuan_pdm_seminar)) }}" class="validasi_form" style="display: inline;" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-xs btn-danger btn-flat btn-validasi"><i class="fa fa-refresh"></i> Tarik Kembali dan Perbaiki Pendaftaran</button>
                    </form>
                    @push('js')
                        <script>
                            $(document).ready(function () {
                                $('button.btn-validasi').on('click', function(e){
                                    e.preventDefault();
                                    var self = $(this);
                                    swal({
                                        title               : "Apakah anda yakin ingin menarik ajuan untuk diperbaiki?",
                                        text                : "Jika sudah yakin, silahkan klik tombol setuju dan lakukan perbaikan data hanya pada bagian yang tidak valid",
                                        icon                : "warning",
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
        </div>
    </div>
@endsection
