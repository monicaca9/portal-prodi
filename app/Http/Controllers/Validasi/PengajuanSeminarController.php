<?php

namespace App\Http\Controllers\Validasi;

use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Ref\JenisDokumen;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Kpta\SyaratSeminar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kpta\ListSyaratDaftar;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\ListSyaratSeminar;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Kpta\NomorBaDaftarSeminar;
use App\Models\Validasi\VerAjuanDftrSeminar;

class PengajuanSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $status = $request->status;
            if ($status == 'Ditolak') {
                // $status_periksa = ['T','C'];
                $status_validasi = 4;
            } elseif ($status == 'Diserahkan') {
                // $status_periksa=['Y'];
                $status_validasi = 2;
            } elseif ($status == 'Disetujui') {
                // $status_periksa = ['L'];
                $status_validasi = 3;
            } else {
                // $status_periksa = ['L'];
                $status_validasi = 5;
            }
            $query = "
            SELECT
                dftr.id_daftar_seminar,
                dftr.status_validasi,
                jns.nm_jns_seminar,
                tr.nim,
                pd.nm_pd,
                dftr.waktu_diajukan,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                DATE_PART('day', NOW() -  dftr.waktu_diajukan::timestamp) AS umur_ajuan,
                COALESCE(CONCAT(no_ba.no_ba_daftar_seminar, no_ba.kode_ba_daftar_seminar), '-') AS no_ba_seminar
            FROM kpta.pendaftaran_seminar AS dftr
            JOIN kpta.seminar_prodi AS semprod ON semprod.id_seminar_prodi=dftr.id_seminar_prodi AND semprod.soft_delete=0
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = semprod.id_jns_seminar
            JOIN pdrd.reg_pd AS tr ON tr.id_reg_pd = dftr.id_reg_pd AND tr.soft_delete=0
            JOIN pdrd.peserta_didik AS pd ON pd.id_pd = tr.id_pd AND pd.soft_delete=0
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN kpta.nomor_ba_daftar_seminar  AS no_ba ON no_ba.id_daftar_seminar = dftr.id_daftar_seminar
            WHERE dftr.soft_delete=0
            AND dftr.status_validasi=" . $status_validasi . "
            ORDER BY dftr.last_update
        ";
        } else {
            $status = 'Diajukan';
            // $status_periksa = ['N'];
            $status_validasi = 1;
            $query = "
            SELECT
                dftr.id_daftar_seminar,
                dftr.status_validasi,
                jns.nm_jns_seminar,
                tr.nim,
                pd.nm_pd,
                dftr.waktu_diajukan,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                DATE_PART('day', NOW() -  dftr.waktu_diajukan::timestamp) AS umur_ajuan
            FROM kpta.pendaftaran_seminar AS dftr
            JOIN kpta.seminar_prodi AS semprod ON semprod.id_seminar_prodi=dftr.id_seminar_prodi AND semprod.soft_delete=0
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = semprod.id_jns_seminar
            JOIN pdrd.reg_pd AS tr ON tr.id_reg_pd = dftr.id_reg_pd AND tr.soft_delete=0
            JOIN pdrd.peserta_didik AS pd ON pd.id_pd = tr.id_pd AND pd.soft_delete=0
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE dftr.soft_delete=0
            AND dftr.status_validasi=" . $status_validasi . "
            ORDER BY dftr.last_update
        ";
        }

        $data = DB::SELECT($query);
        // dd($data);
        return view('validasi.seminar.index', compact('data', 'status', 'status_validasi'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function ubah_aktif(Request $request, $id)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        // dd($data);
        $data->fill($data->prepare([
            'status_validasi'               => '2',
        ]))->save();
        alert()->success('Berhasil mengubah status pengajuan seminar')->persistent('OK');
        return redirect()->back();
    }


    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $data_nilai = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
        $profil = $pesertaDidik->id_detail_mahasiswa($data->RegPd->id_pd);
        $no_ba_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $id_daftar_seminar)
            ->where('soft_delete', 0)
            ->first();
        $syarat = DB::SELECT("
            SELECT
                list_syarat.id_list_syarat,
                syarat.nm_syarat_seminar,
                syarat.keterangan,
                list_daftar.id_list_syarat_daftar,
                list_daftar.wkt_ajuan,
                list_daftar.wkt_update,
                list_daftar.stat_ajuan,
                list_daftar.jns_ajuan,
                dok.jmlh_dok,
                ver.id_ver_ajuan,
                ver.status_periksa,
                ver.ket_periksa,
                ver.nm_verifikator,
                ver.wkt_selesai_ver,
                ver.verifikasi_ke
            FROM kpta.list_syarat_seminar AS list_syarat
            JOIN kpta.syarat_seminar AS syarat ON syarat.id_syarat_seminar = list_syarat.id_syarat_seminar AND syarat.soft_delete=0
            LEFT JOIN kpta.list_syarat_daftar AS list_daftar ON list_daftar.id_list_syarat=list_syarat.id_list_syarat AND list_daftar.soft_delete=0
                AND list_daftar.id_daftar_seminar='" . $data->id_daftar_seminar . "'
            LEFT JOIN (
                SELECT id_list_syarat_daftar, COUNT(*) AS jmlh_dok
                FROM dok.dok_syarat_daftar
                WHERE soft_delete=0
                GROUP BY id_list_syarat_daftar
            ) AS dok ON dok.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            LEFT JOIN (
                SELECT v1.id_ver_ajuan, v1.id_list_syarat_daftar, v1.status_periksa, v1.ket_periksa, v1.nm_verifikator, v1.wkt_selesai_ver, v1.verifikasi_ke
                FROM validasi.ver_ajuan_dft_seminar AS v1
                JOIN (
                    SELECT id_list_syarat_daftar, MAX(verifikasi_ke) AS max_verifikasi
                    FROM validasi.ver_ajuan_dft_seminar
                    WHERE soft_delete=0
                    AND level_ver = '1'
                    GROUP BY id_list_syarat_daftar
                ) AS v2 ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                    AND v2.max_verifikasi=v1.verifikasi_ke
                WHERE v1.soft_delete=0
                AND v1.level_ver = '1'
            ) AS ver ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            WHERE list_syarat.soft_delete=0
            AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
            ORDER BY list_syarat.urutan ASC
        ");
        // dd($syarat);

        $ruangs = Ruang::all();
        $gedung_ruang = [];
        foreach ($ruangs as $ruang) {
            $gedung = Gedung::where('id_gedung', $ruang->id_gedung)->first();
            $arraydata = [

                'id_gedung' => $gedung->id_gedung,
                'nm_gedung' => $gedung->nm_gedung,
                'id_ruang' => $ruang->id_ruang,
                'nm_ruang' => $ruang->nm_ruang,
            ];
            array_push(
                $gedung_ruang,
                $arraydata
            );
        }
        return view('validasi.seminar.detail', compact('data', 'seminar', 'profil', 'syarat', 'no_ba_seminar', 'data_nilai', 'gedung_ruang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $input_simpan = $request->all();
        $input_data = $request->except(['ket_periksa']);
        $simpan = VerAjuanDftrSeminar::find($input_simpan['id_ver_ajuan']);
        unset($input_simpan['id_ver_ajuan']);
        $input_simpan['nm_verifikator']    = auth()->user()->nm_pengguna;
        $input_simpan['id_role_pengguna']  = session()->get('login.peran.id_role_pengguna');
        if (is_null($simpan->wkt_mulai_ver)) {
            $input_simpan['wkt_mulai_ver'] = currDateTime();
        }
        if (is_null($data->wkt_diproses)) {
            $input_data['wkt_diproses'] = currDateTime();
        }
        $data->fill($data->prepare($input_data))->save();
        $simpan->fill($simpan->prepare($input_simpan))->save();
        alert()->success('Status verifikasi berhasil diubah')->persistent('OK');

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $input = $request->all();

        $syarat = DB::SELECT("
            SELECT
                list_syarat.id_list_syarat,
                syarat.nm_syarat_seminar,
                syarat.keterangan,
                list_daftar.id_list_syarat_daftar,
                list_daftar.wkt_ajuan,
                list_daftar.wkt_update,
                list_daftar.stat_ajuan,
                list_daftar.jns_ajuan,
                dok.jmlh_dok,
                ver.id_ver_ajuan,
                ver.status_periksa,
                ver.ket_periksa,
                ver.nm_verifikator,
                ver.wkt_selesai_ver,
                ver.verifikasi_ke,
                ver.level_ver
            FROM kpta.list_syarat_seminar AS list_syarat
            JOIN kpta.syarat_seminar AS syarat ON syarat.id_syarat_seminar = list_syarat.id_syarat_seminar AND syarat.soft_delete=0
            LEFT JOIN kpta.list_syarat_daftar AS list_daftar ON list_daftar.id_list_syarat=list_syarat.id_list_syarat AND list_daftar.soft_delete=0
                AND list_daftar.id_daftar_seminar='" . $data->id_daftar_seminar . "'
            LEFT JOIN (
                SELECT id_list_syarat_daftar, COUNT(*) AS jmlh_dok
                FROM dok.dok_syarat_daftar
                WHERE soft_delete=0
                GROUP BY id_list_syarat_daftar
            ) AS dok ON dok.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            LEFT JOIN (
                SELECT v1.id_ver_ajuan, v1.id_list_syarat_daftar, v1.status_periksa, v1.ket_periksa, v1.nm_verifikator, v1.wkt_selesai_ver, v1.verifikasi_ke, v1.level_ver
                FROM validasi.ver_ajuan_dft_seminar AS v1
                JOIN (
                    SELECT id_list_syarat_daftar, MAX(verifikasi_ke) AS max_verifikasi
                    FROM validasi.ver_ajuan_dft_seminar
                    WHERE soft_delete=0
                    GROUP BY id_list_syarat_daftar
                ) AS v2 ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                    AND v2.max_verifikasi=v1.verifikasi_ke
                WHERE v1.soft_delete=0
            ) AS ver ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            WHERE list_syarat.soft_delete=0
            AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
            ORDER BY list_syarat.urutan ASC
        ");
        // dd($syarat);
        $all_processed = true;

        foreach ($syarat as $each_syarat) {
            $ver = VerAjuanDftrSeminar::find($each_syarat->id_ver_ajuan);
            if ($ver->status_periksa == 'N') {
                $all_processed = false;
                break;
            }
        }
        // dd($all_processed);

        if ($all_processed) {
            foreach ($syarat as $each_syarat) {
                $ver = VerAjuanDftrSeminar::find($each_syarat->id_ver_ajuan);
                $ver->fill($ver->prepare([
                    '_method'               => 'PUT',
                    'wkt_selesai_ver'       => currDateTime(),
                    'stat_ajuan_sesudah'  => $input['status_validasi']
                ]))->save();

                $list_ajuan = ListSyaratDaftar::find($ver->id_list_syarat_daftar);

                if ($ver->level_ver == '1') {
                    if (in_array($ver->status_periksa, ['T', 'C'])) {
                        $status_validasi = 4;
                        $diproses       = 0;
                    } elseif ($ver->status_periksa == 'Y') {
                        $status_validasi = 2;
                        $diproses       = 1;
                    } elseif ($ver->status_periksa == 'L') {
                        $status_validasi = 5;
                        $diproses       = 0;
                    }

                    $list_ajuan->fill($list_ajuan->prepare([
                        '_method'       => 'PUT',
                        'stat_ajuan'    => $status_validasi,
                        'wkt_update'    => currDateTime(),
                        'ket_periksa'   => $ver->ket_periksa,
                        'a_diproses'    => $diproses
                    ]))->save();
                }

                if ($input['status_validasi'] == 2) {
                    $cari_ver = VerAjuanDftrSeminar::where('id_list_syarat_daftar', $each_syarat->id_list_syarat_daftar)
                        ->where('soft_delete', 0)
                        ->where('level_ver', '2')
                        ->orderBy('verifikasi_ke', 'DESC')
                        ->first();

                    if (is_null($cari_ver)) {
                        $simpan_ver = new VerAjuanDftrSeminar();
                        $simpan_ver->fill($simpan_ver->prepare([
                            'id_list_syarat_daftar' => $each_syarat->id_list_syarat_daftar,
                            'level_ver'             => '2',
                            'status_periksa'        => 'N'
                        ]))->save();
                    } else {
                        if (in_array($cari_ver->status_periksa, ['T', 'C', 'L'])) {
                            $simpan_ver = new VerAjuanDftrSeminar();
                            $simpan_ver->fill($simpan_ver->prepare([
                                'id_ver_ajuan_sebelum'  => $cari_ver->id_ver_ajuan,
                                'stat_ajuan_sebelum'    => $cari_ver->status_periksa,
                                'id_list_syarat_daftar' => $each_syarat->id_list_syarat_daftar,
                                'level_ver'             => '2',
                                'status_periksa'        => 'N',
                                'verifikasi_ke'         => ($cari_ver->verifikasi_ke + 1)
                            ]))->save();
                        }
                    }
                }
            }
            $input['nm_validator'] = auth()->user()->nm_pengguna;
            $data->fill($data->prepare($input))->save();

            alert()->success('Berhasil mengubah status pengajuan seminar')->persistent('OK');
        } else {
            alert()->warning('Silakan lengkapi status validasi sebelum melanjutkan')->persistent('OK');
        }
        return redirect()->back();
    }

    public function daftar_riwayat_verifikasi($id, $id_syarat, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
        $syarat = ListSyaratSeminar::find($id_list_syarat);
        $cari_dok_syarat = ListSyaratDaftar::where('id_daftar_seminar', $data->id_daftar_seminar)
            ->where('id_list_syarat', $syarat->id_list_syarat)->where('soft_delete', 0)->first();
        $profil = $pesertaDidik->id_detail_mahasiswa($data->RegPd->id_pd);
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok', 'ASC')->pluck('nm_jns_dok', 'id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_dok_syarat_daftar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_syarat_daftar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_list_syarat_daftar='" . $cari_dok_syarat->id_list_syarat_daftar . "'
        ");
        $dokumen_syarat_seminar = DB::SELECT("
        SELECT 
            dok_syarat.id_dok_syarat_seminar,
            dok_syarat.id_seminar_prodi,
            dok_syarat.id_list_syarat,
            dok_syarat.id_dok,
            jns.nm_jns_dok,
            dok.nm_dok,
            dok.ket_dok,
            dok.file_dok,
            dok.wkt_unggah
        FROM dok.dok_syarat_seminar AS dok_syarat
        JOIN dok.dokumen AS dok ON dok.id_dok = dok_syarat.id_dok
        JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
        WHERE dok_syarat.soft_delete=0
        AND dok_syarat.id_seminar_prodi ='" . $data->id_seminar_prodi . "'
        AND dok_syarat.id_list_syarat ='" . $id_list_syarat . "'
        ORDER BY dok.wkt_unggah ASC
       ");

        $validasi = VerAjuanDftrSeminar::where('id_list_syarat_daftar', $cari_dok_syarat->id_list_syarat_daftar)->where('status_periksa', '!=', 'N')->orderBy('wkt_selesai_ver', 'DESC')->get();
        return view('validasi.seminar.daftar_riwayat_verifikasi', compact('data', 'seminar', 'syarat', 'profil', 'jenis_dok', 'dokumen', 'cari_dok_syarat', 'dokumen_syarat_seminar', 'validasi'));
    }

    public function update_jadwal_seminar($id, Request $request)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $input = $request->all();
        $data->fill($data->prepare($input))->save();
        alert()->success('Jadwal Barhasil Disimpan')->persistent('OK');
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
