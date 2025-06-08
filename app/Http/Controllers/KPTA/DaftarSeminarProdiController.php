<?php

namespace App\Http\Controllers\KPTA;

use Illuminate\Support\Str;
use App\Models\Kpta\NomorBa;
use Illuminate\Http\Request;
use App\Models\Ref\JenisDokumen;
use App\Models\Ref\JenisSeminar;
use App\Models\Kpta\SeminarProdi;
use App\Models\Kpta\SyaratSeminar;
use Illuminate\Support\Facades\DB;
use App\Models\Kpta\NomorBaSeminar;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\KategoriNilaiSeminar;

class DaftarSeminarProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prodi = GetProdiIndividu();
        $data = SeminarProdi::data_semua_seminar_prodi();
        return view('kpta.daftar_seminar_prodi.index', compact('data', 'prodi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $JenisSeminar = JenisSeminar::select('id_jns_seminar', 'nm_jns_seminar')
            ->where('a_seminar', 1)->wherenull('expired_date')
            ->pluck('nm_jns_seminar', 'id_jns_seminar')->toarray();
        $Urutan = [];
        for ($i = 1; $i <= 6; $i++) {
            $Urutan[$i] = $i;
        }
        $idSMS = session()->get('login.peran.id_organisasi');
        $id_mk = collect(DB::select("
            SELECT 
                matkul.id_mk, 
                CONCAT( '( ', matkul.kode_mk, ' ) - ', matkul.nm_mk) AS nm_mk
            FROM pdrd.matkul AS matkul
            WHERE matkul.soft_delete = 0
            AND matkul.id_sms = '$idSMS';
        "))->pluck('nm_mk', 'id_mk')->toArray();

        return view('__partial.form.create', [
            'judul_halaman' => 'Tambah Daftar Seminar Baru',
            'route'         => 'daftar_seminar_prodi.simpan',
            'backLink'      => 'daftar_seminar_prodi',
            'form'          => 'kpta.daftar_seminar_prodi.create',
            'jenis_seminar' => $JenisSeminar,
            'Urutan'        => $Urutan,
            'Id_sms'        => $idSMS,
            'Id_mk'         => $id_mk
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
        $data = new SeminarProdi();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Data Berhasil Disimpan')->persistent('ok');
        return redirect()->route('daftar_seminar_prodi.ubah', Crypt::encrypt($data->id_seminar_prodi));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $syarat = DB::SELECT("
            SELECT
                list.id_list_syarat,
                syarat.id_syarat_seminar,
                syarat.nm_syarat_seminar,
                syarat.keterangan,
                list.urutan,
                list.min_syarat,
                list.maks_syarat,
                CASE WHEN dokumen.total_dokumen IS NULL THEN 0 ELSE dokumen.total_dokumen END AS total_dokumen
            FROM kpta.list_syarat_seminar AS list
            JOIN kpta.syarat_seminar AS syarat ON syarat.id_syarat_seminar = list.id_syarat_seminar
            LEFT JOIN (
                SELECT id_list_syarat,  COUNT(*) AS total_dokumen
                FROM dok.dok_syarat_seminar
                WHERE soft_delete=0
                GROUP BY id_list_syarat ) AS dokumen ON dokumen.id_list_syarat =  list.id_list_syarat
            WHERE list.soft_delete=0
            AND list.id_seminar_prodi = '" . $data->id_seminar_prodi . "'
            ORDER BY list.urutan ASC
        ");
        $list_exclude = [];
        foreach ($syarat as $each_syarat) {
            $list_exclude[] = $each_syarat->id_syarat_seminar;
        }
        $list_syarat = SyaratSeminar::select(
            'id_syarat_seminar',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_syarat_seminar ELSE CONCAT(nm_syarat_seminar,' (',keterangan,')') END) AS nm_syarat")
        )->where('soft_delete', 0)->whereNotIn('id_syarat_seminar', $list_exclude)
            ->orderBy('nm_syarat_seminar')
            ->pluck('nm_syarat', 'id_syarat_seminar')->toArray();

        return view('kpta.daftar_seminar_prodi.detail', compact('data', 'syarat', 'list_syarat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $jenis_seminar = JenisSeminar::select('id_jns_seminar', 'nm_jns_seminar')->where('a_seminar', 1)->wherenull('expired_date')->pluck('nm_jns_seminar', 'id_jns_seminar')->toarray();
        $urutan = [];
        for ($i = 1; $i <= 6; $i++) {
            $urutan[$i] = $i;
        }
        $id_sms = session()->get('login.peran.id_organisasi');

        $id_mk = collect(DB::select("
        SELECT 
            matkul.id_mk, 
            CONCAT( '( ', matkul.kode_mk, ' ) - ', matkul.nm_mk) AS nm_mk
        FROM pdrd.matkul AS matkul
        WHERE matkul.soft_delete = 0
        AND matkul.id_sms = '" . $id_sms . "'
        "))->pluck('nm_mk', 'id_mk')->toArray();

        $list_nm_ba = collect(DB::select("
        SELECT 
            no_ba.id_no_ba,
            CONCAT( no_ba.nm_akt_ba, ' - ( ... ' , no_ba.kode_ba, ' )') AS nm_ba
        FROM kpta.nomor_ba as no_ba
        WHERE no_ba.soft_delete =0
        AND no_ba.id_sms = '" . $id_sms . "' 
        "))->pluck('nm_ba', 'id_no_ba')->toArray();

        $data_nomor_ba_seminar = nomorBaSeminar::where('id_seminar_prodi', $id_seminar_prodi)
            ->where('soft_delete', 0)
            ->first();
        // dd($data_nomor_ba_seminar);
        // dd($list_nm_ba);
        return view('kpta.daftar_seminar_prodi.edit', compact('data', 'jenis_seminar', 'urutan', 'id_mk', 'id_sms', 'list_nm_ba', 'data_nomor_ba_seminar'));
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
        $input = $request->all();
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Data Berhasil Diubah')->persistent('ok');
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
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('ok');
        return redirect()->back();
    }

    public function kategori_nilai($id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorFail($id_seminar_prodi);
        $kategori_nilai = DB::SELECT("
        SELECT
            list.id_list_kategori_nilai,
            list.urutan,
            kategori.id_kategori_nilai,
            kategori.nm_kategori_nilai,
            kategori.keterangan,
            CASE WHEN komponen.total_komponen_nilai IS NULL THEN 0 ELSE komponen.total_komponen_nilai END AS total_komponen_nilai
        FROM kpta.list_kategori_nilai_seminar AS list
        JOIN kpta.kategori_nilai_seminar AS kategori ON kategori.id_kategori_nilai = list.id_kategori_nilai
        LEFT JOIN (
            SELECT id_list_kategori_nilai, COUNT(*) AS total_komponen_nilai
            FROM kpta.list_komponen_nilai_seminar
            WHERE soft_delete=0
            GROUP BY id_list_kategori_nilai) AS komponen ON komponen.id_list_kategori_nilai = list.id_list_kategori_nilai 
        WHERE list.soft_delete=0
        AND list.id_seminar_prodi = '" . $data->id_seminar_prodi . "'
        ORDER BY list.urutan ASC
        ");

        $list_exclude = [];
        foreach ($kategori_nilai as $each_kategori) {
            $list_exclude[] = $each_kategori->id_kategori_nilai;
        }
        $list_kategori_nilai = KategoriNilaiSeminar::select(
            'id_kategori_nilai',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_kategori_nilai ELSE CONCAT(nm_kategori_nilai,' (',keterangan,')') END) AS nm_kategori")
        )->where('soft_delete', 0)->whereNotIn('id_kategori_nilai', $list_exclude)
            ->orderBy('nm_kategori_nilai')
            ->pluck('nm_kategori', 'id_kategori_nilai')->toArray();
        return view('kpta.daftar_seminar_prodi.kategori_nilai.detail', compact('data', 'kategori_nilai', 'list_kategori_nilai'));
    }

    public function distribusi_nilai($id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findOrFail($id_seminar_prodi);

        $distribusi_nilai = DB::table('kpta.distribusi_nilai')
            ->where('soft_delete', 0)
            ->where('id_seminar_prodi', $id_seminar_prodi)
            ->orderByRaw('peran ASC, urutan ASC')
            ->get();
        // dd($distribusi_nilai);
        $list_jabatan = [];
        $jmlh_pembimbing = $data->jmlh_pembimbing ?? 0;
        $jmlh_penguji = $data->jmlh_penguji ?? 0;

        for ($pembimbing = 1; $pembimbing <= $jmlh_pembimbing; $pembimbing++) {
            $list_jabatan["Pembimbing-$pembimbing"] = "Pembimbing $pembimbing";
        }

        for ($penguji = 1; $penguji <= $jmlh_penguji; $penguji++) {
            $list_jabatan["Penguji-$penguji"] = "Penguji $penguji";
        }

        if ($data->jenisSeminar && $data->jenisSeminar->a_tugas_akhir == 0) {
            $list_jabatan["Pembimbing Lapangan"] = "Pembimbing Lapangan";
        }

        foreach ($distribusi_nilai as $item) {
            $peran_seminar = config('mp.data_master.peran_seminar');
            $nama_peran = $peran_seminar[$item->peran] ?? null;

            if ($nama_peran) {
                if ($nama_peran === "Pembimbing Lapangan") {
                    $key = $nama_peran;
                } else {
                    $key = $item->urutan > 0 ? "$nama_peran-$item->urutan" : $nama_peran;
                }

                if (isset($list_jabatan[$key])) {
                    unset($list_jabatan[$key]);
                }
            }
        }

        return view('kpta.daftar_seminar_prodi.distribusi_nilai.detail', compact('data', 'list_jabatan', 'distribusi_nilai'));
    }

    public function store_ba($id, Request $request)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $input = $request->all();
        if ($input['pilihan_no_ba'] == 'baru') {
            $data_ba = [
                'nm_akt_ba' => $input['nm_akt_ba'],
                'no_ba_awal' => $input['no_ba_awal'],
                'no_ba_terbaru' => $input['no_ba_awal'],
                'kode_ba' => $input['kode_ba'],
                'id_sms' => $input['id_sms'],
            ];
            $simpan_data_ba = new NomorBa();
            $simpan_data_ba->fill($simpan_data_ba->prepare($data_ba))->save();
            $input['id_no_ba'] = $simpan_data_ba->id_no_ba;
        }

        $data_ba_seminar = [
            'id_seminar_prodi' => $data->id_seminar_prodi,
            'id_no_ba' => $input['id_no_ba'],
        ];
        $simpan_data_ba_seminar = new NomorBaSeminar();
        $simpan_data_ba_seminar->fill($simpan_data_ba_seminar->prepare($data_ba_seminar))->save();
        alert()->success('Data Berhasil Disimpan')->persistent('ok');
        return redirect()->back();
    }

    public function edit_ba($id, Request $request)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data_seminar_prodi = SeminarProdi::findorfail($id_seminar_prodi);
        $data_nomor_ba_seminar = NomorBaSeminar::where('id_seminar_prodi', $id_seminar_prodi)
            ->where('soft_delete', 0)
            ->first();
        $data_ba = NomorBa::findorfail($data_nomor_ba_seminar->id_no_ba);

        return view('kpta.daftar_seminar_prodi.edit_ba', compact('data_ba', 'data_seminar_prodi', 'data_nomor_ba_seminar'));
    }

    public function update_ba($id, Request $request)
    {
        $id_no_ba = Crypt::decrypt($id);
        $data = NomorBa::findorfail($id_no_ba);
        $input = $request->all();
        $input['no_ba_terbaru'] = $input['no_ba_awal'];
        $data->fill($data->prepare($input))->save();
        alert()->success('Data Berhasil Diubah')->persistent('ok');
        return redirect()->back();
    }

    public function destroy_ba_seminar($id)
    {
        $id_no_ba_seminar = Crypt::decrypt($id);
        $data_ba_seminar = NomorBaSeminar::findorfail($id_no_ba_seminar);
        $data_ba_seminar->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('ok');
        return redirect()->back();
    }
}
