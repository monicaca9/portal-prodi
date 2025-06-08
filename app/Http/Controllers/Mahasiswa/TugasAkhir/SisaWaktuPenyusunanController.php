<?php

namespace App\Http\Controllers\Mahasiswa\TugasAkhir;

use Illuminate\Http\Request;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SisaWaktuPenyusunanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PesertaDidik $pesertaDidik)
    {
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $data_tugas_akhir = DB::table('pendaftaran_seminar as daftar_seminar')
            ->join('kpta.seminar_prodi as tseminar', 'tseminar.id_seminar_prodi', '=', 'daftar_seminar.id_seminar_prodi')
            ->join('ref.jenis_seminar as jns_seminar', 'jns_seminar.id_jns_seminar', '=', 'tseminar.id_jns_seminar')
            ->where('jns_seminar.a_tugas_akhir', 1)
            ->where('jns_seminar.a_seminar', 1)
            ->where('daftar_seminar.id_reg_pd', $profil->id_reg_pd)
            ->orderBy('daftar_seminar.tgl_create', 'desc')
            ->get();

        $jangka_wkt_ta = DB::table('kpta.sisa_waktu_penyusunan as sisa_wkt')
            ->where('sisa_wkt.id_reg_pd', $profil->id_reg_pd)
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
        return view('sisa_waktu_penyusunan.index', compact('prodi', 'profil', 'data_tugas_akhir', 'jangka_wkt_ta'));
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
    public function show(string $id) {}

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
