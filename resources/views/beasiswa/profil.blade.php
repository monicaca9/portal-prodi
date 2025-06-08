@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-user"></i> BIODATA PROFIL</h3>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="biodata_head">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#biodata" aria-expanded="true" aria-controls="biodata">
                                Biodata Diri
                            </button>
                        </h2>
                    </div>

                    <div id="biodata" class="collapse show" aria-labelledby="biodata_head" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                                </div>
                                <div class="col-sm-9">
                                    <table class="table table-striped">
                                        <tbody>
                                        {!! tableRow('Nama Lengkap',$profil->nm_pd) !!}
                                        {!! tableRow('NPM',$profil->nim) !!}
                                        {!! tableRow('Homebase',$profil->nm_lemb) !!}
                                        {!! tableRow('NIK',$profil->nik) !!}
                                        {!! tableRow('Jenis Kelamin',$profil->jk) !!}
                                        {!! tableRow('Tempat/Tanggal Lahir',($profil->tmpt_lahir.', '.tglIndonesia($profil->tgl_lahir))) !!}
                                        {!! tableRow('Agama',null) !!}
                                        {!! tableRow('Alamat',$profil->jln) !!}
                                        {!! tableRow('RT/RW',(is_null($profil->rt)?'-':(($profil->rt==0)?'-':$profil->rt)).'/'.(is_null($profil->rw)?'-':(($profil->rw==0)?'-':$profil->rw))) !!}
                                        {!! tableRow('Desa/Kelurahan',$profil->ds_kel) !!}
                                        {!! tableRow('Kota/Kabupaten',null) !!}
                                        {!! tableRow('Kewarganegaraan',null) !!}
                                        {!! tableRow('No. Telepon Rumah',$profil->tlpn_rumah) !!}
                                        {!! tableRow('No. HP',$profil->tlpn_hp) !!}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="keluarga_head">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#keluarga" aria-expanded="false" aria-controls="keluarga">
                                Data Keluarga
                            </button>
                        </h2>
                    </div>
                    <div id="keluarga" class="collapse" aria-labelledby="keluarga_head" data-parent="#accordionExample">
                        <div class="card-body">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="lainnya_head">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#lainnya" aria-expanded="false" aria-controls="lainnya">
                                Data Lainnya
                            </button>
                        </h2>
                    </div>
                    <div id="lainnya" class="collapse" aria-labelledby="lainnya_head" data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-striped">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
