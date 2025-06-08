@extends('template.default')
@include('__partial.datatable_class')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.min.js"
    integrity="sha256-rPPF6R+AH/Gilj2aC00ZAuB2EKmnEjXlEWx5MkAp7bw=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/main.css"
    integrity="sha256-ejA/z0dc7D+StbJL/0HAnRG/Xae3yS2gzg0OAnIURC4=" crossorigin="anonymous">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var datakelender = [
            <?php
            foreach ($jadwal_seminar as $item) {
            ?> {
                    "title": "<?= $item['title']; ?>",
                    "jam": "<?= $item['jam']; ?>",
                    "start": new Date("<?= $item['start']; ?>").toISOString(),
                    "end": new Date("<?= $item['end']; ?>").toISOString(),
                    "pembimbing_penguji": "<?= addslashes($item['pembimbing_penguji']); ?>"
                },
            <?php
            }
            ?>
        ];
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 550,
            eventClick: function(info) {
                var eventObj = info.event;
                var stringTitle = eventObj.title.split(',');

                Swal.fire({
                    title: stringTitle[0],
                    html: " Seminar " + stringTitle[1] + "<br> Gedung :"+ stringTitle[2] + " <br> Ruang :" + stringTitle[3] +
                        "<br>Jam : " + eventObj.extendedProps.jam +
                        "<br>" + eventObj.extendedProps.pembimbing_penguji,
                    showCloseButton: true,
                });
            },
            events: datakelender
        });
        calendar.render();
    });
</script>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title"> Biodata Dosen</h3>
            </div>
            <div class="card-body" style="margin: 0;padding: 0">
                <div class="text-center">
                    @if(is_null($profil->id_blob))
                    <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                    @else
                    <?php $foto = DB::table('dok.large_object')->where('id_blob', $profil->id_blob)->first(); ?>
                    <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                    @endif
                </div>
                <table class="table table-striped" style="margin: 0;padding: 0">
                    <tbody>
                        {!! tableRow('Nama Lengkap',$profil->nm_sdm) !!}
                        {!! tableRow('NIDN',$profil->nidn) !!}
                        {!! tableRow('NIP',$profil->nip) !!}
                        {!! tableRow('Asal Prodi',$profil->nm_prodi) !!}
                        {!! tableRow('Ikatan Kerja',$profil->nm_ikatan_kerja) !!}
                        {!! tableRow('Status Pegawai', $profil->nm_stat_pegawai) !!}
                        {!! tableRow('Status Aktif',$profil->nm_stat_aktif) !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Jadwal Seminar Prodi {{$profil->nm_prodi}}</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="response"></div>

                <div id='calendar'></div>
            </div>
        </div>
    </div>
</div>
<!-- <div class="col-md-8"> -->
<div class="card">
    <div class="card-header bg-info">
        <h3 class="card-title"> Distribusi Dosen Pembimbing dan Penguji</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered table-data">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama/NIDN</th>
                        <th>Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftar_dosen AS $no=>$each_dosen)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{!! $each_dosen->nm_sdm . ' (' . $each_dosen->nidn . ')' !!}</td>
                        <td>{{ $each_dosen->nm_prodi }}</td>
                        <td class="text-center">
                            <a href="{{route('dashboard.distribusi_dosen_mahasiswa',[Crypt::encrypt($each_dosen->id_sdm)])}}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- </div> -->
<!-- </div> -->
@endsection