<?php

namespace Database\Seeders;

use App\Models\Pdrd\RegPtk;
use App\Models\Pdrd\Sdm;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SdmSeeder extends Seeder
{
    // protected $request;
    protected $base_url;
    protected $regptk;
    protected $sdm;
    // protected $profil;
    // protected $kuliah;
    public $token = '';

    public function __construct(Request $request)
    {
        $this->regptk = new RegPtk();
        $this->sdm = new Sdm();
        // $this->kuliah = new KuliahMahasiswa();
    }

    public function run()
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        // $prodi = DB::table('pdrd.sms')
        //     ->where('soft_delete', 0)
        //     ->whereIn('id_sms', [
        //         'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88'
        //     ])
        //     ->select('id_sms')
        //     ->groupBy('id_sms')
        //     ->pluck('id_sms')
        //     ->toArray();
        
        $func=[
            // 'sdm'
            'sdm_detail'
        ];

        if(in_array('sdm', $func)){
        

            for ($i = 1; $i <= 60; $i++) {
                $param = json_encode([
                    'id_jns_sdm' => 12,
                    'page' => $i,
                    'limit' => 25,
                    'sort_by' => 'desc'
                ]);
                $response = curlApi('GET', $url . '/sdm/daftar', $param, $token);

                $now = currDateTime();
                $soft_delete = 0;
                $no = 1;
                $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $each_data) {
                        // dd($get_data_mahasiswa_aktif['data']);
                        $sdm = $this->sdm->where('id_sdm', $each_data['id_sdm'])->first();
                        $ttl = explode(',', $each_data['tmpt_tgl_lahir']);
                        
                        if(count($ttl) > 2 ){
                            $tmpt_lahir = $ttl[0];
                            $tgl_lahir = $ttl[2];
                        }else{
                            $tmpt_lahir = $ttl[0];
                            $tgl_lahir = $ttl[1];
                        }
                        
                        
                        $stataktif= DB::table('ref.status_keaktifan_pegawai')
                                    ->where('nm_stat_aktif', $each_data['status_keaktifan'])
                                    ->select('id_stat_aktif')
                                    ->first();

                        if(isset($sdm->id_sdm)){
                            // update data mahasiswa
                            $sdm->update([
                                'nm_sdm' => $each_data['nama_sdm'],
                                'jk' => 'L',
                                'tmpt_lahir' => $tmpt_lahir,
                                'tgl_lahir' => $tgl_lahir,
                                // 'nm_ibu_kandung' => 'default',
                                'stat_kawin' => 1,
                                'nik' => '999999999',
                                'nip' => $each_data['nip'],
                                'nidn' => $each_data['nidn'],
                                'kewarganegaraan' => 'ID',
                                'id_jns_sdm' => 12,
                                'id_wil' => '999999',
                                'id_stat_aktif' => $stataktif->id_stat_aktif,
                                'id_agama' => 98,
                                'id_pekerjaan_suami_istri' => 0,
                                'id_lemb_angkat' => 0,
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => currDateTime()
                                // 'updated_at' => $each_data['terakhir_diubah']
                            ]);
                            
                            echo "Data sudah ada, berhasil update sdm = ". $sdm->nm_sdm . " \n";

                        }else{
                            // create sdm
                          
                            $sdm = $this->sdm->create([
                                'id_sdm' => $each_data['id_sdm'],
                                'nm_sdm' => $each_data['nama_sdm'],
                                'jk' => 'L',
                                'tmpt_lahir' => $tmpt_lahir,
                                'tgl_lahir' => $tgl_lahir,
                                // 'nm_ibu_kandung' => 'default',
                                'stat_kawin' => 1,
                                'nik' => '999999999',
                                'nip' => $each_data['nip'],
                                'nidn' => $each_data['nidn'],
                                'kewarganegaraan' => 'ID',
                                'id_jns_sdm' => 12,
                                'id_wil' => '999999',
                                'id_stat_aktif' => $stataktif->id_stat_aktif,
                                'id_agama' => 98,
                                'id_pekerjaan_suami_istri' => 0,
                                'id_lemb_angkat' => 0,
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => $now
                                // 'updated_at' => $each_data['terakhir_diubah']
                            ]);
                            

                            echo "Data sdm berhasil disimpan = ". $sdm->nm_sdm . " \n";

                        }

                        $no++;
                    }
                } elseif ($response['message'] == 'Gagal Otentikasi') {
                    $token = $this->generate_token();
                } else {
                    
                    echo "Error Pesan = " . $response['message'] . "\n";
                    
                    break;
                }
            }
        
    }
        if(in_array('sdm_detail',$func)){
            $sdm = DB::table('pdrd.sdm')
            ->where('soft_delete', 0)
            ->where('id_stat_aktif', 1)
            ->select('id_sdm')
            ->groupBy('id_sdm')
            ->pluck('id_sdm')
            ->toArray();

            $total_sdm = count($sdm);
            // dd(count($sdm));
            

            foreach ($sdm as $key => $id_sdm) {
                $cari_sdm = DB::table('pdrd.sdm')->where('id_sdm', $id_sdm)->first();
                echo "Mendapatkan data sdm " . ($cari_sdm->id_sdm) . ' ' . $key+1 . '/' . $total_sdm . "\n";
                
                // dd($cari_sdm);
                    $param = json_encode([
                        'id_jns_sdm' => 12,
                        'id_sdm' => $id_sdm
                    ]);
    
                    $get_detail_sdm = curlApi('GET', $url . '/sdm/detail', $param, $token);
                    // dd($get_detail_sdm);
                    
                    $now = currDateTime();
                    $soft_delete = 0;
                    $no = 1;
                    $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                    if ($get_detail_sdm['status'] == true) {
                        $total_data = count($get_detail_sdm['data']);
                        foreach ($get_detail_sdm['data']['sdm'] as $each_data) {
                            // dd($get_data_mahasiswa_aktif['data']);
                            echo "Input data SDM " . ($cari_sdm->id_sdm) ." \n";
                            $sdm = $this->sdm->where('id_sdm', $each_data['id_sdm'])->first();
                            
                            // dd($pd);
                            $ttl = explode(',', $each_data['tmpt_tgl_lahir']);
                            if(count($ttl) > 2 ){
                                $tmpt_lahir = $ttl[0];
                                $tgl_lahir = $ttl[2];
                            }else{
                                $tmpt_lahir = $ttl[0];
                                $tgl_lahir = $ttl[1];
                            }

                            $charjk = substr($each_data['jenis_kelamin'], 0, 1);

                            $stataktif= DB::table('ref.status_keaktifan_pegawai')
                                    ->where('nm_stat_aktif', $each_data['status_keaktifan'])
                                    ->select('id_stat_aktif')
                                    ->first();
                                    // ->groupBy('id_stat_aktif')
                                    // ->pluck('id_stat_aktif');

                            // dd($stataktif);
                            if(isset($sdm->id_sdm)){
                                // update data mahasiswa
                                $sdm->update([
                                'nm_sdm' => $each_data['nama_sdm'],
                                'jk' => $charjk,
                                'tmpt_lahir' => $tmpt_lahir,
                                'tgl_lahir' => $tgl_lahir,

                                // 'nm_ibu_kandung' => 'null',
                                // 'stat_kawin' => $each_data['stat_kawin'],
                                // 'nik' => $each_data['nik'],

                                'nip' => $each_data['nip'],
                                'nidn' => $each_data['nidn'],

                                // 'kewarganegaraan' => $each_data['kewarganegaraan'],
                                // 'id_jns_sdm' => $each_data['id_jns_sdm'],
                                // 'id_wil' => $each_data['id_wil'],
                                'id_stat_aktif' => $stataktif->id_stat_aktif,
                                // 'id_agama' => $each_data['id_agama'],
                                // 'id_pekerjaan_suami_istri' => $each_data['id_pekerjaan_suami_istri'],
                                // 'id_lemb_angkat' => $each_data['id_lemb_angkat'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => $now
                                ]);
                                
                               
                                echo "Data sudah ada, berhasil update sdm = ". $sdm->nm_sdm . " \n";
    
                            }
                              

                        }
                    } elseif ($get_detail_sdm['message'] == 'Gagal Otentikasi') {
                        $token = $this->generate_token();
                    } else {
                        // dd($get_detail_mahasiswa['message']);
                        echo "Error Pesan = " . $get_detail_sdm['message'] . "\n";
                        
                        continue;
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