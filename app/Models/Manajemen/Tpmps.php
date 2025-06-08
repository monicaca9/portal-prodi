<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tpmps extends AbstractionModel
{
    protected $table = 'manajemen.tpmps';
    protected $primaryKey = 'id_tpmps';

    public $incrementing = false;

    public static function semuaData()
    {
        $prodi = DB::table('pdrd.sms')->where('id_sms',session()->get('login.peran.id_organisasi'))->first();
        $query = "
            SELECT
                p.id_tpmps, p.no_sk, p.tgl_sk, p.id_dok, p.a_aktif, anggota.nm_anggota,
                s.nm_smt,
                CONCAT('TPMPS ',tp.nm_lemb,' (',tj.nm_jenj_didik,')') AS nm_prodi
            FROM manajemen.tpmps AS p
            JOIN pdrd.sms AS tp ON tp.id_sms = p.id_sms AND tp.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tj ON tj.id_jenj_didik = tp.id_jenj_didik
            JOIN ref.semester AS s ON s.id_smt = p.id_smt
            LEFT JOIN (
                SELECT c.id_tpmps, string_agg(c.nm_dosen,'; ') AS nm_anggota FROM(
                    SELECT ap.id_tpmps, CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')',(
                        CASE WHEN ap.a_ketua=1 THEN ' (Ketua)' END
                    )) AS nm_dosen FROM manajemen.ang_tpmps AS ap
                    JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = ap.id_sdm
                    WHERE ap.soft_delete=0
                    ORDER BY ap.a_ketua DESC
                ) AS c
                GROUP BY c.id_tpmps
            ) AS anggota ON anggota.id_tpmps = p.id_tpmps
            WHERE p.soft_delete=0
        ";
        if (!is_null($prodi)) {
            $query .= " AND tp.id_sms='".$prodi->id_sms."'";
        }
        $query.=" ORDER BY p.id_smt DESC";
        return DB::SELECT($query);
    }

    public function prodi()
    {
        return $this->belongsTo('App\Models\Pdrd\Sms','id_sms','id_sms');
    }

    public function smt()
    {
        return $this->belongsTo('App\Models\Ref\Semester','id_smt','id_smt');
    }
}
