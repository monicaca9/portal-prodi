<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JenisSeminar extends AbstractionModel
{
    protected $table = 'ref.jenis_seminar';
    protected $primaryKey = 'id_jns_seminar';

    public function induk_jenis()
    {
        return $this->belongsTo('App\Models\Ref\JenisSeminar','id_induk_jns_seminar','id_jns_seminar');
    }

    public static function jenis_seminar_prodi($id_prodi)
    {
        $query="
            SELECT
                induk_jns.id_jns_seminar,
                induk_jns.nm_jns_seminar
            FROM kpta.seminar_prodi AS seminar
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = seminar.id_jns_seminar AND jns.expired_date IS NULL
            JOIN ref.jenis_seminar AS induk_jns ON induk_jns.id_jns_seminar = jns.id_induk_jns_seminar AND induk_jns.expired_date IS NULL
            WHERE seminar.soft_delete=0
        ";
        if (!is_null($id_prodi)) {
            $query .= " AND seminar.id_sms='".$id_prodi."'";
        }
        $query.= " GROUP BY induk_jns.id_jns_seminar, induk_jns.nm_jns_seminar
            ORDER BY induk_jns.a_tugas_akhir ASC";

        return DB::SELECT($query);
    }
}
