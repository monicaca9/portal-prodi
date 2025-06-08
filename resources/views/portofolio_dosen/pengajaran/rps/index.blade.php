@extends('template.default')
@include('__partial.datatable')


@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> RPS - {{ $mk->kode_mk.' - '.$mk->nm_mk }}</h3>
            @if(!is_null($data))
            <a href="{{ route('pelaksanaan_pendidikan.pengajaran.viewpdf',Crypt::encrypt($mk->id_mk)) }}" class="btn btn-primary pull-right btn-flat">Cetak RPS</a>
            @endif
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
                </tbody>
            </table>
            </div>
        
            
<br>
            <div class="table-responsive">
                <h4>Capaian Pembelajaran Prodi Yang Dibebankan Pada Mata Kuliah</h4>
                <div class="row">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <tbody>
                        @foreach($cpl_mk AS $each_data)
                            <tr>
                                <td>{{ $each_data->cpl->nm_cpl }}</td>
                                <td>{{ $each_data->cpl->desc_cpl }}</td>
                                @if($cek_role->id_peran == 46)
                                <td>
                                {!! buttonDeleteMultipleId('pelaksanaan_pendidikan.pengajaran.delete_cpl_mk',[Crypt::encrypt($each_data->id_mk),Crypt::encrypt($each_data->id_cpl_mk)],'Hapus CPL') !!}
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>
                @if($cek_role->id_peran == 46)
                    <div class="card-footer">
                    <form action="{{ route('pelaksanaan_pendidikan.pengajaran.simpan_cpl_mk',[Crypt::encrypt($mk->id_mk)]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_cpl_mk" value="{{ $cpl }}">
                    {!! FormInputSelect('id_cpl','Kode CPL',$cpl) !!} 
                        <button type="submit" class="btn btn-primary pull-right btn-flat"><i class="fa fa-pencil"></i> Tambah CPL</button>
                    </form>
                </div>
                @endif

<br>
            <div class="table-responsive">
                <h4>Capaian Pembelajaran Mata Kuliah </h4>
                <div class="row">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <tbody>
                        @foreach($cpmk AS $no=>$cpmks)
                            <tr>
                                <td>CPMK - {{ $no+1 }}</td>
                                <td>{{ $cpmks->cpmk }}</td>
                                @if($cek_role->id_peran == 46)
                                <td>
                                {!! buttonDeleteMultipleId('pelaksanaan_pendidikan.pengajaran.delete_cpmk',[Crypt::encrypt($cpmks->id_mk),Crypt::encrypt($cpmks->id_cpmk)],'Hapus CPMK') !!}
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>
                @if($cek_role->id_peran == 46)
                <form action="{{ route('pelaksanaan_pendidikan.pengajaran.simpan_cpmk',[Crypt::encrypt($mk->id_mk)]) }}" method="POST">
                @csrf
                    <div class="card-footer">
                        <div class="form-group">
                            <textarea name="cpmk" class="form-control" required=""></textarea>
                            <button type="submit" class="btn btn-primary pull-right btn-flat"><i class="fa fa-pencil"></i> Tambah CPMK</button>
                        </div>
                    </div>
                </form>
                @endif

<br>
            <div class="table-responsive">
                <h4> Daftar Pustaka </h4>
                <div class="row">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <tbody>
                        @foreach($dapusmk AS $no=>$dapusmks)
                            <tr>
                                <td>[{{ $no+1 }}]</td>
                                <td>{{ $dapusmks->penulis}}({{$dapusmks->tahun}}),{{ $dapusmks->judul }} </td>
                                @if($cek_role->id_peran==46)
                                <td>
                                {!! buttonDeleteMultipleId('pelaksanaan_pendidikan.pengajaran.delete_daftar_pustaka_mk',[Crypt::encrypt($dapusmks->id_mk),Crypt::encrypt($dapusmks->id_daftar_pustaka_mk)],'Hapus Daftar Pustaka') !!}
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>
                @if($cek_role->id_peran == 46)
                <form action="{{ route('pelaksanaan_pendidikan.pengajaran.simpan_daftar_pustaka_mk',[Crypt::encrypt($mk->id_mk)]) }}" method="POST">
                @csrf
                    <div class="card-footer">
                        <div class="form-group">
                            Penulis<textarea name="penulis" class="form-control" required="true" cols="25" rows="1"></textarea>
                            Tahun<textarea name="tahun" class="form-control" required="true" cols="25" rows="1"></textarea>
                            Judul<textarea name="judul" class="form-control" required="true" cols="25" rows="1"></textarea>
                            <br>
                            <button type="submit" class="btn btn-primary pull-right btn-flat"><i class="fa fa-pencil"></i> Tambah Daftar Pustaka</button>
                        </div>
                    </div>
                </form>
                @endif

            <div class="table-responsive">
                <h4>Rencana Pembelajaran</h4>
                <table class="table table-striped table-hover">
                    <tbody>
                    @if(!is_null($data))
                    @if(!is_null($data))
                <?php $detail = \DB::table('manajemen.rincian_rps')->where('id_rps',$data->id_rps)->where('soft_delete',0)->orderBy('minggu_ke','ASC')->get(); ?>
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
                                <td>{{ $each_detail->minggu_ke }}</td>
                                <td>{!! $each_detail->tujuan_khusus !!}</td>
                                <td>{!! $each_detail->pokok_bahasan !!}</td>
                                <td>{!! $each_detail->referensi !!}</td>
                                <td>{!! $each_detail->sub_pokok_bahasan !!}</td>
                                <td>{!! $each_detail->metode !!}</td>
                                <td>{!! $each_detail->media !!}</td>
                                <td>{!! $each_detail->akt_penugasan !!}</td>
                                @if($each_detail->bobot == null)
                                        <td> </td>
                                        @else
                                        <td>{!! $each_detail->bobot !!}%</td>
                                        @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            @endif
                    @else
                        <?php $ajuan_individu=\DB::table('validasi.ajuan_rps')->where('id_mk',$mk->id_mk)->where('soft_delete',0)->first(); ?>
                        <tr>
                            <td colspan="3" class="text-center">
                                --Belum ada Data--
                                <br>
                                @if(is_null($ajuan_individu))
                                    <a href="{{ route('pelaksanaan_pendidikan.pengajaran.rps.tambah',[Crypt::encrypt($mk->id_mk)]) }}" class="btn btn-primary btn-flat">Ajukan RPS Baru</a>
                                @else
                                    @if($ajuan_individu->stat_ajuan==0)
                                        <a href="{{ route('pelaksanaan_pendidikan.pengajaran.rps.detail',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan_individu->id_ajuan_rps)]) }}" class="btn btn-primary btn-flat">Lanjutkan Ajukan RPS Baru</a>
                                    @else
                                        <a href="{{ route('pelaksanaan_pendidikan.pengajaran.rps.detail',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan_individu->id_ajuan_rps)]) }}" class="btn btn-info btn-flat">Detail Pengajuan RPS</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            @if($cek_role->id_peran == 46)
            <hr>
            <h4>Riwayat Pengajuan</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Pengajuan</th>
                        <th>Jenis Ajuan</th>
                        <th>Status Ajuan</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ajuan AS $no_ajuan=>$each_ajuan)
                        <tr>
                            <td>{{ $no_ajuan+1 }}</td>
                            <td>{{ tglWaktuIndonesia($each_ajuan->wkt_ajuan) }}</td>
                            <td>{{ config('mp.data_master.jenis_ajuan.'.$each_ajuan->jns_ajuan) }}</td>
                            <td>{{ config('mp.data_master.status_ajuan.'.$each_ajuan->stat_ajuan) }}</td>
                            <td>
                                <a href="{{ route('pelaksanaan_pendidikan.pengajaran.rps.detail',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($each_ajuan->id_ajuan_rps)]) }}" class="btn btn-info btn-flat">Detail Pengajuan RPS</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <div class="card-footer">
            {!! buttonBack(route('pelaksanaan_pendidikan.pengajaran')) !!}
        </div>
    </div>
@endsection
