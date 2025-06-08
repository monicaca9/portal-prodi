<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RolePengguna extends AbstractionModel
{
    protected $table = 'man_akses.role_pengguna';
    protected $primaryKey = 'id_role_pengguna';

    public static function list_peran_pengguna($id_pengguna)
    {
        $data = DB::SELECT("
            SELECT
                trole.id_role_pengguna, tperan.nm_peran,
                (CASE WHEN org.id_jns_lemb < 23 THEN org.nm_lemb
                    WHEN org.id_jns_lemb = 23 THEN CONCAT(jns_l.nm_jns_lemb,' ',org.nm_lemb)
                    ELSE CONCAT(jns_l.nm_jns_lemb,' ',org.nm_lemb,' (',tjenj.nm_jenj_didik,')') END) AS nm_lemb
            FROM man_akses.role_pengguna AS trole
            JOIN man_akses.peran AS tperan ON trole.id_peran = tperan.id_peran AND tperan.expired_date IS NULL
            JOIN man_akses.unit_organisasi AS org ON trole.id_organisasi = org.id_organisasi
            JOIN ref.jenis_lembaga AS jns_l ON jns_l.id_jns_lemb = org.id_jns_lemb
            LEFT JOIN pdrd.sms AS tsms ON tsms.id_sms = org.id_lembaga_asal
            LEFT JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tsms.id_jenj_didik
            WHERE trole.soft_delete=0
            AND trole.id_pengguna='".$id_pengguna."'
            ORDER BY tperan.nm_peran ASC
        ");
        return $data;
    }
}
