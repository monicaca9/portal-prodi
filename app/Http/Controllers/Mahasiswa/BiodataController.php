<?php

namespace App\Http\Controllers\Mahasiswa;

use Illuminate\Http\Request;
use App\Models\Dok\LargeObject;
use App\Models\ManAkses\Pengguna;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kpta\KonsentrasiProdi;
use App\Models\ManAkses\RolePengguna;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\KonsentrasiProdiPd;
use Illuminate\Support\Facades\Session;

class BiodataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PesertaDidik::detailPD();
        $konsentrasi_prodi = KonsentrasiProdi::where('id_sms', $data->id_sms)->where('soft_delete', 0)
            ->pluck('nm_konsentrasi_prodi', 'id_konsentrasi_prodi')->toArray();
        return view('sdm.mahasiswa.biodata.index', compact('data', 'konsentrasi_prodi'));
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
    public function edit(Request $request, PesertaDidik $pesertaDidik)
    {
        $mhs = PesertaDidik::findorfail(auth()->user()->id_pd_pengguna);
        $agama = DB::table('ref.agama')->whereNull('expired_date')->orderBy('id_agama', 'ASC')
            ->pluck('nm_agama', 'id_agama')->toArray();
        $info = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $negara = DB::table('ref.negara')->whereNull('expired_date')->orderBy('id_negara', 'ASC')
            ->pluck('nm_negara', 'id_negara')->toArray();
        $pekerjaan = DB::table('ref.pekerjaan')->whereNull('expired_date')->orderBy('id_pekerjaan', 'ASC')
            ->pluck('nm_pekerjaan', 'id_pekerjaan')->toArray();
        $penghasilan = DB::table('ref.penghasilan')->whereNull('expired_date')->orderBy('id_penghasilan', 'ASC')
            ->pluck('nm_penghasilan', 'id_penghasilan')->toArray();
        $jenjang = DB::table('ref.jenjang_pendidikan')->whereNull('expired_date')->orderBy('id_jenj_didik', 'ASC')
            ->pluck('nm_jenj_didik', 'id_jenj_didik')->toArray();
        $jenis_tinggal = DB::table('ref.jenis_tinggal')->whereNull('expired_date')->orderBy('id_jns_tinggal', 'ASC')
            ->pluck('nm_jns_tinggal', 'id_jns_tinggal')->toArray();
        $alat_transport = DB::table('ref.alat_transportasi')->whereNull('expired_date')->orderBy('id_alat_transport', 'ASC')
            ->pluck('nm_alat_transport', 'id_alat_transport')->toArray();
        $kota_kab = DB::table('ref.wilayah')->whereNull('expired_date')
            ->where('id_level_wil', 2)
            ->orderBy('nm_wil', 'ASC')
            ->pluck('nm_wil', 'id_wil')->toArray();
        $kebutuhan_khusus = DB::table('ref.kebutuhan_khusus')
            ->where(function ($kk) {
                $kk->where('id_kk', 0)->orWhere('nm_kk', 'like', '% - %');
            })->select(
                'id_kk',
                DB::RAW("(CASE WHEN id_kk=0 THEN 'NULL' ELSE SPLIT_PART(nm_kk, ' - ', 1) END) AS kode"),
                'nm_kk'
            )
            ->get();
        $konsentrasi_prodi = KonsentrasiProdi::where('id_sms', $info->id_sms)->where('soft_delete', 0)
            ->pluck('nm_konsentrasi_prodi', 'id_konsentrasi_prodi')->toArray();
        $konsentrasi_prodi_pd = KonsentrasiProdiPd::where('id_pd', $info->id_pd)->where('soft_delete', 0)->first();


        if ($request->has('tab')) {
            $tab = $request->tab;
        } else {
            $tab = 'biodata';
        }
        return view('sdm.mahasiswa.biodata.edit', compact(
            'mhs',
            'jenjang',
            'tab',
            'agama',
            'kota_kab',
            'negara',
            'pekerjaan',
            'penghasilan',
            'kebutuhan_khusus',
            'jenis_tinggal',
            'alat_transport',
            'info',
            'konsentrasi_prodi',
            'konsentrasi_prodi_pd'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $kode = $input['kode'];
        $data_mhs = PesertaDidik::findorfail(auth()->user()->id_pd_pengguna);
        $input_biodata = [];
        $input_konsentrasi_prodi = [];
        if ($kode == 'biodata') {
            $input_biodata  = [
                '_method'           => 'PUT',
                'nik'               => $input['nik'],
                'jk'                => $input['jk'],
                'tmpt_lahir'        => $input['tmpt_lahir'],
                'tgl_lahir'         => $input['tgl_lahir'],
                'id_agama'          => $input['id_agama'],
                'jln'               => $input['jln'],
                'rt'                => ($input['rt'] == '-' ? 0 : $input['rt']),
                'rw'                => ($input['rw'] == '-' ? 0 : $input['rw']),
                'nm_dsn'            => $input['nm_dsn'],
                'kode_pos'          => $input['kode_pos'],
                'ds_kel'            => $input['ds_kel'],
                'id_wil'            => $input['id_wil'],
                'id_kewarganegaraan' => $input['id_kewarganegaraan'],
                'tlpn_rumah'        => $input['tlpn_rumah'],
                'tlpn_hp'           => $input['tlpn_hp'],
            ];
            if (!is_null($request->file('foto'))) {
                $foto = $request->file('foto');
                $ext = $foto->getClientOriginalExtension();
                if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG' || $ext == 'PNG') {
                    $size = $foto->getSize();
                    if ($size <= 1000000) {
                        $mime = $foto->getClientMimeType();
                        $nama_asli = $foto->getClientOriginalName();
                        $bytea = base64_encode(file_get_contents($foto->getPathName()));
                        $large_object = new LargeObject();
                        $large_object->fill($large_object->prepare([
                            'blob_content'  => $bytea,
                            'file_name'     => $nama_asli,
                            'mime_type'     => $mime
                        ]))->save();
                        $input_biodata['id_blob'] = $large_object->id_blob;
                    } else {
                        alert()->error('Foto melebihi 1MB')->persistent('OK');
                        return redirect()->back();
                    }
                }
            }
            unset($input_biodata['foto']);
        } elseif ($kode == 'keluarga') {
            $input_biodata = $request->all();
            if (!is_null($request->list_kk_ayah)) {
                if (count($request->list_kk_ayah) > 1) {
                    $kode_kk_ayah = [];
                    foreach ($request->list_kk_ayah as $kk_ayah) {
                        $cari_kk_ayah = DB::table('ref.kebutuhan_khusus')
                            ->select(
                                'id_kk',
                                DB::RAW("(CASE WHEN id_kk=0 THEN 'NULL' ELSE SPLIT_PART(nm_kk, ' - ', 1) END) AS kode"),
                                'nm_kk'
                            )
                            ->where('id_kk', $kk_ayah)->first();
                        if ($cari_kk_ayah->id_kk == 0) {
                            alert()->error('Pada Kebutuhan Khusus Ayah terjadi pengisian yang ambigu dengan memilih "Tidak ada" dan juga memilih pilihan lainnya')->persistent('OK');
                            return redirect()->back();
                        } else {
                            $kode_kk_ayah[] = $cari_kk_ayah->kode;
                        }
                    }
                    $nm_kk_ayah = implode(', ', $kode_kk_ayah);
                    $cari_data_kk_ayah_baru = DB::table('ref.kebutuhan_khusus')->where('nm_kk', $nm_kk_ayah)->first();
                    $input_biodata['id_kk_ayah'] = $cari_data_kk_ayah_baru->id_kk;
                } else {
                    $input_biodata['id_kk_ayah'] = $request->list_kk_ayah[0];
                }
            } else {
                alert()->error('Jika tidak memiliki kebutuhan khusus, silahkan pilih Tidak Ada', 'Data Kebutuhan Khusus Ayah harus terisi')->persistent('OK');
                return redirect()->back()->withInput($input_biodata);
            }

            if (!is_null($request->list_kk_ibu)) {
                if (count($request->list_kk_ibu) > 1) {
                    $kode_kk_ibu = [];
                    foreach ($request->list_kk_ibu as $kk_ibu) {
                        $cari_kk_ibu = DB::table('ref.kebutuhan_khusus')
                            ->select(
                                'id_kk',
                                DB::RAW("(CASE WHEN id_kk=0 THEN 'NULL' ELSE SPLIT_PART(nm_kk, ' - ', 1) END) AS kode"),
                                'nm_kk'
                            )
                            ->where('id_kk', $kk_ibu)->first();
                        if ($cari_kk_ibu->id_kk == 0) {
                            alert()->error('Pada Kebutuhan Khusus Ibu terjadi pengisian yang ambigu dengan memilih "Tidak ada" dan juga memilih pilihan lainnya')->persistent('OK');
                            return redirect()->back();
                        } else {
                            $kode_kk_ibu[] = $cari_kk_ibu->kode;
                        }
                    }
                    $nm_kk_ibu = implode(', ', $kode_kk_ibu);
                    $cari_data_kk_ibu_baru = DB::table('ref.kebutuhan_khusus')->where('nm_kk', $nm_kk_ibu)->first();
                    $input_biodata['id_kk_ibu'] = $cari_data_kk_ibu_baru->id_kk;
                } else {
                    $input_biodata['id_kk_ibu'] = $request->list_kk_ibu[0];
                }
            } else {
                alert()->error('Jika tidak memiliki kebutuhan khusus, silahkan pilih Tidak Ada', 'Data Kebutuhan Khusus Ibu harus terisi')->persistent('OK');
                return redirect()->back()->withInput($input_biodata);
            }
            unset($input_biodata['list_kk_ayah']);
            unset($input_biodata['list_kk_ibu']);
        } elseif ($kode == 'wali') {
            $input_biodata = $request->all();
        } elseif ($kode == 'konsentrasi_prodi') {
            $input_konsentrasi_prodi = $request->all();
        } elseif ($kode == 'lainnya') {
            $input_biodata = $request->all();
            if (!is_null($request->list_kk)) {
                if (count($request->list_kk) > 1) {
                    $kode_kk = [];
                    foreach ($request->list_kk as $kk) {
                        $cari_kk = DB::table('ref.kebutuhan_khusus')
                            ->select(
                                'id_kk',
                                DB::RAW("(CASE WHEN id_kk=0 THEN 'NULL' ELSE SPLIT_PART(nm_kk, ' - ', 1) END) AS kode"),
                                'nm_kk'
                            )
                            ->where('id_kk', $kk)->first();
                        if ($cari_kk->id_kk == 0) {
                            alert()->error('Pada Kebutuhan Khusus Anda terjadi pengisian yang ambigu dengan memilih "Tidak ada" dan juga memilih pilihan lainnya')->persistent('OK');
                            return redirect()->back();
                        } else {
                            $kode_kk[] = $cari_kk->kode;
                        }
                    }
                    $nm_kk = implode(', ', $kode_kk);
                    $cari_data_kk_baru = DB::table('ref.kebutuhan_khusus')->where('nm_kk', $nm_kk)->first();
                    $input_biodata['id_kk'] = $cari_data_kk_baru->id_kk;
                } else {
                    $input_biodata['id_kk'] = $request->list_kk[0];
                }
            } else {
                alert()->error('Jika tidak memiliki kebutuhan khusus, silahkan pilih Tidak Ada', 'Data Kebutuhan Khusus harus terisi')->persistent('OK');
                return redirect()->back()->withInput($input_biodata);
            }
        }
        if (count($input_biodata) > 0) {
            $data_mhs = PesertaDidik::findorfail(auth()->user()->id_pd_pengguna);
            $data_mhs->fill($data_mhs->prepare($input_biodata))->save();
        }

        if (count($input_konsentrasi_prodi) > 0) {
            $data_konsentrasi_prodi = KonsentrasiProdiPd::where('id_pd', auth()->user()->id_pd_pengguna)->where('soft_delete', 0)->first();
            if (is_null($data_konsentrasi_prodi)) {
                $konsentrasi_pd = new KonsentrasiProdiPd();
                $konsentrasi_pd->fill($konsentrasi_pd->prepare(
                    [
                        '_method' => 'POST',
                        'id_pd' => $input_konsentrasi_prodi['id_pd'],
                        'id_konsentrasi_prodi' => $input_konsentrasi_prodi['id_konsentrasi_prodi'],
                    ]
                ))->save();
            } else {
                $data_konsentrasi_prodi->fill($data_konsentrasi_prodi->prepare($input_konsentrasi_prodi))->save();
            }
        }
        alert()->success('Data berhasil diubah')->persistent('OK');
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

    public function validasi($id, Request $request)
    {
        $id_pd = Crypt::decrypt($id);
        $mhs = PesertaDidik::findorfail($id_pd);
        if (is_null($mhs->id_blob)) {
            alert()->error('Foto belum di upload')->persistent('OK');
            return redirect()->back();
        }
        $pengguna = Pengguna::where('id_pd_pengguna', $id_pd)->first();
        $peran = RolePengguna::where('id_pengguna', auth()->user()->id_pengguna)->where('soft_delete', 0)->orderBy('last_active', 'DESC')->first();
        $peran->approval_peran  = 1;
        $peran->sk_penugasan    = 'Terkunci';
        $peran->tgl_sk_penugasan = date('Y-m-d');
        $peran->last_update     = currDateTime();
        $peran->id_updater      = auth()->user()->id_pengguna;
        $peran->save();
        Session::put('login.peran', $peran->toArray());
        alert()->success('Biodata anda telah berhasil di-simpan permanen')->persistent('OK');
        return redirect()->route('biodata');
    }
}
