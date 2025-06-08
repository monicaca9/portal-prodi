<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PeriodeBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.periode_beasiswa';
    protected $primaryKey = 'id_periode_beasiswa';
    protected $keyType = 'string';

    public function Semester()
    {
        return $this->belongsTo('App\Models\Ref\Semester','id_smt','id_smt');
    }

    public function JenisBeasiswa()
    {
        return $this->belongsTo('App\Models\Ref\JenisBeasiswa','id_jns_beasiswa','id_jns_beasiswa');
    }

    public function JenjangPendidikan()
    {
        return $this->belongsTo('App\Models\Ref\JenjangPendidikan','id_jenj_didik','id_jenj_didik');
    }

    public static function EligiblePeriodeBeasiswa($id_jenjang, $id_prodi)
    {
        $data = DB::SELECT("
            SELECT
                tper.id_periode_beasiswa,
                tper.nm_periode_beasiswa,
                tper.ket_beasiswa,
                tper.wkt_mulai,
                tper.wkt_berakhir,
                tper.jmlh_terima,
                tper.a_aktif,
                tprodi.kuota_terima_beasiswa,
                CASE WHEN pendaftar.total_pendaftar IS NULL THEN 0 ELSE pendaftar.total_pendaftar END AS total_daftar
            FROM beasiswa.periode_beasiswa AS tper
            JOIN beasiswa.prodi_beasiswa AS tprodi ON tprodi.id_periode_beasiswa = tper.id_periode_beasiswa
                AND tprodi.soft_delete=0 AND tprodi.id_sms = '".$id_prodi."'
            LEFT JOIN (
                SELECT id_periode_beasiswa, COUNT(id_periode_beasiswa) AS total_pendaftar
                FROM beasiswa.pendaftar_beasiswa
                WHERE soft_delete=0 AND waktu_diajukan IS NOT NULL
                GROUP BY id_periode_beasiswa
            ) AS pendaftar ON pendaftar.id_periode_beasiswa = tper.id_periode_beasiswa
            WHERE tper.soft_delete=0 AND tper.id_jenj_didik = '".$id_jenjang."'
            AND (tper.wkt_mulai<=NOW() AND tper.wkt_berakhir>=NOW())
            ORDER BY tper.wkt_mulai ASC
        ");
        return $data;
    }
}
