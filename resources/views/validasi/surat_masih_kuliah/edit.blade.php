@extends('template.default')

@section('content')
    @php
        $showContentHeader = false;
        $numberLetterExists = isset($numberLetter);
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

    <form action="{{ route('validasi.surat_masih_kuliah.update', ['id' => Crypt::encrypt($data->id)]) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="container-fluid pt-2 py-4">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="m-0 text-dark">Validasi Pengajuan Surat Keterangan Masih Kuliah</h3>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane"></i> VALIDASI PENGAJUAN </h3>
            </div>
            <div class="card-body">
                <div class="card-body">
                <div class="mb-4">
    <h5><i class="fas fa-user-graduate mb-3"></i> Detail Mahasiswa</h5>
    <table class="table table-bordered table-sm">
        <tbody>
            <tr>
                <th style="width: 30%;">Nama Lengkap</th>
                <td>{{ $data->name }}</td>
            </tr>
            <tr>
                <th>NPM</th>
                <td>{{ $data->student_number }}</td>
            </tr>
            <tr>
                <th>Jurusan</th>
                <td>{{ $data->department }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $data->study_program }}</td>
            </tr>
            <tr>
                <th>Tahun Akademik</th>
                <td>{{ $data->academic_year }}</td>
            </tr>
            <tr>
                <th>Semester</th>
                <td>{{ $data->semester }}</td>
            </tr>
            <tr>
                <th>Nomor Whatsapp</th>
                <td>{{ $data->phone_number }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $data->address }}</td>
            </tr>
            <tr>
                <th>Keperluan</th>
                <td>{{ $data->purpose }}</td>
            </tr>
            <tr>
                <th>Nama Wali</th>
                <td>{{ $data->parent_name }}</td>
            </tr>
            <tr>
                <th>NIP</th>
                <td>{{ $data->parent_nip }}</td>
            </tr>
            <tr>
                <th>Pangkat/Gol.</th>
                <td>{{ $data->parent_grade }}</td>
            </tr>
            <tr>
                <th>Instansi/Tempat Kerja</th>
                <td>{{ $data->parent_institution }}</td>
            </tr>
            <tr>
                <th>Alamat Wali</th>
                <td>{{ $data->parent_address }}</td>
            </tr>
            <tr>
                <th>Lihat Surat</th>
                <td>
                    <a href="{{ route('validasi.surat_masih_kuliah.preview-pdf', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="btn btn-xs btn-primary"
                    target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
    <td>
    <div class="card mt-4">
    <div class="card-body">
        <label class="d-block"><strong>Pilih status </strong><span style="color:red;">*</span></label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="status_disetujui" value="disetujui"
            {{ old('status', $data->adminValidation->status ?? '') == 'disetujui' ? 'checked' : '' }}
            {{ $isDisabled ? 'disabled' : '' }} required>
            <label class="form-check-label" for="status_disetujui">Disetujui</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="status_ditolak" value="ditolak"
                {{ old('status', $data->adminValidation->status ?? '') == 'ditolak' ? 'checked' : '' }}
                {{ $isDisabled ? 'disabled' : '' }}>
            <label class="form-check-label" for="status_ditolak">Ditolak</label>
        </div>
    </div>
</div>
</td>

<td>
    <div class="mb-2">
        <label for="notes" class="d-block"><strong>Komentar</strong></label>
        <textarea name="notes" id="notes" class="form-control form-control-sm mt-1 @error('notes') is-invalid @enderror" rows="3"
    placeholder="Tulis komentar di sini..." {{ $isDisabled ? 'disabled' : '' }}>{{ old('notes') }}</textarea>

@error('notes')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

    </div>
</td>

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
                                {{ isset($data->numberLetter) || $hasAdminValidation ? 'disabled' : '' }}>
                            @if (isset($data->numberLetter))
                                <input type="hidden" name="letter_number[number]"
                                    value="{{ $data->numberLetter->number }}">
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
                <a href="{{ route('validasi.surat_masih_kuliah', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-flat" {{ $isDisabled ? 'disabled' : '' }}><i
                            class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
    </form>
@endsection
