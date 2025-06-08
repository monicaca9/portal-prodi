<?php

namespace App\Models\ManAkses;

use Illuminate\Database\Eloquent\Model;
use App\Models\AbstractionModel;
use Illuminate\Support\Facades\DB;

class Pengguna extends AbstractionModel
{
    protected $table = 'man_akses.pengguna';
    protected $primaryKey = 'id_pengguna';

    public $incrementing = false;

    public static function daftar_pengguna()
    {
        $data = DB::SELECT("
            SELECT
                tpeng.id_pengguna,
                tpeng.username,
                tpeng.nm_pengguna,
                tpeng.approval_pengguna,
                tpeng.a_aktif,
                trole.id_peran,
                tperan.nm_peran,
                tunit.nm_lemb
            FROM man_akses.pengguna AS tpeng
            JOIN man_akses.role_pengguna AS trole ON trole.id_pengguna=tpeng.id_pengguna AND trole.soft_delete=0
            JOIN man_akses.peran AS tperan ON tperan.id_peran=trole.id_peran AND tperan.expired_date IS NULL
            JOIN man_akses.unit_organisasi AS tunit ON tunit.id_organisasi=trole.id_organisasi AND tunit.soft_delete=0
            WHERE tpeng.soft_delete=0 AND tperan.id_peran NOT IN (3005)
            ORDER BY tpeng.username ASC
        ");
        return $data;
    }
}
