@extends('template.default')
@include('__partial.datatable_class')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"></i> PENGAJUAN DRAFT USUL</h3>
        <div class="card-tools">
            @if($profil->id_stat_mhs == 'A')
            @if (!is_null($jenis_seminar_ta))

            @if(is_null($pengajuan))
            <form action="{{route('tugas_akhir.pengajuan_draft_usul.tambah')}}" method="GET">
                @csrf
                @method('GET')
                <input type="hidden" name="id_jns_seminar" value="{{ $jenis_seminar_ta->id_jns_seminar }}">
                <button class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Tambah Ajuan Draft Usul</button>
            </form>

            @else
            @if(in_array($pengajuan->stat_ajuan, [0]) && is_null($pengajuan->judul_draft_usul_baru))
            {!! buttonAddMultipleId('tugas_akhir.pengajuan_draft_usul.detail', [Crypt::encrypt($pengajuan->id_ajuan_draft_usul)], 'Tambah Ajuan Draft Usul') !!}

            @elseif(in_array($pengajuan->stat_ajuan, [0]))
            <a href="{{ route('tugas_akhir.pengajuan_draft_usul.detail', Crypt::encrypt($pengajuan->id_ajuan_draft_usul)) }}" class="btn btn-flat btn-xs btn-warning">
                <i class="fas fa-pencil-alt"></i> Lengkapi Ajuan Draft Usul
            </a>
            @elseif (in_array($pengajuan->stat_ajuan, [1, 3, 4]))
            <form action="{{ route('tugas_akhir.pengajuan_draft_usul.delete', Crypt::encrypt($pengajuan->id_ajuan_draft_usul)) }}"
                class="validasi_form" style="display: inline;" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-danger btn-flat btn-validasi"><i class="fa fa-refresh"></i> Tarik Kembali dan Perbaiki Pengajuan Draft Usul</button>
            </form>
            @push('js')
            <script>
                $(document).ready(function() {
                    $('button.btn-validasi').on('click', function(e) {
                        e.preventDefault();
                        var self = $(this);
                        swal({
                            title: "Apakah anda yakin ingin menarik ajuan untuk diperbaiki?",
                            text: "Jika sudah yakin, silahkan klik tombol setuju dan lakukan perbaikan data",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    text: "Batal",
                                    value: null,
                                    closeModal: true,
                                    visible: true,
                                },
                                text: {
                                    text: "Setuju!",
                                    value: true,
                                    visible: true,
                                    closeModal: false,
                                }
                            },
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                self.parents(".validasi_form").submit();
                            }
                        })
                    });
                })
            </script>
            @endpush
            @else
            {{-- Tidak ada tombol --}}
            @endif
            @endif
            @else
            @push('js')
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        html: `Pengajuan draft usul tidak tersedia.<br>
                       Silakan hubungi admin prodi untuk mendaftarkan seminar prodi (Proposal/Hasil/Komprehensif) terlebih dahulu.`,
                        showCloseButton: true
                    });
                });
            </script>
            @endpush

            @endif
            @endif
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-data">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Judul Usul Penelitian</th>
                        <th>Tanggal Ajuan</th>
                        <th class="text-center">Umur Ajuan<br>(Hari)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data AS $no=>$each_data)
                    @if(!is_null ($each_data->judul_draft_usul_baru))
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{$each_data->judul_draft_usul_baru}}</td>
                        <td>{{ tglWaktuIndonesia($each_data->wkt_ajuan) }}</td>
                        <td class="text-center">{{ $each_data->umur_ajuan }}</td>
                        <td>
                            @if($each_data->stat_ajuan==0 && !is_null($each_data->judul_draft_usul_baru))
                            <span class="badge badge--info">--Draft--</span>
                            @elseif($each_data->stat_ajuan==1)
                            <span class="badge badge--info">--Diajukan--</span>
                            @elseif($each_data->stat_ajuan==2)
                            <span class="badge badge--success">--Disetujui--</span>
                            @elseif($each_data->stat_ajuan==3)
                            <span class="badge badge--danger">--Ditolak--</span>
                            @else
                            <span class="badge badge--warning">--Ditangguhkan--</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tugas_akhir.pengajuan_draft_usul.detail',Crypt::encrypt($each_data->id_ajuan_draft_usul)) }}" class="btn btn-flat btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Detail Data"><i class="fas fa-info-circle"></i></a>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection