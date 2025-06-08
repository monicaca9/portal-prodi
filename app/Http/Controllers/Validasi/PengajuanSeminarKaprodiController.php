<?php

namespace App\Http\Controllers\Validasi;

use App\Models\Kpta\NomorBa;
use Illuminate\Http\Request;
use App\Models\Ref\JenisDokumen;
use App\Models\Kpta\PeranSeminar;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Models\Kpta\NomorBaSeminar;
use App\Http\Controllers\Controller;
use App\Models\Kpta\ListSyaratDaftar;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\ListSyaratSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Kpta\PeranDosenPendaftar;
use App\Models\Kpta\KomponenNilaiSeminar;
use App\Models\Kpta\NomorBaDaftarSeminar;
use App\Models\Validasi\VerAjuanDftrSeminar;
use App\Models\Kpta\ListKomponenNilaiSeminar;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\SkorPerKomponen;
use App\Models\Kpta\AvgSkorKategori;
use App\Models\Kpta\AvgSkorKomponen;

class PengajuanSeminarKaprodiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $status = $request->status;
            if ($status == 'Ditolak') {
                $status_validasi = 4;
            } elseif ($status == 'Disetujui') {
                $status_validasi = 3;
            } else {
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
            JOIN kpta.seminar_prodi AS tseminar ON tseminar.id_seminar_prodi=dftr.id_seminar_prodi AND tseminar.soft_delete=0
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = tseminar.id_jns_seminar
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
            $status = 'Diserahkan';
            $status_validasi = 2;
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
            JOIN kpta.seminar_prodi AS tseminar ON tseminar.id_seminar_prodi=dftr.id_seminar_prodi AND tseminar.soft_delete=0
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = tseminar.id_jns_seminar
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
        return view('validasi.seminar_kaprodi.index', compact('data', 'status', 'status_validasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
        $no_ba_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $id_daftar_seminar)
            ->where('soft_delete', 0)
            ->first();
        // dd($data);
        // dd($data, $seminar);
        $profil = $pesertaDidik->id_detail_mahasiswa($data->RegPd->id_pd);
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
                AND level_ver = '2'
                GROUP BY id_list_syarat_daftar
            ) AS v2 ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                AND v2.max_verifikasi=v1.verifikasi_ke
            WHERE v1.soft_delete=0
            AND v1.level_ver = '2'
        ) AS ver ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
        WHERE list_syarat.soft_delete=0
        AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
        ORDER BY list_syarat.urutan ASC
    ");
        return view('validasi.seminar_kaprodi.detail', compact('data', 'seminar', 'profil', 'syarat', 'no_ba_seminar'));
    }

    /**
     * Show the form for editing the specified resource.
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
     */
    public function update(Request $request,  $id)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $input = $request->all();
        // dd($input);

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
                    AND level_ver = '2'
                    GROUP BY id_list_syarat_daftar
                ) AS v2 ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                    AND v2.max_verifikasi=v1.verifikasi_ke
                WHERE v1.soft_delete=0
                AND v1.level_ver = '2'
            ) AS ver ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            WHERE list_syarat.soft_delete=0
            AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
            ORDER BY list_syarat.urutan ASC
        ");

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

                if (in_array($ver->status_periksa, ['T', 'C']) && $ver->level_ver == '2') {
                    $status_validasi = 4;
                    $diproses       = 0;
                } elseif ($ver->status_periksa == 'Y' && $ver->level_ver == '2') {
                    $status_validasi = 3;
                    $diproses       = 1;
                } elseif ($ver->status_periksa == 'L' && $ver->level_ver == '2') {
                    $status_validasi = 5;
                    $diproses       = 0;
                }
                $list_ajuan->fill($list_ajuan->prepare([
                    '_method'       => 'PUT',
                    'stat_ajuan'    => $status_validasi,
                    'a_diproses'    => $diproses
                ]))->save();
            }

            if ($input['status_validasi'] == 3) {
                $nomor_ba_seminar = NomorBaSeminar::where('id_seminar_prodi', $data->id_seminar_prodi)
                    ->where('soft_delete', 0)
                    ->first();
                // dd($nomor_ba_seminar);
                $nomor_ba_dftr_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $data->id_daftar_seminar)
                    ->where('soft_delete', 0)
                    ->first();
                $input['a_valid'] = 1;
                if (!is_null($nomor_ba_seminar)) {
                    if (is_null($nomor_ba_dftr_seminar)) {
                        $data_ba_dftr_seminar = [
                            '_method'       => 'POST',
                            'id_daftar_seminar' => $id_daftar_seminar,
                            'id_no_ba_seminar' => $nomor_ba_seminar->id_no_ba_seminar,
                            'no_ba_daftar_seminar' => $nomor_ba_seminar->nomorBa->no_ba_terbaru,
                            'kode_ba_daftar_seminar' => $nomor_ba_seminar->nomorBa->kode_ba,
                        ];
                        $simpan_data = new NomorBaDaftarSeminar();
                        $simpan_data->fill($simpan_data->prepare($data_ba_dftr_seminar))->save();

                        $nomor_ba = NomorBa::findOrFail($nomor_ba_seminar->id_no_ba);
                        $simpan_nomor_ba = [
                            '_method' => 'PUT',
                            'no_ba_terbaru' => $simpan_data->no_ba_daftar_seminar + 1,
                        ];
                        $nomor_ba->fill($nomor_ba->prepare($simpan_nomor_ba))->save();
                    }
                }
                // dd($nomor_ba_seminar);
                $data_peran_dosen_pendaftar = PeranDosenPendaftar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->get();

                $data_nilai_seminar = DB::SELECT("
                SELECT 
                    list_komponen.id_list_komponen_nilai,
                    list_kategori.id_list_kategori_nilai
                FROM kpta.list_komponen_nilai_seminar AS list_komponen
                RIGHT JOIN kpta.list_kategori_nilai_seminar AS list_kategori 
                    ON list_kategori.id_list_kategori_nilai = list_komponen.id_list_kategori_nilai
                WHERE list_kategori.id_seminar_prodi = '" . $data->id_seminar_prodi . "'
                AND list_kategori.soft_delete = 0
                AND (list_komponen.soft_delete = 0 OR list_komponen.soft_delete IS NULL)
                ");

                if (!is_null($data_peran_dosen_pendaftar)) {
                    foreach ($data_peran_dosen_pendaftar as $each_peran) {
                        foreach ($data_nilai_seminar as $each_nilai) {
                            if (!is_null($each_nilai->id_list_komponen_nilai)) {
                                $skor_komponen = SkorPerKomponen::where('id_peran_dosen_pendaftar', $each_peran->id_peran_dosen_pendaftar)
                                    ->pluck('id_list_komponen_nilai')
                                    ->toArray();

                                if (!in_array($each_nilai->id_list_komponen_nilai, $skor_komponen)) {
                                    $data_skor_komponen = [
                                        '_method'       => 'POST',
                                        'id_peran_dosen_pendaftar' => $each_peran->id_peran_dosen_pendaftar,
                                        'id_list_komponen_nilai' => $each_nilai->id_list_komponen_nilai,
                                        'skor' => 0,
                                    ];
                                    $simpan_data_skor_komponen = new SkorPerKomponen();
                                    $simpan_data_skor_komponen->fill($simpan_data_skor_komponen->prepare($data_skor_komponen))->save();
                                }
                            }

                            if (!is_null($each_nilai->id_list_kategori_nilai)) {
                                $total_skor_komponen = AvgSkorKomponen::where('id_peran_dosen_pendaftar', $each_peran->id_peran_dosen_pendaftar)
                                    ->pluck('id_list_kategori_nilai')
                                    ->toArray();

                                if (!in_array($each_nilai->id_list_kategori_nilai, $total_skor_komponen)) {
                                    $data_total_skor_komponen = [
                                        '_method'       => 'POST',
                                        'id_peran_dosen_pendaftar' => $each_peran->id_peran_dosen_pendaftar,
                                        'id_list_kategori_nilai' => $each_nilai->id_list_kategori_nilai,
                                        'skor' => 0,
                                    ];
                                    $simpan_data_skor_kategori = new AvgSkorKomponen();
                                    $simpan_data_skor_kategori->fill($simpan_data_skor_kategori->prepare($data_total_skor_komponen))->save();
                                }
                            }
                        }

                        $total_skor_kategori = AvgSkorKategori::where('id_peran_dosen_pendaftar',  $each_peran->id_peran_dosen_pendaftar)->first();
                        if (is_null($total_skor_kategori)) {
                            $data_total_skor_kategori = [
                                '_method'       => 'POST',
                                'id_daftar_seminar' => $id_daftar_seminar,
                                'id_peran_dosen_pendaftar' =>  $each_peran->id_peran_dosen_pendaftar,
                                'skor' => 0,
                            ];
                            $simpan_data_total_skor_kategori = new AvgSkorKategori();
                            $simpan_data_total_skor_kategori->fill($simpan_data_total_skor_kategori->prepare($data_total_skor_kategori))->save();
                        }
                    }
                }
                $nilai_akhir_seminar = NilaiAkhirSeminar::where('id_daftar_seminar',  $id_daftar_seminar)->first();
                if (is_null($nilai_akhir_seminar)) {
                    $data_nilai_akhir_seminar = [
                        '_method'       => 'POST',
                        'id_daftar_seminar' => $id_daftar_seminar,
                        'skor' => 0,
                        'a_valid' => 0,
                    ];
                    $simpan_data_nilai_akhir_seminar = new NilaiAkhirSeminar();
                    $simpan_data_nilai_akhir_seminar->fill($simpan_data_nilai_akhir_seminar->prepare($data_nilai_akhir_seminar))->save();
                }
            }
            $input['nm_validator']    = auth()->user()->nm_pengguna;
            $input['waktu_validasi'] = currDateTime();
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
        return view('validasi.seminar_kaprodi.daftar_riwayat_verifikasi', compact('data', 'seminar', 'syarat', 'profil', 'jenis_dok', 'dokumen', 'cari_dok_syarat', 'dokumen_syarat_seminar', 'validasi'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
