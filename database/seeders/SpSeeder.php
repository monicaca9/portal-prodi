<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pdrd\SatuanPendidikan;

class SpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        for ($i = 1; $i <= 200; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50
            ]);
            $response = curlApi('GET', $url . '/lembaga/daftar_satuan_pendidikan', $param, $token);
            // dd($response);
            if ($response['status'] == true) {
                $total_data = count($response['response']['data']);
                foreach ($response['response']['data'] as $each_data) {
                    
                    $id_creator = guid();
                    $id_updater = guid();
                
                    SatuanPendidikan::updateOrInsert([
                        'id_sp' => $each_data['id_sp'],
                    ], [
                        'nm_lemb' => $each_data['nm_lemb'],
                        'nss' => $each_data['nss'],
                        'npsn' => $each_data['npsn'],
                        'nm_singkat' => $each_data['nm_singkat'],
                        'jln' => $each_data['jln'],
                        'id_wil' => $each_data['id_wil'],
                        'id_creator' => $id_creator,
                        'id_updater' => $id_updater,
                        'tgl_create' => $each_data['waktu_data_ditambahkan'],
                        'last_update' => $each_data['terakhir_diubah'],
                        'last_sync' => currDateTime()
                      ]);
                    echo " Data berhasil disimpan ". $each_data['nm_lemb'] ."\n";
                }
            } elseif ($response['message'] == 'Gagal Otentikasi') {
                $token = $this->generate_token();
            } else {
                
                echo "Error Pesan = " . $response['message'] . "\n";
                
                break;
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
