<?php

namespace App\Models\ManLab;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Laboratorium extends AbstractionModel
{
    use HasFactory;
    protected $table = 'man_lab.laboratorium';
    protected $primaryKey = 'id_lab';

    public $incrementing = false;

    public static function getAllLaboratorium()
    {
        $query = "
            SELECT
                ml.id_lab,
                ml.id_sms,
                CASE WHEN jp.id_jenj_didik IS NOT NULL AND tsms.id_jenj_didik!=99 THEN
                    CONCAT(tjs.nm_jns_sms,' ',tsms.nm_lemb,' (',jp.nm_jenj_didik,')')
                    ELSE CONCAT(tjs.nm_jns_sms,' ',tsms.nm_lemb)
                END AS nm_lemb,
                mg.alamat_gedung,
                mg.nm_gedung,
                ml.nm_lab,
                ml.nm_singkat_lab,
                ml.ket
            FROM man_lab.laboratorium AS ml
            JOIN pdrd.sms AS tsms ON tsms.id_sms=ml.id_sms
            JOIN ref.jenis_sms AS tjs ON tjs.id_jns_sms=tsms.id_jns_sms
            LEFT JOIN ref.jenjang_pendidikan AS jp ON jp.id_jenj_didik=tsms.id_jenj_didik
                AND jp.expired_date IS NULL
            JOIN manajemen.gedung AS mg ON mg.id_gedung=ml.id_gedung AND mg.soft_delete=0
            WHERE ml.soft_delete=0
        ";
        return DB::SELECT($query);
    }
}
