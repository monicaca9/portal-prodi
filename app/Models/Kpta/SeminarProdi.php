<?php

namespace App\Models\Kpta;

use App\Models\Pdrd\Sms;
use Illuminate\Database\Eloquent\Model;
use App\Models\AbstractionModel;
use Illuminate\Support\Facades\DB;

class SeminarProdi extends AbstractionModel
{
    protected $table = 'kpta.seminar_prodi';
    protected $primaryKey = 'id_seminar_prodi';

    public $incrementing = false;

    public static function data_semua_seminar_prodi()
    {
        $query = "
            SELECT
                seminar.id_seminar_prodi,
                jns.nm_jns_seminar,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS prodi,
                seminar.urutan,
                seminar.jmlh_pembimbing,
                seminar.jmlh_penguji,
                seminar.id_mk,
                seminar.a_aktif,
                CASE WHEN syarat.total_syarat IS NULL THEN 0 ELSE syarat.total_syarat END AS total_syarat,
                CASE WHEN kategori_nilai.total_kategori_nilai IS NULL THEN 0 ELSE kategori_nilai.total_kategori_nilai END AS total_kategori_nilai
            FROM kpta.seminar_prodi AS seminar
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = seminar.id_jns_seminar
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = seminar.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN (
                SELECT id_seminar_prodi, COUNT(*) AS total_syarat
                FROM kpta.list_syarat_seminar
                WHERE soft_delete=0
                GROUP BY id_seminar_prodi
            ) AS syarat ON syarat.id_seminar_prodi = seminar.id_seminar_prodi
            LEFT JOIN(
                SELECT id_seminar_prodi, COUNT(*) AS total_kategori_nilai
                FROM kpta.list_kategori_nilai_seminar
                WHERE soft_delete=0
                GROUP BY id_seminar_prodi
            ) AS kategori_nilai ON kategori_nilai.id_seminar_prodi = seminar.id_seminar_prodi
            WHERE seminar.soft_delete=0
        ";
        if (session()->get('login.peran.id_peran') == 6) {
            $query .= " AND seminar.id_sms='" . session()->get('login.peran.id_organisasi') . "'";
        }
        $query .= ' ORDER BY seminar.urutan ASC';
        return DB::SELECT($query);
    }

    public function jenisSeminar()
    {
        return $this->belongsTo('App\Models\Ref\JenisSeminar', 'id_jns_seminar', 'id_jns_seminar');
    }

    public function prodi()
    {
        return $this->belongsTo('App\Models\Pdrd\Sms', 'id_sms', 'id_sms');
    }

}
