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
                {!! FormInputText('nama', 'Nama Lengkap', 'text', $data->nama, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('npm', 'NPM', 'number', $data->npm, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('jurusan', 'Jurusan', 'text', $data->jurusan, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('prodi', 'Program Studi', 'text', $data->prodi, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('thn_akademik', 'Tahun Akademik', 'text', $academicYear, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}
                {!! FormInputText('semester', 'Semester', 'text', $data->semester, ['required' => true, 'readonly' => true, 'class' => 'no-click']) !!}

                {!! FormInputText('no_hp', 'Nomor Whatsapp', 'number', $data->no_hp, ['required' => true]) !!}
                {!! FormInputText('alamat', 'Alamat', 'text', $data->alamat, ['required' => true]) !!}
                {!! FormInputText('tujuan', 'Keperluan', 'text', $data->tujuan, ['required' => true]) !!}
                {!! FormInputText('nama_ortu', 'Nama Wali', 'text', $data->nama_ortu, ['required' => true]) !!}
                <div class="form-group row">
    <label for="pangkat_ortu" class="col-sm-2 col-form-label">
        NIP <span style="color:red;">*</span><br>
        <span style="font-size: 0.8em; color: #888;">
            <em>(Isi - jika tidak ada)</em>
        </span>
    </label>
    <div class="col-sm-10">
        <input 
            type="text" 
            name="nip_ortu" 
            id="nip_ortu" 
            class="form-control{{ $errors->has('nip_ortu') ? ' is-invalid' : '' }}" 
            value="{{ old('nip_ortu', $data->nip_ortu) }}" 
            placeholder="NIP" 
            required>
        @if ($errors->has('nip_ortu'))
            <div class="invalid-feedback">
                {{ $errors->first('nip_ortu') }}
            </div>
        @endif
    </div>
</div>


                <div class="form-group row">
    <label for="pangkat_ortu" class="col-sm-2 col-form-label">
        Pangkat/Gol. <span style="color:red;">*</span><br>
        <span style="font-size: 0.8em; color: #888;">
            <em>(Isi - jika tidak ada)</em>
        </span>
    </label>
    <div class="col-sm-10">
        <input 
            type="text" 
            name="pangkat_ortu" 
            id="pangkat_ortu" 
            class="form-control{{ $errors->has('pangkat_ortu') ? ' is-invalid' : '' }}" 
            value="{{ old('pangkat_ortu', $data->pangkat_ortu) }}" 
            placeholder="Pangkat/Gol." 
            required>
        @if ($errors->has('pangkat_ortu'))
            <div class="invalid-feedback">
                {{ $errors->first('pangkat_ortu') }}
            </div>
        @endif
    </div>
</div>
                {!! FormInputText('pekerjaan_ortu', 'Pekerjaan', 'text', $data->pekerjaan_ortu, ['required' => true]) !!}
                {!! FormInputText('instansi_ortu', 'Instansi/Tempat Kerja', 'text', $data->instansi_ortu, ['required' => true]) !!}
                {!! FormInputText('alamat_ortu', 'Alamat Wali', 'text', $data->alamat_ortu, ['required' => true]) !!}
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
                <input type="hidden" name="validasi" id="validasi">
        
                <!-- Validasi error -->
                @if ($errors->has('validasi'))
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('validasi') }}
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
                document.getElementById('validasi').value = "";
                }

            // Saat form disubmit, isi input hidden dengan base64 dari canvas
            document.querySelector('form').addEventListener('submit', function () {
            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('validasi').value = dataURL;
            });
        </script>
                <div class="form-group row">
                    <label for="dokumen" class="col-sm-2 col-form-label">
                    Slip UKT Terakhir <span style="color:red;">*</span><br>
                    <span style="font-size: 0.8em; color: #888;">
                    <em>(Format file .pdf <br> Max. ukuran file 2MB)</em>
                    </span>
                     </label>
                    <div class="col-sm-10">
                        <input type="file" name="dokumen" id="dokumen" class="form-control"
                            accept="application/pdf" required>
                        @if ($errors->has('dokumen'))
                            <div class="invalid-feedback">
                                {{ $errors->first('dokumen') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dokumen2" class="col-sm-2 col-form-label">
                        KP4 Orang Tua (PNS aktif) atau<br>SK Pensiun (pensiun PNS) atau<br>Surat Keterangan Kerja Orang Tua (Swasta) <span style="color:red;">*</span><br>
                    <span style="font-size: 0.8em; color: #888;">
                    <em>(Format file .pdf <br> Max. ukuran file 2MB)</em>
                    </span>
                     </label>
                    <div class="col-sm-10">
                        <input type="file" name="dokumen2" id="dokumen2" class="form-control"
                            accept="application/pdf" required>
                        @if ($errors->has('dokumen2'))
                            <div class="invalid-feedback">
                                {{ $errors->first('dokumen2') }}
                            </div>
                        @endif
                    </div>
                </div>
                {!! FormInputSelect(
                    'dosen_pa',
                    'Pilih Dosen PA',
                    true,
                    true,
                    $academicAdvisors,
                    $data->dosen_pa,
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