<?php

namespace App\Http\Controllers\Kpta\SeminarProdi;

use Carbon\Carbon;
use App\Models\Pdrd\Sdm;
use Illuminate\Http\Request;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\PeranSeminar;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kpta\AvgSkorKategori;
use App\Models\Kpta\AvgSkorKomponen;
use App\Models\Kpta\SkorPerKomponen;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Kpta\PeranDosenPendaftar;
use App\Models\Kpta\KategoriNilaiSeminar;
use App\Models\Kpta\ListKomponenNilaiSeminar;
use App\Models\Kpta\SeminarProdi\SisaWaktuPenyusunan;
use App\Models\Validasi\AjuanPdmSeminar;

class PenilaianSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Sdm $sdm)
    {
        $prodi = GetProdiIndividu();
        $detail_dosen = $sdm->detail_dosen(auth()->user()->id_sdm_pengguna);
        // dd($prodi, $detail_dosen);
        $list_jns_seminar = collect(SeminarProdi::data_semua_seminar_prodi())
            ->pluck('nm_jns_seminar', 'nm_jns_seminar')
            ->toArray();
        $jenis_seminar = $request->jenis_seminar ?? null;
        // dd($jenis_seminar);
        $peran_dosen_pendaftar = PeranSeminar::DataMahasiswaSeminar($detail_dosen->id_sdm, $jenis_seminar);
        return view('pendaftaran_seminar.penilaian_seminar.index', compact('detail_dosen', 'list_jns_seminar', 'peran_dosen_pendaftar', 'jenis_seminar'));
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
    public function show($id, PesertaDidik $pesertaDidik, Request $request)
    {
        $id_daftar_seminar = Crypt::decrypt($id);
        $data_daftar_seminar = PendaftaranSeminar::findorfail($id_daftar_seminar);
        $profil = $pesertaDidik->id_detail_mahasiswa($data_daftar_seminar->RegPd->id_pd);
        $seminar = SeminarProdi::find($data_daftar_seminar->id_seminar_prodi);

        $list_peran_seminar = DB::table('kpta.peran_dosen_pendaftar as peran_dosen')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->select('peran_dosen.id_peran_dosen_pendaftar', 'peran.peran', 'peran.urutan')
            ->where('peran_dosen.id_daftar_seminar', '=', $id_daftar_seminar)
            ->where('peran.soft_delete', 0)
            ->where('peran.a_aktif', 1)
            ->where('peran.a_ganti', 0)
            ->orderBy('peran.peran')
            ->orderBy('peran.urutan')
            ->get()
            ->mapWithKeys(function ($item) {
                $nama_peran = config('mp.data_master.peran_seminar.' . $item->peran, 'Peran Tidak Diketahui');
                $peran = $nama_peran . ' Ke-' . $item->urutan;
                return [$item->id_peran_dosen_pendaftar => $peran];
            })
            ->toArray();

        $default_peran_id = array_key_first($list_peran_seminar);
        $default_peran_nama = $list_peran_seminar[$default_peran_id] ?? null;

        $peran_nama = $request->input('peran', $default_peran_nama);

        $peran_id = array_search($peran_nama, $list_peran_seminar);

        if ($peran_id === false) {
            $peran_id = $default_peran_id;
            $peran_nama = $default_peran_nama;
        }
        $data_peran_seminar = PeranDosenPendaftar::find($peran_id);
        // dd($data_peran_seminar);
        $data_nilai = AvgSkorKategori::DataNilai($data_daftar_seminar->id_seminar_prodi, $id_daftar_seminar, $peran_id);
        $nilai_akhir_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();
        // dd($data_nilai);
        return view('pendaftaran_seminar.penilaian_seminar.detail', compact('data_daftar_seminar', 'profil',  'seminar', 'list_peran_seminar',  'data_nilai', 'peran_id', 'data_peran_seminar', 'nilai_akhir_seminar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $id_daftar_seminar = crypt::decrypt($id);

        $data_jangka_wkt = DB::table('kpta.pendaftaran_seminar as daftar_seminar')
            ->join('kpta.seminar_prodi as tseminar', 'tseminar.id_seminar_prodi', '=', 'daftar_seminar.id_seminar_prodi')
            ->join('pdrd.sms as sms', 'sms.id_sms', '=', 'tseminar.id_sms')
            ->join('ref.jenis_seminar as jns', 'jns.id_jns_seminar', '=', 'tseminar.id_jns_seminar')
            ->leftJoin('ref.jenis_seminar as induk_jns', 'induk_jns.id_jns_seminar', '=', 'jns.id_induk_jns_seminar')
            ->join('kpta.jangka_waktu_penyusunan as jangka_wkt', function ($join) {
                $join->on('jangka_wkt.id_jns_seminar', '=', 'jns.id_jns_seminar')
                    ->orOn('jangka_wkt.id_jns_seminar', '=', 'induk_jns.id_jns_seminar')
                    ->on('jangka_wkt.id_sp', '=', 'sms.id_sp')
                    ->on('jangka_wkt.id_jenj_didik', '=', 'sms.id_jenj_didik');
            })
            ->where('daftar_seminar.id_daftar_seminar', $id_daftar_seminar)
            ->select(
                'jangka_wkt.durasi_perpanjangan',
                'jangka_wkt.durasi_penyusunan',
                'tseminar.id_jns_seminar',
                'induk_jns.id_jns_seminar as induk_id_jns_seminar',
                'daftar_seminar.id_reg_pd',
                'daftar_seminar.tgl_mulai'
            )
            ->first();

        $a_valid = $request->input('a_valid_nilai');

        if ($a_valid == 1) {
            if (!is_null($data_jangka_wkt)) {
                $tgl_mulai = Carbon::parse($data_jangka_wkt->tgl_mulai);
                $tgl_batas_penyusunan = $tgl_mulai->copy()->addMonths((int) $data_jangka_wkt->durasi_penyusunan);

                $data_sisa_wkt = SisaWaktuPenyusunan::where('id_reg_pd', $data_jangka_wkt->id_reg_pd)->where('id_jns_seminar', $data_jangka_wkt->induk_id_jns_seminar)->where('soft_delete', 0)->first();
                if (is_null($data_sisa_wkt)) {
                    if ($data_jangka_wkt->id_jns_seminar == 2) {
                        $data_sisa_wkt = new SisaWaktuPenyusunan();
                        $data_sisa_wkt->fill($data_sisa_wkt->prepare([
                            'id_reg_pd' => $data_jangka_wkt->id_reg_pd,
                            'id_jns_seminar' => $data_jangka_wkt->induk_id_jns_seminar,
                            'tgl_mulai' => $tgl_mulai->toDateString(),
                            'tgl_batas_penyusunan' => $tgl_batas_penyusunan->toDateString(),
                        ]))->save();
                    }
                } else {
                    if ($data_jangka_wkt->id_jns_seminar == 4) {
                        $data_sisa_wkt->fill($data_sisa_wkt->prepare([
                            'id_sisa_wkt' => $data_sisa_wkt->id_sisa_wkt,
                            'tgl_selesai' => $data_jangka_wkt->tgl_mulai,
                        ]))->save();
                    }
                }
            }

            $nilai_akhir_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();
            if (!is_null($nilai_akhir_seminar)) {
                $nilai_akhir_seminar->fill($nilai_akhir_seminar->prepare([
                    'id_nilai_akhir_seminar' => $nilai_akhir_seminar->id_nilai_akhir_seminar,
                    'a_valid' => $a_valid,
                ]))->save();
            }

            alert()->success('Data Nilai berhasil divalidasi')->persistent('OK');
        }
        return redirect()->back();
    }

    // public function valid(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $id_peran_dosen_pendaftar = crypt::decrypt($id);
        $data_daftar_seminar = DB::table('kpta.peran_dosen_pendaftar as peran_dosen')
            ->join('kpta.pendaftaran_seminar as daftar_seminar', 'daftar_seminar.id_daftar_seminar', '=', 'peran_dosen.id_daftar_seminar')
            ->where('peran_dosen.id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)
            ->first();
        $skorData = $request->input('skor', []);
        foreach ($skorData as $id_skor_komponen => $skor) {
            $data = SkorPerKomponen::find($id_skor_komponen);
            if ($data) {
                $data->fill($data->prepare([
                    'id_skor_komponen' => $id_skor_komponen,
                    'skor' => $skor
                ]))->save();
            }
        }

        $list_kategori_nilai = DB::table('kpta.skor_per_komponen as skor_komponen')
            ->join('kpta.list_komponen_nilai_seminar as list_komponen', 'list_komponen.id_list_komponen_nilai', '=', 'skor_komponen.id_list_komponen_nilai')
            ->join('kpta.list_kategori_nilai_seminar as list_kategori', 'list_kategori.id_list_kategori_nilai', '=', 'list_komponen.id_list_kategori_nilai')
            ->where('skor_komponen.id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)
            ->where('list_kategori.soft_delete', 0)
            ->where('list_komponen.soft_delete', 0)
            ->where('skor_komponen.soft_delete', 0)
            ->pluck('list_kategori.id_list_kategori_nilai')
            ->unique();

        foreach ($list_kategori_nilai as $each_list_kategori) {
            $data_nilai_komponen = DB::table('kpta.skor_per_komponen as skor_komponen')
                ->join('kpta.list_komponen_nilai_seminar as list_komponen', 'list_komponen.id_list_komponen_nilai', '=', 'skor_komponen.id_list_komponen_nilai')
                ->join('kpta.list_kategori_nilai_seminar as list_kategori', 'list_kategori.id_list_kategori_nilai', '=', 'list_komponen.id_list_kategori_nilai')
                ->where('list_kategori.id_list_kategori_nilai', $each_list_kategori)
                ->where('skor_komponen.id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)
                ->where('list_kategori.soft_delete', 0)
                ->where('list_komponen.soft_delete', 0)
                ->where('skor_komponen.soft_delete', 0)
                ->select('skor_komponen.skor', 'skor_komponen.id_skor_komponen', 'list_kategori.id_list_kategori_nilai')
                ->get();

            $total_skor_komponen = $data_nilai_komponen->sum('skor');
            $jumlah_komponen = $data_nilai_komponen->count();
            $rata_rata_komponen = $jumlah_komponen > 0 ? $total_skor_komponen / $jumlah_komponen : 0;

            $data_nilai_kategori = AvgSkorKomponen::where('id_list_kategori_nilai', $each_list_kategori)
                ->where('id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)
                ->where('soft_delete', 0)
                ->first();

            if ($data_nilai_kategori) {
                $data_nilai_kategori->fill($data_nilai_kategori->prepare([
                    'id_avg_skor_komponen' => $data_nilai_kategori->id_avg_skor_komponen,
                    'skor' => $rata_rata_komponen
                ]))->save();
            }
        }

        $data_nilai_kategori = DB::table('kpta.avg_skor_komponen as skor_komponen')
            ->join('kpta.list_kategori_nilai_seminar as list_kategori', 'list_kategori.id_list_kategori_nilai', '=', 'skor_komponen.id_list_kategori_nilai')
            ->where('skor_komponen.id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)
            ->where('list_kategori.soft_delete', 0)
            ->get();

        $total_skor_kategori = $data_nilai_kategori->sum('skor');
        $jumlah_kategori = $data_nilai_kategori->count();
        $rata_rata_kategori = $jumlah_kategori > 0 ? $total_skor_kategori / $jumlah_kategori : 0;

        $data_total_skor_kategori = AvgSkorKategori::where('id_peran_dosen_pendaftar', $id_peran_dosen_pendaftar)->first();
        if ($data_total_skor_kategori) {
            $data_total_skor_kategori->fill($data_total_skor_kategori->prepare([
                'id_total_skor_kategori' => $data_total_skor_kategori->id_total_skor_kategori,
                'skor' => $rata_rata_kategori
            ]))->save();
        }

        $data_skor_kategori = DB::table('kpta.avg_skor_kategori as skor_kategori')
            ->join('kpta.peran_dosen_pendaftar as peran_dosen', 'peran_dosen.id_peran_dosen_pendaftar', '=', 'skor_kategori.id_peran_dosen_pendaftar')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->where('peran_dosen.id_daftar_seminar', $data_daftar_seminar->id_daftar_seminar)
            ->where('peran_dosen.soft_delete', 0)
            ->where('peran.soft_delete', 0)
            ->where('peran.a_aktif', 1)
            ->where('peran.a_ganti', 0)
            ->where('peran_dosen.soft_delete', 0)
            ->get();
        // dd($total_skor_kategori);

        $data_distribusi_nilai =  DB::table('kpta.distribusi_nilai as distribusi')
            ->where('distribusi.id_seminar_prodi', $data_daftar_seminar->id_seminar_prodi)
            ->where('soft_delete', 0)
            ->get();


        $total_nilai = 0;
        foreach ($data_skor_kategori as $each_skor_komponen) {
            foreach ($data_distribusi_nilai as $distribusi) {
                if (
                    $each_skor_komponen->peran == $distribusi->peran &&
                    $each_skor_komponen->urutan == $distribusi->urutan
                ) {
                    $nilai = $each_skor_komponen->skor * ($distribusi->persentase / 100);
                    $total_nilai += $nilai;
                }
            }
        }

        $data_nilai_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $data_daftar_seminar->id_daftar_seminar)->where('soft_delete', 0)->first();
        if ($data_nilai_seminar) {
            $data_nilai_seminar->fill($data_nilai_seminar->prepare([
                'id_nilai_akhir_seminar' => $data_nilai_seminar->id_nilai_akhir_seminar,
                'skor' => $total_nilai
            ]))->save();
        }
        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
