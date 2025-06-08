<?php

namespace Database\Seeders;

use App\Models\Pdrd\RegPd;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SyncMhsSeeder extends Seeder
{
    // protected $request;
    protected $base_url;
    protected $regpd;
    protected $pd;
    // protected $profil;
    // protected $kuliah;
    public $token = '';

    public function __construct(Request $request)
    {
        $this->regpd = new RegPd();
        $this->pd = new PesertaDidik();
        // $this->kuliah = new KuliahMahasiswa();
    }

    public function run()
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        $prodi = DB::table('pdrd.sms')
            ->where('soft_delete', 0)
            ->whereIn('id_sms', [
                'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88'
            ])
            ->select('id_sms')
            ->groupBy('id_sms')
            ->pluck('id_sms')
            ->toArray();
        
        $func=[
            // 'peserta_didik',
            'profil_mahasiswa'
        ];

        if(in_array('peserta_didik', $func)){
        foreach ($prodi as $id_prodi) {
            $cari_prodi = DB::table('pdrd.sms')->where('id_sms', $id_prodi)->first();
            echo "Mendapatkan data mahasiswa dari prodi " . ($cari_prodi->nm_lemb) . "\n";

            for ($i = 1; $i <= 200; $i++) {
                $param = json_encode([
                    'id_prodi' => $id_prodi,
                    'status_mahasiswa' => 'A',
                    'page' => $i,
                    'limit' => 50
                ]);

                $get_data_mahasiswa_aktif = curlApi('GET', $url . '/mahasiswa/list_status', $param, $token);

                $now = currDateTime();
                $soft_delete = 0;
                $no = 1;
                $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                if ($get_data_mahasiswa_aktif['status'] == true) {
                    $total_data = count($get_data_mahasiswa_aktif['data']);
                    foreach ($get_data_mahasiswa_aktif['data'] as $each_data) {
                        // dd($get_data_mahasiswa_aktif['data']);
                        echo "Input data Mahasiswa Prodi " . ($cari_prodi->nm_lemb) . " " . $no . " dari " . $total_data . " data " . "halaman " . $i . " \n";
                        $pd = $this->pd->where('id_pd', $each_data['id_peserta_didik'])->first();
                        $ttl = explode(',', $each_data['tmpt_tgl_lahir']);
                        
                        if(count($ttl) > 2 ){
                            $tmpt_lahir = $ttl[0];
                            $tgl_lahir = $ttl[2];
                        }else{
                            $tmpt_lahir = $ttl[0];
                            $tgl_lahir = $ttl[1];
                        }
                        
            
                        if(isset($pd->id_pd)){
                            // update data mahasiswa
                            $pd->update([
                                'nm_pd' => $each_data['nama_mahasiswa'],
                                'jk' => $each_data['jk'],
                                'tmpt_lahir' => $tmpt_lahir,
                                'tgl_lahir' => $tgl_lahir,
                                'jln' => $each_data['jln'],
                                'tlpn_hp' => $each_data['tlpn_hp'],
                                'id_kewarganegaraan' => 'ID',
                                'id_agama' => '99',
                                'id_wil' => $each_data['id_wil'],
                                'id_stat_mhs' => $each_data['status'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => $now
                            ]);
                            
                            // dd($regpd);
                            $regpd = $this->regpd->where('id_pd', $pd->id_pd)->first();
                            // dd($regpd);
                            if(!is_null($regpd)){
                                $regpd->update([
                                    'id_pd' => $each_data['id_peserta_didik'],
                                    'id_sp' => 'e2b705a7-173e-464a-9fac-509128709515',
                                    'id_sms' => $each_data['id_prodi'],
                                    // 'id_jns_daftar' => $each_data['nama_mahasiswa'],
                                //     'id_jalur_daftar' => $each_data['id_prodi'],
                                //     'id_pembiayaan' => $each_data['id_pembiayaan'],
                                //     'id_smt' => $each_data['program_studi'],
                                //     'id_jns_keluar' => $each_data['periode_masuk'],
                                    'nim' => $each_data['NPM'],
                                //     'tgl_daftar' => $each_data['ips'],
                                //     'sks_diakui' => $each_data['ipk'],
                                //     'id_sp_asal' => $each_data['total_sks'],
                                //     'id_prodi_asal' => $each_data['status'],
                                    'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                    'id_creator' => $id_creator,
                                    'last_update' => $each_data['terakhir_diubah'],
                                    'id_updater' => $id_creator,
                                    'soft_delete' => $soft_delete,
                                    'last_sync' => $now
                                ]);
                            }
                            
                            echo "Data sudah ada, berhasil update id_reg_pd = ". $pd->nm_pd . " \n";

                        }else{
                            // create mahasiswa
                          
                            $pd = $this->pd->create([
                                'id_pd' => $each_data['id_peserta_didik'],
                                'nm_pd' => $each_data['nama_mahasiswa'],
                                'jk' => $each_data['jk'],
                                'tmpt_lahir' => $tmpt_lahir,
                                'tgl_lahir' => $tgl_lahir,
                                'jln' => $each_data['jln'],
                                'tlpn_hp' => $each_data['tlpn_hp'],
                                'id_kewarganegaraan' => 'ID',
                                'id_agama' => '99',
                                'id_wil' => $each_data['id_wil'],
                                'id_stat_mhs' => $each_data['status'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => $now
                            ]);
                            $regpd = $this->regpd->create([
                                'id_reg_pd' => $each_data['id_reg_pd'],
                                'id_sp' => 'e2b705a7-173e-464a-9fac-509128709515',
                                'id_sms' => $each_data['id_prodi'],
                                'id_pd' => $each_data['id_peserta_didik'],
                                'id_jns_daftar' => '1',
                                'id_jalur_daftar' => '1',
                                'id_pembiayaan' => '1',
                                'id_smt' => '20211',
                                'nim' => $each_data['NPM'],
                                'tgl_daftar' => $now,
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => $now
                            ]);

                            echo "Data berhasil disimpan id_reg_pd = ". $pd->nm_pd . " \n";

                        }

                        $no++;
                    }
                } elseif ($get_data_mahasiswa_aktif['message'] == 'Gagal Otentikasi') {
                    $token = $this->generate_token();
                } else {
                    
                    echo "Error Pesan = " . $get_data_mahasiswa_aktif['message'] . "\n";
                    
                    break;
                }
            }
        }
    }
        if(in_array('profil_mahasiswa',$func)){
            $pd = DB::table('pdrd.reg_pd')
            ->where('soft_delete', 0)
            ->where('id_sms', 'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88')
            ->where('id_smt')
            ->select('id_pd')
            ->groupBy('id_pd')
            ->pluck('id_pd')
            ->toArray();

            foreach ($pd as $id_pd) {
                $cari_mahasiswa = DB::table('pdrd.reg_pd')->where('id_pd', $id_pd)->first();
                echo "Mendapatkan data mahasiswa " . ($cari_mahasiswa->nim) . "\n";
    
                    $param = json_encode([
                        'id_peserta_didik' => $id_pd
                    ]);
    
                    $get_detail_mahasiswa = curlApi('GET', $url . '/mahasiswa/detail', $param, $token);
                    // dd($get_detail_mahasiswa);
                    
                    $now = currDateTime();
                    $soft_delete = 0;
                    $no = 1;
                    $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                    if ($get_detail_mahasiswa['status'] == true) {
                        $total_data = count($get_detail_mahasiswa['data']);
                        foreach ($get_detail_mahasiswa['data'] as $each_data) {
                            // dd($get_data_mahasiswa_aktif['data']);
                            echo "Input data Mahasiswa NPM " . ($cari_mahasiswa->nim) ." \n";
                            $pd = $this->pd->where('id_pd', $each_data['id_pd'])->first();
                            // dd($pd);
                            if(isset($pd->id_pd)){
                                // update data mahasiswa
                                $pd->update([
                                    'nm_pd' => $each_data['nm_pd'],
                                    'jk' => $each_data['jk'],
                                    'nisn' => $each_data['nisn'],
                                    'nik' => $each_data['nik'],
                                    'tmpt_lahir' => $each_data['tmpt_lahir'],
                                    'tgl_lahir' =>  $each_data['tgl_lahir'],
                                    'jln' => $each_data['jln'],
                                    'rt' => $each_data['rt'],
                                    'rw' => $each_data['rw'],
                                    'nm_dsn' => $each_data['nm_dsn'],
                                    'ds_kel' => $each_data['ds_kel'],
                                    'kode_pos' => $each_data['kode_pos'],
                                    'tlpn_rumah' => $each_data['tlpn_rumah'],
                                    'tlpn_hp' => $each_data['tlpn_hp'],
                                    'nm_wali' => $each_data['nm_wali'],
                                    'tgl_lahir_wali' => $each_data['tgl_lahir_wali'],
                                    'id_pekerjaan_wali' => $each_data['id_pekerjaan_wali'],
                                    'id_penghasilan_wali' => $each_data['id_penghasilan_wali'],
                                    'id_pendidikan_wali' => $each_data['id_pendidikan_wali'],
                                    'nm_ibu_kandung' => $each_data['nm_ibu_kandung'],
                                    'tgl_lahir_ibu' => $each_data['tgl_lahir_ibu'],
                                    'nik_ibu' => $each_data['nik_ibu'],
                                    'id_pekerjaan_ibu' => $each_data['id_pekerjaan_ibu'],
                                    'id_pendidikan_ibu' => $each_data['id_pendidikan_ibu'],
                                    'id_penghasilan_ibu' => $each_data['id_penghasilan_ibu'],
                                    'id_kk_ibu' => $each_data['id_kk_ibu'],
                                    'nm_ayah' => $each_data['nm_ayah'],
                                    'tgl_lahir_ayah' => $each_data['tgl_lahir_ayah'],
                                    'nik_ayah' => $each_data['nik_ayah'],
                                    'id_pekerjaan_ayah' => $each_data['id_pekerjaan_ayah'],
                                    'id_penghasilan_ayah' => $each_data['id_penghasilan_ayah'],
                                    'id_pendidikan_ayah' => $each_data['id_pendidikan_ayah'],
                                    'id_kk_ayah' => $each_data['id_kk_ayah'],
                                    'a_terima_kps' => $each_data['a_terima_kps'],
                                    'no_kps' => $each_data['no_kps'],
                                    'id_blob' => $each_data['id_blob'],
                                    'id_kk' => $each_data['id_kk'],
                                    'id_alat_transport' => $each_data['id_alat_transport'],
                                    'id_kewarganegaraan' => $each_data['id_kewarganegaraan'],
                                    'id_agama' => $each_data['id_agama'],
                                    'id_jns_tinggal' => $each_data['id_jns_tinggal'],
                                    'id_wil' => $each_data['id_wil'],
                                    'id_stat_mhs' => $each_data['id_stat_mhs'],
                                    // 'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                    'id_creator' => $id_creator,
                                    // 'last_update' => $each_data['terakhir_diubah'],
                                    'id_updater' => $id_creator,
                                    'soft_delete' => $soft_delete,
                                    'last_sync' => $now
                                ]);
                                
                                // dd($regpd);
                                $regpd = $this->regpd->where('id_pd', $pd->id_pd)->first();
                                // dd($regpd);
                                if(!is_null($regpd)){
                                    $regpd->update([
                                        'id_pd' => $each_data['id_pd'],
                                        'id_sp' => $each_data['id_sp'],
                                        'id_sms' => $each_data['id_sms'],
                                        'id_jns_daftar' => $each_data['id_jns_daftar'],
                                        'id_jalur_daftar' => $each_data['id_jalur_daftar'],
                                        'id_pembiayaan' => $each_data['id_pembiayaan'],
                                        'id_smt' => $each_data['id_semester_masuk'],
                                        'id_jns_keluar' => $each_data['id_jns_keluar'],
                                        'nim' => $each_data['npm'],
                                        'tgl_daftar' => $each_data['tgl_masuk_sp'],
                                        'sks_diakui' => $each_data['sks_diakui'],
                                        'id_sp_asal' => $each_data['id_pt_asal'],
                                        'id_prodi_asal' => $each_data['id_prodi_asal'],
                                        // 'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                        'id_creator' => $id_creator,
                                        // 'last_update' => $each_data['terakhir_diubah'],
                                        'id_updater' => $id_creator,
                                        'soft_delete' => $soft_delete,
                                        'last_sync' => $now
                                    ]);
                                }
                                
                                echo "Data sudah ada, berhasil update id_reg_pd = ". $pd->nm_pd . " \n";
    
                            }
                              

                        }
                    } elseif ($get_detail_mahasiswa['message'] == 'Gagal Otentikasi') {
                        $token = $this->generate_token();
                    } else {
                        // dd($get_detail_mahasiswa['message']);
                        echo "Error Pesan = " . $get_detail_mahasiswa['message'] . "\n";
                        break;
                    }
            
            }
        }
    }

    function generate_token()
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = generate_token_onedata('POST', $url . '/auth/login');

        $this->token = $token;
        return  $token;
    }
}