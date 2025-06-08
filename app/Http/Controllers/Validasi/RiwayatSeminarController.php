<?php

namespace App\Http\Controllers\Validasi;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Pdrd\RegPd;
use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RwySeminarPd;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Validasi\AjuanPdmSeminar;
use App\Models\Kpta\NomorBaDaftarSeminar;
use App\Models\Validasi\VerAjuanPdmSeminar;

class RiwayatSeminarController extends Controller
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
                $status_periksa = ['T', 'C'];
                $status_validasi = 3;
            } elseif ($status == 'Disetujui') {
                $status_periksa = ['Y'];
                $status_validasi = 2;
            } else {
                $status_periksa = ['L'];
                $status_validasi = 4;
            }
        } else {
            $status = 'Diajukan';
            $status_periksa = ['N'];
            $status_validasi = 1;
        }
        $query = "
            SELECT
                valid.id_ver_ajuan,
                ajuan.id_ajuan_pdm_seminar,
                ajuan.id_jns_seminar_lama,
                ajuan.stat_ajuan,
                jns.nm_jns_seminar,
                tr.nim,
                pd.nm_pd,
                ajuan.wkt_ajuan,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan
            FROM validasi.ajuan_pdm_seminar AS ajuan
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = ajuan.id_jns_seminar_lama
            JOIN validasi.ver_ajuan_pdm_seminar AS valid ON valid.id_ajuan_pdm_seminar = ajuan.id_ajuan_pdm_seminar
            JOIN pdrd.peserta_didik AS pd ON pd.id_pd = ajuan.id_pd AND pd.soft_delete=0
            JOIN pdrd.reg_pd AS tr ON tr.id_pd = pd.id_pd AND tr.soft_delete=0
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE ajuan.soft_delete=0
            AND tprodi.id_sms = '" . session()->get('login.peran.id_organisasi') . "'
            AND ajuan.stat_ajuan=" . $status_validasi . "
            AND valid.status_periksa IN ('" . implode("','", $status_periksa) . "')
            ORDER BY valid.last_update
        ";
        $data = DB::SELECT($query);
        return view('validasi.rwy_seminar.index', compact('data', 'status', 'status_periksa', 'status_validasi'));
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
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_ver_ajuan = Crypt::decrypt($id);
        $ajuan = VerAjuanPdmSeminar::find($id_ver_ajuan);
        $data = $ajuan->ajuanSeminar;
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_pdm_seminar, list.id_dok_ajuan_seminar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_seminar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_pdm_seminar='" . $data->id_ajuan_pdm_seminar . "'
        ");
        return view('validasi.rwy_seminar.detail', compact('data', 'profil', 'dokumen', 'ajuan'));
    }

    public function beritaacara($id, PesertaDidik $pesertaDidik)
    {
        $id_ajuan_pdm_seminar = Crypt::decrypt($id);
        $data_ajuan = AjuanPdmSeminar::findorfail($id_ajuan_pdm_seminar);
        $data_reg_pd = RegPd::where('id_pd', $data_ajuan->id_pd)->where('soft_delete', 0)->first();
        $data_seminar_prodi = SeminarProdi::where('id_sms', $data_reg_pd->id_sms)->where('id_jns_seminar', $data_ajuan->id_jns_seminar_lama)->where('soft_delete', 0)->first();
        $data = PendaftaranSeminar::where('id_reg_pd', $data_reg_pd->id_reg_pd)->where('id_seminar_prodi', $data_seminar_prodi->id_seminar_prodi)->where('soft_delete', 0)->first();


        $no_ba_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $data->id_daftar_seminar)->where('soft_delete', 0)->first();
        $nm_jns_seminar = $data->SeminarProdi->jenisSeminar->nm_jns_seminar;
        $profil = $pesertaDidik->id_detail_mahasiswa($data->RegPd->id_pd);
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
                ['peran_dosen.id_daftar_seminar', $data->id_daftar_seminar],
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
            ->where('peran_dosen.id_daftar_seminar', $data->id_daftar_seminar)
            ->where('peran_dosen.soft_delete', 0)
            ->where('peran.soft_delete', 0)
            ->orderby('peran.peran', 'ASC')
            ->orderby('peran.urutan', 'ASC')
            ->get();
        // dd($data_skor_kategori);

        $data_distribusi_nilai =  DB::table('kpta.distribusi_nilai as distribusi')
            ->where('distribusi.id_seminar_prodi', $data->id_seminar_prodi)
            ->where('soft_delete', 0)
            ->get();
        $data_nilai_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $data->id_daftar_seminar)->where('soft_delete', 0)->first();

        // dd($data_skor_kategori,$data_nilai_seminar);

        $html = view('pendaftaran_seminar.beritaacara.berita', compact('komponen_nilai', 'data', 'nm_jns_seminar', 'profil', 'ruang', 'gedung', 'no_ba_seminar', 'data_distribusi_nilai', 'data_skor_kategori', 'data_nilai_seminar'));

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);


        $dompdf->loadhtml($html);


        $dompdf->setPaper('A4', 'potrait');

        $dompdf->render();

        $dompdf->stream("beritaacara.pdf", array("Attachment" => false));
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
    public function update(Request $request, $id, PesertaDidik $pesertaDidik)
    {
        $id_ver_ajuan = Crypt::decrypt($id);
        $ajuan = VerAjuanPdmSeminar::find($id_ver_ajuan);
        $data = AjuanPdmSeminar::find($ajuan->id_ajuan_pdm_seminar);
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);
        $input = $request->all();
        if ($input['status_validasi'] != 'N') {
            if ($input['status_validasi'] == 'Y') {
                if ($data->jns_ajuan == 'B') {
                    $simpan = new RwySeminarPd();
                    $simpan->fill($simpan->prepare([
                        'judul_akt_mhs'         => $data->judul_seminar_baru,
                        'lokasi_kegiatan'       => $data->lokasi_seminar_baru,
                        'tgl_seminar'           => $data->tgl_seminar_baru,
                        'sk_seminar'            => $data->sk_seminar_baru,
                        'tgl_sk_seminar'        => $data->tgl_sk_seminar_baru,
                        'nilai_seminar'         => $data->nilai_seminar_baru,
                        'huruf_nilai_seminar'   => $data->huruf_nilai_seminar_baru,
                        'id_jns_seminar'        => $data->id_jns_seminar_lama,
                        'id_reg_pd'             => $profil->id_reg_pd
                    ]))->save();
                } else {
                    $input_simpan = [
                        '_method'               => 'PUT',
                        'judul_akt_mhs'         => $data->judul_seminar_baru,
                        'lokasi_kegiatan'       => $data->lokasi_seminar_baru,
                        'tgl_seminar'           => $data->tgl_seminar_baru,
                        'sk_seminar'            => $data->sk_seminar_baru,
                        'tgl_sk_seminar'        => $data->tgl_sk_seminar_baru,
                        'nilai_seminar'         => $data->nilai_seminar_baru,
                        'huruf_nilai_seminar'   => $data->huruf_nilai_seminar_baru
                    ];
                    $simpan = RwySeminarPd::find($data->id_rwy_seminar);
                    $simpan->fill($simpan->prepare(array_filter($input_simpan)))->save();
                }

                $data->fill($data->prepare([
                    '_method' => 'PUT',
                    'id_rwy_seminar' => $simpan->id_rwy_seminar
                ]))->save();
                $stat_ajuan = 2;
            } elseif (in_array($input['status_validasi'], ['T', 'C'])) {
                $stat_ajuan = 3;
            } elseif ($input['status_validasi'] == 'L') {
                $stat_ajuan = 4;
            }
            $input['status_periksa'] = $input['status_validasi'];
            $input['wkt_selesai_ver'] = currDateTime();
            $input['stat_ajuan_sesudah'] = $stat_ajuan;
            unset($input['status_validasi']);
            $data->fill($data->prepare([
                '_method'       => 'PUT',
                'stat_ajuan'    => $stat_ajuan,
                'wkt_update'    => currDateTime(),
                'updater_id'    => getIDUser()
            ]))->save();

            $ajuan->fill($ajuan->prepare($input))->save();
        }
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
