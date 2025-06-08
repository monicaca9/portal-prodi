<?php

namespace App\Http\Controllers\Mahasiswa;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Pdrd\RegPd;
use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Ref\JenisDokumen;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\PeranSeminar;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RwySeminarPd;
use App\Models\Kpta\SyaratSeminar;
use Illuminate\Support\Facades\DB;
use App\Models\Dok\DokSyaratDaftar;
use App\Http\Controllers\Controller;
use App\Models\Kpta\ListSyaratDaftar;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\DokumenTrait;
use App\Models\Kpta\ListSyaratSeminar;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Kpta\PeranDosenPendaftar;
use App\Models\Kpta\NomorBaDaftarSeminar;
use App\Models\Validasi\VerAjuanDftrSeminar;

class PendaftaranSeminarController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PesertaDidik $pesertaDidik)
    {
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $rwy_seminar = RwySeminarPd::where('id_reg_pd', $profil->id_reg_pd)->where('soft_delete', 0)->orderBy('id_jns_seminar', 'ASC')->get();
        $daftar_seminar = [];
        foreach ($rwy_seminar as $each_dftr) {
            $daftar_seminar[] = $each_dftr->id_jns_seminar;
        }

        $list_seminar = SeminarProdi::where('id_sms', $prodi->id_sms)
            ->whereNotIn('id_jns_seminar', $daftar_seminar)
            ->where('soft_delete', 0)
            ->orderBy('urutan', 'ASC')
            ->first();

        $pendaftaran = DB::SELECT("
            SELECT
                daftar.id_daftar_seminar,
                daftar.id_ruang,
                truang.nm_ruang,
                tgedung.nm_gedung,
                daftar.id_seminar_prodi,
                tjns.nm_jns_seminar,
                tseminar.urutan,
                tseminar.jmlh_pembimbing,
                tseminar.jmlh_penguji,
                tseminar.a_aktif,
                daftar.judul_akt_mhs,
                daftar.tgl_mulai,
                daftar.hari,
                daftar.waktu,
                daftar.a_diproses,
                daftar.wkt_diproses,
                daftar.a_selesai,
                daftar.wkt_selesai,
                daftar.status_validasi
            FROM kpta.pendaftaran_seminar AS daftar
            JOIN kpta.seminar_prodi AS tseminar ON tseminar.id_seminar_prodi = daftar.id_seminar_prodi
                AND tseminar.soft_delete=0
            JOIN ref.jenis_seminar AS tjns ON tjns.id_jns_seminar = tseminar.id_jns_seminar
                AND tjns.expired_date IS NULL
            LEFT JOIN manajemen.ruang AS truang ON truang.id_ruang = daftar.id_ruang
            LEFT JOIN manajemen.gedung AS tgedung ON tgedung.id_gedung = truang.id_gedung
            WHERE daftar.soft_delete=0
            AND daftar.id_reg_pd='" . $profil->id_reg_pd . "'
            ORDER BY tseminar.urutan DESC
        ");
        $daftar_awal = collect($pendaftaran)->first();
        return view('pendaftaran_seminar.index', compact('prodi', 'daftar_awal', 'profil', 'rwy_seminar', 'list_seminar', 'pendaftaran'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, PesertaDidik $pesertaDidik)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $seminar = SeminarProdi::find($id_seminar_prodi);
        $jns = JenisSeminar::find($seminar->id_jns_seminar);
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $cari_rwy = RwySeminarPd::where('id_jns_seminar', $seminar->id_jns_seminar)->where('id_reg_pd', $profil->id_reg_pd)->where('soft_delete', 0)->first();
        $induk_jns_seminar = DB::table('kpta.seminar_prodi as seminar')
            ->join('ref.jenis_seminar as jns', 'jns.id_jns_seminar', '=', 'seminar.id_jns_seminar')
            ->join('ref.jenis_seminar as induk_jns', 'induk_jns.id_jns_seminar', '=', 'jns.id_induk_jns_seminar')
            ->where('seminar.soft_delete', 0)
            ->where('seminar.id_seminar_prodi', $id_seminar_prodi)
            ->whereNull('jns.expired_date')
            ->whereNull('induk_jns.expired_date')
            ->select('induk_jns.id_jns_seminar')
            ->first();

        $peran_dosen = DB::SELECT("
            SELECT
                peran.id_peran_seminar
            FROM kpta.peran_seminar as peran
            JOIN ref.jenis_seminar as jns_seminar ON jns_seminar.id_jns_seminar = peran.id_jns_seminar
            WHERE peran.soft_delete = 0
            AND peran.a_aktif = 1
            AND peran.a_ganti = 0
            AND peran.id_reg_pd = '" . $profil->id_reg_pd . "'
            AND peran.id_jns_seminar = $induk_jns_seminar->id_jns_seminar
            ");
        // dd(empty($peran_dosen), $peran_dosen);

        if (is_null($cari_rwy)) {
            $cari_pendaftaran = PendaftaranSeminar::where('id_seminar_prodi', $id_seminar_prodi)->where('id_reg_pd', $profil->id_reg_pd)->where('soft_delete', 0)->first();
            if (is_null($cari_pendaftaran)) {
                if (!empty($peran_dosen)) {
                    $daftar = new PendaftaranSeminar();
                    $daftar->fill($daftar->prepare([
                        'id_reg_pd'         => $profil->id_reg_pd,
                        'id_seminar_prodi'  => $id_seminar_prodi
                    ]))->save();
                    $syarat = ListSyaratSeminar::where('id_seminar_prodi', $id_seminar_prodi)->where('soft_delete', 0)->orderBy('urutan', 'ASC')->get();
                    foreach ($syarat as $each_syarat) {
                        $simpan_syarat = new ListSyaratDaftar();
                        $simpan_syarat->fill($simpan_syarat->prepare([
                            'id_daftar_seminar' => $daftar->id_daftar_seminar,
                            'id_list_syarat'    => $each_syarat->id_list_syarat,
                            'stat_ajuan'        => 0,
                            'jns_ajuan'         => 'B'
                        ]))->save();
                    }
                    $id_daftar = $daftar->id_daftar_seminar;
                    alert()->info('Silahkan lengkapi persyaratan berikut')->persistent('OK');
                } else {
                    alert()->info(
                        "Anda Belum memiliki Pembimbing dan Penguji Seminar.
                        Silahkan menghubungi Ketua Prodi untuk entri Dosen Pembimbing dan Penguji."
                    )->persistent('OK');
                    return redirect()->back();
                }
            } else {
                $id_daftar = $cari_pendaftaran->id_daftar_seminar;
                if ($cari_pendaftaran->status_validasi == 0) {
                    alert()->info('Silahkan lengkapi persyaratan berikut')->persistent('OK');
                }
            }

            foreach ($peran_dosen as $peran_dosen_seminar) {
                $data_peran_dosen_pendaftar = PeranDosenPendaftar::where('id_daftar_seminar', $daftar->id_daftar_seminar)->where('id_peran_seminar', $peran_dosen_seminar->id_peran_seminar)->where('soft_delete', 0)->first();
                if (is_null($data_peran_dosen_pendaftar)) {
                    $simpan_peran_dosen_pendaftar = new PeranDosenPendaftar();
                    $simpan_peran_dosen_pendaftar->fill($simpan_peran_dosen_pendaftar->prepare([
                        'id_daftar_seminar' => $daftar->id_daftar_seminar,
                        'id_peran_seminar' => $peran_dosen_seminar->id_peran_seminar,
                    ]))->save();
                }
            }

            return redirect()->route('pendaftaran_seminar.detail', Crypt::encrypt($id_daftar));
        } else {
            alert()->info('Anda sudah melaksanakan seminar ' . $jns->nm_jns_seminar)->persistent('OK');
            return redirect()->back();
        }
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

    public function beritaacara($id, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id); // id seminar enkripsi
        $data = PendaftaranSeminar::find($id_daftar_seminar); // id pendaftaran seminar
        $no_ba_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();
        $nm_jns_seminar = $data->SeminarProdi->jenisSeminar->nm_jns_seminar;
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $ruang = Ruang::where('id_ruang', $data->id_ruang)->first();
        $gedung = Gedung::where('id_gedung', $ruang->id_gedung)->first();

        $komponen_nilai = DB::table('kpta.skor_per_komponen as skor_komponen')
            ->join('kpta.list_komponen_nilai_seminar as list_komponen', 'list_komponen.id_list_komponen_nilai', '=', 'skor_komponen.id_list_komponen_nilai')
            ->join('kpta.komponen_nilai_seminar as komponen', 'komponen.id_komponen_nilai', '=', 'list_komponen.id_komponen_nilai')
            ->join('kpta.list_kategori_nilai_seminar as list_kategori', 'list_kategori.id_list_kategori_nilai', '=', 'list_komponen.id_list_kategori_nilai')
            ->join('kpta.kategori_nilai_seminar as kategori', 'kategori.id_kategori_nilai', '=', 'list_kategori.id_kategori_nilai')
            ->join('kpta.peran_dosen_pendaftar as peran_dosen', 'peran_dosen.id_peran_dosen_pendaftar', '=', 'skor_komponen.id_peran_dosen_pendaftar')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->leftjoin('pdrd.sdm as tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
            ->where([
                ['peran_dosen.id_daftar_seminar', $id_daftar_seminar],
                ['skor_komponen.soft_delete', 0],
                ['peran_dosen.soft_delete', 0],
                ['list_komponen.soft_delete', 0],
                ['list_kategori.soft_delete', 0],
                ['peran.soft_delete', 0],
            ])
            ->select(
                'skor_komponen.id_peran_dosen_pendaftar',
                'kategori.nm_kategori_nilai',
                'list_kategori.id_list_kategori_nilai',
                'komponen.nm_komponen_nilai',
                'skor_komponen.skor',
                'peran.peran',
                'peran.urutan',
                'peran.nm_pembimbing_luar_kampus',
                'peran.nm_penguji_luar_kampus',
                'peran.nm_pemb_lapangan',
                'tsdm.nm_sdm',
                'tsdm.nip'
            )
            ->get()
            ->groupBy('id_peran_dosen_pendaftar')
            ->map(function ($items) {
                return $items->groupBy('id_list_kategori_nilai');
            });
        // dd($komponen_nilai);

        $data_skor_kategori = DB::table('kpta.avg_skor_kategori as skor_kategori')
            ->join('kpta.peran_dosen_pendaftar as peran_dosen', 'peran_dosen.id_peran_dosen_pendaftar', '=', 'skor_kategori.id_peran_dosen_pendaftar')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->leftjoin('pdrd.sdm as tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
            ->where('peran_dosen.id_daftar_seminar', $id_daftar_seminar)
            ->where('peran_dosen.soft_delete', 0)
            ->where('peran.soft_delete', 0)
            ->orderby('peran.peran', 'ASC')
            ->orderby('peran.urutan', 'ASC')
            ->get();
        // dd($total_skor_kategori);

        $data_distribusi_nilai =  DB::table('kpta.distribusi_nilai as distribusi')
            ->where('distribusi.id_seminar_prodi', $data->id_seminar_prodi)
            ->where('soft_delete', 0)
            ->get();
        $data_nilai_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();

        // dd($komponen_nilai);

        $html = view('pendaftaran_seminar.beritaacara.berita', compact('komponen_nilai', 'data', 'nm_jns_seminar', 'profil', 'ruang', 'gedung', 'no_ba_seminar', 'data_distribusi_nilai', 'data_skor_kategori', 'data_nilai_seminar'));


        //Jika seminar kerja praktik
        // if ($seminar->id_jns_seminar == '1') {

        //     $html = view('pendaftaran_seminar.beritaacara.berita', compact('komponen_nilai', 'data', 'seminar', 'profil','ruang', 'gedung'));
        //     //Jika seminar Proposal
        // } elseif ($seminar->id_jns_seminar == '2') {

        //     $html = view('pendaftaran_seminar.beritaacara.beritaacara', $send);
        // } elseif ($seminar->id_jns_seminar == '3') {
        //     $html = view('pendaftaran_seminar.beritaacara.beritaacara_hasil', $send);
        // } elseif ($seminar->id_jns_seminar == '4') {
        //     $html = view('pendaftaran_seminar.beritaacara.beritaacara_komprehensif', $send);
        // } else {
        //     $html = view('pendaftaran_seminar.beritaacara.beritaacara', $send);
        // }


        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);



        $dompdf->loadhtml($html);


        $dompdf->setPaper('A4', 'potrait');

        $dompdf->render();

        $dompdf->stream("beritaacara.pdf", array("Attachment" => false));
    }


    //     public function beritaacara($id, PesertaDidik $pesertaDidik){

    //  // dd(public_path());

    // $options = new Options();
    // $options->set('isRemoteEnabled',true);
    // $dompdf = new Dompdf( $options );


    //       $html = view('pendaftaran_seminar.beritaacara.test');
    //       $dompdf->loadhtml($html);

    //       $dompdf->setPaper('A4', 'potrait');

    //       $dompdf->render();

    //      $dompdf->stream("filename.pdf", array("Attachment" => false));


    //     }

    public function show($id, PesertaDidik $pesertaDidik)
    {

        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
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
        $jadwal_seminar = [];
        foreach ($datajadwal as $jadwal) {
            $ruang = Ruang::where('id_ruang', $jadwal->id_ruang)->first();
            if ($ruang != null) {
                $reg_pd = RegPd::where('id_reg_pd', $jadwal->id_reg_pd)->first();
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
        // die(
        //     print_r($gedung_ruang)
        // );
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);

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
                ver.ket_periksa,
                ver.status_periksa,
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
                SELECT v1.id_ver_ajuan, v1.id_list_syarat_daftar, v1.status_periksa, v1.ket_periksa, v1.nm_verifikator, v1.wkt_selesai_ver, v1.verifikasi_ke, v1.level_ver
                FROM validasi.ver_ajuan_dft_seminar AS v1
                JOIN (
                    SELECT DISTINCT ON (id_list_syarat_daftar)
                        id_list_syarat_daftar, level_ver
                    FROM validasi.ver_ajuan_dft_seminar
                    WHERE soft_delete = 0
                    ORDER BY id_list_syarat_daftar, tgl_create DESC
                ) AS latest_level 
                    ON latest_level.id_list_syarat_daftar = v1.id_list_syarat_daftar 
                    AND latest_level.level_ver = v1.level_ver
                JOIN (
                    SELECT id_list_syarat_daftar, MAX(verifikasi_ke) AS max_verifikasi, level_ver
                    FROM validasi.ver_ajuan_dft_seminar
                    WHERE soft_delete=0
                    GROUP BY id_list_syarat_daftar , level_ver
                ) AS v2 
                    ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                    AND v2.level_ver=v1.level_ver
                    AND v2.max_verifikasi=v1.verifikasi_ke
                WHERE v1.soft_delete=0
            ) AS ver 
                ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            WHERE list_syarat.soft_delete=0
            AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
            ORDER BY list_syarat.urutan ASC
        ");
        // dd($syarat);
        $nilai_akhir_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();

        return view('pendaftaran_seminar.detail', compact('data', 'profil', 'syarat', 'seminar', 'gedung_ruang', 'jadwal_seminar', 'nilai_akhir_seminar'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $awal = $request->awal;
        if ($awal == 1) {
            $data->fill($data->prepare([
                '_method'       => 'PUT',
                'judul_akt_mhs' => $request->judul_akt_mhs
            ]))->save();

            if ($data->SeminarProdi->jenisSeminar->a_tugas_akhir == 0) {
                $dosen_pembimbing_non_ta = DB::table('kpta.peran_dosen_pendaftar')
                    ->join('kpta.peran_seminar AS peran', 'peran.id_peran_seminar', '=', 'kpta.peran_dosen_pendaftar.id_peran_seminar')
                    ->where('peran.soft_delete', 0)
                    ->where('peran.a_aktif', 1)
                    ->where('peran.a_ganti', 0)
                    ->where('peran.id_reg_pd', $profil->id_reg_pd)
                    ->where('peran.peran', 6)
                    ->where('kpta.peran_dosen_pendaftar.id_daftar_seminar', $data->id_daftar_seminar)
                    ->where('peran.id_jns_seminar', $data->SeminarProdi->jenisSeminar->id_jns_seminar)
                    ->where('kpta.peran_dosen_pendaftar.soft_delete', 0)
                    ->first();
                if (is_null($dosen_pembimbing_non_ta)) {
                    $bimbing_non_ta_baru = new PeranSeminar();
                    $bimbing_non_ta_baru->fill($bimbing_non_ta_baru->prepare([
                        'id_reg_pd'         => $profil->id_reg_pd,
                        'id_jns_seminar'    => $data->SeminarProdi->jenisSeminar->id_jns_seminar,
                        'nm_pemb_lapangan'  => $request->nm_pemb_lapangan,
                        'jabatan'           => $request->jabatan,
                        'lokasi'            => $request->lokasi,
                        'peran'             => 6,
                        'urutan'            => 1
                    ]))->save();
                    $id_peran_seminar_non_ta = $bimbing_non_ta_baru->id_peran_seminar;
                } else {
                    $bimbing_update = PeranSeminar::find($dosen_pembimbing_non_ta->id_peran_seminar);
                    $bimbing_update->fill($bimbing_update->prepare([
                        '_method'           => 'PUT',
                        'id_reg_pd'         => $profil->id_reg_pd,
                        'id_jns_seminar'    => $data->SeminarProdi->jenisSeminar->id_jns_seminar,
                        'nm_pemb_lapangan'  => $request->nm_pemb_lapangan,
                        'jabatan'           => $request->jabatan,
                        'lokasi'            => $request->lokasi,
                        'peran'             => 6,
                        'urutan'            => 1
                    ]))->save();
                    $id_peran_seminar_non_ta = $dosen_pembimbing_non_ta->id_peran_seminar;
                }
                $cari_peran_non_ta = PeranDosenPendaftar::where('id_daftar_seminar', $data->id_daftar_seminar)
                    ->where('id_peran_seminar', $id_peran_seminar_non_ta)->where('soft_delete', 0)->first();
                if (is_null($cari_peran_non_ta)) {
                    $simpan_peran = new PeranDosenPendaftar();
                    $simpan_peran->fill($simpan_peran->prepare([
                        'id_daftar_seminar' => $data->id_daftar_seminar,
                        'id_peran_seminar'  => $id_peran_seminar_non_ta
                    ]))->save();
                }
            }
            alert()->success('Berhasil mengubah Judul, dan data pembimbing')->persistent('OK');
        } elseif ($awal == 2) {
            $input = $request->all();
            $data->fill($data->prepare($input))->save();
            alert()->success('Berhasil waktu seminar')->persistent('OK');
        } else {
        }
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
        $data->fill($data->prepare([
            '_method'           => 'PUT',
            'waktu_diajukan'    => currDateTime(),
            'status_validasi'   => 1,
        ]))->save();
        $syarat = ListSyaratDaftar::where('id_daftar_seminar', $data->id_daftar_seminar)->where('stat_ajuan', 0)->where('soft_delete', 0)->get();
        foreach ($syarat as $each_syarat) {
            $update_syarat = ListSyaratDaftar::find($each_syarat->id_list_syarat_daftar);
            $update_syarat->fill($update_syarat->prepare([
                '_method'   => 'PUT',
                'wkt_ajuan' => currDateTime(),
                'stat_ajuan' => 1
            ]))->save();

            $cari_ver = VerAjuanDftrSeminar::where('id_list_syarat_daftar', $each_syarat->id_list_syarat_daftar)->where('soft_delete', 0)->orderBy('verifikasi_ke', 'DESC')->first();
            if (is_null($cari_ver)) {
                $simpan_ver = new VerAjuanDftrSeminar();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_list_syarat_daftar' => $each_syarat->id_list_syarat_daftar,
                ]))->save();
            }
        }


        alert()->success('Ajuan berhasil diajukan untuk diverifikasi')->persistent('OK');
        return redirect()->route('pendaftaran_seminar');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
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
                    GROUP BY id_list_syarat_daftar
                ) AS v2 ON v2.id_list_syarat_daftar=v1.id_list_syarat_daftar
                    AND v2.max_verifikasi=v1.verifikasi_ke
                WHERE v1.soft_delete=0
            ) AS ver ON ver.id_list_syarat_daftar = list_daftar.id_list_syarat_daftar
            WHERE list_syarat.soft_delete=0
            AND list_syarat.id_seminar_prodi='" . $data->id_seminar_prodi . "'
            AND ver.status_periksa IN ('T','L','C')
            ORDER BY list_syarat.urutan ASC
        ");
        // dd($syarat)
        foreach ($syarat as $each_syarat) {
            $simpan_syarat = ListSyaratDaftar::find($each_syarat->id_list_syarat_daftar);
            $simpan_syarat->fill($simpan_syarat->prepare([
                '_method'           => 'PUT',
                'wkt_update'        => currDateTime(),
                'stat_ajuan'        => 0,
                'jns_ajuan'         => 'U'
            ]))->save();

            $cari_ver = VerAjuanDftrSeminar::where('id_list_syarat_daftar', $each_syarat->id_list_syarat_daftar)->where('soft_delete', 0)->orderBy('verifikasi_ke', 'DESC')->first();
            if (is_null($cari_ver)) {
                $simpan_ver = new VerAjuanDftrSeminar();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_list_syarat_daftar' => $each_syarat->id_list_syarat_daftar,
                ]))->save();
            } else {
                $simpan_ver = new VerAjuanDftrSeminar();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_ver_ajuan_sebelum'  => $cari_ver->id_ver_ajuan,
                    'stat_ajuan_sebelum'    => $cari_ver->status_periksa,
                    'id_list_syarat_daftar' => $each_syarat->id_list_syarat_daftar,
                    'verifikasi_ke'         => (($cari_ver->verifikasi_ke) + 1)
                ]))->save();
            }
        }
        $data->fill($data->prepare([
            '_method'           => 'PUT',
            'status_validasi'   => 0
        ]))->save();
        return redirect()->back();
    }

    public function daftar_dokumen($id, $id_syarat, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $seminar = SeminarProdi::find($data->id_seminar_prodi);
        $syarat = ListSyaratSeminar::find($id_list_syarat);
        $cari_dok_syarat = ListSyaratDaftar::where('id_daftar_seminar', $data->id_daftar_seminar)
            ->where('id_list_syarat', $syarat->id_list_syarat)->where('soft_delete', 0)->first();
        if (is_null($cari_dok_syarat)) {
            $simpan_syarat = new ListSyaratDaftar();
            $simpan_syarat->fill($simpan_syarat->prepare([
                'id_daftar_seminar' => $id_daftar_seminar,
                'id_list_syarat'    => $id_list_syarat,
                'stat_ajuan'        => 0,
                'jns_ajuan'         => 'B'
            ]))->save();
            $id_list_syarat_daftar = $simpan_syarat->id_list_syarat_daftar;
        } else {
            $id_list_syarat_daftar = $cari_dok_syarat->id_list_syarat_daftar;
        }
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok', 'ASC')->pluck('nm_jns_dok', 'id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_dok_syarat_daftar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_syarat_daftar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_list_syarat_daftar='" . $id_list_syarat_daftar . "'
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

        //dd($dokumen_syarat_seminar);

        $validasi = VerAjuanDftrSeminar::where('id_list_syarat_daftar', $id_list_syarat_daftar)->where('status_periksa', '!=', 'N')->orderBy('wkt_selesai_ver', 'DESC')->get();
        return view('pendaftaran_seminar.dokumen', compact('data', 'seminar', 'syarat', 'profil', 'jenis_dok', 'dokumen', 'cari_dok_syarat', 'dokumen_syarat_seminar', 'validasi'));
    }

    public function store_dokumen(Request $request, $id, $id_syarat, PesertaDidik $pesertaDidik)
    {
        $this->validate($request, [
            'nm_dok'        => 'string|max:60',
            'ket_dok'       => 'string|max:200|nullable'
        ]);
        $input = $request->all();
        if (is_null(@$input['file_dok'])) {
            alert()->error('File harus terisi')->persistent('OK');
        }
        $id_daftar_seminar = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = PendaftaranSeminar::find($id_daftar_seminar);
        $syarat = ListSyaratSeminar::find($id_list_syarat);
        $cari_dok_syarat = ListSyaratDaftar::where('id_daftar_seminar', $data->id_daftar_seminar)
            ->where('id_list_syarat', $syarat->id_list_syarat)->where('soft_delete', 0)->first();
        if (is_null($cari_dok_syarat)) {
            $simpan_syarat = new ListSyaratDaftar();
            $simpan_syarat->fill($simpan_syarat->prepare([
                'id_daftar_seminar' => $id_daftar_seminar,
                'id_list_syarat'    => $id_list_syarat,
                'stat_ajuan'        => 0,
                'jns_ajuan'         => 'B'
            ]))->save();
            $id_list_syarat_daftar = $simpan_syarat->id_list_syarat_daftar;
        } else {
            $id_list_syarat_daftar = $cari_dok_syarat->id_list_syarat_daftar;
        }
        $file = $request->file('file_dok');
        if (!is_null($file)) {
            $ext = $file->getClientOriginalExtension();
            if ($ext == 'pdf') {
                $data_dokumen = $this->simpan_dokumen([
                    'url' => $request->url,
                    'file' => $file,
                    'nm_dok' => $request->nm_dok,
                    'id_jns_dok' => $request->id_jns_dok,
                    'ket_dok' => $request->ket_dok
                ]);

                $simpan_dok = new DokSyaratDaftar();
                $simpan_dok->fill($simpan_dok->prepare([
                    'id_dok'                => $data_dokumen,
                    'id_list_syarat_daftar' => $id_list_syarat_daftar,
                ]))->save();

                alert()->success('Data Dokumen Pendukung lainnya berhasil disimpan')->persistent('OK');
            } else {
                alert()->error('Dokumen Pendukung harus dalam format .pdf')->persistent('OK');
            }
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function hapus_dokumen($id, $id_syarat, $id_dok)
    {
        $id_dok = Crypt::decrypt($id_dok);
        $data = DokSyaratDaftar::find($id_dok);
        $data->drop();

        alert()->success('Berhasil menghapus dokumen')->persistent('OK');
        return redirect()->back();
    }
}
