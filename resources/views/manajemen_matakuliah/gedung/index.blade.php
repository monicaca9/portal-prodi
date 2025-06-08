@extends('template.default')
@include('__partial.datatable')

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
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-building"></i> Gedung dan Ruang {{ is_null($prodi)?'':$prodi->fakultas }}</h3>
        <div class="card-tools">
            {!! buttonAdd('gedung_ruang.tambah', 'Tambah Gedung Baru') !!}
        </div>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>LOKASI</th>
                        <th>GEDUNG</th>
                        <th>&Sigma; RUANG</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $no => $each_data)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ $each_data->fakultas->nm_lemb }}</td>
                        <td>{{ $each_data->nm_gedung }}</td>
                        <td>
                            <a href="{{ route('gedung_ruang.detail_ruang', Crypt::encrypt($each_data->id_gedung)) }}"
                                class="btn btn-info btn-flat btn-xs">{{ count($each_data->ruang) . ' Ruang' }}</a>
                        </td>
                        <td>
                            {!! buttonEdit(
                            'gedung_ruang.ubah',
                            Crypt::encrypt($each_data->id_gedung),
                            'Ubah
                            Gedung',
                            ) !!}
                            {!! buttonDelete(
                            'gedung_ruang.delete',
                            Crypt::encrypt($each_data->id_gedung),
                            'Hapus
                            Gedung',
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-building"></i> Jadwal Seminar</h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="response"></div>

        <div id='calendar'></div>
    </div>
</div>
<script>
</script>
@endsection