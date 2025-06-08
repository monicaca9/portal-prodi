<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdiBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.prodi_beasiswa';
    protected $primaryKey = 'id_prodi_beasiswa';
    protected $keyType = 'string';

    public static function list_prodi($id_periode)
    {
        $data = DB::SELECT("
            SELECT
                tpb.id_prodi_beasiswa,
                concat(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_prodi,
                tpb.kuota_terima_beasiswa
            FROM beasiswa.prodi_beasiswa AS tpb
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tpb.id_sms
                AND tprodi.stat_prodi='A' AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE tpb.soft_delete=0
            AND tpb.id_periode_beasiswa='".$id_periode."'
            ORDER BY tjenj.id_jenj_didik ASC, tprodi.nm_lemb ASC
        ");
        return $data;
    }

    public static function list_form_prodi($id_periode,$id_jenjang,$edit=0,$id_prodi=null)
    {
        $query = "
            SELECT
                tprodi.id_sms,
                concat(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_prodi
            FROM pdrd.sms AS tprodi
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN beasiswa.prodi_beasiswa AS tpb ON tpb.id_sms = tprodi.id_sms
                AND tpb.soft_delete=0 AND tpb.id_periode_beasiswa='".$id_periode."'
            WHERE tprodi.stat_prodi='A' AND tprodi.soft_delete=0
                AND tprodi.id_jenj_didik='".$id_jenjang."'
        ";
        if ($edit==1) {
            $query .= " AND (tpb.id_prodi_beasiswa IS NULL OR tpb.id_prodi_beasiswa='".$id_prodi."') ";
        } else {
            $query .= " AND tpb.id_prodi_beasiswa IS NULL ";
        }
        $query .= ' ORDER BY tjenj.id_jenj_didik ASC, tprodi.nm_lemb ASC';
        return collect(DB::SELECT($query))->pluck('nm_prodi','id_sms')->toArray();
    }
}
