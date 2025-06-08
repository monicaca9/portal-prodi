<?php

namespace App\Http\Controllers\ManajemenPerkuliahan;

use Carbon\Carbon;
use App\Models\Pdrd\RegPd;
use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\PendaftaranSeminar;

class GedungController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prodi = GetProdiIndividu();
        // Query belum difilter ke tingkat fakultas *
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
        if (session()->get('login.peran.id_peran') == 1) {
            $data = Gedung::where('soft_delete', 0)->orderBy('nm_gedung', 'ASC')->get();
        } else {
            $data = Gedung::where('id_sms', $prodi->id_induk_sms)->where('soft_delete', 0)->orderBy('nm_gedung', 'ASC')->get();
        }
        // $datajadwal =  '<pre>'.print_r($datajadwal).'</pre>'
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
        //dd($jadwal_seminar);
        return view('manajemen_matakuliah.gedung.index', compact('prodi', 'data', 'jadwal_seminar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prodi = GetProdiIndividu();
        return view('__partial.form.create', [
            'judul_halaman' => 'Tambah Gedung Baru',
            'route'         => 'gedung_ruang.simpan',
            'backLink'      => 'gedung_ruang',
            'form'          => 'manajemen_matakuliah.gedung.create',
            'prodi'         => $prodi,
        ]);
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
        $data = new Gedung();
        $data->fill($data->prepare($input))->save();
        alert()->success('Gedung baru ditambahkan')->persistent('OK');
        return redirect()->route('gedung_ruang');
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
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $data = Gedung::findorfail($id);
        $prodi = GetProdiIndividu();
        return view('__partial.form.edit', [
            'judul_halaman' => 'Ubah Gedung',
            'route'         => 'gedung_ruang.update',
            'backLink'      => 'gedung_ruang',
            'form'          => 'manajemen_matakuliah.gedung.edit',
            'prodi'         => $prodi,
            'id'            => $id,
            'data'          => $data
        ]);
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
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $data = Gedung::findorfail($id);
        $data->fill($data->prepare($input))->save();
        alert()->success('Gedung berhasil diubah')->persistent('OK');
        return redirect()->route('gedung_ruang');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $data = Gedung::findorfail($id);
        $data->drop();
        alert()->success('Gedung berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
