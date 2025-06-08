<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gedung extends AbstractionModel
{
    protected $table = 'manajemen.gedung';
    protected $primaryKey = 'id_gedung';

    public $incrementing = false;

    public static function get_list_gedung()
    {
        return DB::table('manajemen.gedung AS g')
            ->join('pdrd.sms AS tsms','tsms.id_sms','=','g.id_sms')
            ->leftJoin(DB::RAW("(
                SELECT id_jenj_didik, nm_jenj_didik FROM ref.jenjang_pendidikan
                WHERE expired_date IS NULL
            ) AS j"),'j.id_jenj_didik','=','tsms.id_jenj_didik')
            ->join('ref.jenis_sms AS js','js.id_jns_sms','=','tsms.id_jns_sms')
            ->select('id_gedung',
                DB::RAW("(
                    CASE WHEN j.id_jenj_didik IS NULL
                        THEN CONCAT(g.nm_gedung,' - ',js.nm_jns_sms,' ',tsms.nm_lemb)
                        ELSE CONCAT(g.nm_gedung,' - ',js.nm_jns_sms,' ',tsms.nm_lemb,' (',j.nm_jenj_didik,')')
                    END
                ) AS nm_gedung")
            )
            ->where('g.soft_delete',0)
            ->orderBy('tsms.nm_lemb','ASC')
            ->orderBy('g.nm_gedung','ASC')
            ->pluck('nm_gedung','id_gedung')
            ->toArray()
            ;
    }
    public function ruang()
    {
        return $this->hasMany('App\Models\Manajemen\Ruang',$this->primaryKey,$this->primaryKey);
    }

    public function fakultas()
    {
        return $this->belongsTo('App\Models\Pdrd\Sms','id_sms','id_sms');
    }
}
