<?php

namespace App\Http\Controllers\KPTA;

use Illuminate\Http\Request;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\PeranSeminar;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RwySeminarPd;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Validasi\AjuanDraftUsul;
use App\Models\Kpta\RincianPeranSeminar;
use App\Models\Kpta\PeranDosenRwySeminar;
use App\Models\Validasi\VerAjuanPeranSeminar;

class DistribusiDosenMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prodi = GetProdiIndividu();
        $list_angkatan = collect(PesertaDidik::ListPesertaDidikProdi($prodi->id_sms))
            ->pluck('angkatan', 'angkatan')->toArray();
        if ($request->has('angkatan')) {
            $angkatan = $request->angkatan;
        } else {
            $angkatan = null;
        }
        $jenis_seminar = JenisSeminar::jenis_seminar_prodi($prodi->id_sms);
        $data_peserta = PesertaDidik::ListPesertaDidikProdi($prodi->id_sms, $angkatan);
        // dd($data_peserta);
        return view('kpta.distribusi_dosen_mahasiswa.index', compact('data_peserta', 'list_angkatan', 'angkatan', 'prodi', 'jenis_seminar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = RincianPeranSeminar::where('id_rincian_peran_seminar', $input['id_rincian_peran_seminar'])->where('soft_delete', 0)->first();
        $data->fill($data->prepare($input))->save();
        $data_peran_seminar = PeranSeminar::where('id_rincian_peran_seminar', $data->id_rincian_peran_seminar)->where('a_aktif', 1)->where('a_ganti', 0)->get();
        // dd($data_peran_seminar);
        foreach ($data_peran_seminar as $each_data) {
            $each_data->fill($each_data->prepare(
                [
                    '_method'=>'PUT',
                    'sk_tugas' => $data->sk_tugas,
                    'tgl_sk_tugas' => $data->tgl_sk_tugas,
                ]
            ))->save();
        }
        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, PesertaDidik $pesertaDidik)
    {
        $data_awal = Crypt::decrypt($id);
        $prodi = GetProdiIndividu();
        $pd = $pesertaDidik->detail_mahasiswa($data_awal['id_pd']);
        $jenis_seminar = JenisSeminar::find($data_awal['id_jns_seminar']);
        $rincian_peran_seminar = RincianPeranSeminar::where('id_reg_pd', $pd->id_reg_pd)->where('id_jns_seminar', $data_awal['id_jns_seminar'])->where('soft_delete', 0)->first();
        if (is_null($rincian_peran_seminar)) {
            $judul_akt_mhs = AjuanDraftUsul::where('id_pd', $data_awal['id_pd'])->where('id_jns_seminar', $data_awal['id_jns_seminar'])->where('stat_ajuan', 2)->where('soft_delete', 0)->first();
            $data = new RincianPeranSeminar();
            if (!is_null($judul_akt_mhs)) {
                $data->fill($data->prepare([
                    'judul_akt_mhs' => $judul_akt_mhs->judul_draft_usul_baru,
                    'id_reg_pd' => $pd->id_reg_pd,
                    'id_jns_seminar' => $data_awal['id_jns_seminar'],
                ]))->save();
            }
            $data->fill($data->prepare([
                'id_reg_pd' => $pd->id_reg_pd,
                'id_jns_seminar' => $data_awal['id_jns_seminar'],
            ]))->save();
            $rincian_peran_seminar = RincianPeranSeminar::find($data->id_rincian_peran_seminar);
        }

        $seminar = collect(DB::SELECT("
            SELECT
                seminar.jmlh_pembimbing,
                seminar.jmlh_penguji
            FROM ref.jenis_seminar AS jns
            JOIN ref.jenis_seminar AS t1 ON t1.id_induk_jns_seminar = jns.id_jns_seminar AND t1.expired_date IS NULL
            JOIN kpta.seminar_prodi AS seminar ON seminar.id_jns_seminar=t1.id_jns_seminar AND seminar.soft_delete=0
                AND seminar.a_aktif=1
            WHERE jns.expired_date IS NULL
            AND seminar.id_sms = '" . $pd->id_sms . "'
            AND jns.id_jns_seminar=" . $jenis_seminar->id_jns_seminar . "
            ORDER BY seminar.urutan DESC
        "))->first();
        $dosen = collect(DB::SELECT("
            SELECT
                tsdm.id_sdm,
                CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,') - ',tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_dosen
            FROM pdrd.sdm AS tsdm
            JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm AND tr.soft_delete=0
                AND tr.id_jns_keluar IS NULL AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar<=NOW())
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik=tprodi.id_jenj_didik
            WHERE tsdm.soft_delete=0
            AND tsdm.id_stat_aktif=1
            AND tsdm.id_jns_sdm=12
            ORDER BY tjenj.nm_jenj_didik ASC, tprodi.nm_lemb ASC, tsdm.nm_sdm ASC
        "))->pluck('nm_dosen', 'id_sdm')->toArray();

        return view('kpta.distribusi_dosen_mahasiswa.edit', compact('prodi', 'data_awal', 'pd', 'jenis_seminar', 'seminar', 'dosen', 'rincian_peran_seminar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, PesertaDidik $pesertaDidik)
    {
        $data_awal = Crypt::decrypt($id);
        $prodi = GetProdiIndividu();
        $pd = $pesertaDidik->detail_mahasiswa($data_awal['id_pd']);
        $jenis_seminar = JenisSeminar::find($data_awal['id_jns_seminar']);
        $rincian_peran_seminar = RincianPeranSeminar::where('id_reg_pd', $pd->id_reg_pd)->where('id_jns_seminar', $data_awal['id_jns_seminar'])->where('soft_delete', 0)->first();

        $input = $request->all();
        $pembimbing = $request->pembimbing;
        $nm_sdm_pembimbing = $request->nm_pembimbing_luar_kampus;
        $alasan_ganti = $request->alasan_ganti;
        $a_input_baru_pembimbing = $request->a_input_baru_pembimbing;

        if (!is_null($pembimbing)) {
            foreach ($pembimbing as $urutan_bimbing => $each_pembimbing) {
                $is_luar_unila = (isset($a_input_baru_pembimbing[$urutan_bimbing]) && $a_input_baru_pembimbing[$urutan_bimbing] == 1)
                    || (isset($request->a_ganti_pembimbing_baru[$urutan_bimbing]) && $request->a_ganti_pembimbing_baru[$urutan_bimbing] == 1);

                $cari_pembimbing = PeranSeminar::where('id_reg_pd', $pd->id_reg_pd)
                    ->where('soft_delete', 0)
                    ->where('peran', 1)
                    ->where('a_aktif', 1)
                    ->where('a_ganti', 0)
                    ->where('urutan', $urutan_bimbing)
                    ->where('id_jns_seminar', $jenis_seminar->id_jns_seminar)
                    ->first();

                if ($is_luar_unila) {
                    $data = [
                        'id_reg_pd' => $pd->id_reg_pd,
                        'nm_pembimbing_luar_kampus' =>  $nm_sdm_pembimbing[$urutan_bimbing] ?? null,
                        'id_sdm' => null,
                        'id_jns_seminar' => $jenis_seminar->id_jns_seminar,
                        'peran' => 1,
                        'urutan' => $urutan_bimbing,
                        'sk_tugas' => $rincian_peran_seminar->sk_tugas,
                        'tgl_sk_tugas' => $rincian_peran_seminar->tgl_sk_tugas,
                        'id_rincian_peran_seminar' => $rincian_peran_seminar->id_rincian_peran_seminar,
                    ];
                } else {
                    $data = [
                        'id_reg_pd' => $pd->id_reg_pd,
                        'nm_pembimbing_luar_kampus' => null,
                        'id_sdm' => $each_pembimbing,
                        'id_jns_seminar' => $jenis_seminar->id_jns_seminar,
                        'peran' => 1,
                        'urutan' => $urutan_bimbing,
                        'sk_tugas' => $rincian_peran_seminar->sk_tugas,
                        'tgl_sk_tugas' => $rincian_peran_seminar->tgl_sk_tugas,
                        'id_rincian_peran_seminar' => $rincian_peran_seminar->id_rincian_peran_seminar,
                    ];
                }

                if (is_null($cari_pembimbing)) {
                    $pembimbing_baru = new PeranSeminar();
                    $pembimbing_baru->fill($pembimbing_baru->prepare($data))->save();
                } else {
                    $ganti_pembimbing = PeranSeminar::find($cari_pembimbing->id_peran_seminar);
                    $ganti_pembimbing->fill($ganti_pembimbing->prepare([
                        '_method' => 'PUT',
                        'tgl_ganti' => currDateTime(),
                        'a_aktif' => 0,
                        'a_ganti' => 1,
                        'alasan_ganti' => $alasan_ganti[$urutan_bimbing] ?? null,
                    ]))->save();
                    $pembimbing_baru = new PeranSeminar();
                    $pembimbing_baru->fill($pembimbing_baru->prepare($data) + [
                        'id_induk_peran_seminar' => $cari_pembimbing->id_peran_seminar
                    ])->save();
                }
            }
        }

        $penguji = $request->penguji;
        $nm_sdm_penguji = $request->nm_penguji_luar_kampus;
        $a_input_baru_penguji = $request->a_input_baru_penguji;
        if (!is_null($penguji)) {
            foreach ($penguji as $urutan_uji => $each_penguji) {
                $is_luar_unila = isset($a_input_baru_penguji[$urutan_uji]) && $a_input_baru_penguji[$urutan_uji] == 1
                    || (isset($request->a_ganti_penguji_baru[$urutan_uji]) && $request->a_ganti_penguji_baru[$urutan_uji] == 1);
                $cari_penguji = PeranSeminar::where('id_reg_pd', $pd->id_reg_pd)
                    ->where('soft_delete', 0)->where('peran', 2)->where('a_aktif', 1)->where('a_ganti', 0)->where('urutan', $urutan_uji)
                    ->where('id_jns_seminar', $jenis_seminar->id_jns_seminar)
                    ->first();

                if ($is_luar_unila) {
                    $data = [
                        'id_reg_pd' => $pd->id_reg_pd,
                        'nm_penguji_luar_kampus' => $nm_sdm_penguji[$urutan_uji] ?? null,
                        'id_sdm' => null,
                        'id_jns_seminar' => $jenis_seminar->id_jns_seminar,
                        'peran' => 2,
                        'urutan' => $urutan_uji,
                        'sk_tugas' => $rincian_peran_seminar->sk_tugas,
                        'tgl_sk_tugas' => $rincian_peran_seminar->tgl_sk_tugas,
                        'id_rincian_peran_seminar' => $rincian_peran_seminar->id_rincian_peran_seminar,
                    ];
                } else {
                    $data = [
                        'id_reg_pd' => $pd->id_reg_pd,
                        'nm_penguji_luar_kampus' => null,
                        'id_sdm' => $each_penguji,
                        'id_jns_seminar' => $jenis_seminar->id_jns_seminar,
                        'peran' => 2,
                        'urutan' => $urutan_uji,
                        'sk_tugas' => $rincian_peran_seminar->sk_tugas,
                        'tgl_sk_tugas' => $rincian_peran_seminar->tgl_sk_tugas,
                        'id_rincian_peran_seminar' => $rincian_peran_seminar->id_rincian_peran_seminar,
                    ];
                }
                if (is_null($cari_penguji)) {
                    $penguji_baru = new PeranSeminar();
                    $penguji_baru->fill($penguji_baru->prepare($data))->save();
                } else {
                    $penguji_ganti = PeranSeminar::find($cari_penguji->id_peran_seminar);
                    $penguji_ganti->fill($penguji_ganti->prepare([
                        '_method' => 'PUT',
                        'tgl_ganti' => currDateTime(),
                        'a_aktif' => 0,
                        'a_ganti' => 1,
                        'alasan_ganti' => $alasan_ganti[$urutan_uji] ?? null,
                    ]))->save();
                    $penguji_baru = new PeranSeminar();
                    $penguji_baru->fill($penguji_baru->prepare($data) + [
                        'id_induk_peran_seminar' => $cari_penguji->id_peran_seminar
                    ])->save();
                }

                // if (is_null($cari_penguji)) {
                //     $bimbing_baru = new PeranSeminar();
                //     $bimbing_baru->fill($bimbing_baru->prepare([
                //         'id_reg_pd'         => $pd->id_reg_pd,
                //         'id_sdm'            => $each_penguji,
                //         'id_jns_seminar'    => $jenis_seminar->id_jns_seminar,
                //         'peran'             => 2,
                //         'urutan'            => $urutan_uji
                //     ]))->save();
                //     $id_peran_seminar = $bimbing_baru->id_peran_seminar;
                // } else {
                //     $bimbing_update = PeranSeminar::find($cari_penguji->id_peran_seminar);
                //     $bimbing_update->fill($bimbing_update->prepare([
                //         '_method'           => 'PUT',
                //         'id_reg_pd'         => $pd->id_reg_pd,
                //         'id_sdm'            => $each_penguji,
                //         'id_jns_seminar'    => $jenis_seminar->id_jns_seminar,
                //         'peran'             => 2,
                //         'urutan'            => $urutan_uji
                //     ]))->save();
                //     $id_peran_seminar = $cari_penguji->id_peran_seminar;
                // }
            }
            // foreach ($turunan_jenis_seminar AS $each_turunan) {
            //     $cari_rwy = RwySeminarPd::where('id_reg_pd',$pd->id_reg_pd)
            //         ->where('id_jns_seminar',$each_turunan->id_jns_seminar)
            //         ->where('soft_delete',0)->first();
            //     if (!is_null($cari_rwy)) {
            //         $cari_peran_dosen = PeranDosenRwySeminar::where('id_rwy_seminar',$cari_rwy->id_rwy_seminar)
            //             ->where('id_peran_seminar',$id_peran_seminar)
            //             ->where('soft_delete',0)
            //             ->first();
            //         if (is_null($cari_peran_dosen)) {
            //             $simpan_peran = new PeranDosenRwySeminar();
            //             $simpan_peran->fill($simpan_peran->prepare([
            //                 'id_daftar_seminar' => $cari_rwy->id_daftar_seminar,
            //                 'id_peran_seminar'  => $id_peran_seminar
            //             ]))->save();
            //         }
            //     }
            // }
        }
        //        if($jenis_seminar->a_tugas_akhir==0)
        //        {
        //            $dosen_pembimbing_non_ta = DB::table('kpta.peran_seminar AS peran')
        //                ->where('peran.soft_delete',0)
        //                ->where('peran.id_reg_pd',$pd->id_reg_pd)
        //                ->where('peran.peran',6)
        //                ->where('peran.id_jns_seminar',$jenis_seminar->id_jns_seminar)
        //                ->first();
        //            if (is_null($dosen_pembimbing_non_ta)) {
        //                $bimbing_non_ta_baru = new PeranSeminar();
        //                $bimbing_non_ta_baru->fill($bimbing_non_ta_baru->prepare([
        //                    'id_reg_pd'         => $pd->id_reg_pd,
        //                    'id_jns_seminar'    => $jenis_seminar->id_jns_seminar,
        //                    'nm_pemb_lapangan'  => $request->nm_pemb_lapangan,
        //                    'jabatan'           => $request->jabatan,
        //                    'lokasi'            => $request->lokasi,
        //                    'peran'             => 6,
        //                    'urutan'            => 1
        //                ]))->save();
        //            } else {
        //                $bimbing_update = PeranSeminar::find($dosen_pembimbing_non_ta->id_peran_seminar);
        //                $bimbing_update->fill($bimbing_update->prepare([
        //                    '_method'           => 'PUT',
        //                    'id_reg_pd'         => $pd->id_reg_pd,
        //                    'id_jns_seminar'    => $jenis_seminar->id_jns_seminar,
        //                    'nm_pemb_lapangan'  => $request->nm_pemb_lapangan,
        //                    'jabatan'           => $request->jabatan,
        //                    'lokasi'            => $request->lokasi,
        //                    'peran'             => 6,
        //                    'urutan'            => 1
        //                ]))->save();
        //            }
        //        }
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
