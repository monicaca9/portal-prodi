@extends('template.default')
@include('__partial.datatable_class')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-alt"></i> Detail Ajuan</h3>
            <div class="card-tools">
                <a href="{{ route('administrasi.surat_masih_kuliah.history') }}"
                    class="btn btn-flat btn-secondary btn-sm ml-2">
                    <i class="fas fa-history"></i> Riwayat Ajuan
                </a>
                {!! buttonAdd('administrasi.surat_masih_kuliah.add', 'Tambah Ajuan') !!}

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-data">
                    <thead>
                        <tr>
                            <th>Jenis Administrasi</th>
                            <th>Dokumen Pendukung</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> Surat Aktif Kuliah</td>
                            <td> 1. Surat Keterangan Aktif Kuliah<br>
                                2. Surat Permohonan Pengantar Aktif Kuliah<br>
                                3. Surat Permohonan Pembuatan Surat Keterangan Aktif Kuliah<br>
                                4. Slip UKT Terakhir<br>
                                5. KP4 Orang Tua (PNS aktif) / SK Pensiun (pensiun PNS) / Surat Keterangan Kerja Orang Tua (Swasta)
                            </td>
                            @php
                                $rejectedNotes = collect([
                                    $data->adminValidation,
                                    $data->advisorSignature,
                                    $data->headOfProgramSignature,
                                    $data->headOfDepartmentSignature,
                                ])
                                    ->filter(function ($signature) {
                                        return $signature && $signature->status === 'ditolak';
                                    })
                                    ->pluck('notes')
                                    ->filter()
                                    ->all();
                            @endphp

                            <td>
                                {{ ucfirst($data->status) }}
                                <br>

                                @if (count($rejectedNotes) > 0)
                                    Alasan:
                                    <ul style="padding-left: 20px; margin: 0;">
                                        @foreach ($rejectedNotes as $note)
                                            <li>{{ $note }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('administrasi.surat_masih_kuliah.preview', ['id' => Crypt::encrypt($data->id)]) }}"
                                    class="btn btn-primary">
                                    Lihat
                                </a>
                            </td>

                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('administrasi.surat_masih_kuliah') }}" class="btn btn-default btn-flat"><i
                    class="fa fa-arrow-left"></i> Kembali</a>

        </div>
    </div>
    @push('css')
        <link href="{{ asset('bower_components/datatables/media/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
        <style>
            div.dataTables_filter {
                display: none !important;
            }

            div.dataTables_length {
                display: none !important;
            }

            .dataTables_info,
            .dataTables_paginate {
                display: none !important;
            }
        </style>
    @endpush
@endsection