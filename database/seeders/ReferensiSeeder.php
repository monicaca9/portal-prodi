<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ref\Semester;
use App\Models\Ref\JabTgs;
use App\Models\Ref\TahunAjaran;
use App\Models\Ref\JenisKeluar;
class ReferensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        //
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();
        $func=[
            // 'jenis_keluar',
            // 'tahun_ajaran',
            // 'semester',
            'jab_tgs',
        ];
        
        if(in_array('jenis_keluar', $func)){
            for ($i = 1; $i <= 10; $i++) {
                $param = json_encode([
                    'page' => $i,
                    'limit' => 10
                ]);
                $response = curlApi('GET', $url . '/referensi/jenis_keluar', $param, $token);
                // dd($response['message']);
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $each_data) {
                        JenisKeluar::updateOrInsert([
                            'id_jns_keluar' => $each_data['id_jns_keluar']
                        ], [
                            'ket_keluar' => $each_data['ket_keluar'],
                            'a_pd' => $each_data['a_pd'],
                            'a_ptk' => $each_data['a_ptk'],
                            'a_sdm_iptek' => $each_data['a_sdm_iptek'],
                            'tgl_create' => $each_data['waktu_data_ditambahkan'],
                            'last_update' => $each_data['terakhir_diubah'],
                            'last_sync' => currDateTime()
                          ]);
                        echo " Data berhasil disimpan ". $each_data['id_jns_keluar'] ."\n";
                    }
                } elseif ($response['message'] == 'Gagal Otentikasi') {
                    $token = $this->generate_token();
                } else {
                    
                    echo "Error Pesan = " . $response['message'] . "\n";
                    
                    break;
                }
        }
    }

    if(in_array('tahun_ajaran', $func)){
        for ($i = 1; $i <= 20; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50
            ]);
            $response = curlApi('GET', $url . '/referensi/tahun_ajaran', $param, $token);
            // dd($response['message']);
            if ($response['status'] == true) {
                $total_data = count($response['data']);
                foreach ($response['data'] as $each_data) {
                    TahunAjaran::updateOrInsert([
                        'id_thn_ajaran' => $each_data['id_thn_ajaran'],
                    ], [
                        'nm_thn_ajaran' => $each_data['nm_thn_ajaran'],
                        'a_periode_aktif' => $each_data['a_periode_aktif'],
                        'tgl_mulai' => $each_data['tgl_mulai'],
                        'tgl_selesai' => $each_data['tgl_selesai'],
                        'tgl_create' => $each_data['waktu_data_ditambahkan'],
                        'last_update' => $each_data['terakhir_diubah'],
                        'last_sync' => currDateTime()
                      ]);
                    echo " Data berhasil disimpan ". $each_data['id_thn_ajaran'] ."\n";
                }
            } elseif ($response['message'] == 'Gagal Otentikasi') {
                $token = $this->generate_token();
            } else {
                
                echo "Error Pesan = " . $response['message'] . "\n";
                
                break;
            }
    }
}

        if(in_array('semester', $func)){
        for ($i = 1; $i <= 200; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50
            ]);

            $response = curlApi('GET', $url . '/referensi/semester', $param, $token);
            // dd($response['message']);
            if ($response['status'] == true) {
                $total_data = count($response['data']);
                foreach ($response['data'] as $each_data) {
                    Semester::updateOrInsert([
                        'id_smt' => $each_data['id_smt']
                    ], [
                        'id_thn_ajaran' => $each_data['id_thn_ajaran'],
                        'nm_smt' => $each_data['nm_smt'],
                        'smt' => $each_data['smt'],
                        'a_periode_aktif' => $each_data['a_periode_aktif'],
                        'tgl_mulai' => $each_data['tgl_mulai'],
                        'tgl_selesai' => $each_data['tgl_selesai'],
                        'tgl_create' => $each_data['waktu_data_ditambahkan'],
                        'last_update' => $each_data['terakhir_diubah'],
                        'last_sync' => currDateTime()
                      ]);
                    echo " Data berhasil disimpan ". $each_data['id_smt'] ."\n";
                }
            } elseif ($response['message'] == 'Gagal Otentikasi') {
                $token = $this->generate_token();
            } else {
                
                echo "Error Pesan = " . $response['message'] . "\n";
                
                break;
            }
        }
    }    

     if(in_array('jenis_keluar', $func)){
            for ($i = 1; $i <= 10; $i++) {
                $param = json_encode([
                    'page' => $i,
                    'limit' => 10
                ]);
                $response = curlApi('GET', $url . '/referensi/jenis_keluar', $param, $token);
                // dd($response['message']);
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $each_data) {
                        JenisKeluar::updateOrInsert([
                            'id_jns_keluar' => $each_data['id_jns_keluar']
                        ], [
                            'ket_keluar' => $each_data['ket_keluar'],
                            'a_pd' => $each_data['a_pd'],
                            'a_ptk' => $each_data['a_ptk'],
                            'a_sdm_iptek' => $each_data['a_sdm_iptek'],
                            'tgl_create' => $each_data['waktu_data_ditambahkan'],
                            'last_update' => $each_data['terakhir_diubah'],
                            'last_sync' => currDateTime()
                          ]);
                        echo " Data berhasil disimpan ". $each_data['id_jns_keluar'] ."\n";
                    }
                } elseif ($response['message'] == 'Gagal Otentikasi') {
                    $token = $this->generate_token();
                } else {
                    
                    echo "Error Pesan = " . $response['message'] . "\n";
                    
                    break;
                }
        }
    }

    if(in_array('tahun_ajaran', $func)){
        for ($i = 1; $i <= 20; $i++) {
            $param = json_encode([
                'page' => $i,
                'limit' => 50
            ]);
            $response = curlApi('GET', $url . '/referensi/tahun_ajaran', $param, $token);
            // dd($response['message']);
            if ($response['status'] == true) {
                $total_data = count($response['data']);
                foreach ($response['data'] as $each_data) {
                    TahunAjaran::updateOrInsert([
                        'id_thn_ajaran' => $each_data['id_thn_ajaran'],
                    ], [
                        'nm_thn_ajaran' => $each_data['nm_thn_ajaran'],
                        'a_periode_aktif' => $each_data['a_periode_aktif'],
                        'tgl_mulai' => $each_data['tgl_mulai'],
                        'tgl_selesai' => $each_data['tgl_selesai'],
                        'tgl_create' => $each_data['waktu_data_ditambahkan'],
                        'last_update' => $each_data['terakhir_diubah'],
                        'last_sync' => currDateTime()
                      ]);
                    echo " Data berhasil disimpan ". $each_data['id_thn_ajaran'] ."\n";
                }
            } elseif ($response['message'] == 'Gagal Otentikasi') {
                $token = $this->generate_token();
            } else {
                
                echo "Error Pesan = " . $response['message'] . "\n";
                
                break;
            }
    }
}
if(in_array('jenis_keluar', $func)){
    for ($i = 1; $i <= 10; $i++) {
        $param = json_encode([
            'page' => $i,
            'limit' => 10
        ]);
        $response = curlApi('GET', $url . '/referensi/jenis_keluar', $param, $token);
        // dd($response['message']);
        if ($response['status'] == true) {
            $total_data = count($response['data']);
            foreach ($response['data'] as $each_data) {
                JenisKeluar::updateOrInsert([
                    'id_jns_keluar' => $each_data['id_jns_keluar']
                ], [
                    'ket_keluar' => $each_data['ket_keluar'],
                    'a_pd' => $each_data['a_pd'],
                    'a_ptk' => $each_data['a_ptk'],
                    'a_sdm_iptek' => $each_data['a_sdm_iptek'],
                    'tgl_create' => $each_data['waktu_data_ditambahkan'],
                    'last_update' => $each_data['terakhir_diubah'],
                    'last_sync' => currDateTime()
                  ]);
                echo " Data berhasil disimpan ". $each_data['id_jns_keluar'] ."\n";
            }
        } elseif ($response['message'] == 'Gagal Otentikasi') {
            $token = $this->generate_token();
        } else {
            
            echo "Error Pesan = " . $response['message'] . "\n";
            
            break;
        }
}
}

if(in_array('jab_tgs', $func)){
for ($i = 1; $i <= 102; $i++) {
    $param = json_encode([
        'page' => $i,
        'limit' => 200
    ]);
    $response = curlApi('GET', $url . '/referensi/jab_tgs', $param, $token);
    // dd($response['message']);
    if ($response['status'] == true) {
        $total_data = count($response['data']);
        foreach ($response['data'] as $each_data) {
            JabTgs::updateOrInsert([
                'id_jab_tgs' => $each_data['id_jab_tgs'],
            ], [
                'id_kel_prof' => $each_data['id_kel_prof'],
                'nm_jab_tgs' => $each_data['nm_jab_tgs'],
                'a_jab_utama_sek' => $each_data['a_jab_utama_sek'],
                'a_jab_utama_pt' => $each_data['a_jab_utama_pt'],
                'a_jab_utama_lpnk' => $each_data['a_jab_utama_lpnk'],
                'a_jab_utama_lpk' => $each_data['a_jab_utama_lpk'],
                'jml_jam_diakui' => $each_data['jml_jam_diakui'],
                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                'last_update' => $each_data['terakhir_diubah'],
                'last_sync' => currDateTime()
              ]);
            echo " Data berhasil disimpan ". $each_data['nm_jab_tgs'] ."\n";
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