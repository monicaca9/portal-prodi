<?php

namespace App\Http\Controllers\ManajemenPerkuliahan;

use App\Http\Controllers\Controller;
use App\Models\Manajemen\AngPeerGroup;
use App\Models\Manajemen\BidangPeer;
use App\Models\Manajemen\PeerGroup;
use App\Models\Ref\KelompokBidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PeerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PeerGroup::semuaData();
        return view('manajemen_matakuliah.peer_group.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Peer Group Baru',
            'route'         => 'peer_group.simpan',
            'backLink'      => 'peer_group',
            'form'          => 'manajemen_matakuliah.peer_group.create'
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
        $data = new PeerGroup();
        $data->fill($data->prepare($input));
        $data->save();

        alert()->success('Peer group baru ditambahkan')->persistent('OK');
        return redirect()->route('peer_group');
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
        $data = PeerGroup::find($id);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Peer Group',
            'route'         => 'peer_group.update',
            'backLink'      => 'peer_group',
            'form'          => 'manajemen_matakuliah.peer_group.edit',
            'data'          => $data,
            'id'            => $id
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
        $data = PeerGroup::findorfail($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Peer Group berhasil diubah')->persistent('OK');
        return redirect()->route('peer_group');
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
        $data = PeerGroup::findorfail($id);
        $data->drop();
        alert()->success('Peer Group berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_aktif($id)
    {
        $id_peer = Crypt::decrypt($id);
        $data = PeerGroup::findorfail($id_peer);
        $input = [];
        $input['_method']='PUT';
        if($data->a_aktif==0) {
            $input['a_aktif'] = 1;
            $input['pesan'] = 'Peer Group '.$data->nm_peer_group.' berhasil di-aktifkan';
        } else {
            $input['a_aktif'] = 0;
            $input['pesan'] = 'Peer Group '.$data->nm_peer_group.' berhasil di-non-aktifkan';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }

    public function bidang($id)
    {
        $id_peer = Crypt::decrypt($id);
        $data = PeerGroup::findorfail($id_peer);
        $bidang = BidangPeer::where('id_peer_group',$data->id_peer_group)->where('soft_delete',0)->get();
        $bidang_buang = [];
        foreach ($bidang AS $each_bidang) {
            $bidang_buang[] = $each_bidang->id_kel_bidang;
        }
        $kelompok_bidang = KelompokBidang::select(
                'id_kel_bidang',
                DB::RAW("CONCAT(kode_kel_bidang,' - ',nm_kel_bidang) AS nm_bidang")
            )->whereNull('expired_date')->whereNotIn('id_kel_bidang',$bidang_buang)
            ->orderBy('nm_kel_bidang')->pluck('nm_bidang','id_kel_bidang')->toArray();
        return view('manajemen_matakuliah.peer_group.bidang',compact('data','kelompok_bidang','bidang'));
    }

    public function simpan_bidang(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $peer = PeerGroup::find($id);
        $data = new BidangPeer();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Bidang Peer Group '. $peer->nm_peer_group .' berhasil ditambahkan')->persistent('OK');
        return redirect()->back();
    }

    public function destroy_bidang($id,$id_bidang)
    {
        $id = Crypt::decrypt($id);
        $peer = PeerGroup::find($id);
        $id_bidang = Crypt::decrypt($id_bidang);
        $data = BidangPeer::findorfail($id_bidang);
        $data->drop();
        alert()->success('Bidang Peer Group '. $peer->nm_peer_group .' berhasil dihapuskan')->persistent('OK');
        return redirect()->back();
    }

    public function anggota($id)
    {
        $id_peer = Crypt::decrypt($id);
        $data = PeerGroup::findorfail($id_peer);
        $anggota = AngPeerGroup::where('id_peer_group',$data->id_peer_group)->where('soft_delete',0)
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
        return view('manajemen_matakuliah.peer_group.anggota',compact('data','kelompok_anggota','anggota'));
    }

    public function simpan_anggota(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $peer = PeerGroup::find($id);
        $data = new AngPeerGroup();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Anggota pada Peer Group '. $peer->nm_peer_group .' berhasil ditambahkan')->persistent('OK');
        return redirect()->back();
    }

    public function destroy_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = PeerGroup::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngPeerGroup::findorfail($id_anggota);
        $data->drop();
        alert()->success('Anggota pada Peer Group '. $peer->nm_peer_group .' berhasil dihapuskan')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_ketua_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = PeerGroup::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngPeerGroup::findorfail($id_anggota);
        $input = [];
        $input['_method']='PUT';
        if($data->a_ketua==0) {
            $input['a_ketua'] = 1;
            $input['pesan'] = 'Anggota Peer Group '.$data->nm_peer_group.' berhasil diubah menjadi ketua';
        } else {
            $input['a_ketua'] = 0;
            $input['pesan'] = 'Anggota Peer Group '.$data->nm_peer_group.' berhasil diubah menjadi anggota';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }

    public function ubah_status_anggota($id,$id_anggota)
    {
        $id = Crypt::decrypt($id);
        $peer = PeerGroup::find($id);
        $id_anggota = Crypt::decrypt($id_anggota);
        $data = AngPeerGroup::findorfail($id_anggota);
        $input = [];
        $input['_method']='PUT';
        if($data->a_aktif==0) {
            $input['a_aktif'] = 1;
            $input['pesan'] = 'Anggota Peer Group '.$data->nm_peer_group.' berhasil diubah menjadi aktif';
        } else {
            $input['a_aktif'] = 0;
            $input['pesan'] = 'Anggota Peer Group '.$data->nm_peer_group.' berhasil diubah menjadi tidak aktif';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }
}
