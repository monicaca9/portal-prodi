<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PesertaDidik extends AbstractionModel
{
    protected $table = 'pdrd.peserta_didik';
    protected $primaryKey = 'id_pd';
    // protected $keyType ='string';

    public $timestamps = false;
    public $incrementing = false;

    public function list_mahasiswa()
    {
        //
    }

    public function register_pd()
    {
        return $this->belongsTo('App\Models\Pdrd\RegPd','id_pd','id_pd');
    }

    public function status_mahasiswa()
    {
        return $this->belongsTo('App\Models\Ref\StatusMahasiswa','id_stat_mhs','id_stat_mhs');
    }

    public function prodi()
    {
        return $this->belongsTo('App\Models\Pdrd\Sms','id_sms','id_sms');
    }

    public function cari_daftar_akun_mahasiswa($nim)
    {
        $data = DB::SELECT("
            SELECT
                pd.id_pd,
                pd.nm_pd,
                pd.jk,
                pd.id_stat_mhs,
                stat_pd.nm_stat_mhs,
                trpd.nim,
                trpd.id_sms,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS prodi,
                tpeng.id_pengguna,
                tpeng.username,
                tpeng.last_active,
                tpeng.a_aktif,
                tpeng.approval_peran,
                tkeluar.ket_keluar,
                t8.ips,
                t8.ipk,
                t8.sks_semester,
                t8.total_sks,
                smt.nm_smt
            FROM pdrd.peserta_didik AS pd
            JOIN ref.status_mahasiswa AS stat_pd ON stat_pd.id_stat_mhs = pd.id_stat_mhs
            JOIN pdrd.reg_pd AS trpd ON trpd.id_pd = pd.id_pd AND trpd.soft_delete=0
            LEFT JOIN (
                SELECT t01.id_reg_pd, t01.id_smt, t01.ips, t01.ipk, t01.sks_semester, t01.total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
                WHERE soft_delete=0
            ) AS t8 ON t8.id_reg_pd = trpd.id_reg_pd
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN (
                SELECT
                    t1.id_pengguna, username, id_pd_pengguna AS id_pd, last_active, a_aktif, approval_peran
                FROM man_akses.pengguna AS t1
                JOIN man_akses.role_pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                WHERE t1.soft_delete=0
            ) AS tpeng ON tpeng.id_pd = pd.id_pd
            LEFT JOIN ref.jenis_keluar AS tkeluar ON tkeluar.id_jns_keluar=trpd.id_jns_keluar
            LEFT JOIN ref.semester AS smt ON smt.id_smt = t8.id_smt
            WHERE pd.soft_delete=0
            AND trpd.nim LIKE '%".$nim."%'
            ORDER BY trpd.nim ASC, pd.nm_pd ASC
        ");
        return $data;
    }

    public function cari_mahasiswa_daftar_akun($nim)
    {
        $data = collect(DB::SELECT("
            SELECT
                pd.id_pd,
                pd.nm_pd,
                pd.jk,
                pd.id_stat_mhs,
                stat_pd.nm_stat_mhs,
                trpd.nim,
                trpd.id_sms,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS prodi,
                tpeng.id_pengguna,
                tpeng.username,
                tpeng.last_active,
                tpeng.a_aktif,
                tpeng.approval_peran,
                tkeluar.ket_keluar,
                t8.ips,
                t8.ipk,
                t8.sks_semester,
                t8.total_sks,
                smt.nm_smt
            FROM pdrd.peserta_didik AS pd
            JOIN ref.status_mahasiswa AS stat_pd ON stat_pd.id_stat_mhs = pd.id_stat_mhs
            JOIN pdrd.reg_pd AS trpd ON trpd.id_pd = pd.id_pd AND trpd.soft_delete=0
            LEFT JOIN (
                SELECT t01.id_reg_pd, t01.id_smt, t01.ips, t01.ipk, t01.sks_semester, t01.total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
                WHERE soft_delete=0
            ) AS t8 ON t8.id_reg_pd = trpd.id_reg_pd
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN (
                SELECT
                    t1.id_pengguna, username, id_pd_pengguna AS id_pd, last_active, a_aktif, approval_peran
                FROM man_akses.pengguna AS t1
                JOIN man_akses.role_pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                WHERE t1.soft_delete=0
            ) AS tpeng ON tpeng.id_pd = pd.id_pd
            LEFT JOIN ref.jenis_keluar AS tkeluar ON tkeluar.id_jns_keluar=trpd.id_jns_keluar
            LEFT JOIN ref.semester AS smt ON smt.id_smt = t8.id_smt
            WHERE pd.soft_delete=0
            AND trpd.nim = '".$nim."'
            ORDER BY trpd.nim ASC, pd.nm_pd ASC
        "))->first();
        return $data;
    }

    public function detail_mahasiswa($id_pd)
    {
        $data = DB::SELECT("
            SELECT
                pd.id_pd,
                pd.nm_pd,
                pd.jk,
                pd.tgl_lahir,
                stat_pd.nm_stat_mhs,
                trpd.nim,
                trpd.id_sms,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS prodi,
                tpeng.id_pengguna,
                trpd.id_reg_pd,
                tpeng.username,
                tpeng.last_active,
                tpeng.a_aktif,
                tpeng.approval_peran,
                tkeluar.ket_keluar,
                t8.ips,
                t8.ipk,
                t8.sks_semester,
                t8.total_sks,
                smt.nm_smt,
                smt_angkatan.id_thn_ajaran AS angkatan
            FROM pdrd.peserta_didik AS pd
            JOIN pdrd.reg_pd AS trpd ON trpd.id_pd = pd.id_pd AND trpd.soft_delete=0
            LEFT JOIN (
                SELECT t01.id_reg_pd, t01.id_smt, t01.ips, t01.ipk, t01.sks_semester, t01.total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
                WHERE soft_delete=0
            ) AS t8 ON t8.id_reg_pd = trpd.id_reg_pd
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            JOIN ref.status_mahasiswa AS stat_pd ON stat_pd.id_stat_mhs = (CASE WHEN t8.id_stat_mhs IS NULL THEN pd.id_stat_mhs ELSE t8.id_stat_mhs END)
            LEFT JOIN ref.semester AS smt ON smt.id_smt = t8.id_smt
            LEFT JOIN ref.semester AS smt_angkatan ON smt_angkatan.id_smt = trpd.id_smt
            LEFT JOIN (
                SELECT
                    t1.id_pengguna, username, id_pd_pengguna AS id_pd, last_active, a_aktif, approval_peran
                FROM man_akses.pengguna AS t1
                JOIN man_akses.role_pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                WHERE t1.soft_delete=0
            ) AS tpeng ON tpeng.id_pd = pd.id_pd
            LEFT JOIN ref.jenis_keluar AS tkeluar ON tkeluar.id_jns_keluar=trpd.id_jns_keluar
            WHERE pd.soft_delete=0
            AND pd.id_pd = '".$id_pd."'
            ORDER BY trpd.nim ASC, pd.nm_pd ASC
        ");
        return collect($data)->first();
    }

    public function id_detail_mahasiswa($id_pd)
    {
        $data = DB::SELECT("
            SELECT
                pd.id_pd,
                pd.nm_pd,
                pd.jk,
                pd.id_blob,
                pd.tmpt_lahir,
                pd.tgl_lahir,
                trpd.id_reg_pd,
                trpd.id_sp,
                trpd.id_sms,
                trpd.nim,
                tprodi.id_jenj_didik,
                UPPER(tprodi.nm_lemb) AS nm_prodi,
                UPPER(tprodi_induk.nm_lemb) AS fakultas,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS prodi,
                pd.id_stat_mhs,
                pd.tlpn_hp,
                stat_pd.nm_stat_mhs,
                tpeng.id_pengguna,
                tpeng.username,
                tpeng.last_active,
                tpeng.a_aktif,
                tpeng.approval_peran,
                tkeluar.ket_keluar,
                t8.ips,
                t8.ipk,
                t8.sks_semester,
                t8.total_sks,
                smt.nm_smt
            FROM pdrd.peserta_didik AS pd
            JOIN pdrd.reg_pd AS trpd ON trpd.id_pd = pd.id_pd AND trpd.soft_delete=0
            LEFT JOIN (
                SELECT t01.id_smt, t01.id_reg_pd, t01.ips, t01.ipk, t01.sks_semester, t01.total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
                WHERE soft_delete=0
            ) AS t8 ON t8.id_reg_pd = trpd.id_reg_pd
            JOIN ref.status_mahasiswa AS stat_pd ON stat_pd.id_stat_mhs = (CASE WHEN t8.id_stat_mhs IS NULL THEN pd.id_stat_mhs ELSE t8.id_stat_mhs END)
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms
            JOIN pdrd.sms AS tprodi_induk ON tprodi_induk.id_sms = tprodi.id_induk_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN (
                SELECT
                    t1.id_pengguna, username, id_pd_pengguna AS id_pd, last_active, a_aktif, approval_peran
                FROM man_akses.pengguna AS t1
                JOIN man_akses.role_pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                WHERE t1.soft_delete=0
            ) AS tpeng ON tpeng.id_pd = pd.id_pd
            LEFT JOIN ref.jenis_keluar AS tkeluar ON tkeluar.id_jns_keluar=trpd.id_jns_keluar
            LEFT JOIN ref.semester AS smt ON smt.id_smt = t8.id_smt
            WHERE pd.soft_delete=0
            AND pd.id_pd = '".$id_pd."'
            ORDER BY trpd.nim ASC, pd.nm_pd ASC
        ");
        return collect($data)->first();
    }

    
    public static function detailPD()
    {
        $data = DB::SELECT("
            SELECT
                pd.id_pd,
                pd.id_blob,
                pd.nm_pd,
                pd.nisn,
                pd.nik,
                CASE WHEN pd.jk='L' THEN 'Laki-laki' ELSE 'Perempuan' END jenis_kelamin,
                pd.tmpt_lahir,
                pd.tgl_lahir,
                pd.a_terima_kps,
                pd.no_kps,
                pd.id_agama,
                tagama.nm_agama,
                kk_ayah.nm_kk AS nm_kk_ayah,
                kk_ibu.nm_kk AS nm_kk_ibu,
                kk.nm_kk,
                transport.nm_alat_transport,
                pd.id_jns_tinggal,
                tinggal.nm_jns_tinggal,
                pd.jln,
                pd.rt,
                pd.rw,
                pd.nm_dsn,
                pd.ds_kel,
                pd.kode_pos,
                pd.tlpn_rumah,
                pd.tlpn_hp,
                pd.id_wil,
                prov.nm_wil,
                pd.id_kewarganegaraan,
                tnegara.nm_negara,
                pd.nm_wali,
                pd.tgl_lahir_wali,
                pd.id_pekerjaan_wali,
                pekerjaan_wali.nm_pekerjaan AS nm_pekerjaan_wali,
                pd.id_penghasilan_wali,
                penghasilan_wali.nm_penghasilan AS nm_penghasilan_wali,
                pd.id_pendidikan_wali,
                jenjang_pendidikan_wali.nm_jenj_didik AS nm_jenj_didik_wali,
                pd.nm_ayah,
                pd.nik_ayah,
                pd.tgl_lahir_ayah,
                pd.id_pekerjaan_ayah,
                pekerjaan_ayah.nm_pekerjaan AS nm_pekerjaan_ayah,
                pd.id_penghasilan_ayah,
                penghasilan_ayah.nm_penghasilan AS nm_penghasilan_ayah,
                pd.id_pendidikan_ayah,
                jenjang_pendidikan_ayah.nm_jenj_didik AS nm_jenj_didik_ayah,
                pd.nm_ibu_kandung,
                pd.nik_ibu,
                pd.tgl_lahir_ibu,
                pd.id_pekerjaan_ibu,
                pekerjaan_ibu.nm_pekerjaan AS nm_pekerjaan_ibu,
                pd.id_penghasilan_ibu,
                penghasilan_ibu.nm_penghasilan AS nm_penghasilan_ibu,
                pd.id_pendidikan_ibu,
                jenjang_pendidikan_ibu.nm_jenj_didik AS nm_jenj_didik_ibu,
                lo.mime_type,
                lo.blob_content,
                trpd.nim,
                trpd.id_sms,
                tsp.nm_lemb AS pt,
                CONCAT(tprodi.nm_lemb,' (',tjenj_prodi.nm_jenj_didik,')') AS prodi,
                tfak.nm_lemb AS fakultas,
                biaya.nm_pembiayaan,
                jns_dftr.nm_jns_daftar,
                dftr.nm_jalur_daftar,
                stat_mhs.nm_stat_mhs,
                smt.id_thn_ajaran AS angkatan,
                trpd.tgl_daftar AS tgl_masuk,
                konsentrasi.id_konsentrasi_prodi
            FROM pdrd.peserta_didik AS pd
            JOIN pdrd.reg_pd AS trpd ON trpd.id_pd = pd.id_pd AND trpd.soft_delete=0
            JOIN ref.status_mahasiswa AS stat_mhs ON stat_mhs.id_stat_mhs = pd.id_stat_mhs
            JOIN pdrd.satuan_pendidikan AS tsp ON tsp.id_sp = trpd.id_sp
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj_prodi ON tjenj_prodi.id_jenj_didik = tprodi.id_jenj_didik
            LEFT JOIN pdrd.sms AS tfak ON tfak.id_sms = tprodi.id_induk_sms
            JOIN ref.jalur_daftar AS dftr ON dftr.id_jalur_daftar = trpd.id_jalur_daftar
            JOIN ref.jenis_pendaftaran AS jns_dftr ON jns_dftr.id_jns_daftar = trpd.id_jns_daftar
            JOIN ref.pembiayaan AS biaya ON biaya.id_pembiayaan = trpd.id_pembiayaan
            JOIN ref.agama AS tagama ON tagama.id_agama = pd.id_agama
            JOIN ref.kebutuhan_khusus AS kk_ayah ON kk_ayah.id_kk = pd.id_kk_ayah
            JOIN ref.kebutuhan_khusus AS kk_ibu ON kk_ibu.id_kk = pd.id_kk_ibu
            LEFT JOIN ref.kebutuhan_khusus AS kk ON kk.id_kk = pd.id_kk
            JOIN ref.alat_transportasi AS transport ON transport.id_alat_transport = pd.id_alat_transport
            JOIN ref.jenis_tinggal AS tinggal ON tinggal.id_jns_tinggal = pd.id_jns_tinggal
            JOIN ref.wilayah AS prov ON prov.id_wil = pd.id_wil
            JOIN ref.negara AS tnegara ON tnegara.id_negara = pd.id_kewarganegaraan
            JOIN ref.pekerjaan AS pekerjaan_ayah ON pekerjaan_ayah.id_pekerjaan = pd.id_pekerjaan_ayah
            JOIN ref.pekerjaan AS pekerjaan_ibu ON pekerjaan_ibu.id_pekerjaan = pd.id_pekerjaan_ibu
            LEFT JOIN ref.pekerjaan AS pekerjaan_wali ON pekerjaan_wali.id_pekerjaan = pd.id_pekerjaan_wali
            JOIN ref.penghasilan AS penghasilan_ayah ON penghasilan_ayah.id_penghasilan = pd.id_penghasilan_ayah
            JOIN ref.penghasilan AS penghasilan_ibu ON penghasilan_ibu.id_penghasilan = pd.id_penghasilan_ibu
            LEFT JOIN ref.penghasilan AS penghasilan_wali ON penghasilan_wali.id_penghasilan = pd.id_penghasilan_wali
            LEFT JOIN ref.jenjang_pendidikan AS jenjang_pendidikan_ayah ON jenjang_pendidikan_ayah.id_jenj_didik = pd.id_pendidikan_ayah
            LEFT JOIN ref.jenjang_pendidikan AS jenjang_pendidikan_ibu ON jenjang_pendidikan_ibu.id_jenj_didik = pd.id_pendidikan_ibu
            LEFT JOIN ref.jenjang_pendidikan AS jenjang_pendidikan_wali ON jenjang_pendidikan_wali.id_jenj_didik = pd.id_pendidikan_wali
            LEFT JOIN dok.large_object AS lo ON lo.id_blob = pd.id_blob
            JOIN ref.semester AS smt ON smt.id_smt = trpd.id_smt
            LEFT JOIN kpta.konsentrasi_prodi_pd AS konsentrasi ON konsentrasi.id_pd = pd.id_pd
            WHERE pd.id_pd='".auth()->user()->id_pd_pengguna."'
        ");
        return collect($data)->first();
    }

    public static function ListPesertaDidikProdi($id_prodi,$angkatan=null)
    {
        $query = "
            SELECT
                pd.id_pd,
                CONCAT(pd.nm_pd,' (',RTRIM(tr.nim),')') AS nm_pd,
                pd.id_stat_mhs,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                tr.id_reg_pd,
                tr.id_jns_keluar,
                tr.id_smt,
                smt.id_thn_ajaran AS angkatan,
                stat_pd.nm_stat_mhs,
                tkeluar.ket_keluar,
                t8.ips,
                t8.ipk,
                t8.sks_semester,
                t8.total_sks,
                CASE WHEN pd.id_stat_mhs='N' THEN CASE WHEN tr.id_jns_keluar IS NULL THEN 'Non Aktif' ELSE tkeluar.ket_keluar END ELSE stat_pd.nm_stat_mhs END AS status_terbaru
            FROM pdrd.peserta_didik AS pd
            JOIN pdrd.reg_pd AS tr ON tr.id_pd = pd.id_pd AND tr.soft_delete=0
            LEFT JOIN (
                SELECT t01.id_smt, t01.id_reg_pd, t01.ips, t01.ipk, t01.sks_semester, t01.total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
                WHERE soft_delete=0
            ) AS t8 ON t8.id_reg_pd = tr.id_reg_pd
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0 AND tprodi.stat_prodi='A'
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            JOIN ref.semester AS smt ON smt.id_smt = tr.id_smt
            LEFT JOIN ref.jenis_keluar AS tkeluar ON tkeluar.id_jns_keluar=tr.id_jns_keluar
            JOIN ref.status_mahasiswa AS stat_pd ON stat_pd.id_stat_mhs = (CASE WHEN t8.id_stat_mhs IS NULL THEN pd.id_stat_mhs ELSE t8.id_stat_mhs END)
            WHERE pd.soft_delete=0
        ";
        if (!is_null($id_prodi)) {
            $query .= " AND tprodi.id_sms='".$id_prodi."'";
        }
        if (!is_null($angkatan)) {
            $query .= "AND smt.id_thn_ajaran = ".$angkatan."";
        } else {
            $query .= "AND smt.id_thn_ajaran >= ".(get_tahun_keaktifan()-7)."";

        }
        $query .= " ORDER BY smt.id_thn_ajaran ASC, tr.nim ASC";
        return DB::SELECT($query);
    }
}
