@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <form action="{{ route('administrasi.surat_aktif_kuliah.tambah') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Ajukan Administrasi -
                    {{ $profile->nm_pd . ' (' . $profile->nim . ')' }}</h3>
            </div>
            <div class="card-body">
                {!! FormInputText('name', 'Nama Lengkap', 'text', $data->name, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('student_number', 'NPM', 'number', $data->student_number, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('department', 'Jurusan', 'text', $data->department, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('study_program', 'Program Studi', 'text', $data->study_program, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputSelect(
                    'academic_year',
                    'Pilih Tahun Akademik',
                    true,
                    true,
                    $academicYears,
                    $data->academic_year,
                ) !!}
                {!! FormInputText('semester', 'Semester', 'number', $data->semester, ['required' => true]) !!}
                {!! FormInputText('phone_number', 'Nomor Whatsapp', 'number', $data->phone_number, ['required' => true]) !!}
                {!! FormInputText('address', 'Alamat', 'text', $data->address, ['required' => true]) !!}
                {!! FormInputText('purpose', 'Keperluan', 'text', $data->purpose, ['required' => true]) !!}
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
            <span style="font-size: 0.8em; color: #555;">
                <em>(Silakan unggah file dengan format .pdf)</em>
            </span>
            </label>

                    <div class="col-sm-10">
                        <input type="file" name="supporting_document" id="supporting_document" class="form-control"
                            accept="image/png, image/jpeg, application/pdf" required>
                        @if ($errors->has('supporting_document'))
                            <div class="invalid-feedback">
                                {{ $errors->first('supporting_document') }}
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
@endsection