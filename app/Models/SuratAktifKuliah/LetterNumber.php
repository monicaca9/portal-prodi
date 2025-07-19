<?php

namespace App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Pdrd\Sdm;

class LetterNumber extends Model
{
    use HasFactory;

    protected $table = 'surat_aktif.letter_number';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'year',
        'number',
        'code',
         'created_by',        // id_creator
         'created_at',        // tgl_create
         'updated_by',        // id_updater
         'updated_at',        // last_update
         'soft_delete',       // soft_delete
         'last_sync',         // last_sync
    ];

    // Fungsi ini bisa dipanggil dari luar tanpa bikin objek (karena static), dan dia butuh:
    // $year = tahun (contoh: 2025)
    // $code = kode surat (contoh: UN26.15.07/KM)
    // $initialNumber = nomor manual yang diketik user (boleh kosong)
    public static function generateNextLetterNumber($year, $code, $initialNumber = null)
    {
        // Cari di database surat terakhir yang punya tahun dan kode yang sama, lalu ambil nomor yang paling besar (terakhir dipakai)
        // Contoh:
        // 2025	UN26.15.07/KM	5
        // 2025	UN26.15.07/KM	6 ← ini yang diambil (desc)
        $lastLetterNumber = self::where('year', $year)
            ->where('code', $code)
            ->orderBy('number', 'desc')
            ->first();

        // Kalau user ngisi sendiri nomornya, langsung pakai itu
        if ($initialNumber !== null && $initialNumber !== '') {
            $nextNumber = (int) $initialNumber;
        // Kalau belum ada nomor surat sebelumnya, mulai dari 1
        } elseif (!$lastLetterNumber) {
            $nextNumber = 1;
        // Kalau ada surat sebelumnya, ambil nomornya + 1
        } else {
            $nextNumber = $lastLetterNumber->number + 1;
        }

        // Balikin data nomor surat yang dibuat
        return [
            'id' => Str::uuid(),
            'year' => $year,
            'number' => $nextNumber,
            'code' => $code,
        ];
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::check()
                ? getIDUser()
                : (request()->get('id_akun') ?? Str::uuid());

            $model->created_at = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::check()
                ? getIDUser()
                : (request()->get('id_akun') ?? Str::uuid());

            $model->updated_at = now();
            $model->last_sync = now();
        });
    }

}
