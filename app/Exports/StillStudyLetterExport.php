<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StillStudyLetterExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $stillStudyLetters;
    protected $index = 0;

    public function __construct($stillStudyLetters)
    {
        $this->stillStudyLetters = $stillStudyLetters;
    }

    public function collection()
    {
        return $this->stillStudyLetters;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NPM',
            'Jenis Administrasi',
            'Tanggal Ajuan',
            'Tanggal Selesai',
            'Umur Ajuan',
            'Status',
            'Alasan',
        ];
    }

    public function map($letter): array
    {
        $this->index++;

        $rejectedNotes = collect([
            $letter->adminValidation,
            $letter->advisorSignature,
            $letter->headOfProgramSignature,
            $letter->headOfDepartmentSignature,
        ])
        ->filter(fn($signature) => $signature && $signature->status === 'ditolak')
        ->pluck('notes')
        ->filter()
        ->implode('; ');

        return [
            $this->index,
            $letter->name,
            $letter->student_number,
            'Surat Aktif Kuliah',
            $letter->tgl_create ? \Carbon\Carbon::parse($letter->tgl_create)->format('d-m-Y') : '-',
            $letter->status_updated_at instanceof \Carbon\Carbon
                ? $letter->status_updated_at->format('d-m-Y')
                : (is_string($letter->status_updated_at) || is_null($letter->status_updated_at)
                    ? '-'
                    : \Carbon\Carbon::parse($letter->status_updated_at)->format('d-m-Y')),
            $letter->time_diff,
            ucfirst($letter->status),
            $rejectedNotes,
        ];
    }
}