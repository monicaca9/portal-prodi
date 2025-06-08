@extends('template.default')
@include('__partial.datatable')

@section('content')
    <?php
        $profil = DB::table('pdrd.peserta_didik AS pd')
            ->join('pdrd.reg_pd AS tr','tr.id_pd','=','pd.id_pd')
            ->join('pdrd.sms AS tprodi','tprodi.id_sms','=','tr.id_sms')
            ->where('pd.soft_delete',0)
            ->where('pd.id_pd',config('mp.data_master.default_mhs'))
            ->first();
        $beasiswa = [
            'PPA',
            'Beasiswa Djarum'
        ]
    ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-user"></i> DAFTAR BEASISWA MEMENUHI SYARAT AWAL</h3>
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
                        @foreach($beasiswa AS $no=>$each_beasiswa)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_beasiswa }}</td>
                                <td>8 Agustus 2020 - 10 November 2020</td>
                                <td><a href="" class="btn btn-sm btn-info btn-flat">Lihat Persyaratan</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-user"></i> PROFIL</h3>
                </div><!-- /.card-header -->
                <div class="card-body" style="margin: 0;padding: 0">
                    <table class="table table-striped">
                        <tbody>
                        {!! tableRow('Nama',$profil->nm_pd) !!}
                        {!! tableRow('NPM',$profil->nim) !!}
                        {!! tableRow('Homebase',$profil->nm_lemb) !!}
                        {!! tableRow('TMT',$profil->tmpt_lahir.', '.tglIndonesia($profil->tgl_lahir)) !!}
                        {!! tableRow('No. HP',$profil->tlpn_hp) !!}
                        {!! tableRow('Status','Aktif') !!}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
