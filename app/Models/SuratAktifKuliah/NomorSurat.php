<?php

namespace App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Pdrd\Sdm;

class NomorSurat extends Model
{
    use HasFactory;

    protected $table = 'surat_aktif.no_surat';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tahun',
        'nomor',
        'kode',
        'id_creator',        // created_by
        'tgl_create',        // created_at
        'last_update',        
        'updated_at',
        'updated_by',
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];
    const CREATED_AT = 'tgl_create';
    // Fungsi ini bisa dipanggil dari luar tanpa bikin objek (karena static), dan dia butuh:
    // $year = tahun (contoh: 2025)
    // $code = kode surat (contoh: UN26.15.07/KM)
    // $initialNumber = nomor manual yang diketik user (boleh kosong)
    public static function generateNextLetterNumber($tahun, $kode, $initialNumber = null)
    {
        // Cari di database surat terakhir yang punya tahun dan kode yang sama, lalu ambil nomor yang paling besar (terakhir dipakai)
        // Contoh:
        // 2025	UN26.15.07/KM	5
        // 2025	UN26.15.07/KM	6 ← ini yang diambil (desc)
        $lastLetterNumber = self::where('tahun', $tahun)
            ->where('kode', $kode)
            ->orderBy('nomor', 'desc')
            ->first();

        // Kalau user ngisi sendiri nomornya, langsung pakai itu
        if ($initialNumber !== null && $initialNumber !== '') {
            $nextNumber = (int) $initialNumber;
        // Kalau belum ada nomor surat sebelumnya, mulai dari 1
        } elseif (!$lastLetterNumber) {
            $nextNumber = 1;
        // Kalau ada surat sebelumnya, ambil nomornya + 1
        } else {
            $nextNumber = $lastLetterNumber->nomor + 1;
        }

        // Balikin data nomor surat yang dibuat
        return [
            'id' => Str::uuid(),
            'tahun' => $tahun,
            'nomor' => $nextNumber,
            'kode' => $kode,
        ];
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id_creator = Auth::check()
                ? getIDUser()
                : (request()->get('id_akun') ?? Str::uuid());

            $model->tgl_create = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::check()
                ? getIDUser()
                : (request()->get('id_akun') ?? Str::uuid());

            $model->last_update = now();
            $model->last_sync = now();
        });
    }

}
