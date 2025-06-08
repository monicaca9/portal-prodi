@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-registered"> </i> Pengajuan Draf Usul Penelitian - {{ $profil->nm_pd.' ('.$profil->nim.')' }}</h3>
    </div>

    <div class="card-body">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Biodata Mahasiswa</h3>
            </div>
            <div class="card-body" style="margin: 0;padding: 0">
                <div class="row">
                    <div class="col-sm-3 text-center mt-4">
                        @if (is_null($profil->id_blob))
                        <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                        @else
                        <?php $foto = DB::table('dok.large_object')
                            ->where('id_blob', $profil->id_blob)
                            ->first();
                        ?>
                        <img src="data:{{ $foto->mime_type }};base64,{{ stream_get_contents($foto->blob_content) }}"
                            width="200" alt="foto">
                        @endif
                    </div>
                    <!-- end of div class="col-sm-3 text-center mt-4 -->

                    <div class="col-sm-9">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Lengkap', $profil->nm_pd)!!}
                                {!! tableRow('NPM', $profil->nim)!!}
                                {!! tableRow('Program Studi', $profil->prodi)!!}
                                {!! tableRow('IPK Terakhir', $profil->ipk . ' (Semester: ' . $profil->nm_smt . ')') !!}
                                {!! tableRow('Tempat/Tanggal Lahir', $profil->tmpt_lahir . ', ' . tglIndonesia($profil->tgl_lahir)) !!}
                                {!! tableRow('No. HP', $profil->tlpn_hp)!!}
                                {!! tableRow('Status', $profil->nm_stat_mhs)!!}
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div class="row" -->

            </div>
            <!-- end of class card body biodata mahasiswa -->
        </div>
        <!-- end of class card biodata mahasiswa -->

        @if($data->stat_ajuan==0)
        <form action="{{route('tugas_akhir.pengajuan_draft_usul.simpan')}}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_ajuan_draft_usul" value="{{ $data->id_ajuan_draft_usul }}">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-history"></i> Detail Data Ajuan Draft Usul Penelitian</h3>
                    @if($data->stat_ajuan ==0)
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    {!! FormInputText('judul_draft_usul_baru', 'Judul Draft Usul Penelitian', 'text', $data->judul_draft_usul_baru, ['required'=>true])!!}
                    {!! FormInputTextarea('keterangan','Keterangan',true, $data->keterangan) !!}
                </div>
            </div>
        </form>
        @else
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Ajuan Draft Usul Penelitian</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        {!! tableRow('Judul Draft Usul Penelitian',$data->judul_draft_usul_baru) !!}
                        {!! tableRow('Keterangan',$data->keterangan) !!}
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header bg-black">
                <h3 class="card-title"><i class="fas fa-th-list"></i> Daftar Dokumen</h3>
            </div>

            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Jenis Dokumemn</th>
                                <th>Waktu Unggah</th>
                                <th>File</th>
                                @if($data->stat_ajuan==0)
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($dokumen AS $no=>$each_dok)
                            <tr>
                                <td>{{$no+1 }}</td>
                                <td>{{$each_dok->nm_dok }}</td>
                                <td>{{$each_dok->nm_jns_dok }}</td>
                                <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah)}}</td>
                                <td><a href="{{ route('dokumen.preview', Crypt::encrypt($each_dok->id_dok))}}" target="_blank" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
                                @if($data->stat_ajuan==0)
                                <td>
                                    {!! buttonDelete('tugas_akhir.pengajuan_draft_usul.delete_dok_ajuan',Crypt::encrypt($each_dok->id_dok_ajuan_draft_usul),'Hapus Dokumen') !!}
                                </td>
                                @endif
                            </tr>

                            @empty
                            <tr>
                                <td colspan="6" class="text-center">--Tidak ada dokumen--</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- upload dokumen -->
        @if($data->stat_ajuan==0)
        <div class="card">
            <form action="{{ route('tugas_akhir.pengajuan_draft_usul.simpan_dokumen') }}" enctype="multipart/form-data" method="post">
                @csrf
                <input type="hidden" name="id_ajuan_draft_usul" value="{{ $data->id_ajuan_draft_usul }}">
                <div class="card-header bg-black">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> Upload Dokumen</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-primary" type="submit"><i class="fa fa-save"></i> SIMPAN</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Maksimal dokumen 1 MB, dengan format PDF
                    </div>
                    {!! FormInputText('nm_dok','Nama Dokumen','text',null,['required'=>true]) !!}
                    {!! FormInputSelect('id_jns_dok','Jenis Dokumen',true,true,$jenis_dok) !!}
                    {!! FormInputText('file_dok','File Dokumen','file',null,['properties'=>['accept'=>'application/pdf'], 'required'=>true]) !!}
                    {!! FormInputText('url','URL','text',null) !!}
                    {!! FormInputTextarea('ket_dok','Keterangan Dokumen') !!}
                </div>
            </form>
        </div>
        @else
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Daftar Riwayat Validasi</h3>
            </div>
            <div class="card-body" style="margin: 0; padding: 0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Validator</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Waktu Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validasi AS $no_val=>$each_validasi)
                            <tr>
                                <td>{{ $no_val+1 }}</td>
                                <td>{{ $each_validasi->nm_verifikator }}</td>
                                <td>{{ config('mp.data_master.status_periksa.'.$each_validasi->status_periksa) }}</td>
                                <td>{{ $each_validasi->ket_periksa }}</td>
                                <td>{{ tglWaktuIndonesia($each_validasi->wkt_selesai_ver) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        @endif

        <!-- end of div upload dokumen -->


        <div class="card-footer">
            {!! buttonBack(route('tugas_akhir.pengajuan_draft_usul')) !!}
            <div class="pull-right">
                @if($data->stat_ajuan==0)
                <form action="{{ route('tugas_akhir.pengajuan_draft_usul.simpan_permanen_ajuan') }}" class="validasi_form" style="display: inline;" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_ajuan_draft_usul" value="{{ $data->id_ajuan_draft_usul }}">
                    <button class="btn btn-xs btn-success btn-flat btn-validasi"><i class="fa fa-check"></i> Ajukan Draft Usul Penelitian</button>
                </form>
                @push('js')
                <script>
                    $(document).ready(function() {
                        $('button.btn-validasi').on('click', function(e) {
                            e.preventDefault();
                            var self = $(this);
                            swal({
                                title: "Apakah anda yakin mengajukan pengajuan draft usul penelitian?",
                                text: "Jika sudah terkirim maka anda tidak bisa mengubah/menambahkan datanya kembali",
                                icon: "warning",
                                buttons: {
                                    cancel: {
                                        text: "Batal",
                                        value: null,
                                        closeModal: true,
                                        visible: true,
                                    },
                                    text: {
                                        text: "Ya, saya yakin!",
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
                @elseif(in_array($data->stat_ajuan,[1,3,4]))
                <form action="{{ route('tugas_akhir.pengajuan_draft_usul.delete',Crypt::encrypt($data->id_ajuan_draft_usul)) }}" class="validasi_form" style="display: inline;" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-danger btn-flat btn-validasi"><i class="fa fa-refresh"></i> Tarik Kembali dan Perbaiki Pengajuan Draft Usul </button>
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
                @endif
            </div>
        </div>
    </div>
    @endsection