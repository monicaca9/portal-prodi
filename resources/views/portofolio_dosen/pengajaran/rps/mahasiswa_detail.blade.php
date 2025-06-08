@extends('template.default')
@include('__partial.datatable')


@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> RPS - {{ $mk->kode_mk.' - '.$mk->nm_mk }}</h3>
            <a href="{{ route('pelaksanaan_pendidikan.pengajaran.viewpdf',Crypt::encrypt($mk->id_mk)) }}" class="btn btn-primary pull-right btn-flat">Cetak RPS</a>
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
                    
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>

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
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>
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
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            
                </div>

            <div class="table-responsive">
                <h4>Rencana Pembelajaran Semester (RPS)</h4>
                <table class="table table-striped table-hover">
                    <tbody>
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
                                <td>{!! $each_detail->bobot !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
</table>
                    </tbody>
                </table>
            </div>
            
        </div>
        <div class="card-footer">
            {!! buttonBack(route('pelaksanaan_pendidikan.pengajaran')) !!}
        </div>
    </div>
@endsection
