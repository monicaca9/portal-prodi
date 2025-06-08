<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pdrd\Matkul;
use Illuminate\Http\Request;

class MatkulSeeder extends Seeder
{
    /*
     * Run the database seeds.
     */
    public function __construct(Request $request)
    {
        $this->mk = new Matkul();
        // $this->kuliah = new KuliahMahasiswa();
    }
    public function run(): void
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        $prodi = DB::table('pdrd.sms')
            ->where('soft_delete', 0)
            // ->where('id_sms')
            ->select('id_sms')
            ->groupBy('id_sms')
            ->pluck('id_sms')
            ->toArray();



        $func=[
            'matkul'
            // 'cek'
        ];
        if(in_array('matkul', $func)){
            foreach ($prodi as $id_prodi) {
                $cari_prodi = DB::table('pdrd.sms')->where('id_sms', $id_prodi)->first();
                echo "Mendapatkan data matkul dari prodi " . ($cari_prodi->nm_lemb) . "\n";

            for ($i = 1; $i <= 50; $i++) {
                $param = json_encode([
                    'page' => $i,
                    'limit' => 50,
                    'id_prodi' => $id_prodi
                ]);
                $response = curlApi('GET', $url . '/mata_kuliah/list_matkul', $param, $token);
                
                
                // dd($response['message']);
                $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $each_data) {
                        $prodi = DB::table('pdrd.sms')
                            ->where('soft_delete', 0)
                            ->where('id_sms', $each_data['id_sms'])
                            ->first();
                        $mk = $this->mk->where('id_mk', $each_data['id_mk'])->first();
                        // $matkul = DB::table('pdrd.kelas')
                        if(isset($mk->id_mk)){
                            $mk->update([
                            'id_sms' => $each_data['id_sms'],
                            'id_jenj_didik' => $prodi->id_jenj_didik,
                            'kode_mk' => $each_data['kode_mk'],
                            'nm_mk' => $each_data['nm_mk'],
                            // 'jns_mk' => $each_data[''],
                            // 'kel_mk' => $each_data['a_sdm_iptek'],
                            'sks_mk' => $each_data['sks_mk'],
                            // 'sks_tm' => $each_data['a_sdm_iptek'],
                            // 'sks_prak' => $each_data['a_sdm_iptek'],
                            // 'sks_prak_lap' => $each_data['a_sdm_iptek'],
                            // 'sks_sim' => $each_data['a_sdm_iptek'],
                            // 'metode_pelaksanaan_kuliah' => $each_data['a_sdm_iptek'],
                            // 'a_sap' => $each_data['a_sdm_iptek'],
                            // 'a_silabus' => $each_data['a_sdm_iptek'],
                            // 'a_bahan_ajar' => $each_data['a_sdm_iptek'],
                            // 'acara_prak' => $each_data['a_sdm_iptek'],
                            // 'a_diktat' => $each_data['a_sdm_iptek'],
                            // 'tgl_mulai_efektif' => $each_data['a_sdm_iptek'],
                            // 'tgl_akhir_efektif' => $each_data['a_sdm_iptek'],
                            'tgl_create' => $each_data['waktu_data_ditambahkan'],
                            'id_creator' => $id_creator,
                            'last_update' => $each_data['terakhir_diubah'],
                            'id_updater' => $id_creator,
                            'last_sync' => currDateTime()
                          ]);

                          echo "Data sudah ada, berhasil update nama matkul = ". $mk->nm_mk . " \n";
                        }else{
                            $mk =$this->mk->create([
                                'id_mk' => $each_data['id_mk'],
                                'id_sms' => $each_data['id_sms'],
                                'id_jenj_didik' => $prodi->id_jenj_didik,
                                'kode_mk' => $each_data['kode_mk'],
                                'nm_mk' => $each_data['nm_mk'],
                                // 'jns_mk' => $each_data[''],
                                // 'kel_mk' => $each_data['a_sdm_iptek'],
                                'sks_mk' => $each_data['sks_mk'],
                                // 'sks_tm' => $each_data['a_sdm_iptek'],
                                // 'sks_prak' => $each_data['a_sdm_iptek'],
                                // 'sks_prak_lap' => $each_data['a_sdm_iptek'],
                                // 'sks_sim' => $each_data['a_sdm_iptek'],
                                // 'metode_pelaksanaan_kuliah' => $each_data['a_sdm_iptek'],
                                // 'a_sap' => $each_data['a_sdm_iptek'],
                                // 'a_silabus' => $each_data['a_sdm_iptek'],
                                // 'a_bahan_ajar' => $each_data['a_sdm_iptek'],
                                // 'acara_prak' => $each_data['a_sdm_iptek'],
                                // 'a_diktat' => $each_data['a_sdm_iptek'],
                                // 'tgl_mulai_efektif' => $each_data['a_sdm_iptek'],
                                // 'tgl_akhir_efektif' => $each_data['a_sdm_iptek'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                // 'updated_at' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'last_sync' => currDateTime()  
                            ]);
                            echo "Data berhasil disimpan mata kuliah = ". $mk->nm_mk . " \n";
                        }
                        
                        
                    }
                    
                } elseif ($response['message'] == 'Gagal Otentikasi') {
                    $token = $this->generate_token();
                } else {
                    
                    echo "Error Pesan = " . $response['message'] . "\n";
                    
                    break;
                }
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
