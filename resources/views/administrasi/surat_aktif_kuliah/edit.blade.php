@extends('template.default')
@include('__partial.datatable')

@section('content')
    <div class="card">
        <form action="{{ route('administrasi.surat_aktif_kuliah.update', ['id' => Crypt::encrypt($data->id)]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Ubah Data Administrasi - {{ $data->name }}</h3>
            </div>
            <div class="card-body">
                {!! FormInputText('name', 'Nama Lengkap', 'text', $data->name, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('student_number', 'NPM', 'number', $data->student_number, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('department', 'Jurusan', 'text', $data->department, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('study_program', 'Program Studi', 'text', $data->study_program, ['required' => true, 'readonly' => true]) !!}
                {!! FormInputText('semester', 'Semester', 'number', $data->semester, ['required' => true]) !!}
                {!! FormInputText('academic_year', 'Tahun Akademik', 'text', $data->academic_year, ['required' => true]) !!}
                {!! FormInputText('phone_number', 'Nomor Whatsapp', 'number', $data->phone_number, ['required' => true]) !!}
                {!! FormInputText('address', 'Alamat', 'text', $data->address, ['required' => true]) !!}
                {!! FormInputText('purpose', 'Keperluan', 'text', $data->purpose, ['required' => true]) !!}

                <div class="mb-3 d-flex align-items-center">
                    <label for="signatureCanvas" style="width: 150px; margin-bottom: 0;">Tanda Tangan</label>

                <!-- Preview tanda tangan lama -->
                <img id="signaturePreview" src="{{ $data->signature ? Storage::url($data->signature) : '' }}" 
                alt="Tanda Tangan" style="max-height: 150px; cursor: default; margin-right: 15px;" />

                <!-- Button untuk ubah tanda tangan -->
                <button type="button" class="btn btn-secondary btn-sm mt-2" id="btnChangeSignature" style="margin-right: 15px;">Ubah TTD</button>

                <!-- Canvas tanda tangan baru, awalnya disembunyikan -->
                <canvas id="signatureCanvas" width="300" height="150" style="border:1px solid #000; display:none; margin-right: 15px;"></canvas>

                <!-- Tombol reset tanda tangan ulang -->
                <button type="button" class="btn btn-secondary btn-sm mt-2" id="clearSignature" style="display:none; margin-right: 15px;">Bersihkan</button>

                <!-- Input hidden untuk menyimpan data tanda tangan base64 -->
                <input type="hidden" name="signature" id="signatureInput" value="{{ old('signature', $data->signature) }}">
            </div>

        <script>
            const signaturePreview = document.getElementById('signaturePreview');
            const btnChangeSignature = document.getElementById('btnChangeSignature');
            const signatureCanvas = document.getElementById('signatureCanvas');
            const clearBtn = document.getElementById('clearSignature');
            const signatureInput = document.getElementById('signatureInput');

            const ctx = signatureCanvas.getContext('2d');
            let drawing = false;

            btnChangeSignature.addEventListener('click', () => {
            // Sembunyikan gambar preview dan tombol ubah
            signaturePreview.style.display = 'none';
            btnChangeSignature.style.display = 'none';
            // Tampilkan canvas dan tombol bersihkan
            signatureCanvas.style.display = 'block';
            clearBtn.style.display = 'inline-block';
            // Reset input base64 lama
            signatureInput.value = '';
            // Clear canvas
            ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
             });

            // Event mouse untuk menggambar tanda tangan
            signatureCanvas.addEventListener('mousedown', e => {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
            });

            signatureCanvas.addEventListener('mousemove', e => {
            if (!drawing) return;
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
            });

            signatureCanvas.addEventListener('mouseup', e => {
            drawing = false;
            // Simpan gambar canvas ke input hidden
            signatureInput.value = signatureCanvas.toDataURL('image/png');
            });

            signatureCanvas.addEventListener('mouseleave', e => {
            if (drawing) {
            drawing = false;
            signatureInput.value = signatureCanvas.toDataURL('image/png');
            }
            });

            clearBtn.addEventListener('click', () => {
            ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
            signatureInput.value = '';
            });
        </script>


                <div class="mb-3 d-flex align-items-center">
                    <label for="supporting_document" style="width: 150px; margin-bottom: 0;">Slip UKT Terakhir</label>
                    <img id="supportingDocumentPreview" src="{{ $data->supporting_document ? Storage::url($data->supporting_document) : '' }}"
                        alt="Dokumen Pendukung" style="max-height: 150px; cursor: pointer; margin-right: 15px;"
                        onclick="document.getElementById('supporting_document').click()" />
                    <input type="file" name="supporting_document" id="supporting_document" accept="image/png, image/jpeg"
                        style="display:none" onchange="previewImage(event, 'supportingDocumentPreview')">
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
                <a href="{{ route('administrasi.surat_aktif_kuliah.preview', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>

    @push('js')
        <script>
            function previewImage(event, previewId) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(previewId).src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection