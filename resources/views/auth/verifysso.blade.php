@extends('template.default')
@section('title', 'Pastikan email yang dimasukkan valid!')
@section('content')
    <form action="{{ route('auth.do_verifysso') }}" method="post">
        {!! csrf_field() !!}
        @section('content')
    <div class="card">
        <div class="card-header">
                <h3 class="card-title"><i class="fa fa-user"></i> DETAIL BIODATA</h3>
        </div>
        <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div class="card-header" id="biodata_head">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#biodata" aria-expanded="true" aria-controls="biodata">
                                    Biodata Diri
                                </button>
                            </h2>
                
                            <div id="biodata" class="collapse show" aria-labelledby="biodata_head" data-parent="#accordionExample">
                                <div class="card-body">
                                <table class="table table-striped">
                                    <tbody>
                                        {!! tableRow('Nama Lengkap',$datas['nm_pd']) !!}
                                        {!! tableRow('NPM',$datas['npm']) !!}
                                        {!! tableRow('Prodi',$datas['nm_prodi']) !!}
                                        {!! tableRow('Status',$datas['status_sekarang']) !!}
                                        {!! tableRow('Periode Masuk',$datas['periode_masuk']) !!}
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->

            <div class="row">
                <button type="submit" class="btn btn-info btn-block">Klaim npm</button>
            </div>
            </div>
    </div>
    </form>
@endsection