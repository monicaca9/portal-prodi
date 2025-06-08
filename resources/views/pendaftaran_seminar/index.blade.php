@extends('template.default')
@include('__partial.datatable_class')

@section('content')
    @if(config('modul_pp.module.daftar_seminar')==1)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-registered"></i> PENDAFTARAN SEMINAR</h3>
                <div class="card-tools">
                    @if($profil->id_stat_mhs=='A')
                        @if(is_null($daftar_awal))
                            @if(!is_null($list_seminar))
                                {!! buttonAddMultipleId('pendaftaran_seminar.tambah', [Crypt::encrypt($list_seminar->id_seminar_prodi)], 'Daftar ' . $list_seminar->jenisSeminar->nm_jns_seminar) !!}
                            @else
                                <p>Data seminar tidak tersedia. Silakan hubungi admin prodi.</p>
                            @endif

                        @else
                            @if(!is_null($list_seminar) && $daftar_awal->id_seminar_prodi != $list_seminar->id_seminar_prodi)
                                {!! buttonAddMultipleId('pendaftaran_seminar.tambah', [Crypt::encrypt($list_seminar->id_seminar_prodi)], 'Daftar ' . $list_seminar->jenisSeminar->nm_jns_seminar) !!}
                            @elseif($daftar_awal->nm_jns_seminar == 'Komprehensif' && $daftar_awal->status_validasi == '2' && $daftar_awal->hari != null)
                                {{-- Tidak ada tombol --}}
                            @else
                                <a href="{{ route('pendaftaran_seminar.detail', Crypt::encrypt($daftar_awal->id_daftar_seminar)) }}" class="btn btn-flat btn-xs btn-warning">
                                    <i class="fas fa-pencil-alt"></i> Lengkapi Pendaftaran {{ $list_seminar?->jenisSeminar->nm_jns_seminar ?? 'Seminar' }}
                                </a>
                            @endif
                        @endif
                    @endif
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-data">
                        <thead>
                        <tr>
                            <th>NO</th>
                            <th>JENIS SEMINAR</th>
                            <th>KETERANGAN</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pendaftaran AS $no_dftr=>$each_daftar)
                            <tr>
                                <td>{{ $no_dftr+1 }}</td>
                                <td>{{ $each_daftar->nm_jns_seminar }}</td>
                                <td style="margin: 0;padding: 0">
                                    <table style="width: 100%">
                                        <tbody>
                                        {!! tableRow('Judul',$each_daftar->judul_akt_mhs) !!}
                                        {!! tableRow('Lokasi Seminar',(!is_null($each_daftar->nm_gedung)?($each_daftar->nm_gedung.' ('.$each_daftar->nm_ruang.')'):null)) !!}
                                        {!! tableRow('Waktu Seminar',(!is_null($each_daftar->hari)?(config('mp.data_master.hari')[$each_daftar->hari].', '.$each_daftar->tgl_mulai.' Pukul ( '.config('mp.data_master.waktu')[$each_daftar->waktu]." )"):null)) !!}
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    @if($each_daftar->status_validasi==0)
                                        <span class="badge badge--info">--Draft--</span>
                                    @elseif($each_daftar->status_validasi==1)
                                        <span class="badge badge--info">--Diajukan--</span>
                                    @elseif($each_daftar->status_validasi==2)
                                        <span class="badge badge--info">--Diserahkan ke Kaprodi--</span>
                                    @elseif($each_daftar->status_validasi==3)
                                        @if($each_daftar->a_selesai==0)
                                            @if($each_daftar->a_diproses==0)
                                                <span class="badge badge--success">--Disetujui--</span>
                                            @else
                                                <span class="badge badge-primary">--Bisa diproses--</span>
                                            @endif
                                        @else
                                            <span class="badge badge--success">--Selesai--</span>
                                        @endif
                                    @elseif($each_daftar->status_validasi==4)
                                        <span class="badge badge--danger">--Ditolak--</span>
                                    @else
                                        <span class="badge badge--warning">--Ditangguhkan--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($profil->id_stat_mhs=='A')
                                        @if($each_daftar->status_validasi==0)
                                            <a href="{{ route('pendaftaran_seminar.detail',Crypt::encrypt($each_daftar->id_daftar_seminar)) }}" class="btn btn-flat btn-xs btn-warning"><i class="fas fa-pencil-alt"></i> Lengkapi</a>
                                        @else
                                            {!! buttonShow('pendaftaran_seminar.detail',Crypt::encrypt($each_daftar->id_daftar_seminar),'Detail Pendaftaran '.$each_daftar->nm_jns_seminar) !!}
                                           
                                            @if($each_daftar->hari == null)

                                            @else
                                            <a href="{{ route('pendaftaran_seminar.beritaacara',Crypt::encrypt($each_daftar->id_daftar_seminar)) }}" target="_blank" class="btn btn-flat btn-xs btn-warning"><i class="fas fa-print"></i> Berita Acara</a>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-registered"></i> PENDAFTARAN SEMINAR</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="alert alert-info">
                    Pendaftaran Seminar sedang maintenance
                </div>
            </div>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history"></i> RIWAYAT SEMINAR</h3>
            <div class="card-tools">
                <a href="{{ route('pendaftaran_seminar.daftar_ajuan_riwayat') }}" class="btn btn-flat btn-info btn-sm"><i class="fas fa-list-alt"></i> Daftar Ajuan</a>
                {!! buttonAdd('pendaftaran_seminar.tambah_riwayat','Ajukan Riwayat Seminar') !!}
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-data">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Seminar</th>
                        <th>Judul Seminar</th>
                        <th>SK Seminar</th>
                        <th>Tanggal Seminar</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rwy_seminar AS $no_rwy=> $each_rwy)
                        <tr>
                            <td>{{ $no_rwy+1 }}</td>
                            <td>{{ $each_rwy->jenisSeminar->nm_jns_seminar }}</td>
                            <td>{{ $each_rwy->judul_akt_mhs }}</td>
                            <td>{!! $each_rwy->sk_seminar.'<br><strong>Tanggal SK: '.tglIndonesia($each_rwy->tgl_sk_seminar).'</strong>' !!}</td>
                            <td>{{ tglIndonesia($each_rwy->tgl_seminar) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
