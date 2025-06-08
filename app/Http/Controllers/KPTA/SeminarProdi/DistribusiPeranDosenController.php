<?php

namespace App\Http\Controllers\Kpta\SeminarProdi;

use App\Models\Pdrd\Sdm;
use App\Models\Pdrd\RegPd;
use Illuminate\Http\Request;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\PeranSeminar;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kpta\PendaftaranSeminar;
use Illuminate\Support\Facades\Crypt;

class DistribusiPeranDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Sdm $sdm)
    {
        $prodi = GetProdiIndividu();
        $detail_dosen = $sdm->detail_dosen(auth()->user()->id_sdm_pengguna);

        $list_angkatan = collect(PesertaDidik::ListPesertaDidikProdi($prodi->id_sms))
            ->pluck('angkatan', 'angkatan')
            ->toArray();
        $angkatan = $request->angkatan ?? null;

        $list_jns_seminar = collect(JenisSeminar::jenis_seminar_prodi($prodi->id_sms))
            ->pluck('nm_jns_seminar', 'nm_jns_seminar')
            ->toArray();
        $kategori = $request->kategori ?? null;
        // dd($nm_jns_seminar);

        $list_peran_seminar = collect(PeranSeminar::DataPeranSeminar(auth()->user()->id_sdm_pengguna))
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

        $peran_seminar = PeranSeminar::DataMahasiswa($detail_dosen->id_sdm, $angkatan, $kategori, $inisial_peran, $urutan);
        // dd($peran_seminar);

        return view('kpta.distribusi_peran_dosen.index', compact('peran_seminar', 'angkatan', 'kategori', 'detail_dosen', 'list_angkatan', 'list_jns_seminar', 'list_peran_seminar', 'peran_dosen'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $data_awal = Crypt::decrypt($id);
        $id_jns_seminar = $data_awal['id_jns_seminar'];
        $id_reg_pd = $data_awal['id_reg_pd'];
        $data_reg_pd = RegPd::find($data_awal['id_reg_pd']);
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
                DATE_PART('day', sisa_wkt.tgl_batas_penyusunan - CURRENT_DATE) AS sisa_hari,
                DATE_PART('day', sisa_wkt.tgl_batas_penyusunan - sisa_wkt.tgl_mulai) as total_hari
            ")
            ->first();
        $profil = $pesertaDidik->id_detail_mahasiswa($data_reg_pd->id_pd);
        // dd($data_daftar_seminar);
        return view('kpta.distribusi_peran_dosen.detail', compact('data_reg_pd', 'data_daftar_seminar', 'profil', 'data_seminar_prodi', 'data_tugas_akhir', 'jangka_wkt_ta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
