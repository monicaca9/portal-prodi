<?php

namespace App\Http\Controllers\ManajemenPerkuliahan;

use App\Http\Controllers\Controller;
use App\Models\Manajemen\AngTpmps;
use App\Models\Manajemen\Tpmps;
use App\Models\Ref\JenisDokumen;
use App\Models\Ref\Semester;
use Illuminate\Http\Request;
use App\Http\Controllers\DokumenTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TimPenjaminMutuController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Tpmps::semuaData();
        return view('manajemen_matakuliah.tpmps.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $smt = Semester::whereNull('expired_date')->orderBy('id_smt','DESC')->pluck('nm_smt','id_smt')->toArray();
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok','ASC')->pluck('nm_jns_dok','id_jns_dok')->toArray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Tim Penjamin Mutu Baru',
            'route'         => 'tim_penjamin_mutu.simpan',
            'backLink'      => 'tim_penjamin_mutu',
            'form'          => 'manajemen_matakuliah.tpmps.create',
            'smt'           => $smt,
            'jenis_dok'     => $jenis_dok
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
        $this->validate($request, [
            'nm_dok'        => 'string|max:60',
            'ket_dok'       => 'string|max:200|nullable'
        ]);
        $input = $request->all();
        if (is_null(@$input['url']) && is_null(@$input['file_dok'])) {
            alert()->error('File/Url harus terisi')->persistent('OK');
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
                $input['id_dok'] = $data_dokumen;

                $data = new Tpmps();
                $data->fill($data->prepare($input));
                $data->save();

                alert()->success('Tim Penjamin Mutu ditambahkan')->persistent('OK');
                return redirect()->route('tim_penjamin_mutu');
            } else {
                alert()->error('Dokumen Pendukung harus dalam format .pdf')->persistent('OK');
                return redirect()->back();
            }
        } else {
            return redirect()->back();
        }
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
        $data = Tpmps::find($id);
        $smt = Semester::whereNull('expired_date')->orderBy('id_smt','DESC')->pluck('nm_smt','id_smt')->toArray();
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok','ASC')->pluck('nm_jns_dok','id_jns_dok')->toArray();
        return view('__partial.form.edit',[
            'judul_halaman' => 'Tambah Tim Penjamin Mutu Baru',
            'route'         => 'tim_penjamin_mutu.update',
            'backLink'      => 'tim_penjamin_mutu',
            'form'          => 'manajemen_matakuliah.tpmps.edit',
            'data'          => $data,
            'id'            => $id,
            'smt'           => $smt,
            'jenis_dok'     => $jenis_dok
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
        $data = Tpmps::find($id);

        $input = $request->all();
        if ($input['ubah_file']==1) {
            $this->validate($request, [
                'nm_dok'        => 'string|max:60',
                'ket_dok'       => 'string|max:200|nullable'
            ]);
            if (is_null(@$input['url']) && is_null(@$input['file_dok'])) {
                alert()->error('File/Url harus terisi')->persistent('OK');
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
                    $input['id_dok'] = $data_dokumen;

                    $data->fill($data->prepare($input));
                    $data->save();

                    alert()->success('Tim Penjamin Mutu diubah')->persistent('OK');
                    return redirect()->route('tim_penjamin_mutu');
                } else {
                    alert()->error('Dokumen Pendukung harus dalam format .pdf')->persistent('OK');
                    return redirect()->back();
                }
            } else {
                return redirect()->back();
            }
        } else {
            $data->fill($data->prepare($input));
            $data->save();

            alert()->success('Tim Penjamin Mutu diubah')->persistent('OK');
            return redirect()->route('tim_penjamin_mutu');
        }
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
        $data = Tpmps::findorfail($id);
        $data->drop();
        alert()->success('Tim Penjamin Mutu berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_aktif($id)
    {
        $id_peer = Crypt::decrypt($id);
        $data = Tpmps::findorfail($id_peer);
        $input = [];
        $input['_method']='PUT';
        if($data->a_aktif==0) {
            $input['a_aktif'] = 1;
            $input['pesan'] = 'Tim Penjaminan Mutu berhasil di-aktifkan';
        } else {
            $input['a_aktif'] = 0;
            $input['pesan'] = 'Tim Penjaminan Mutu berhasil di-non-aktifkan';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }

    public function anggota($id)
    {
        $id_peer = Crypt::decrypt($id);
        $data = Tpmps::findorfail($id_peer);
        $anggota = AngTpmps::where('id_tpmps',$data->id_tpmps)->where('soft_delete',0)
            ->orderBy('a_ketua','DESC')
            ->get();
        $anggota_buang = [];
        foreach ($anggota AS $each_anggota) {
            $anggota_buang[] = $each_anggota->id_sdm;
        }
        $kelompok_anggota = DB::table('pdrd.sdm AS tsdm')
            ->join('pdrd.reg_ptk AS tr','tr.id_sdm','=','tsdm.id_sdm')
            ->whereNull('tr.id_jns_keluar')->where(function($a) {
                $a->whereNull('tr.tgl_ptk_keluar')->orWhere('tr.tgl_ptk_keluar','<=',currDateTime());
            })->where('tr.id_sms',$data->id_sms)
            ->select(
                'tsdm.id_sdm',
                DB::RAW("CONCAT(tsdm.nm_sdm,' - ',tsdm.nidn) AS nm_anggota")
            )->where('tsdm.soft_delete',0)->where('tsdm.id_jns_sdm',12)
            ->whereNotIn('tsdm.id_sdm',$anggota_buang)
            ->orderBy('tsdm.nm_sdm','ASC')->pluck('nm_anggota','id_sdm')->toArray();
        return view('manajemen_matakuliah.tpmps.anggota',compact('data','kelompok_anggota','anggota'));
    }

    public function simpan_anggota(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $peer = Tpmps::find($id);
        $data = new AngTpmps();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Anggota pada Tim Penjamin Mutu '. $peer->nm_tpmps .' berhasil ditambahkan')->persistent('OK');
        return redirect()->back();
    }

    public function destroy_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = Tpmps::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngTpmps::findorfail($id_anggota);
        $data->drop();
        alert()->success('Anggota pada Tim Penjamin Mutu '. $peer->nm_tpmps .' berhasil dihapuskan')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_ketua_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = Tpmps::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngTpmps::findorfail($id_anggota);
        $input = [];
        $input['_method']='PUT';
        if($data->a_ketua==0) {
            $input['a_ketua'] = 1;
            $input['pesan'] = 'Anggota Tim Penjamin Mutu '.$data->nm_tpmps.' berhasil diubah menjadi ketua';
        } else {
            $input['a_ketua'] = 0;
            $input['pesan'] = 'Anggota Tim Penjamin Mutu '.$data->nm_tpmps.' berhasil diubah menjadi anggota';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }

    public function ubah_status_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = Tpmps::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngTpmps::findorfail($id_anggota);
        $input = [];
        $input['_method']='PUT';
        if($data->a_aktif==0) {
            $input['a_aktif'] = 1;
            $input['pesan'] = 'Anggota Tim Penjamin Mutu '.$data->nm_tpmps.' berhasil diubah menjadi aktif';
        } else {
            $input['a_aktif'] = 0;
            $input['pesan'] = 'Anggota Tim Penjamin Mutu '.$data->nm_tpmps.' berhasil diubah menjadi tidak aktif';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }
}
