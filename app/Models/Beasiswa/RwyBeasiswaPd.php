<?php

namespace App\Models\Beasiswa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RwyBeasiswaPd extends Model
{
    public static function InfoBeasiswaPd($id_pd)
    {
        $data = DB::SELECT("
            SELECT
                trwy.id_rwy_beasiswa_pd,
                trwy.sk_beasiswa,
                trwy.tgl_terima,
                trwy.tgl_selesai,
                trwy.a_msh_terima,
                trwy.status_beasiswa,
                tper.nm_periode_beasiswa,
                tjns_bea.nm_jns_beasiswa
            FROM beasiswa.rwy_beasiswa_pd AS trwy
            JOIN beasiswa.periode_beasiswa AS tper ON tper.id_periode_beasiswa = trwy.id_periode_beasiswa AND tper.soft_delete=0
            JOIN ref.jenis_beasiswa AS tjns_bea ON tjns_bea.id_jns_beasiswa = tper.id_jns_beasiswa
            WHERE trwy.soft_delete=0
                AND trwy.id_pd = '".$id_pd."'
        ");
        return collect($data)->first();
    }
}
