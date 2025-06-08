<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pdrd\RegPtk;
use Illuminate\Http\Request;

class RegPtkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function __construct(Request $request)
    {
        $this->regptk = new RegPtk();
        // $this->kuliah = new KuliahMahasiswa();
    }
    public function run(): void
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();

        $func=[
            'create'
            // 'updatedetail'
        ];
        
        if(in_array('create', $func)){ 

            for ($i = 1; $i <= 60; $i++) {
                $param = json_encode([
                    'id_jns_sdm' => 12,
                    'page' => $i,
                    'limit' => 25,
                    'sort_by' => 'desc',
                    'id_prodi' => 'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88'
                ]);

                
                $response = curlApi('GET', $url . '/sdm/daftar', $param, $token);
                // dd($response);
                $now = currDateTime();
                $soft_delete = 0;
                $no = 1;
                $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';
                if ($response['status'] == true) {
                    $total_data = count($response['data']);
                    foreach ($response['data'] as $key => $each_data) {

                        $regptk = $this->regptk->where('id_reg_ptk', $each_data['id_reg_ptk'])->first();
                        // dd($regptk);
                        if(isset($regptk)){
                            $regptk->update([
                                
                                'no_srt_tgs' => 'default',
                                'tgl_srt_tgs' => currDateTime(),
                                'tmt_srt_tgs' => currDateTime(),
                                'nidn' => $each_data['nidn'],
                                'id_stat_pegawai' => 99,
                                'id_sdm' => $each_data['id_sdm'],
                                'id_sp' => 'e2b705a7-173e-464a-9fac-509128709515',
                                'id_ikatan_kerja' => 'A',
                                'id_sms' => $each_data['id_prodi'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => currDateTime(),
                            ]);
                            echo "Data sudah ada, berhasil update id_reg_pd = ". $regptk->id_reg_ptk . ' ' . $key+1 . '/' . " \n";
                        }else{
                        $regptk =$this->regptk->create([
                                'id_reg_ptk' => $each_data['id_reg_ptk'],
                                'no_srt_tgs' => 'default',
                                'tgl_srt_tgs' => currDateTime(),
                                'tmt_srt_tgs' => currDateTime(),
                                'nidn' => $each_data['nidn'],
                                'id_stat_pegawai' => 99,
                                'id_sdm' => $each_data['id_sdm'],
                                'id_sp' => 'e2b705a7-173e-464a-9fac-509128709515',
                                'id_ikatan_kerja' => 'A',
                                'id_sms' => $each_data['id_prodi'],
                                'tgl_create' => $each_data['waktu_data_ditambahkan'],
                                'id_creator' => $id_creator,
                                'last_update' => $each_data['terakhir_diubah'],
                                'id_updater' => $id_creator,
                                'soft_delete' => $soft_delete,
                                'last_sync' => currDateTime(),
                                // 'updated_at' => $each_data['terakhir_diubah']
                            ]);

                            echo "Data berhasil disimpan id_reg_pd = ". $regptk->id_reg_ptk . ' ' . $key+1 . '/' . " \n";
                        }
                    }
            }elseif ($response['message'] == 'Gagal Otentikasi') {
                $token = $this->generate_token();
            } else {
                
                echo "Error Pesan = " . $response['message'] . "\n";
                
                break;
            }
        }
    }

    if(in_array('updatedetail', $func)){
        $sdm = DB::table('pdrd.reg_ptk')
        ->where('soft_delete', 0)
        ->select('id_sdm')
        ->groupBy('id_sdm')
        ->pluck('id_sdm')
        ->toArray();

        $total_sdm = count($sdm);
            // dd(count($sdm));
            

        foreach ($sdm as $key => $id_sdm) {
            $cari_sdm = DB::table('pdrd.sdm')->where('id_sdm', $id_sdm)->first();
            echo "Mendapatkan data regptk " . ($cari_sdm->id_sdm) . ' '. $key+1 . '/'. $total_sdm .  "\n";

                // dd($cari_sdm);
            $param = json_encode([
                'id_jns_sdm' => 12,
                'id_sdm' => $id_sdm
            ]);
    
            $response= curlApi('GET', $url . '/sdm/detail', $param, $token);
            $now = currDateTime();
            $soft_delete = 0;
            $no = 1;
            $id_creator = '167ed40c-e013-4f2b-8b79-54bc828d5090';

            if ($response['status'] == true) {
                $total_data = count($response['data']);
                foreach ($response['data']['sdm'] as $each_data) {
                    $regptk = $this->regptk->where('id_reg_ptk', $each_data['id_reg_ptk'])->first();
                    if(!is_null($regptk)){
                        $regptk->update([
                            'id_reg_ptk' => $each_data['id_reg_ptk'],
                            'no_srt_tgs' => $each_data['no_srt_tgs'],
                            'tgl_srt_tgs' => $each_data['tgl_srt_tgs'],
                            'tmt_srt_tgs' => $each_data['tmt_srt_tgs'],
                            'nidn' => $each_data['nidn'],
                            'id_stat_pegawai' => $each_data['id_stat_pegawai'],
                            'id_sdm' => $each_data['id_sdm'],
                            'id_sp' => 'e2b705a7-173e-464a-9fac-509128709515',
                            'id_ikatan_kerja' => $each_data['id_ikatan_kerja'],
                            'id_sms' => $each_data['id_prodi'],
                            'tgl_create' => $each_data['waktu_data_ditambahkan'],
                            'id_creator' => $id_creator,
                            'last_update' => $each_data['terakhir_diubah'],
                            'id_updater' => $id_creator,
                            'soft_delete' => $soft_delete,
                            'last_sync' => currDateTime(),
                            // 'updated_at' => $each_data['terakhir_diubah']
                        ]);
                        echo "Data berhasil disimpan id_reg_ptk = ". $regptk->id_reg_ptk . " \n";
                    }
                }
        }elseif ($response['message'] == 'Gagal Otentikasi') {
            $token = $this->generate_token();
        } else {
            
            echo "Error Pesan = " . $response['message'] . "\n";
            
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

