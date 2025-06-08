<?php

namespace App\Http\Controllers\ManAkses;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManAkses\PenggunaRequest;
use App\Models\ManAkses\Pengguna;
use App\Models\ManAkses\Peran;
use App\Models\ManAkses\RolePengguna;
use App\Models\ManAkses\UnitOrganisasi;
use App\Models\Simanila\BiodataCalonPd;
use App\Models\Simanila\CalonPd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Pengguna::daftar_pengguna();
        return view('man_akses.pengguna.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unit = DB::table('man_akses.unit_organisasi AS unit')
            ->join('ref.jenis_lembaga AS jns_l','jns_l.id_jns_lemb','=','unit.id_jns_lemb')
            ->leftJoin('pdrd.sms AS tsms','tsms.id_sms','=','unit.id_lembaga_asal')
            ->leftJoin('ref.jenjang_pendidikan AS tjenj','tjenj.id_jenj_didik','=','tsms.id_jenj_didik')
            ->where('unit.soft_delete',0)
            ->select(
                DB::RAW(
                    "(CASE WHEN unit.id_jns_lemb < 23 THEN unit.nm_lemb
                    WHEN unit.id_jns_lemb = 23 THEN CONCAT(jns_l.nm_jns_lemb,' ',unit.nm_lemb)
                    ELSE CONCAT(jns_l.nm_jns_lemb,' ',unit.nm_lemb,' (',tjenj.nm_jenj_didik,')') END) AS nm_lemb"
                ),
                'unit.id_organisasi'
            )
            ->pluck('unit.nm_lemb','unit.id_organisasi')
            ->toArray();
        $peran = Peran::whereNotIn('id_peran',[3005])->whereNull('expired_date')->pluck('nm_peran','id_peran')->toArray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Pengguna Baru',
            'route'         => 'manajemen_akses.pengguna.simpan',
            'backLink'      => 'manajemen_akses.pengguna',
            'form'          => 'man_akses.pengguna.create',
            'unit'          => $unit,
            'peran'         => $peran
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PenggunaRequest $request)
    {
        $input = $request->all();
        $input['a_aktif'] = 1;
        if ($input['id_peran']==3001) {
            $input = $request->all();
            $data_calon_pd = [
                'nm_peserta'    => $input['nm_pengguna'],
                'jk'            => $input['jenis_kelamin'],
            ];
            $calon_pd = new CalonPd();
            $calon_pd->fill($calon_pd->prepare($data_calon_pd))->save();

            $biodata = new BiodataCalonPd();
            $biodata->fill($biodata->prepare(['id_calon_pd'   => $calon_pd->id_calon_pd]))->save();

            $input['id_calon_pd_pengguna'] = $calon_pd->id_calon_pd;
        }
        $data = new Pengguna();
        $data->fill($data->prepare($input));
        $data->save();

        $input['id_pengguna'] = $data->id_pengguna;
        $data_role = new RolePengguna();
        $data_role->fill($data_role->prepare($input));
        $data_role->save();

        alert()->success('Pengguna baru dengan username '.$input['username'].' berhasil ditambahkan')->persistent('OK');
        return redirect()->route('manajemen_akses.pengguna');
    }

    public function store_peran_pengguna(Request $request)
    {
        $input = $request->all();

        $data_role = new RolePengguna();
        $data_role->fill($data_role->prepare($input));
        $data_role->save();

        alert()->success('Peran untuk Pengguna berhasil ditambahkan')->persistent('OK');
        return redirect()->back();
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
        $id_pengguna = Crypt::decrypt($id);
        $data = Pengguna::findorfail($id_pengguna);
        $unit = DB::table('man_akses.unit_organisasi AS unit')
            ->join('ref.jenis_lembaga AS jns_l','jns_l.id_jns_lemb','=','unit.id_jns_lemb')
            ->leftJoin('pdrd.sms AS tsms','tsms.id_sms','=','unit.id_lembaga_asal')
            ->leftJoin('ref.jenjang_pendidikan AS tjenj','tjenj.id_jenj_didik','=','tsms.id_jenj_didik')
            ->where('unit.soft_delete',0)
            ->select(
                DB::RAW(
                    "(CASE WHEN unit.id_jns_lemb < 23 THEN unit.nm_lemb
                    WHEN unit.id_jns_lemb = 23 THEN CONCAT(jns_l.nm_jns_lemb,' ',unit.nm_lemb)
                    ELSE CONCAT(jns_l.nm_jns_lemb,' ',unit.nm_lemb,' (',tjenj.nm_jenj_didik,')') END) AS nm_lemb"
                ),
                'unit.id_organisasi'
            )
            ->pluck('unit.nm_lemb','unit.id_organisasi')
            ->toArray();
        $peran = Peran::whereNull('expired_date')->pluck('nm_peran','id_peran')->toArray();
        $daftar_peran = RolePengguna::list_peran_pengguna($id_pengguna);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Pengguna',
            'route'         => 'manajemen_akses.pengguna.update',
            'backLink'      => 'manajemen_akses.pengguna',
            'form'          => 'man_akses.pengguna.edit',
            'id'            => $id_pengguna,
            'unit'          => $unit,
            'peran'         => $peran,
            'data'          => $data,
            'daftar_peran'  => $daftar_peran
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PenggunaRequest $request, $id)
    {
        $id_pengguna = Crypt::decrypt($id);
        $input = $request->all();
        if (is_null($input['password'])) {
            unset($input['password']);
            unset($input['confirm_password']);
        }
        $data = Pengguna::findorfail($id_pengguna);
        $data->fill($data->prepare($input));
        $data->save();

        alert()->success('Pengguna dengan username '.$input['username'].' berhasil diubah')->persistent('OK');
        return redirect()->route('manajemen_akses.pengguna');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id_pengguna = Crypt::decrypt($id);
        $data = Pengguna::findorfail($id_pengguna);
        $data->drop();
        alert()->success('Pengguna dengan username '.$data->username.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function destroy_peran($id)
    {
        $id_role_pengguna = Crypt::decrypt($id);
        $data = RolePengguna::findorfail($id_role_pengguna);
        $daftar_peran = RolePengguna::list_peran_pengguna($data->id_pengguna);
        if (count($daftar_peran)>1) {
            $data->drop();
            alert()->success('Peran Pengguna berhasil dihapus')->persistent('OK');
        } else {
            alert()->error('Peran anda hanya punya 1','Peran Pengguna gagal dihapus')->persistent('OK');
        }
        return redirect()->back();
    }

    public function ubah_aktif($id)
    {
        $id_thn_ajaran = Crypt::decrypt($id);
        $data = Pengguna::findorfail($id_thn_ajaran);
        $input = [];
        $input['_method']='PUT';
        if($data->a_aktif==0) {
            $input['a_aktif'] = 1;
            $input['pesan'] = 'Pengguna dengan username '.$data->username.' berhasil di-aktifkan';
        } else {
            $input['a_aktif'] = 0;
            $input['pesan'] = 'Pengguna dengan username '.$data->username.' berhasil di-non-aktifkan';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }
}
