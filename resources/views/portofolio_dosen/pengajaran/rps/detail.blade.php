    @extends('template.default')
@include('__partial.datatable')
@include('__partial.ckeditor')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> Ajuan RPS - {{ $mk->kode_mk.' - '.$mk->nm_mk }}</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">
                <h4>Rincian MK</h4>
                <table class="table table-striped table-hover">
                    <tbody>
                        {!! tableRow('Kode MK',$mk->kode_mk) !!}
                        {!! tableRow('Nama MK',$mk->nm_mk) !!}
                        {!! tableRow('SKS MK',$mk->sks_mk) !!}
                        {!! tableRow('Jenis MK',config('mp.data_master.jenis_matkul.'.$mk->jns_mk)) !!}
                        @if ($ajuan->stat_ajuan!=0)
                        {!! tableRow('Status Ajuan','<span class="badge badge-info">'.config('mp.data_master.status_periksa.'.$cek_stat->status_periksa).' oleh '.config('mp.data_master.level_verifikasi_prodi.'.$cek_stat->level_ver).'</span>') !!}
                        @endif
                        {!! tableRow('Metode',config('mp.data_master.metode.'.$ajuan->metode)) !!}
                    </tbody>
                </table>
            </div>
            @if($ajuan->stat_ajuan==0)
                <form action="{{ route('pelaksanaan_pendidikan.pengajaran.rps.simpan',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps)]) }}" method="POST">
                @csrf
                @method('PUT')
            @endif
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-list-alt"></i> Ajuan Rencana Pembelajaran</h3>
                            
                        </div>
                        <div class="card-body">
                            @if($ajuan->stat_ajuan==0)
                            Template : 
                            <a href="https://docs.google.com/document/d/1FlklLhbHRk0f37esZyjmSDlGTW-Q9CDv/edit#heading=h.gjdgxs">Case Method</a> , 
                            <a href="https://docs.google.com/document/d/1bS8DhuVlDa5mDIOMgk4z5JvoeiVMA9bf/edit">Project Based Method</a>
                            <br>
                            <br>
                            {!! FormInputSelect('metode','Metode',config('mp.data_master.metode'),$ajuan->metode) !!}
                            @if($ajuan->stat_ajuan==0)
                                <div class="card-tools">
                                    <button class="btn btn-xs btn-primary pull-right" type="submit"><i class="fa fa-save"></i> SIMPAN METODE</button>
                                </div>
                            @endif
                            @endif
                            <br>
                            <hr>
                            <div class='table-responsive'>
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
                                    @if($ajuan->stat_ajuan==0)
                                    <th>Aksi</th>
                                    @endif
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
                                        @if($ajuan->stat_ajuan==0)
                                        <td>
                                            {!! buttonEditMultipleId('pelaksanaan_pendidikan.pengajaran.rps.detail_rincian',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps),Crypt::encrypt($each_detail->id_rincian_ajuan_rps)],'Edit Rincian Minggu ke-'.$each_detail->minggu_ke_baru) !!}
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            @if($ajuan->stat_ajuan==0)
                </form>
            @endif
        </table>
</div>
<table>
    
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
                                @if($ajuan->stat_ajuan==0)
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
                                    @if($ajuan->stat_ajuan==0)
                                        <td>
                                            {!! buttonDeleteMultipleId('pelaksanaan_pendidikan.pengajaran.rps.delete_dokumen',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps),Crypt::encrypt($each_dok->id_dok_ajuan_rps)],'Hapus Dokumen') !!}
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
            @if($ajuan->stat_ajuan==0)
                <hr>
                    <div class="card">
                        <form action="{{ route('pelaksanaan_pendidikan.pengajaran.rps.simpan_dokumen',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps)]) }}" enctype="multipart/form-data" method="post">
                            @csrf
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
                                {!! FormInputSelect('id_jns_dok','Jenis Dokumen',$jenis_dok) !!}
                                {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf']]) !!}
                                {!! FormInputText('url','URL','text',null) !!}
                                {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
                            </div>
                        </form>
                    </div>
            @endif
        </div>
        <div class="card-footer">
            {!! buttonBack(route('pelaksanaan_pendidikan.pengajaran.rps',Crypt::encrypt($mk->id_mk))) !!}
            @if($ajuan->stat_ajuan==0)
                <div class="pull-right">
                    <form action="{{ route('pelaksanaan_pendidikan.pengajaran.rps.simpan_permanen',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps)]) }}" class="validasi_form" style="display: inline;" method="POST">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-xs btn-primary btn-flat btn-validasi"><i class="fa fa-check"></i> Ajukan RPS Baru</button>
                    </form>

                    @push('js')
                        <script>
                            $(document).ready(function () {
                                $('button.btn-validasi').on('click', function(e){
                                    e.preventDefault();
                                    var self = $(this);
                                    swal({
                                        title               : "Apakah anda yakin mengajukan RPS Baru?",
                                        text                : "Jika sudah diajukan maka anda tidak bisa mengubah/menambahkan datanya kembali",
                                        icon                : "warning",
                                        buttons: {
                                            cancel: {
                                                text: "Batal",
                                                value: null,
                                                closeModal: true,
                                                visible: true,
                                            },
                                            text: {
                                                text: "Ya, Ajukan!",
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
                </div>
            @endif
        </div>
    </div>
@endsection
