<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AvgSkorKategori extends AbstractionModel
{
    protected $table = 'kpta.avg_skor_kategori';
    protected $primaryKey = 'id_avg_skor_kategori';

    public static function DataNilai($id_seminar_prodi, $id_daftar_seminar, $peran_id){
        $query = "
        SELECT 
            list_kategori.id_list_kategori_nilai,
            list_komponen.id_list_komponen_nilai,
            skor_komponen.id_skor_komponen, 
            kategori.nm_kategori_nilai,
            komponen.nm_komponen_nilai, 
            avg_komponen.skor as rata_rata_komponen, 
            skor_komponen.skor as skor_komponen, 
            avg_komponen.id_avg_skor_komponen,
            avg_kategori.skor as nilai_akhir
        FROM kpta.list_kategori_nilai_seminar AS list_kategori
        JOIN kpta.kategori_nilai_seminar AS kategori ON kategori.id_kategori_nilai = list_kategori.id_kategori_nilai      
        LEFT JOIN kpta.list_komponen_nilai_seminar AS list_komponen ON list_komponen.id_list_kategori_nilai = list_kategori.id_list_kategori_nilai  
        JOIN kpta.komponen_nilai_seminar AS komponen ON komponen.id_komponen_nilai = list_komponen.id_komponen_nilai
        LEFT JOIN kpta.avg_skor_komponen AS avg_komponen ON avg_komponen.id_list_kategori_nilai = list_kategori.id_list_kategori_nilai
        LEFT JOIN kpta.skor_per_komponen AS skor_komponen ON skor_komponen.id_list_komponen_nilai = list_komponen.id_list_komponen_nilai
        LEFT JOIN kpta.avg_skor_kategori AS avg_kategori ON avg_kategori.id_peran_dosen_pendaftar = '" . $peran_id . "'
        WHERE list_kategori.id_seminar_prodi = '".$id_seminar_prodi."' 
        AND list_kategori.soft_delete = 0
        AND list_komponen.soft_delete = 0
        ";
        $conditions = [];

        if (!is_null($peran_id)) {
            $conditions[] = "avg_komponen.id_peran_dosen_pendaftar = '" . $peran_id . "' AND skor_komponen.id_peran_dosen_pendaftar =  '".$peran_id."'";
        }
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        return DB::SELECT($query);
    }
}
