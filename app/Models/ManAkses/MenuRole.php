<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MenuRole extends AbstractionModel
{
    protected $table = 'man_akses.menu_role';
    protected $primaryKey = 'id_menu';

    public $incrementing = false;

    public static function menu_role_list($id_peran)
    {
        $data = DB::SELECT("
                SELECT
                    tm.id_menu,
                    tm.id_aplikasi,
                    tm.nm_menu,
                    tm.nm_file,
                    tm.urutan_menu,
                    tm.a_aktif,
                    tm.a_tampil,
                    tm.icon,
                    tm.level_menu,
                    mr.a_boleh_insert,
                    mr.a_boleh_delete,
                    mr.a_boleh_update,
                    mr.a_boleh_sanggah,
                    mr.tgl_create,
                    mr.last_update
                FROM man_akses.menu AS tm
                JOIN man_akses.menu_role AS mr ON mr.id_menu=tm.id_menu AND mr.soft_delete=0
                    AND mr.id_peran=?
                WHERE tm.expired_date IS NULL
                ORDER BY tm.a_tampil DESC, tm.level_menu ASC, tm.urutan_menu ASC
            ",[$id_peran]);
        return $data;
    }

    public function peran()
    {
        return $this->belongsTo('App\Models\ManAkses\Peran','id_peran','id_peran');
    }
}
