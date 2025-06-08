@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-registered"></i> Detail Riwayat Seminar - {{ $profil->nm_pd }}</h3>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"> Biodata Pendaftar</h3>
                </div>
                <div class="card-body" style="margin: 0;padding: 0">
                    <div class="row">
                        <div class="col-sm-3 text-center mt-4">
                            @if(is_null($profil->id_blob))
                                <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                            @else
                                <?php $foto = DB::table('dok.large_object')->where('id_blob',$profil->id_blob)->first(); ?>
                                <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                            @endif
                        </div>
                        <div class="col-sm-9">
                            <table class="table table-striped">
                                <tbody>
                                {!! tableRow('Nama Lengkap',$profil->nm_pd) !!}
                                {!! tableRow('NPM',$profil->nim) !!}
                                {!! tableRow('Homebase',$profil->prodi) !!}
                                {!! tableRow('IPK Terakhir',$profil->ipk.' (Semester: '.$profil->nm_smt.')') !!}
                                {!! tableRow('Tempat/Tanggal Lahir',($profil->tmpt_lahir.', '.tglIndonesia($profil->tgl_lahir))) !!}
                                {!! tableRow('No. HP',$profil->tlpn_hp) !!}
                                {!! tableRow('Status',$profil->nm_stat_mhs) !!}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Detail Data Seminar</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            {!! tableRow('Jenis Seminar',$data->jenis_seminar->nm_jns_seminar) !!}
                            {!! tableRow('Judul Seminar',$data->lokasi_seminar_baru) !!}
                            {!! tableRow('Tanggal Seminar',tglIndonesia($data->tgl_seminar_baru)) !!}
                            {!! tableRow('No. SK Seminar',$data->sk_seminar_baru) !!}
                            {!! tableRow('Tanggal SK Seminar',tglIndonesia($data->tgl_sk_seminar_baru)) !!}
                            {!! tableRow('Nilai Seminar',$data->nilai_seminar_baru) !!}
                            {!! tableRow('Huruf Nilai',$data->huruf_nilai_seminar_baru) !!}
                        </tbody>
                    </table>
                </div>
            </div>
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
                                <th>Jenis Dokumen</th>
                                <th>Waktu Unggah</th>
                                <th>File</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($dokumen AS $no_dok=>$each_dok)
                                <tr>
                                    <td>{{ $no_dok+1 }}</td>
                                    <td>{{ $each_dok->nm_dok }}</td>
                                    <td>{{ $each_dok->nm_jns_dok }}</td>
                                    <td>{{ tglWaktuIndonesia($each_dok->wkt_unggah) }}</td>
                                    <td><a href="{{ route('dokumen.preview',Crypt::encrypt($each_dok->id_dok)) }}" class="btn btn-xs btn-flat btn-info"><i class="fa fa-download"></i></a></td>
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
            @if($data->stat_ajuan=='1')
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title"><i class="fas fa-check-square"></i> Validasi Ajuan Riwayat Seminar</h3>
                    </div>
                    <form class="validasi_form" action="{{ route('validasi.riwayat_seminar.update',Crypt::encrypt($ajuan->id_ver_ajuan)) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="nm_verifikator" value="{{ auth()->user()->nm_pengguna }}">
                        {!! FormInputSelect('status_validasi','Status Periksa',true,false,config('mp.data_master.status_periksa'),$data->stat_ajuan) !!}
                        {!! FormInputTextarea('ket_periksa','Keterangan',true,$ajuan->ket_periksa) !!}
                    </div>
                    <div class="card-footer">
                        {!! buttonBack(route('validasi.riwayat_seminar')) !!}
                        <div class="pull-right">
                            <button type="submit" class="btn btn-flat btn-primary tombol_validasi"><i class="fa fa-save"></i> Simpan Validasi</button>
                        </div>
                    </div>
                    </form>
                </div>

                @push('js')
                    <script>
                        $(document).ready(function () {
                            $('button.tombol_validasi').on('click', function(e){
                                e.preventDefault();
                                var self = $(this);
                                swal({
                                    title               : "Lakukan Validasi Riwayat Ajuan?",
                                    text                : "Jika sudah divalidasi maka anda tidak bisa mengubah datanya kembali",
                                    icon                : "warning",
                                    buttons: {
                                        cancel: {
                                            text: "Batal",
                                            value: null,
                                            closeModal: true,
                                            visible: true,
                                        },
                                        text: {
                                            text: "Ya, Validasi Ajuan!",
                                            value: true,
                                            visible: true,
                                            closeModal: false,
                                        }
                                    },
                                    dangerMode         : true,
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
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Detail Ajuan</h3>
                    </div>
                    <div class="card-body" style="margin: 0; padding: 0">
                        <table class="table table-striped">
                            <tbody>
                            {!! tableRow('Status Ajuan','<span class="badge badge-info">'.config('mp.data_master.status_validasi.'.$data->stat_ajuan).'</span>') !!}
                            {!! tableRow('Waktu Verifikasi',tglWaktuIndonesia($ajuan->wkt_selesai_ver)) !!}
                            {!! tableRow('Verifikator',$ajuan->nm_verifikator) !!}
                            {!! tableRow('Keterangan',$ajuan->ket_periksa) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        @if($ajuan->status_periksa!='N')
            <div class="card-footer">
                {!! buttonBack(route('validasi.riwayat_seminar_kaprodi')) !!}
            </div>
        @endif
    </div>
@endsection
