<?php

namespace App\Models\SuratMasihKuliah;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NomorSurat extends Model
{
    use HasFactory;

    protected $table = 'surat_masih.no_surat';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tahun',
        'nomor',
        'kode',
         // Kolom baru sesuai standar lama:
        'id_creator',        // id_creator
        'tgl_create',        // tgl_create
        'last_update',        
        'updated_at',
        'updated_by',
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];
    const CREATED_AT = 'tgl_create';

    public static function generateNextLetterNumber($tahun, $kode, $initialNumber = null)
    {
        $lastLetterNumber = self::where('tahun', $tahun)
            ->where('kode', $kode)
            ->orderBy('nomor', 'desc')
            ->first();

        if ($initialNumber !== null && $initialNumber !== '') {
            $nextNumber = (int) $initialNumber;
        } elseif (!$lastLetterNumber) {
            $nextNumber = 1;
        } else {
            $nextNumber = $lastLetterNumber->nomor + 1;
        }

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
