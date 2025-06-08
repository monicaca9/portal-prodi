@extends('template.default')
@include('__partial.datatable_class')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Distribusi Peran Dosen Pembimbing dan Penguji - {{ $detail_dosen->nm_sdm . ' (' . $detail_dosen->nidn . ')' }}</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tbody>
                    {!! tableRow('Nama ', $detail_dosen->nm_sdm) !!}
                    {!! tableRow('NIP ', $detail_dosen->nip) !!}
                    {!! tableRow('Asal Prodi ', $detail_dosen->nm_prodi) !!}
                    {!! tableRow('Total Mahasiswa Dibimbing/Diuji', isset($peran_seminar[0]) ? $peran_seminar[0]->total_mahasiswa : 0) !!}

                </tbody>
            </table>
        </div>
        <hr style=" background-color: orange;">
        <form action="" class="form-horizontal form-inside">
            {!! FormInputSelect('angkatan','Angkatan Mahasiswa',false,true,$list_angkatan,(isset($angkatan)?$angkatan:null)) !!}
            {!! FormInputSelect('kategori','Kategori',false,true,$list_jns_seminar,(isset($kategori)?$kategori:null)) !!}
            {!! FormInputSelect('peran','Peran Dosen',false,true,$list_peran_seminar,(isset($peran_dosen)?$peran_dosen:null)) !!}
        </form>
        <hr style=" background-color: green;">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama/NPM Mahasiswa</th>
                        <th>Angkatan</th>
                        <th>Asal Prodi</th>
                        <th>Kategori</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peran_seminar AS $no => $each_peran_seminar)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $each_peran_seminar->nm_pd }}</td>
                        <td>{{ $each_peran_seminar->angkatan }}</td>
                        <td>{{ $each_peran_seminar->asal_prodi }}</td>
                        <td>{{ $each_peran_seminar->nm_jns_seminar }}</td>
                        <td>
                            {{ config('mp.data_master.peran_seminar.' . $each_peran_seminar->peran) . ' Ke-' . $each_peran_seminar->urutan }}
                        </td>
                        <td>
                            <a href="{{ route('dashboard.distribusi_dosen_mahasiswa.detail',[Crypt::encrypt($each_peran_seminar->id_reg_pd), Crypt::encrypt($each_peran_seminar->id_jns_seminar)]) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('dashboard')) !!}
    </div>
</div>
</div>

@endsection
@push('js')
<script>
    $(document).ready(function() {
        $('#angkatan, #kategori, #peran').on('change', function() {
            var angkatan = $('#angkatan').val();
            var kategori = $('#kategori').val();
            var peran = $('#peran').val();
            var urlParams = new URLSearchParams(window.location.search);

            if ($(this).attr('id') === 'angkatan') {
                if (angkatan) {
                    urlParams.set('angkatan', angkatan);
                } else {
                    urlParams.delete('angkatan');
                }
            }

            if ($(this).attr('id') === 'kategori') {
                if (kategori) {
                    urlParams.set('kategori', kategori);
                } else {
                    urlParams.delete('kategori');
                }
            }

            if ($(this).attr('id') === 'peran') {
                if (peran) {
                    urlParams.set('peran', peran);
                } else {
                    urlParams.delete('peran');
                }
            }

            window.location.search = urlParams.toString();
        });
    });
</script>
@endpush