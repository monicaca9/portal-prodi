<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PeranSeminar extends AbstractionModel
{
    protected $table = 'kpta.peran_seminar';
    protected $primaryKey = 'id_peran_seminar';

    public $timestamps = false;

    public static function DataMahasiswa($id_sdm, $angkatan = null, $kategori = null, $inisial_peran = null, $urutan = null)
    {
        $query = "
        SELECT 
            peran.peran, 
            peran.urutan,
            peran.id_reg_pd,
            peran.id_jns_seminar,
            CONCAT(pd.nm_pd,' (',RTRIM(tr.nim),')') AS nm_pd,
            CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
            smt.id_thn_ajaran AS angkatan,
            seminar.nm_jns_seminar,
            CASE WHEN peran2.total_mahasiswa IS NULL THEN 0 ELSE peran2.total_mahasiswa END AS total_mahasiswa
        FROM kpta.peran_seminar AS peran
        JOIN ref.jenis_seminar AS seminar ON seminar.id_jns_seminar = peran.id_jns_seminar AND seminar.a_seminar=1
        JOIN pdrd.reg_pd AS tr ON tr.id_reg_pd = peran.id_reg_pd AND tr.soft_delete=0
        JOIN pdrd.sdm AS sdm ON sdm.id_sdm = peran.id_sdm 
        JOIN pdrd.peserta_didik AS pd ON pd.id_pd = tr.id_pd AND pd.soft_delete=0   
        JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0 AND tprodi.stat_prodi='A'
        JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
        JOIN ref.semester AS smt ON smt.id_smt = tr.id_smt  
        LEFT JOIN (
            SELECT peran_inner.id_sdm, COUNT(*) AS total_mahasiswa
            FROM kpta.peran_seminar AS peran_inner
            JOIN pdrd.reg_pd AS tr_inner ON tr_inner.id_reg_pd = peran_inner.id_reg_pd AND tr_inner.soft_delete=0
            JOIN ref.semester AS smt_inner ON smt_inner.id_smt = tr_inner.id_smt
            JOIN ref.jenis_seminar AS seminar_inner ON seminar_inner.id_jns_seminar = peran_inner.id_jns_seminar AND seminar_inner.a_seminar=1
            WHERE peran_inner.soft_delete=0
            AND peran_inner.a_aktif=1
            AND peran_inner.a_ganti=0
            AND peran_inner.id_sdm = '" . $id_sdm . "'
            ";

        if (!is_null($angkatan)) {
            $query .= " AND smt_inner.id_thn_ajaran = " . $angkatan;
        }
        if (!is_null($kategori)) {
            $query .= " AND seminar_inner.nm_jns_seminar = '" . $kategori . "'";
        }
        if (!is_null($inisial_peran)) {
            $query .= " AND peran_inner.peran = '" . $inisial_peran . "'";
        }
        if (!is_null($urutan)) {
            $query .= " AND peran_inner.urutan = '" . $urutan . "'";
        }

        $query .= "
        GROUP BY peran_inner.id_sdm
        ) AS peran2 ON peran2.id_sdm = peran.id_sdm 
        WHERE peran.soft_delete = 0
        AND peran.a_aktif = 1
        AND peran.a_ganti = 0
        ";

        if (!is_null($id_sdm)) {
            $query .= " AND peran.id_sdm='" . $id_sdm . "'";
        }
        $conditions = [];

        if (!is_null($angkatan)) {
            $conditions[] = "smt.id_thn_ajaran = " . $angkatan;
        }
        if (!is_null($kategori)) {
            $conditions[] = "seminar.nm_jns_seminar = '" . $kategori . "'";
        }
        if (!is_null($inisial_peran)) {
            $conditions[] = "peran.peran = '" . $inisial_peran . "'";
        }
        if (!is_null($urutan)) {
            $conditions[] = "peran.urutan = '" . $urutan . "'";
        }

        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY smt.id_thn_ajaran ASC, tr.nim ASC";

        return DB::SELECT($query);
    }

    public static function  DataPeranSeminar($id_sdm)
    {
        $query = "
        SELECT 
            peran.id_sdm, 
            peran.peran, 
            peran.urutan
        FROM kpta.peran_seminar AS peran
        WHERE peran.soft_delete =0
        AND peran.a_aktif = 1
        AND peran.a_ganti = 0
        AND peran.id_sdm = '" . $id_sdm . "'
        GROUP BY peran.id_sdm, peran.peran,peran.urutan
        ";
        return DB::SELECT($query);
    }

    public static function DataMahasiswaSeminar($id_sdm, $jenis_seminar)
    {
        $query = "
        SELECT 
            sdm.nm_sdm, 
            pd.nm_pd,
            tseminar.id_jns_seminar,
            peran.peran,
            peran.urutan,
            jns_seminar.nm_jns_seminar,
            daftar_seminar.id_seminar_prodi,
            daftar_seminar.id_daftar_seminar,
            daftar_seminar.hari,
            daftar_seminar.waktu,
            daftar_seminar.tgl_mulai,
            pd.nm_pd,
            tr.nim,
            CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
            smt.id_thn_ajaran AS angkatan
        FROM kpta.peran_seminar AS peran
        JOIN kpta.pendaftaran_seminar AS daftar_seminar ON daftar_seminar.id_reg_pd = peran.id_reg_pd
        JOIN kpta.seminar_prodi AS tseminar ON tseminar.id_seminar_prodi = daftar_seminar.id_seminar_prodi
        JOIN ref.jenis_seminar AS jns_seminar ON jns_seminar.id_jns_seminar = tseminar.id_jns_seminar AND jns_seminar.a_seminar=1
        LEFT JOIN ref.jenis_seminar AS induk_seminar ON jns_seminar.id_induk_jns_seminar = induk_seminar.id_jns_seminar  
        JOIN pdrd.reg_pd AS tr ON tr.id_reg_pd = peran.id_reg_pd AND tr.soft_delete=0
        JOIN pdrd.sdm AS sdm ON sdm.id_sdm = peran.id_sdm 
        JOIN pdrd.peserta_didik AS pd ON pd.id_pd = tr.id_pd AND pd.soft_delete=0   
        JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0 AND tprodi.stat_prodi='A'
        JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
        JOIN ref.semester AS smt ON smt.id_smt = tr.id_smt  
        AND (
            peran.id_jns_seminar = jns_seminar.id_jns_seminar 
            OR peran.id_jns_seminar = jns_seminar.id_induk_jns_seminar
        )
    ";
        if (!is_null($id_sdm)) {
            $query .= " AND peran.id_sdm='" . $id_sdm . "'";
        }
        $conditions = [];

        if (!is_null($jenis_seminar)) {
            $conditions[] = "jns_seminar.nm_jns_seminar = '" . $jenis_seminar . "'";
        }

        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        $query .= " 
        WHERE peran.soft_delete = 0
        AND peran.a_aktif=1
        AND peran.a_ganti =0
        ORDER BY daftar_seminar.tgl_mulai DESC";
        return DB::SELECT($query);
    }
}
