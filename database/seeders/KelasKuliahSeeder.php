<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pdrd\KelasKuliah;
use Illuminate\Support\Facades\DB;

class KelasKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    //  public function __construct(Request $request)
    // {
    //     $this->mk = new Matkul();
    //     $this->kls = new KelasKuliah();
    // }
    public function run(): void
    {
        

        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        $prodi = DB::table('pdrd.sms')
            ->where('soft_delete', 0)
            ->where('id_sms','fc4fc29a-85ca-47b3-8e61-3a9e9e129a88')
            ->select('id_sms')
            ->groupBy('id_sms')
            ->pluck('id_sms')
            ->toArray();
        // dd($prodi);
        
        // dd($matkul);
        // $semester = DB::table('ref.semester');
        // >where('soft_delete', 0)
        // ->where('id_smt')
        // ->select('id_smt')
        // ->groupBy('id_smt')
        // ->pluck('id_smt')
        // ->toArray();

        $func=[
            'kelaskuliah'
        ];
        if(in_array('kelaskuliah', $func)){
            foreach ($prodi as $id_prodi) {
                $cari_prodi = DB::table('pdrd.sms')->where('id_sms', $id_prodi)->first();
                echo "Mendapatkan data kelas dari prodi " . ($cari_prodi->nm_lemb) . "\n";
                
                $semester = DB::table('ref.semester')
                ->where('expired_date', null)
                ->where('a_periode_aktif', 1)
                // ->where('id_smt', 20222)
                ->orderBy('id_smt', 'asc')
                ->get();

                // dd($semester);
            foreach ($semester as $smt){
                for ($i = 1; $i <= 10; $i++) {
                $param = json_encode([
                    'page' => $i,
                    'limit' => 50,
                    'id_prodi' => $id_prodi,
                    'id_semester' => (int)$smt->id_smt,
                ]);
                // dd($param);
            
            $response = curlApi('GET', $url . '/mata_kuliah/list_kelas', $param, $token);
            
            // dd($response);

                
                // dd($response['message']);
                $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $each_data) { 
                        kelasKuliah::updateOrInsert([
                            'id_kls' => $each_data['id_kls']
                        ], [
                            'id_sms' => $each_data['id_sms'],
                            'id_mk' => $each_data['id_mk'],
                            'id_smt' => $each_data['id_smt'],
                            'nm_kls' => $each_data['nm_kls'],
                            'sks_mk' => $each_data['sks_mk'],
                            // 'jns_mk' => $each_data[''],
                            // 'kel_mk' => $each_data['a_sdm_iptek'],
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
                        echo " Data berhasil disimpan ". $each_data['nm_kls'] ."\n";
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
    echo "Data Berhasil Disimpan";
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

