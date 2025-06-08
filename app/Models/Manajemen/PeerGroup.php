<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PeerGroup extends AbstractionModel
{
    protected $table = 'manajemen.peer_group';
    protected $primaryKey = 'id_peer_group';

    public $incrementing = false;

    public static function semuaData()
    {
        $prodi = DB::table('pdrd.sms')->where('id_sms',session()->get('login.peran.id_organisasi'))->first();
        $query = "
            SELECT
                p.id_peer_group, p.nm_peer_group, p.a_aktif, bidang.nm_bidang, anggota.nm_anggota,
                CONCAT(tp.nm_lemb,' (',tj.nm_jenj_didik,')') AS nm_prodi
            FROM manajemen.peer_group AS p
            JOIN pdrd.sms AS tp ON tp.id_sms = p.id_sms AND tp.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tj ON tj.id_jenj_didik = tp.id_jenj_didik
            LEFT JOIN (
                SELECT b.id_peer_group, string_agg(b.nm_kel_bidang,'; ') AS nm_bidang FROM(
                    SELECT bp.id_peer_group, kb.nm_kel_bidang FROM manajemen.bidang_peer AS bp
                    JOIN ref.kelompok_bidang AS kb ON kb.id_kel_bidang = bp.id_kel_bidang
                    WHERE bp.soft_delete=0
                    ORDER BY kb.nm_kel_bidang ASC
                ) AS b
                GROUP BY b.id_peer_group
            ) AS bidang ON bidang.id_peer_group = p.id_peer_group
            LEFT JOIN (
                SELECT c.id_peer_group, string_agg(c.nm_dosen,'; ') AS nm_anggota FROM(
                    SELECT ap.id_peer_group, CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')',(
                        CASE WHEN ap.a_ketua=1 THEN ' (Ketua)' END
                    )) AS nm_dosen FROM manajemen.ang_peer_group AS ap
                    JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = ap.id_sdm
                    WHERE ap.soft_delete=0
                    ORDER BY ap.a_ketua DESC
                ) AS c
                GROUP BY c.id_peer_group
            ) AS anggota ON anggota.id_peer_group = p.id_peer_group
            WHERE p.soft_delete=0
        ";
        if (!is_null($prodi)) {
            $query .= " AND tp.id_sms='".$prodi->id_sms."'";
        }
        $query.=" ORDER BY p.nm_peer_group ASC";
        return DB::SELECT($query);
    }
}
