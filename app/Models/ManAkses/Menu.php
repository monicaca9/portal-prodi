<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends AbstractionModel
{
    protected $table = 'man_akses.menu';
    protected $primaryKey = 'id_menu';

    public $incrementing = false;

    public static function generateMenu($id_peran) {
        $list_menu = [];
        $menu_lvl_1 = DB::select("
            SELECT m.id_menu, m.nm_menu, m.nm_file, CASE WHEN m.icon IS NOT NULL THEN m.icon ELSE 'fas fa-circle' END AS icon_menu
            FROM man_akses.menu_role AS mr
            JOIN man_akses.menu AS m ON mr.id_menu = m.id_menu
            WHERE mr.id_peran = '".$id_peran."'
            AND m.a_aktif = 1
            AND m.a_tampil = 1
            AND m.id_group_menu IS NULL
            ORDER BY m.urutan_menu ASC
        ");

        foreach($menu_lvl_1 AS $m1) {
            $menu_lvl_2 = DB::select("
                SELECT m.id_menu, m.nm_menu, m.nm_file, CASE WHEN m.icon IS NOT NULL THEN m.icon ELSE 'fas fa-circle' END AS icon_menu FROM man_akses.menu_role AS mr
                JOIN man_akses.menu AS m ON mr.id_menu = m.id_menu
                WHERE mr.id_peran = '".$id_peran."'
                AND m.a_aktif = 1
                AND m.a_tampil = 1
                AND m.id_group_menu = '".$m1->id_menu."'
                ORDER BY m.urutan_menu ASC
            ");
            if(!$menu_lvl_2) {
                $list_menu[$m1->nm_menu] = [false, $m1->nm_file, $m1->icon_menu];
            } else {
                $get_lvl2=[];
                foreach($menu_lvl_2 AS $m2) {
                    $get_lvl2[$m2->nm_menu] = [false, $m2->nm_file, $m2->icon_menu];
                }
                $list_menu[$m1->nm_menu] = [true, $get_lvl2, $m1->icon_menu];
            }
        }
        return $list_menu;
    }
}
