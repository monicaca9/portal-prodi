<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pdrd\Sdm;
use App\Models\Pdrd\RegPd;
use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\PeranSeminar;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $peran = session()->get('login.peran');

        if (in_array($peran['id_peran'], [6, 3000, 46, 3005])) {
            $prodi = GetProdiIndividu();
            $datajadwal = DB::SELECT("
            SELECT 
                daftar_seminar.id_daftar_seminar,
                daftar_seminar.id_reg_pd,
                daftar_seminar.id_ruang,
                daftar_seminar.id_seminar_prodi,
                daftar_seminar.hari,
                daftar_seminar.waktu,            
                daftar_seminar.tgl_mulai,
                jenis_seminar.nm_jns_seminar
            FROM kpta.pendaftaran_seminar AS daftar_seminar
            JOIN kpta.seminar_prodi AS seminar ON seminar.id_seminar_prodi = daftar_seminar.id_seminar_prodi
            JOIN ref.jenis_seminar AS jenis_seminar ON jenis_seminar.id_jns_seminar = seminar.id_jns_seminar
            WHERE daftar_seminar.soft_delete = 0
            AND seminar.id_sms = '" . $prodi->id_sms . "'
            ");
            // $datajadwal =  PendaftaranSeminar::all();
            $jadwal_seminar = [];
            foreach ($datajadwal as $jadwal) {
                $ruang = Ruang::where('id_ruang', $jadwal->id_ruang)->first();
                if ($ruang != null) {
                    $reg_pd = RegPd::where('id_reg_pd', $jadwal->id_reg_pd)->first();
                    //  dd($datajadwal,$jadwal,["perserta"=>$pd]);
                    if ($reg_pd != null) {
                        $pd = PesertaDidik::where('id_pd', $reg_pd->id_pd)->first();
                        $gedung = Gedung::where('id_gedung', $ruang->id_gedung)->first();
                        $jam = config('mp.data_master.waktu')[$jadwal->waktu];
                        $startText = $jadwal->tgl_mulai . " " . $jam;
                        $endText = $jadwal->tgl_mulai . " " . $jam;
                        $nm_jns_seminar = $jadwal->nm_jns_seminar;
                        $start = Carbon::parse($startText);
                        $end = Carbon::parse($endText);
                        $cari_pembimbing = DB::SELECT("
                        SELECT
                            peran.id_peran_seminar,
                            CASE WHEN tsdm.nm_sdm IS NOT NULL THEN CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') END AS nm_dosen,
                            peran.peran,
                            peran.urutan,
                            peran.nm_pembimbing_luar_kampus,
                            peran.nm_penguji_luar_kampus,
                            CONCAT(peran.nm_pemb_lapangan,' (',peran.jabatan,')') AS nm_pemb_lapangan
                        FROM kpta.peran_seminar AS peran
                        LEFT JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm=peran.id_sdm
                        LEFT JOIN kpta.peran_dosen_pendaftar AS peran_dosen ON peran_dosen.id_peran_seminar = peran.id_peran_seminar
                        WHERE peran.soft_delete=0
                        AND peran.a_aktif =1
                        AND peran.a_ganti = 0
                        AND peran_dosen.soft_delete=0
                        AND peran.id_reg_pd = '" .  $jadwal->id_reg_pd . "'
                        AND peran_dosen.id_daftar_seminar = '" .  $jadwal->id_daftar_seminar . "'
                        ORDER BY peran.peran ASC, peran.urutan ASC
                    ");
                        $pembimbing_pengujis = [];
                        foreach ($cari_pembimbing as $pembimbing) {
                            if ($pembimbing->peran == 1) {
                                $pembimbing_pengujis[] = config('mp.data_master.peran_seminar.' . $pembimbing->peran) . ' ke-' . $pembimbing->urutan . ': ' .
                                    ($pembimbing->nm_dosen ?? $pembimbing->nm_pembimbing_luar_kampus ?? $pembimbing->nm_pemb_lapangan);
                            } else if ($pembimbing->peran == 2) {
                                $pembimbing_pengujis[] = config('mp.data_master.peran_seminar.' . $pembimbing->peran) . ' ke-' . $pembimbing->urutan . ': ' .
                                    ($pembimbing->nm_dosen ?? $pembimbing->nm_penguji_luar_kampus);
                            } else if ($pembimbing->peran == 6) {
                                $pembimbing_pengujis[] = config('mp.data_master.peran_seminar.' . $pembimbing->peran) . ' ke-' . $pembimbing->urutan . ': ' .
                                    ($pembimbing->nm_pemb_lapangan);
                            }
                        }
                        $dataseminar = [
                            "title" => $pd->nm_pd . "," . $nm_jns_seminar . ", " . $gedung->nm_gedung . ", " . $ruang->nm_ruang,
                            "jam" => $jam,
                            "start" => $start,
                            "end" => $end,
                            "pembimbing_penguji" => implode("<br>", $pembimbing_pengujis),
                        ];
                        array_push($jadwal_seminar, $dataseminar);
                    }
                }
            }
        }

        $sdm = new Sdm();
        $daftar_dosen = $sdm->list_dosen(session()->get('id_sms'));
        if ($peran['id_peran'] == 3005) {
            $pd = new PesertaDidik();
            $profil = $pd->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
            // $daftar_dosen = DB::SELECT(" SELECT
            //         tsdm.id_sdm,
            //         CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') AS nm_dosen,
            //         CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
            //         CASE WHEN bimbing.pembimbing_1 IS NULL THEN 0 ELSE bimbing.pembimbing_1 END AS total_pembimbing_1,
            //         CASE WHEN bimbing.pembimbing_2 IS NULL THEN 0 ELSE bimbing.pembimbing_2 END AS total_pembimbing_2,
            //         CASE WHEN bimbing.penguji IS NULL THEN 0 ELSE bimbing.penguji END AS total_penguji
            //     FROM pdrd.sdm AS tsdm
            //     JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm AND tr.soft_delete=0
            //         AND tr.id_jns_keluar IS NULL AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar<=NOW())
            //     JOIN pdrd.keaktifan_ptk AS taktif ON taktif.id_reg_ptk = tr.id_reg_ptk AND taktif.soft_delete=0
            //         AND taktif.id_thn_ajaran=(date_part('year',NOW())-1)
            //     JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            //     JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik=tprodi.id_jenj_didik
            //     LEFT JOIN (
            //         SELECT
            //             peran.id_sdm,
            //             SUM(CASE WHEN (peran.peran=1 AND peran.urutan=1) THEN 1 ELSE 0 END) AS pembimbing_1,
            //             SUM(CASE WHEN (peran.peran=1 AND peran.urutan=2) THEN 1 ELSE 0 END) AS pembimbing_2,
            //             SUM(CASE WHEN (peran.peran=2 AND peran.urutan=1) THEN 1 ELSE 0 END) AS penguji
            //             FROM kpta.peran_seminar AS peran
            //             JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = peran.id_jns_seminar
            //             LEFT JOIN (
            //                 SELECT b1.id_sdm, ang_akt.id_ang_akt_mhs, ang_akt.id_reg_pd FROM pdrd.bimbing_mhs AS b1
            //                 JOIN pdrd.anggota_akt_mhs AS ang_akt ON ang_akt.id_akt_mhs=b1.id_akt_mhs
            //                     AND ang_akt.soft_delete=0
            //                 WHERE b1.soft_delete=0
            //                 GROUP BY b1.id_sdm, ang_akt.id_ang_akt_mhs, ang_akt.id_reg_pd
            //             ) AS bimb ON bimb.id_sdm = peran.id_sdm AND bimb.id_reg_pd=peran.id_reg_pd
            //             WHERE peran.soft_delete=0 AND peran.id_reg_pd=bimb.id_reg_pd
            //             AND bimb.id_ang_akt_mhs IS NULL
            //         GROUP BY peran.id_sdm
            //     ) AS bimbing ON bimbing.id_sdm=tsdm.id_sdm
            //     WHERE tsdm.soft_delete=0
            //     AND tsdm.id_stat_aktif=1
            //     AND tsdm.id_jns_sdm=12
            //     AND tprodi.id_sms = '".session()->get('login.peran.id_organisasi')."'
            //     ORDER BY tjenj.nm_jenj_didik ASC, tprodi.nm_lemb ASC, tsdm.nm_sdm ASC
            // ");
            return view('dashboard_pd', compact('profil', 'daftar_dosen', 'jadwal_seminar'));
        } elseif ($peran['id_peran'] == 46) {
            $sdm = new Sdm();
            $profil = $sdm->detail_dosen(auth()->user()->id_sdm_pengguna);
            return view('dashboard_sdm', compact('daftar_dosen', 'profil', 'jadwal_seminar'));
        } elseif (in_array($peran['id_peran'], [6, 3000])) {
            $profil = DB::table('man_akses.role_pengguna as role')
                ->join('man_akses.peran as peran', 'peran.id_peran', '=', 'role.id_peran')
                ->join('man_akses.pengguna as pengguna', 'pengguna.id_pengguna', '=', 'role.id_pengguna')
                ->join('pdrd.sms as tprodi', 'tprodi.id_sms', '=', 'role.id_organisasi')
                ->join('ref.jenjang_pendidikan as tjenj', 'tjenj.id_jenj_didik', '=', 'tprodi.id_jenj_didik')
                ->where('role.id_role_pengguna', $peran['id_role_pengguna'])
                ->select(
                    'peran.nm_peran',
                    'pengguna.nm_pengguna',
                    'pengguna.tempat_lahir',
                    'pengguna.tgl_lahir',
                    'pengguna.no_tel',
                    DB::raw("CONCAT(tprodi.nm_lemb, ' (', tjenj.nm_jenj_didik, ')') AS nm_prodi")
                )
                ->first();
            return view('dashboard_admin_prodi', compact('daftar_dosen', 'profil', 'jadwal_seminar'));
        } else {
            return view('dashboard');
        }
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
    public function distribusi_dosen_mahasiswa(Request $request, $id, Sdm $sdm)
    {
        $prodi = GetProdiIndividu();
        $id_sdm = Crypt::decrypt($id);
        $detail_dosen = collect(DB::SELECT("
            SELECT
                tsdm.id_sdm,
                tsdm.nm_sdm,
                tsdm.nidn,
                tsdm.nip,
                tr.id_reg_ptk,
                ikat.nm_ikatan_kerja,
                tpeg.nm_stat_pegawai,
                taktif.nm_stat_aktif,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_prodi,
                tsdm.last_sync
            FROM pdrd.sdm AS tsdm
            JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm
                AND tr.soft_delete=0
                AND tr.id_jns_keluar IS NULL
                AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar <=NOW())
            JOIN ref.ikatan_kerja_sdm AS ikat ON ikat.id_ikatan_kerja = tr.id_ikatan_kerja
            JOIN pdrd.keaktifan_ptk AS taptk ON taptk.id_reg_ptk = tr.id_reg_ptk
                AND taptk.soft_delete=0
                AND taptk.a_sp_homebase=1
                AND taptk.id_thn_ajaran=" . get_tahun_keaktifan() . "
            JOIN ref.status_kepegawaian AS tpeg ON tpeg.id_stat_pegawai = tr.id_stat_pegawai
            JOIN ref.status_keaktifan_pegawai AS taktif ON taktif.id_stat_aktif = tsdm.id_stat_aktif
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE tsdm.soft_delete=0
            AND tsdm.id_jns_sdm=12
            AND tsdm.id_sdm = '$id_sdm'
        "))->first();

        $list_angkatan = collect(PesertaDidik::ListPesertaDidikProdi($prodi->id_sms))
            ->pluck('angkatan', 'angkatan')
            ->toArray();
        $angkatan = $request->angkatan ?? null;

        $list_jns_seminar = collect(JenisSeminar::jenis_seminar_prodi($prodi->id_sms))
            ->pluck('nm_jns_seminar', 'nm_jns_seminar')
            ->toArray();
        $kategori = $request->kategori ?? null;
        // dd($nm_jns_seminar);

        $list_peran_seminar = collect(PeranSeminar::DataPeranSeminar($id_sdm))
            ->mapWithKeys(function ($item) {
                $nama_peran = config('mp.data_master.peran_seminar.' . $item->peran, 'Peran Tidak Diketahui');
                $peran = $nama_peran . ' Ke-' . $item->urutan;
                return [$peran => $peran];
            })
            ->toArray();

        $peran_dosen = $request->peran ?? null;
        // dd($peran_dosen);
        if (!is_null($peran_dosen)) {
            preg_match('/(.+)\sKe-(\d+)/', $peran_dosen, $matches);

            if (isset($matches[1], $matches[2])) {
                $peran = trim($matches[1]);
                $urutan = (int) trim($matches[2]);

                $peran_map = array_flip(config('mp.data_master.peran_seminar', []));

                $inisial_peran = $peran_map[$peran] ?? null;
            } else {
                $peran = null;
                $urutan = null;
                $inisial_peran = null;
            }
        } else {
            $peran = null;
            $urutan = null;
            $inisial_peran = null;
        }

        $peran_seminar = PeranSeminar::DataMahasiswa($id_sdm, $angkatan, $kategori, $inisial_peran, $urutan);
        return view('dashboard_distribusi_peran_dosen', compact('peran_seminar', 'angkatan', 'kategori', 'detail_dosen', 'list_angkatan', 'list_jns_seminar', 'list_peran_seminar', 'peran_dosen'));
    }


    public function show($id, $id_jns_seminar, PesertaDidik $pesertaDidik)
    {
        $id_reg_pd = Crypt::decrypt($id);
        $id_jns_seminar = Crypt::decrypt($id_jns_seminar);
        $data_reg_pd = RegPd::find($id_reg_pd);
        $data_seminar_prodi = DB::table('kpta.seminar_prodi as seminar')
            ->join('ref.jenis_seminar as jns', 'jns.id_jns_seminar', '=', 'seminar.id_jns_seminar')
            ->join('ref.jenis_seminar as induk_jns', 'induk_jns.id_jns_seminar', '=', 'jns.id_induk_jns_seminar')
            ->select(
                'seminar.id_seminar_prodi',
                DB::raw('COALESCE(induk_jns.id_jns_seminar, jns.id_jns_seminar) AS id_jns_seminar'),
                DB::raw('COALESCE(induk_jns.nm_jns_seminar, jns.nm_jns_seminar) AS nm_jns_seminar')
            )
            ->whereNull('jns.expired_date')
            ->whereNull('induk_jns.expired_date')
            ->where('seminar.soft_delete', 0)
            ->where(function ($query) use ($id_jns_seminar) {
                $query->where('induk_jns.id_jns_seminar', $id_jns_seminar)
                    ->orWhere('jns.id_jns_seminar', $id_jns_seminar);
            });


        if (!is_null($data_reg_pd->id_sms)) {
            $data_seminar_prodi->where('seminar.id_sms', $data_reg_pd->id_sms);
        }

        $data_seminar_prodi = $data_seminar_prodi->first();
        // dd($data_seminar_prodi);
        $data_peran_seminar = PeranSeminar::where('id_reg_pd', $id_reg_pd)->where('id_jns_seminar', $id_jns_seminar)->where('a_aktif', 0)->where('soft_delete', 0)->where('a_ganti', 0)->get();
        $data_daftar_seminar = DB::table('pendaftaran_seminar as daftar_seminar')
            ->where('daftar_seminar.id_reg_pd', $id_reg_pd)
            ->where('daftar_seminar.id_seminar_prodi', $data_seminar_prodi->id_seminar_prodi)
            ->where('daftar_seminar.soft_delete', 0)
            ->join('manajemen.ruang as truang', 'truang.id_ruang', '=', 'daftar_seminar.id_ruang')
            ->join('manajemen.gedung as tgedung', 'tgedung.id_gedung', '=', 'truang.id_gedung')
            ->select(
                'daftar_seminar.judul_akt_mhs',
                'daftar_seminar.hari',
                'daftar_seminar.tgl_mulai',
                'daftar_seminar.waktu',
                'tgedung.nm_gedung',
                'truang.nm_ruang'
            )
            ->first();
        $data_tugas_akhir = DB::table('pendaftaran_seminar as daftar_seminar')
            ->join('kpta.seminar_prodi as tseminar', 'tseminar.id_seminar_prodi', '=', 'daftar_seminar.id_seminar_prodi')
            ->join('ref.jenis_seminar as jns_seminar', 'jns_seminar.id_jns_seminar', '=', 'tseminar.id_jns_seminar')
            ->where('jns_seminar.a_tugas_akhir', 1)
            ->where('jns_seminar.a_seminar', 1)
            ->where('daftar_seminar.id_reg_pd', $id_reg_pd)
            ->orderBy('daftar_seminar.tgl_create', 'desc')
            ->get();

        $jangka_wkt_ta = DB::table('kpta.sisa_waktu_penyusunan as sisa_wkt')
            ->where('sisa_wkt.id_reg_pd', $id_reg_pd)
            ->where('sisa_wkt.soft_delete', 0)
            ->where('sisa_wkt.id_jns_seminar', 5)
            ->selectRaw("
                sisa_wkt.tgl_batas_penyusunan,
                sisa_wkt.tgl_mulai,
                sisa_wkt.tgl_selesai, 
                DATE_PART('day', sisa_wkt.tgl_batas_penyusunan - '2025-08-27'::timestamp) as sisa_hari, 
                DATE_PART('day', sisa_wkt.tgl_batas_penyusunan - sisa_wkt.tgl_mulai) as total_hari
            ")
            ->first();
        $profil = $pesertaDidik->id_detail_mahasiswa($data_reg_pd->id_pd);
        // dd($data_daftar_seminar);
        return view('dashboard_distribusi_peran_dosen_detail', compact('data_reg_pd', 'data_daftar_seminar', 'profil', 'data_seminar_prodi', 'data_tugas_akhir', 'jangka_wkt_ta'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
