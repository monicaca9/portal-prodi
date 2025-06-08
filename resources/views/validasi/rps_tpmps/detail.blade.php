@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Detail Ajuan RPS TPMPS - {{ $prodi->prodi }} - {{ $data->nm_mk }}</h3>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"> Informasi Pengajuan</h3>
                </div>
                <div class="card-body" style="margin: 0;padding: 0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                            {!! tableRow('Kode MK',$mk->kode_mk) !!}
                            {!! tableRow('Nama MK',$mk->nm_mk) !!}
                            {!! tableRow('SKS MK',$mk->sks_mk) !!}
                            {!! tableRow('Jenis MK',config('mp.data_master.jenis_matkul.'.$mk->jns_mk)) !!}
                            {!! tableRow('Identitas Pengaju RPS',$data->nm_sdm) !!}
                            {!! tableRow('Asal Prodi',$data->nm_lemb) !!}
                            {!! tableRow('Metode',config('mp.data_master.metode.'.$data->metode)) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Ajuan Rencana Pembelajaran Semester (RPS)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <h5>Capaian Pembelajaran Prodi Yang Dibebankan Pada Mata Kuliah</h5>
                    <hr>
                        <table class="table table-striped table-bordered">
                        <tbody>
                        @foreach($cpl_mk AS $each_data)
                            <tr>
                                <td>{{ $each_data->cpl->nm_cpl }}</td>
                                <td>{{ $each_data->cpl->desc_cpl }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        </table>
                <h5>Capaian Pembelajaran Mata Kuliah </h5>
                <hr>
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <tbody>
                        @foreach($cpmk AS $no=>$cpmks)
                            <tr>
                                <td>CPMK-{{ $no+1 }}</td>
                                <td>{{ $cpmks->cpmk }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>    
<h5>Daftar Pustaka</h5>
<hr>
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <tbody>
                        @foreach($dapusmk AS $no=>$dapusmks)
                            <tr>
                                <td>[{{ $no+1 }}]</td>
                                <td>{{ $dapusmks->daftar_pustaka }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                        <h4>Rincian Rencana Pembelajaran</h4>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Minggu ke-</th>
                                <th>Tujuan Khusus</th>
                                <th>Pokok Bahasan</th>
                                <th>Referensi</th>
                                <th>Sub Pokok Bahasan</th>
                                <th>Metode</th>
                                <th>Media</th>
                                <th>Aktifitas Penugasan</th>
                                <th>Bobot</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($detail AS $each_detail)
                                <tr>
                                    <td>{{ $each_detail->minggu_ke_baru }}</td>
                                    <td>{!! $each_detail->tujuan_khusus_baru !!}</td>
                                    <td>{!! $each_detail->pokok_bahasan_baru !!}</td>
                                    <td>{!! $each_detail->referensi_baru !!}</td>
                                    <td>{!! $each_detail->sub_pokok_bahasan_baru !!}</td>
                                    <td>{!! $each_detail->metode_baru !!}</td>
                                    <td>{!! $each_detail->media_baru !!}</td>
                                    <td>{!! $each_detail->akt_penugasan_baru !!}</td>
                                    @if($each_detail->bobot == null)
                                        <td> </td>
                                        @else
                                        <td>{!! $each_detail->bobot !!}%</td>
                                        @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-money-check-alt"></i> Dokumen Pendukung</h3>
                </div><!-- /.card-header -->
                <div class="card-body">
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
                                    <td colspan="5" class="text-center">--Tidak ada dokumen--</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Detail Ajuan</h3>
                </div>
                <div class="card-body" style="margin: 0; padding: 0">
                    <table class="table table-striped">
                        <tbody>
                        {!! tableRow('Status Ajuan','<span class="badge badge-info">'.config('mp.data_master.status_periksa.'.$data->status_periksa).' oleh '.config('mp.data_master.level_verifikasi_prodi.'.$data->level_ver).'</span>') !!}
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
                        <h3 class="card-title"><i class="fas fa-check-square"></i> Validasi Ajuan RPS</h3>
                    </div>
                    <form class="validasi_form" action="{{ route('validasi.rps_tpmps.update',Crypt::encrypt($data->id_ver_ajuan)) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="nm_verifikator" value="{{ auth()->user()->nm_pengguna }}">
                        {!! FormInputSelect('status_periksa','Status Periksa',config('mp.data_master.status_periksa'),$data->status_periksa) !!}
                        {!! FormInputTextarea('ket_periksa','Keterangan',$data->ket_periksa) !!}
                    </div>
                    <div class="card-footer">

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
            </div>
        @endif
    </div>
@endsection
