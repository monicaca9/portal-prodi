@extends('template.default')

@section('content')
    @php
        $showContentHeader = false;
        $letterNumberExists = isset($letterNumber);
        $hasAdminValidation = !empty($data->admin_validation_id);
        $userRole = session()->get('login.peran.id_peran');

    $role = session()->get('login.peran.id_peran');
    $isDisabled = match ($role) {
        6 => !empty($data->admin_validation_id),
        46 => !empty($data->advisor_signature_id),
        3000 => !empty($data->head_of_program_signature_id),
        3001 => !empty($data->head_of_department_signature_id),
        default => true,
    };


    @endphp

    <form action="{{ route('validasi.surat_aktif_kuliah.update', ['id' => Crypt::encrypt($data->id)]) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="container-fluid pt-2 py-4">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0 text-dark">Validasi Pengajuan Surat Keterangan Aktif Kuliah</h3>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane"></i> VALIDASI PENGAJUAN </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Dokumen Pendukung</th>
                                <th class="text-center">Komentar</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data->name }} ({{ $data->student_number }})</td>
                                <td>
                                    1. Surat Keterangan Aktif Kuliah<br>
                                    2. Surat Permohonan Pengantar Aktif Kuliah<br>
                                    3. Surat Permohonan Pembuatan Surat Keterangan Aktif Kuliah<br>
                                    4. Slip UKT Terakhir
                                </td>

                                <td>
                                    <textarea name="notes" class="form-control form-control-sm" {{ $isDisabled ? 'disabled' : '' }}>{{ old('notes') }}</textarea>
                                </td>
                                <td>
                                    <select name="status" class="form-control form-control-sm" {{ $isDisabled ? 'disabled' : '' }}>
                                        <option value="disetujui" {{ old('status', $data->adminValidation->status ?? '') == 'disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="ditolak" {{ old('status', $data->adminValidation->status ?? '') == 'ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            @if ($userRole == 6)
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-pencil-alt"></i> PENOMORAN SURAT</h3>
                </div>
                <div class="card-body">
                    {{-- Pesan Error no surat sudah digunakan --}}
                    @if (session('error'))
                    <div class="alert alert-danger">
                    {{ session('error') }}
                    </div>
                    @endif

                    <div class="form-group row">
                        <label for="letter_number" class="col-sm-3 col-form-label">Nomor Urut Surat</label>
                        <div class="col-sm-9">
                            <input type="text" name="letter_number[number]" id="letter_number" class="form-control"
                                value="{{ old('letter_number.number', $nextNumber) }}"
                                {{ isset($data->letterNumber) || $hasAdminValidation ? 'disabled' : '' }}>
                            @if (isset($data->letterNumber))
                                <input type="hidden" name="letter_number[number]"
                                    value="{{ $data->letterNumber->number }}">
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="document_code" class="col-sm-3 col-form-label">Kode Dokumen</label>
                        <div class="col-sm-9">
                            <input type="text" id="document_code" class="form-control"
                                value="{{ $code }}/{{ $year }}" disabled>
                            <input type="hidden" name="letter_number[code]" value="{{ $code }}">
                        </div>
                    </div>
                </div>
            @endif

            <div class="card-footer">
                <a href="{{ route('validasi.surat_aktif_kuliah', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-flat" {{ $isDisabled ? 'disabled' : '' }}><i
                            class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
    </form>
@endsection
