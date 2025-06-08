@extends('template.default')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-user"></i> DETAIL BIODATA</h3>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="biodata_head">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#biodata" aria-expanded="true" aria-controls="biodata">
                            Biodata Diri
                        </button>
                    </h2>
                </div>

                <div id="biodata" class="collapse show" aria-labelledby="biodata_head" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                @if(is_null($data->id_blob))
                                <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                                @else
                                <?php $foto = DB::table('dok.large_object')->where('id_blob', $data->id_blob)->first(); ?>
                                <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                                @endif
                            </div>
                            <div class="col-sm-9">
                                <table class="table table-striped">
                                    <tbody>
                                        {!! tableRow('Nama Lengkap',$data->nm_pd) !!}
                                        {!! tableRow('NIK',$data->nik) !!}
                                        {!! tableRow('Jenis Kelamin',$data->jenis_kelamin) !!}
                                        {!! tableRow('Tempat/Tanggal Lahir',($data->tmpt_lahir.', '.tglIndonesia($data->tgl_lahir))) !!}
                                        {!! tableRow('Agama',$data->nm_agama) !!}
                                        {!! tableRow('Alamat',$data->jln) !!}
                                        {!! tableRow('RT/RW',(is_null($data->rt)?'-':(($data->rt==0)?'-':$data->rt)).'/'.(is_null($data->rw)?'-':(($data->rw==0)?'-':$data->rw))) !!}
                                        {!! tableRow('Desa/Kelurahan',$data->ds_kel) !!}
                                        {!! tableRow('Kota/Kabupaten',$data->nm_wil) !!}
                                        {!! tableRow('Kewarganegaraan',$data->nm_negara) !!}
                                        {!! tableRow('No. Telepon Rumah',$data->tlpn_rumah) !!}
                                        {!! tableRow('No. HP',$data->tlpn_hp) !!}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header" id="pddikti_head">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#pddikti" aria-expanded="false" aria-controls="pddikti">
                            Data PDDIKTI
                        </button>
                    </h2>
                </div>
                <div id="pddikti" class="collapse" aria-labelledby="pddikti_head" data-parent="#accordionExample">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Nama Perguruan Tinggi',$data->pt) !!}
                                {!! tableRow('Fakultas',$data->fakultas) !!}
                                {!! tableRow('Program Studi',$data->prodi) !!}
                                {!! tableRow('NIM',$data->nim) !!}
                                {!! tableRow('Angkatan',$data->angkatan) !!}
                                {!! tableRow('Tanggal Masuk',tglIndonesia($data->tgl_masuk)) !!}
                                {!! tableRow('Status Keaktifan',$data->nm_stat_mhs) !!}
                                {!! tableRow('Jalur Pendaftaran',$data->nm_jalur_daftar) !!}
                                {!! tableRow('Jenis Pendaftaran',$data->nm_jns_daftar) !!}
                                {!! tableRow('Pembiayaan',$data->nm_pembiayaan) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="keluarga_head">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#keluarga" aria-expanded="false" aria-controls="keluarga">
                            Data Keluarga
                        </button>
                    </h2>
                </div>
                <div id="keluarga" class="collapse" aria-labelledby="keluarga_head" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>Ayah</h4>
                                <table class="table table-striped">
                                    <tbody>
                                        {!! tableRow('Nama Ayah',$data->nm_ayah) !!}
                                        {!! tableRow('NIK Ayah',$data->nik_ayah) !!}
                                        {!! tableRow('Tanggal Lahir Ayah',tglIndonesia($data->tgl_lahir_ayah)) !!}
                                        {!! tableRow('Pend. Terakhir Ayah',$data->nm_jenj_didik_ayah) !!}
                                        {!! tableRow('Pekerjaan Ayah',$data->nm_pekerjaan_ayah) !!}
                                        {!! tableRow('Range Penghasilan Ayah',$data->nm_penghasilan_ayah) !!}
                                        <tr>
                                            <td><strong>Kebutuhan Khusus Ayah</strong></td>
                                            <td>:</td>
                                            <td>
                                                <ul>
                                                    <?php
                                                    if (strpos($data->nm_kk_ayah, ',')) {
                                                        $list_kk_ayah = explode(', ', $data->nm_kk_ayah);
                                                        foreach ($list_kk_ayah as $each_kk_ayah) {
                                                            $cari_kk_ayah = DB::table('ref.kebutuhan_khusus')->where('nm_kk', 'LIKE', $each_kk_ayah . ' - %')->first();
                                                            echo "<li>" . $cari_kk_ayah->nm_kk . "</li>";
                                                        }
                                                    } else {
                                                        echo "<li>" . $data->nm_kk_ayah . "</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <h4>Ibu</h4>
                                <table class="table table-striped">
                                    <tbody>
                                        {!! tableRow('Nama Ibu',$data->nm_ibu_kandung) !!}
                                        {!! tableRow('NIK Ibu',$data->nik_ibu) !!}
                                        {!! tableRow('Tanggal Lahir Ibu',tglIndonesia($data->tgl_lahir_ibu)) !!}
                                        {!! tableRow('Pend. Terakhir Ibu',$data->nm_jenj_didik_ibu) !!}
                                        {!! tableRow('Pekerjaan Ibu',$data->nm_pekerjaan_ibu) !!}
                                        {!! tableRow('Range Penghasilan Ibu',$data->nm_penghasilan_ibu) !!}
                                        <tr>
                                            <td><strong>Kebutuhan Khusus Ibu</strong></td>
                                            <td>:</td>
                                            <td>
                                                <ul>
                                                    <?php
                                                    if (strpos($data->nm_kk_ibu, ',')) {
                                                        $list_kk_ibu = explode(', ', $data->nm_kk_ibu);
                                                        foreach ($list_kk_ibu as $each_kk_ibu) {
                                                            $cari_kk_ibu = DB::table('ref.kebutuhan_khusus')->where('nm_kk', 'LIKE', $each_kk_ibu . ' - %')->first();
                                                            echo "<li>" . $cari_kk_ibu->nm_kk . "</li>";
                                                        }
                                                    } else {
                                                        echo "<li>" . $data->nm_kk_ibu . "</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Tempat Tinggal',$data->nm_jns_tinggal) !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($data->id_jns_tinggal==2)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header" id="wali_head">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#wali" aria-expanded="false" aria-controls="wali">
                                    Data Wali
                                </button>
                            </h2>
                        </div>
                        <div id="wali" class="collapse" aria-labelledby="wali_head" data-parent="#accordionExample">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tbody>
                                        {!! tableRow('Nama Wali',$data->nm_wali) !!}
                                        {!! tableRow('Tanggal Lahir Wali',tglIndonesia($data->tgl_lahir_wali)) !!}
                                        {!! tableRow('Pekerjaan Wali',$data->nm_pekerjaan_wali) !!}
                                        {!! tableRow('Penghasilan Wali',$data->nm_penghasilan_wali) !!}
                                        {!! tableRow('Pend. Terakhir Wali',$data->nm_jenj_didik_wali) !!}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header" id="konsentrasi_prodi_head">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#konsentrasi_prodi" aria-expanded="false" aria-controls="konsentrasi_prodi">
                            Data Konsentrasi Prodi
                        </button>
                    </h2>
                </div>
                <div id="konsentrasi_prodi" class="collapse" aria-labelledby="konsentrasi_prodi_head" data-parent="#accordionExample">
                    <div class="card-body">
                        <form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
                            @csrf
                            @method('PUT')
                            <div class="alert alert-info">
                                <i class="text-danger">*</i> Silahkan pilih Konsentrasi Prodi Anda.<br>
                                Pastikan Anda memilih konsentrasi prodi sesuai dengan ketentuan dan semester yang berlaku di program studi Anda.
                            </div>
                            <input type="hidden" name="kode" value="konsentrasi_prodi">
                            <input type="hidden" name="id_pd" value='{{$data->id_pd}}'>
                            {!! FormInputSelect('id_konsentrasi_prodi','Konsentrasi Prodi',false,true,$konsentrasi_prodi, $data->id_konsentrasi_prodi ?? null) !!}
                            <hr>
                            <div class="card-tools">
                                <button class="btn btn-xs btn-primary float-right" type="submit">
                                    <i class="fa fa-save"></i> SIMPAN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="lainnya_head">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#lainnya" aria-expanded="false" aria-controls="lainnya">
                            Data Lainnya
                        </button>
                    </h2>
                </div>
                <div id="lainnya" class="collapse" aria-labelledby="lainnya_head" data-parent="#accordionExample">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                {!! tableRow('Menerima KPS?',config('mp.data_master.kps.'.$data->a_terima_kps)) !!}
                                @if($data->a_terima_kps==1)
                                {!! tableRow('No. KPS',$data->no_kps) !!}
                                @endif
                                {!! tableRow('Alat Transportasi',$data->nm_alat_transport) !!}
                                <tr>
                                    <td><strong>Kebutuhan Khusus</strong></td>
                                    <td>:</td>
                                    <td>
                                        <ul>
                                            <?php
                                            if (strpos($data->nm_kk, ',')) {
                                                $list_kk = explode(', ', $data->nm_kk);
                                                foreach ($list_kk as $each_kk) {
                                                    $cari_kk = DB::table('ref.kebutuhan_khusus')->where('nm_kk', 'LIKE', $each_kk . ' - %')->first();
                                                    echo "<li>" . $cari_kk->nm_kk . "</li>";
                                                }
                                            } else {
                                                echo "<li>" . $data->nm_kk . "</li>";
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection