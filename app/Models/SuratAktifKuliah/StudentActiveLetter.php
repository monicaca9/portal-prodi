<?php

namespace App\Models\SuratAktifKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AbstractionModel;
use App\Models\Pdrd\PesertaDidik;
use App\Models\SuratAktifKuliah\LetterSignature;

class StudentActiveLetter extends AbstractionModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_aktif.student_active_letter';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'student_number',
        'department',
        'study_program',
        'semester',
        'academic_year',
        'phone_number',
        'address',
        'purpose',
        'signature',
        'academic_advisor',
        'supporting_document',
        'status',
        'admin_validation_id',
        'advisor_signature_id',
        'head_of_program_signature_id',
        'head_of_department_signature_id',
        'letter_number',
        
        'created_by',        // id_creator
        'created_at',        // tgl_create
        'updated_by',        // id_updater
        'updated_at',        // last_update
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];

    const STATUS_PENDING = 'menunggu';
    const STATUS_APPROVED = 'disetujui';
    const STATUS_REJECTED = 'ditolak';
    const STATUS_PROCESSED = 'proses';
    const STATUS_DONE = 'selesai';

    public function createdBy()
    {
        return $this->belongsTo(PesertaDidik::class, 'created_by', 'id_pd');
    }

    public function letterNumber()
    {
        return $this->belongsTo(LetterNumber::class, 'letter_number', 'id');
    }


    // Ini bikin relasi hasOne antara tabel student_active_letter dan letter_signature.
    // Artinya: Satu surat punya satu tanda tangan validasi.
    // Dicari di tabel letter_signature yang submission_id sama dengan id surat, dan role harus 6 (kode role admin).
    public function adminValidation()
    {
        return $this->hasOne(LetterSignature::class, 'submission_id', 'id')
            ->where('role', 6);
    }

    public function advisorSignature()
    {
        return $this->hasOne(LetterSignature::class, 'submission_id', 'id')
            ->where('role', 46);
    }

    public function headOfProgramSignature()
    {
        return $this->hasOne(LetterSignature::class, 'submission_id', 'id')
            ->where('role', 3000);
    }

    public function headOfDepartmentSignature()
    {
        return $this->hasOne(LetterSignature::class, 'submission_id', 'id')
            ->where('role', 3001);
    }

    protected $appends = ['disable_validation_button'];

    public function getDisableValidationButtonAttribute()
    {
        return $this->attributes['disable_validation_button'] ?? false;
    }
    
    // dipake di controller validasi method update
    public function updateStatusBasedOnSignatures()
    {
        // Buat array $signatures yang isinya 4 data tanda tangan
        $signatures = [
            $this->adminValidation()->first(),
            $this->advisorSignature()->first(),
            $this->headOfProgramSignature()->first(),
            $this->headOfDepartmentSignature()->first(),
        ];

        // Buat 4 variabel kondisi awal
        // anggap semua tanda tangan disetujui
        $allApproved = true; 
        // cek kalau ada minimal 1 yang disetujui
        $anyApproved = false;
        // cek kalau ada minimal 1 yang ditolak
        $anyRejected = false;
        // cek kalau ada minimal 1 tanda tangan
        $hasSignature = false;

        // Lakukan perulangan untuk setiap tanda tangan
        foreach ($signatures as $signature) {
            if ($signature) {
                // Kalau tanda tangan ada (tidak null), tandai $hasSignature jadi true
                $hasSignature = true;

                if ($signature->status === self::STATUS_REJECTED) {
                // Kalau tanda tangan ditolak, tandai $anyRejected jadi true
                    $anyRejected = true;
                }

                if ($signature->status === self::STATUS_APPROVED) {
                // Kalau tanda tangan disetujui, tandai $anyApproved jadi true
                    $anyApproved = true;
                }

                if ($signature->status !== self::STATUS_APPROVED) {
                // Kalau tanda tangan tidak disetujui, tandai $allApproved jadi false
                    $allApproved = false;
                }
            } else {
                // Kalau tanda tangan null, tandai $allApproved jadi false
                $allApproved = false;
            }
        }

        //  Kalau ada minimal 1 ditolak, maka status surat jadi REJECTED
        if ($anyRejected) {
            $this->status = self::STATUS_REJECTED;
        } elseif ($hasSignature && $allApproved) {
        //  Kalau ada tanda tangan dan semua disetujui, status surat jadi DONE (selesai)
            $this->status = self::STATUS_DONE;
        } elseif ($anyApproved) {
        // Kalau ada minimal 1 disetujui (tapi belum semua), status jadi PROCESSED (sedang diproses)
            $this->status = self::STATUS_PROCESSED;
        } else {
        // Kalau belum ada tanda tangan atau masih menunggu, status tetap PENDING
            $this->status = self::STATUS_PENDING;
        }

        // Simpan perubahan status ke database
        $this->save();
    }


    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getTimeDiffAttribute()
    {
        if (!$this->created_at) return '-';

        $createdAt = \Carbon\Carbon::parse($this->created_at);
        $diffInSeconds = now()->diffInSeconds($createdAt);

        if ($diffInSeconds < 3600) {
            return now()->diffInMinutes($createdAt) . ' menit';
        } elseif ($diffInSeconds < 86400) {
            return now()->diffInHours($createdAt) . ' jam';
        } else {
            return now()->startOfDay()->diffInDays($createdAt->startOfDay()) . ' hari';
        }
    }

    public function getStatusUpdatedAtAttribute()
    {
        if ($this->status === self::STATUS_APPROVED || $this->status === self::STATUS_REJECTED) {
            return $this->updated_at ? \Carbon\Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : '-';
        }

        return '-';
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->created_by = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->created_at = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->updated_by = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->updated_at = now();
            $model->last_sync = now();
        });
    }
}
