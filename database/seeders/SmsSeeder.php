<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pdrd\Sms;
use Illuminate\Support\Facades\DB;

class SmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        for ($i = 1; $i <= 20; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50
            ]);
            $response = curlApi('GET', $url . '/lembaga/daftar_lembaga', $param, $token);
            // dd($response);
            if ($response['success'] == true) {
                $total_data = count($response['data']);
                foreach ($response['data'] as $each_data) {
                    
                    $id_creator = guid();
                    $id_updater = guid();
                    $jenjdidik= DB::table('ref.jenjang_pendidikan')
                                    ->where('nm_jenj_didik', 'like', '%' . $each_data['nm_jenj_didik'] . '%' )
                                    ->select('id_jenj_didik')
                                    ->first();

                    Sms::updateOrInsert([
                        'id_sms' => $each_data['id_sms'],
                    ], [
                        'nm_lemb' => $each_data['nm_lemb'],
                        'no_tel' => $each_data['no_tel'],
                        'no_fax' => $each_data['no_fax'],
                        'email' => $each_data['email'],
                        'tgl_berdiri' => $each_data['tgl_berdiri'],
                        'stat_prodi' => $each_data['stat_prodi'],
                        'id_sp' => $each_data['id_sp'],
                        'id_jns_sms' => $each_data['id_jns_sms'],
                        'id_wil' => $each_data['id_wil'],
                        'id_jenj_didik' => $jenjdidik->id_jenj_didik ,
                        'gelar_lulusan' => $each_data['gelar_lulusan'] ,
                        'id_fungsi_lab' => '*',
                        'id_kel_usaha' => '*',
                        'id_creator' => $id_creator,
                        'id_updater' => $id_updater,
                        // 'id_induk_sms' => $each_data['id_induk_sms'],
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
