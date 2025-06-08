@extends('template.default')
@include('__partial.datatable_class')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Penilaian Seminar Prodi </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tbody>
                    {!! tableRow('Nama ', $detail_dosen->nm_sdm) !!}
                    {!! tableRow('NIP ', $detail_dosen->nip) !!}
                    {!! tableRow('Asal Prodi ', $detail_dosen->nm_prodi) !!}
                </tbody>
            </table>
        </div>
        <hr style=" background-color: orange;">
        <form action="" class="form-horizontal form-inside">
            {!! FormInputSelect('jenis_seminar','Jenis Seminar',false,true,$list_jns_seminar,(isset($jenis_seminar)?$jenis_seminar:null)) !!}
        </form>
        <hr>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama/NPM Mahasiswa</th>
                        <th>Angkatan</th>
                        <th>Asal Prodi</th>
                        <th>Jenis Seminar</th>
                        <th>Waktu Seminar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peran_dosen_pendaftar AS $no => $each_peran_seminar)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{!! $each_peran_seminar->nm_pd.'<br>('.$each_peran_seminar->nim.')' !!}</td>
                        <td>{{ $each_peran_seminar->angkatan }}</td>
                        <td>{{ $each_peran_seminar->asal_prodi }}</td>
                        <td>{{ $each_peran_seminar->nm_jns_seminar }}</td>
                        <td>
                            {{ config('mp.data_master.hari')[$each_peran_seminar->hari] ?? '-' }},
                            {{ tglIndonesia($each_peran_seminar->tgl_mulai) ?? '-'}} <br>
                            Pukul : {{ config('mp.data_master.waktu')[$each_peran_seminar->waktu] ?? '-' }}
                        </td>
                        <td>
                            <a href="{{ route('seminar_prodi.penilaian_seminar.detail', [Crypt::encrypt($each_peran_seminar->id_daftar_seminar)]) }}" class="btn btn-flat btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title=" Nilai Seminar Mahasiswa"><i class="fa fa-check-square-o"></i></a>
                            <a href="{{ route('berita_acara.detail',Crypt::encrypt($each_peran_seminar->id_daftar_seminar)) }}" target="_blank" class="btn btn-flat btn-xs btn-primary"><i class="fas fa-print"></i> Berita Acara</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@endsection
@push('js')
<script>
    $(document).ready(function() {
        $('#jenis_seminar').on('change', function() {
            var jenis_seminar = $('#jenis_seminar').val();
            var urlParams = new URLSearchParams(window.location.search);

            if ($(this).attr('id') === 'jenis_seminar') {
                if (jenis_seminar) {
                    urlParams.set('jenis_seminar', jenis_seminar);
                } else {
                    urlParams.delete('jenis_seminar');
                }
            }
            window.location.search = urlParams.toString();
        });
    });
</script>
@endpush