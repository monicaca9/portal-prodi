<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pdrd\AktAjarDosen;

class AktAjarDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        $kelas = DB::table('pdrd.kelas_kuliah')
            ->where('soft_delete', 0)
            ->where('id_sms', 'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88')
            ->where('id_smt','20231')
            ->select('id_kls')
            ->groupBy('id_kls')
            ->pluck('id_kls')
            ->toArray();
        
        // dd($kelas);
        $total_sdm = count($kelas);
        // dd($kelas);
        // $katgiat = DB::table('ref.katgiat')
        //     ->where('soft_delete', 0)
        //     ->select('id_katgiat')
        //     ->groupBy('id_katgiat')
        //     ->pluck('id_katgiat')
        //     ->toArray();
        // dd($matkul);


        foreach ($kelas as $key => $id_kelas) {
            $cari_kelas = DB::table('pdrd.kelas_kuliah')->where('id_kls', $id_kelas)->first();
            echo "Mendapatkan data kelas dari matkul = " . ($cari_kelas->id_kls) . ' ' . $key+1 . '/' . $total_sdm . "\n";

            // dd($cari_matkul);    

        for ($i = 1; $i <= 5; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50,
                'id_kelas' => $id_kelas
            ]);
            $response = curlApi('GET', $url . '/mata_kuliah/list_dosen_ajar', $param, $token);
            

            // dd($response['message']);
            $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
            if ($response['status'] == true) {
                $total_data = count($response['response']['data']);
                foreach ($response['response']['data'] as $each_data) {
                    // $katgiat = $this->katgiat->where('nm_kat', $each_data[''])->first();
                    AktAjarDosen::updateOrInsert([
                        'id_ajar' => $each_data['id_ajar']
                    ], [
                        'id_katgiat' => $each_data['id_katgiat'],
                        'id_jns_eval' => $each_data['id_jns_eval'],
                        'id_reg_ptk' => $each_data['id_reg_ptk'],
                        'id_kls' => $each_data['id_kls'],
                        'sks_subst_tot' => $each_data['sks_substansi_total'],
                        'sks_tm_subst' => $each_data['sks_tatap_muka_substansi'],
                        'sks_prak_subst' => $each_data['sks_praktikum_substansi'],
                        'sks_prak_lap_subst' => $each_data['sks_praktikum_lap_substansi'],
                        'sks_sim_subst' => $each_data['sks_sim_substansi'],
                        'jml_tm_renc' => $each_data['jml_tatap_muka_rencana'],
                        'jml_tm_real' => $each_data['jml_tatap_muka_realisasi'],
                        'jml_mhs' => $each_data['jml_mhs'],
                        'tgl_create' => $each_data['waktu_data_ditambahkan'],
                        'id_creator' => $id_creator,
                        'last_update' => $each_data['terakhir_diubah'],
                        'id_updater' => $id_creator,
                        'last_sync' => currDateTime()
                      ]);
                    echo " Data berhasil disimpan ". $each_data['id_ajar'] ."\n";
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

    function generate_token()
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = generate_token_onedata('POST', $url . '/auth/login');

        $this->token = $token;
        return  $token;
    }
}
