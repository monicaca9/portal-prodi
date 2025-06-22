@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <form action="{{ route('administrasi.surat_masih_kuliah.add') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Ajukan Administrasi -
                    {{ $profile->nm_pd . ' (' . $profile->nim . ')' }}</h3>
            </div>
            <div class="card-body">
                {!! FormInputText('name', 'Nama Lengkap', 'text', $data->name, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('student_number', 'NPM', 'number', $data->student_number, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('department', 'Jurusan', 'text', $data->department, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('study_program', 'Program Studi', 'text', $data->study_program, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('academic_year', 'Tahun Akademik', 'text', $currentAcademicYear, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('semester', 'Semester', 'text', $data->semester, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                
                {!! FormInputText('phone_number', 'Nomor Whatsapp', 'number', $data->phone_number, ['required' => true]) !!}
                {!! FormInputText('address', 'Alamat', 'text', $data->address, ['required' => true]) !!}
                {!! FormInputText('purpose', 'Keperluan', 'text', $data->purpose, ['required' => true]) !!}
                {!! FormInputText('parent_name', 'Nama Wali', 'text', $data->parent_name, ['required' => true]) !!}
                <div class="form-group row">
    <label for="parent_grade" class="col-sm-2 col-form-label">
        NIP <span style="color:red;">*</span><br>
        <span style="font-size: 0.8em; color: #888;">
            <em>(Isi - jika tidak ada)</em>
        </span>
    </label>
    <div class="col-sm-10">
        <input 
            type="text" 
            name="parent_nip" 
            id="parent_nip" 
            class="form-control{{ $errors->has('parent_nip') ? ' is-invalid' : '' }}" 
            value="{{ old('parent_nip', $data->parent_nip) }}" 
            placeholder="NIP" 
            required>
        @if ($errors->has('parent_nip'))
            <div class="invalid-feedback">
                {{ $errors->first('parent_nip') }}
            </div>
        @endif
    </div>
</div>


                <div class="form-group row">
    <label for="parent_grade" class="col-sm-2 col-form-label">
        Pangkat/Gol. <span style="color:red;">*</span><br>
        <span style="font-size: 0.8em; color: #888;">
            <em>(Isi - jika tidak ada)</em>
        </span>
    </label>
    <div class="col-sm-10">
        <input 
            type="text" 
            name="parent_grade" 
            id="parent_grade" 
            class="form-control{{ $errors->has('parent_grade') ? ' is-invalid' : '' }}" 
            value="{{ old('parent_grade', $data->parent_grade) }}" 
            placeholder="Pangkat/Gol." 
            required>
        @if ($errors->has('parent_grade'))
            <div class="invalid-feedback">
                {{ $errors->first('parent_grade') }}
            </div>
        @endif
    </div>
</div>
                {!! FormInputText('parent_job', 'Pekerjaan', 'text', $data->parent_job, ['required' => true]) !!}
                {!! FormInputText('parent_institution', 'Instansi/Tempat Kerja', 'text', $data->parent_institution, ['required' => true]) !!}
                {!! FormInputText('parent_address', 'Alamat Wali', 'text', $data->parent_address, ['required' => true]) !!}
                <div class="form-group row">
                    <label for="signature-pad" class="col-sm-2 col-form-label">
                        Tanda Tangan <span style="color:red;">*</span>
                    </label>
                <div class="col-sm-10">
                <!-- Canvas untuk menggambar tanda tangan -->
                <canvas id="signature-pad" width="400" height="200" style="border:1px solid #ccc;"></canvas>
                    <br>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="clearPad()">Bersihkan</button>

                <!-- Hidden input untuk menyimpan base64 -->
                <input type="hidden" name="signature" id="signature">
        
                <!-- Validasi error -->
                @if ($errors->has('signature'))
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('signature') }}
                    </div>
                @endif
            </div>
        </div>
        <script>
            const canvas = document.getElementById('signature-pad');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;

            canvas.addEventListener('mousedown', () => isDrawing = true);
            canvas.addEventListener('mouseup', () => {
                isDrawing = false;
                ctx.beginPath();
            });
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseleave', () => {
                isDrawing = false;
                ctx.beginPath();
            });

            function draw(e) {
                if (!isDrawing) return;
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#000';

                ctx.lineTo(x, y);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(x, y);
                }

                function clearPad() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById('signature').value = "";
                }

            // Saat form disubmit, isi input hidden dengan base64 dari canvas
            document.querySelector('form').addEventListener('submit', function () {
            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('signature').value = dataURL;
            });
        </script>
                <div class="form-group row">
                    <label for="supporting_document" class="col-sm-2 col-form-label">
                    Slip UKT Terakhir <span style="color:red;">*</span><br>
                    <span style="font-size: 0.8em; color: #888;">
                    <em>(Silakan unggah file dengan format .pdf)</em>
                    </span>
                     </label>
                    <div class="col-sm-10">
                        <input type="file" name="supporting_document" id="supporting_document" class="form-control"
                            accept="application/pdf" required>
                        @if ($errors->has('supporting_document'))
                            <div class="invalid-feedback">
                                {{ $errors->first('supporting_document') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="supporting_document2" class="col-sm-2 col-form-label">
                        KP4 Orang Tua (PNS aktif) atau<br>SK Pensiun (pensiun PNS) atau<br>Surat Keterangan Kerja Orang Tua (Swasta) <span style="color:red;">*</span><br>
                    <span style="font-size: 0.8em; color: #888;">
                    <em>(Silakan unggah file dengan format .pdf)</em>
                    </span>
                     </label>
                    <div class="col-sm-10">
                        <input type="file" name="supporting_document2" id="supporting_document2" class="form-control"
                            accept="application/pdf" required>
                        @if ($errors->has('supporting_document2'))
                            <div class="invalid-feedback">
                                {{ $errors->first('supporting_document2') }}
                            </div>
                        @endif
                    </div>
                </div>
                {!! FormInputSelect(
                    'academic_advisor',
                    'Pilih Dosen PA',
                    true,
                    true,
                    $academicAdvisors,
                    $data->academic_advisor,
                ) !!}
            </div>

            <div class="card-footer">
                <a href="{{ route('administrasi.surat_aktif_kuliah') }}" class="btn btn-default btn-flat"><i
                        class="fa fa-arrow-left"></i> Kembali</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>

        {{-- Supaya readonly gabisa di klik --}}
    @push('css')
<style>
    input.no-click {
        pointer-events: none !important;
        user-select: none !important;
        background-color: #e9ecef !important; /* Abu-abu terang */
        border-color: #ced4da !important;
        color: #495057 !important;
        cursor: default !important;
        box-shadow: none !important;
    }

    input.no-click:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    /* Hilangkan panah angka di input number */
    input.no-click[type="number"]::-webkit-inner-spin-button,
    input.no-click[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endpush
@endsection