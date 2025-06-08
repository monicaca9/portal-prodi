<?php

namespace App\Http\Controllers\Validasi;

use Illuminate\Http\Request;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Validasi\AjuanDraftUsul;
use App\Models\Validasi\VerAjuanDraftUsul;
use Illuminate\Support\Facades\Crypt;

class PengajuanDraftUsulController extends Controller
{
    /**
     * Display a listing of the resource.
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

        $data = DB::SELECT("
            SELECT
                valid.id_ver_ajuan,
                ajuan.id_ajuan_draft_usul,
                ajuan.stat_ajuan,
                ajuan.id_jns_seminar,
                tr.nim,
                pd.nm_pd,
                pd.id_pd,
                ajuan.wkt_ajuan,
                valid.wkt_selesai_ver,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan
            FROM validasi.ajuan_draft_usul AS ajuan
            JOIN validasi.ver_ajuan_draft_usul AS valid ON valid.id_ajuan_draft_usul = ajuan.id_ajuan_draft_usul
            JOIN pdrd.peserta_didik AS pd ON pd.id_pd = ajuan.id_pd AND pd.soft_delete=0
            JOIN pdrd.reg_pd AS tr ON tr.id_pd = pd.id_pd AND tr.soft_delete=0
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE ajuan.soft_delete=0 
            AND tprodi.id_sms = '" . session()->get('login.peran.id_organisasi') . "'
            AND ajuan.stat_ajuan=" . $status_validasi . "
            AND valid.status_periksa IN ('" . implode("','", $status_periksa) . "')
            ORDER BY valid.last_update
        ");
        return view('validasi.draft_usul.index', compact('data', 'status', 'status_periksa', 'status_validasi'));
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
        $id_ver_ajuan = Crypt::decrypt($id);
        $ajuan = VerAjuanDraftUsul::find($id_ver_ajuan);
        //dd($ajuan);
        $data = $ajuan->ajuanDraftUsul;
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);

        if (is_null($ajuan->wkt_mulai_ver)) {
            $ajuan->id_role_pengguna  = session()->get('login.peran.id_role_pengguna');
            $ajuan->wkt_mulai_ver     = currDateTime();
            $ajuan->last_update       = currDateTime();
            $ajuan->id_updater        = getIDUser();
            $ajuan->save();
        }

        //dd($data);
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_draft_usul, list.id_dok_ajuan_draft_usul, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_draft_usul AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok 
            WHERE list.soft_delete=0
            AND list.id_ajuan_draft_usul = '" . $data->id_ajuan_draft_usul . "'
        ");

        return view('validasi.draft_usul.detail', compact('data', 'ajuan', 'profil', 'dokumen'));
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
    public function update(Request $request, $id, PesertaDidik $pesertaDidik)
    {
        $id_ver_ajuan =  Crypt::decrypt($id);
        $ajuan = VerAjuanDraftUsul::find($id_ver_ajuan);
        $data = AjuanDraftUsul::find($ajuan->id_ajuan_draft_usul);
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);
        $input = $request->all();

        if ($input['status_validasi'] != 'N') {
            if ($input['status_validasi'] == 'Y') {
                $stat_ajuan = 2;
            } elseif (in_array($input['status_validasi'], ['T', 'C'])) {
                $stat_ajuan = 3;
            } elseif ($input['status_validasi'] == 'L') {
                $stat_ajuan = 4;
            }
            $input['status_periksa'] = $input['status_validasi'];
            $input['wkt_selesai_ver'] = currDateTime();
            $input['stat_ajuan_sesudah'] = $stat_ajuan;
            unset($input['stat_validasi']);
            $data->fill($data->prepare([
                '_method'       => 'PUT',
                'stat_ajuan'    => $stat_ajuan,
                'wkt_update'    => currDateTime(),
                'updater_id'    => getIDUser()
            ]))->save();

            $ajuan->fill($ajuan->prepare($input))->save();
            alert()->success('Berhasil mengubah status pengajuan draft usul penelitian')->persistent('OK');

        }
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
