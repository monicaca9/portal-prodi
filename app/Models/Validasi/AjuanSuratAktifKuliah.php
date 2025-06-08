<?php

namespace App\Models\Validasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuanSuratAktifKuliah extends Model
{
    use HasFactory;

    protected $table = 'validasi.ajuan_surataktif'; // Ganti dengan nama table di database
    protected $primaryKey = 'id_pengajuan'; // Sesuaikan jika primary key-nya bukan 'id'
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengajuan',
        'id_pd',
        'nama',
        'npm',
        'jurusan',
        'program_studi',
        'semester',
        'tahun_akademik',
        'no_wa',
        'alamat',
        'keperluan',
        'ttd',
        'dosen_pa',
        'id_creator',
    ];
}
