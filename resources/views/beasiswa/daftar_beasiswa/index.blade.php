@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if(is_null($beasiswa))
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Beasiswa Aktif</h5>
                    <hr>
                    Anda belum terdaftar Beasiswa manapun
                </div>
            @else
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> Beasiswa Aktif</h5>
                    <hr>
                    Anda sudah terdaftar dan diterima {{ $beasiswa->nm_periode_beasiswa }}<br>
                    Terhitung mulai dari tanggal {{ tglIndonesia($beasiswa->tgl_terima) }}
                    @if(!is_null($beasiswa->tgl_selesai))
                        <br>
                        Berakhir tanggal {{ tglIndonesia($beasiswa->tgl_selesai) }}
                    @endif
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
                            <th class="text-center">Periode Pendaftaran</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(is_null($beasiswa))
                            @foreach($list_beasiswa AS $no=>$each_beasiswa)
                                <tr>
                                    <td>{{ $no+1 }}</td>
                                    <td>
                                        {!! $each_beasiswa->nm_periode_beasiswa.'<br>Total Pendaftar: '.$each_beasiswa->total_daftar.' Pendaftar<br>Total Kuota: '.$each_beasiswa->jmlh_terima !!}
                                    </td>
                                    <td class="text-center">{!! tglWaktuIndonesia($each_beasiswa->wkt_mulai).' <br>sampai<br> '.tglWaktuIndonesia($each_beasiswa->wkt_berakhir) !!}</td>
                                    <td><button type="button" class="btn btn-sm btn-info btn-flat button_info_periode" data-id_periode="{{ $each_beasiswa->id_periode_beasiswa }}">Lihat Persyaratan</button></td>
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
    <div class="modal fade" id="myModal" aria-modal="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul_modal"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="isi_modal">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                    <div id="tombol_modal"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var token = $("meta[name=csrf-token]").attr("content");
            $('.button_info_periode').click(function () {
                var id_periode = $(this).data('id_periode');
                $.ajax({
                    type  : "POST",
                    dataType  : "json",
                    url   : "{{ route('daftar_beasiswa.info') }}" ,
                    data  : {
                        "_token"    : "{{ csrf_token() }}",
                        "id_periode": id_periode,
                        "id_reg"    : "{{ $profil->id_reg_pd }}"
                    },
                    success : function(data){
                        $('#myModal').modal('show');
                        $('#judul_modal').html(data.judul);
                        $('#isi_modal').html(data.isi);
                        $('#tombol_modal').html(data.tombol);
                    }
                })
            });
        });
    </script>
@endpush
