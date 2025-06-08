<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Pdrd\RegPtk;

class Sdm extends AbstractionModel
{
    protected $table='pdrd.sdm';
    protected $primaryKey='id_sdm';
    public $incrementing = false;
    public function list_dosen($id_sms=null)
    {
        $query = "
            SELECT
                tsdm.id_sdm,
                tsdm.nm_sdm,
                tsdm.nidn,
                tsdm.nip,
                ikat.nm_ikatan_kerja,
                tpeg.nm_stat_pegawai,
                taktif.nm_stat_aktif,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_prodi,
                tsdm.last_sync
            FROM pdrd.sdm AS tsdm
            JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm
                AND tr.soft_delete=0
                AND tr.id_jns_keluar IS NULL
                AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar <=NOW())
            JOIN ref.ikatan_kerja_sdm AS ikat ON ikat.id_ikatan_kerja = tr.id_ikatan_kerja
            JOIN pdrd.keaktifan_ptk AS taptk ON taptk.id_reg_ptk = tr.id_reg_ptk
                AND taptk.soft_delete=0
                AND taptk.a_sp_homebase=1
                AND taptk.id_thn_ajaran=".get_tahun_keaktifan()."
            JOIN ref.status_kepegawaian AS tpeg ON tpeg.id_stat_pegawai = tr.id_stat_pegawai
            JOIN ref.status_keaktifan_pegawai AS taktif ON taktif.id_stat_aktif = tsdm.id_stat_aktif
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            WHERE tsdm.soft_delete=0
            AND tsdm.id_jns_sdm=12
        ";
        if (in_array(session()->get('login.peran.id_peran'),[6, 3005, 46, 3000])) {
            $query .= " AND tprodi.id_sms = '".session()->get('login.peran.id_organisasi')."'";
        } elseif (in_array(session()->get('login.peran.id_peran'),[106])) {
            $query .= " AND tprodi.id_induk_sms = '".session()->get('login.peran.id_organisasi')."'";
        }
        $query .= ' ORDER BY tprodi.nm_lemb ASC, tsdm.nm_sdm ASC';
        return DB::SELECT($query);
    }

    public function get_data_user(){
        $data = RegPtk::where('id_sdm', auth()->user()->id_sdm_pengguna)
        ->first();

        return $data;
    }

    public function detail_dosen()
    {   
        $data = collect(DB::SELECT("
        SELECT
            tsdm.id_sdm,
            tsdm.nm_sdm,
            tsdm.nidn,
            tsdm.id_blob,
            tsdm.nip,
            tr.id_reg_ptk,
            ikat.nm_ikatan_kerja,
            tpeg.nm_stat_pegawai,
            taktif.nm_stat_aktif,
            CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_prodi,
            tsdm.last_sync
        FROM pdrd.sdm AS tsdm
        JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm
            AND tr.soft_delete=0
            AND tr.id_jns_keluar IS NULL
            AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar <=NOW())
        JOIN ref.ikatan_kerja_sdm AS ikat ON ikat.id_ikatan_kerja = tr.id_ikatan_kerja
        JOIN pdrd.keaktifan_ptk AS taptk ON taptk.id_reg_ptk = tr.id_reg_ptk
            AND taptk.soft_delete=0
            AND taptk.a_sp_homebase=1
            AND taptk.id_thn_ajaran=".get_tahun_keaktifan()."
        JOIN ref.status_kepegawaian AS tpeg ON tpeg.id_stat_pegawai = tr.id_stat_pegawai
        JOIN ref.status_keaktifan_pegawai AS taktif ON taktif.id_stat_aktif = tsdm.id_stat_aktif
        JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms
        JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
        WHERE tsdm.soft_delete=0
        AND tsdm.id_jns_sdm=12
        AND tsdm.id_sdm = '".auth()->user()->id_sdm_pengguna."'
    "));
       
        return collect($data)->first();
    }
    public function reg_ptk()
        {
            return $this->hasMany('App\Models\Pdrd\RegPtk','id_sdm','id_sdm');
        }
}
