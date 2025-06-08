<?php

namespace App\Models\SuratMasihKuliah;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NumberLetter extends Model
{
    use HasFactory;

    protected $table = 'surat_masih.number_letter';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'year',
        'number',
        'code',
         // Kolom baru sesuai standar lama:
         'created_by',        // id_creator
         'created_at',        // tgl_create
         'updated_by',        // id_updater
         'updated_at',        // last_update
         'soft_delete',       // soft_delete
         'last_sync',         // last_sync
    ];

    public static function generateNextLetterNumber($year, $code, $initialNumber = null)
    {
        $lastLetterNumber = self::where('year', $year)
            ->where('code', $code)
            ->orderBy('number', 'desc')
            ->first();

        if ($initialNumber !== null && $initialNumber !== '') {
            $nextNumber = (int) $initialNumber;
        } elseif (!$lastLetterNumber) {
            $nextNumber = 1;
        } else {
            $nextNumber = $lastLetterNumber->number + 1;
        }

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
