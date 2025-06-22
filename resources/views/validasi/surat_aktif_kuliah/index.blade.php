@extends('template.default')
@include('__partial.datatable_class')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope"></i> ADMINISTRASI SURAT AKTIF KULIAH</h3>
            <div class="card-tools">
                <a href="{{ route('validasi.surat_aktif_kuliah.history') }}" class="btn btn-flat btn-secondary btn-sm ml-2">
                    <i class="fas fa-history"></i> Riwayat Ajuan
                </a>
            </div>
        </div>
        <form method="GET" id="filterForm">
            <div class="row g-3 align-items-center mt-2 mx-4">
                <div class="col-md-3 d-flex flex-column">
                    <label for="status" class="form-label fw-semibold text-muted mb-2">Status</label>
                    <select id="status" name="status" class="form-select rounded border border-primary"
                        style="padding: 0.5rem 0.75rem; font-size: 1rem;">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                        <option value="menunggu" {{ strtolower(request('status')) == 'menunggu' ? 'selected' : '' }}>
                            Menunggu</option>
                        <option value="proses" {{ strtolower(request('status')) == 'proses' ? 'selected' : '' }}>Proses
                        </option>
                        <option value="disetujui" {{ strtolower(request('status')) == 'disetujui' ? 'selected' : '' }}>
                            Disetujui</option>
                        <option value="ditolak" {{ strtolower(request('status')) == 'ditolak' ? 'selected' : '' }}>Ditolak
                        </option>
                    </select>
                </div>

                <div class="row g-3 align-items-center my-2 mx-2">
                    <label class="form-label fw-semibold text-muted mb-2 mx-2">Legenda</label>
                    <div class="col-md-12 d-flex flex-column">
                        <div class="border border-secondary rounded p-2" style="font-size: 1rem; color: #555;">
                            <div class="d-flex flex-wrap gap-4">
                                @foreach ([['color' => 'gray', 'icon' => 'fa-clock', 'label' => 'Menunggu'], ['color' => 'green', 'icon' => 'fa-check', 'label' => 'Disetujui'], ['color' => 'red', 'icon' => 'fa-times', 'label' => 'Ditolak']] as $item)
                                    <div class="d-flex align-items-center gap-3 mx-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-2"
                                            style="width: 1.25rem; height: 1.25rem; background-color: {{ $item['color'] }};">
                                            <i class="fa {{ $item['icon'] }}" style="font-size: 0.75rem; color: white;"></i>
                                        </div>
                                        {{ $item['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa </th>
                            <th>Tanggal Diterbitkan</th>
                            <th>Umur Ajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentActiveLetters as $no => $letter)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $letter->name }} ({{ $letter->student_number }})</td>
                                <td>{{ isset($letter->created_at) ? tglIndonesia($letter->created_at) : '-' }}</td>
                                <td>{{ $letter->time_diff }}</td>
                                <td>
                                    <ul
                                        style="list-style: none; padding: 0; margin: 0; font-family: sans-serif; position: relative;">
                                        @php
                                            $steps = [
                                                [
                                                    'label' => 'Validasi Dosen PA',
                                                    'data' => $letter->advisorSignature ?? null,
                                                    'check' => $letter->advisor_signature_id,
                                                ],
                                                [
                                                    'label' => 'Validasi Admin',
                                                    'data' => $letter->adminValidation ?? null,
                                                    'check' => $letter->admin_validation_id,
                                                ],
                                                [
                                                    'label' => 'Validasi Prodi',
                                                    'data' => $letter->headOfProgramSignature ?? null,
                                                    'check' => $letter->head_of_program_signature_id,
                                                ],
                                                [
                                                    'label' => 'Validasi Kajur',
                                                    'data' => $letter->headOfDepartmentSignature ?? null,
                                                    'check' => $letter->head_of_department_signature_id,
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($steps as $index => $step)
                                            @php
                                                $status = $step['data']->status ?? null;
                                                $time = $step['data']->created_at ?? '';
                                                $color = is_null($step['check'])
                                                    ? 'gray'
                                                    : ($status === 'disetujui'
                                                        ? 'green'
                                                        : ($status === 'ditolak'
                                                            ? 'red'
                                                            : 'orange'));

                                                $icon = is_null($step['check'])
                                                    ? 'fa-clock'
                                                    : ($status === 'disetujui'
                                                        ? 'fa-check'
                                                        : ($status === 'ditolak'
                                                            ? 'fa-times'
                                                            : 'fa-clock'));
                                            @endphp
                                            <li style="position: relative; padding-left: 40px; margin-bottom: 16px;">
                                                <div
                                                    style="position: absolute; top: 20px; left: 18px; width: 2px; height: 100%; background-color: lightgray;">
                                                </div>
                                                <div
                                                    style="position: absolute; top: 0; left: 0; width: 36px; text-align: center;">
                                                    <div
                                                        style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $color }}; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                        <i class="fa {{ $icon }}"
                                                            style="font-size: 10px; color: white;"></i>
                                                    </div>
                                                </div>
                                                <div style="margin-left: 8px;">
                                                    <div>{{ $step['label'] }}</div>
                                                    <div style="font-size: 0.85em; color: gray;">
                                                        {{ $time }}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach

                                        @php
                                            $selesaiColor = 'gray';
                                            $selesaiIcon = 'fa-clock';

                                            if (in_array($letter->status, ['selesai', 'ditolak'])) {
                                                $selesaiColor = $letter->status === 'selesai' ? 'green' : 'red';
                                                $selesaiIcon =
                                                    $letter->status === 'selesai' ? 'fa-check' : 'fa-times';
                                            }
                                        @endphp

                                        <li style="position: relative; padding-left: 40px; margin-bottom: 16px;">
                                            <div
                                                style="position: absolute; top: 0; left: 0; width: 36px; text-align: center;">
                                                <div
                                                    style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $selesaiColor }}; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                    <i class="fa {{ $selesaiIcon }}"
                                                        style="font-size: 10px; color: white;"></i>
                                                </div>
                                            </div>
                                            <div style="margin-left: 8px;">
                                                <div>Selesai</div>
                                                @if (in_array($letter->status, ['selesai', 'ditolak']))
                                                    <div style="font-size: 0.85em; color: gray;">
                                                        {{ $letter->updated_at }}
                                                    </div>
                                                @php
        $rejectedNotes = collect([
            $letter->adminValidation,
            $letter->advisorSignature,
            $letter->headOfProgramSignature,
            $letter->headOfDepartmentSignature,
        ])
            ->filter(fn($signature) => $signature && $signature->status === 'ditolak')
            ->pluck('notes')
            ->filter()
            ->all();
    @endphp

    @if (count($rejectedNotes) > 0)
        <div style="font-size: 0.95em; color: red; margin-top: 4px;">
            Alasan penolakan:
            <ul style="padding-left: 20px; margin: 0;">
                @foreach ($rejectedNotes as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endif
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <a href="{{ route('validasi.surat_aktif_kuliah.preview-pdf', ['id' => Crypt::encrypt($letter->id)]) }}"
                                        class="btn btn-xs btn-primary"
                                        target="_blank">
                                        <i class="fas fa-eye"></i>

                                    </a>
                                    <a href="{{ route('validasi.surat_aktif_kuliah.edit', ['id' => Crypt::encrypt($letter->id)]) }}"
                                        class="btn btn-xs btn-success {{ $letter->disable_validation_button ? 'disabled' : '' }}"
                                        {{ $letter->disable_validation_button ? 'aria-disabled=true tabindex=-1' : '' }}>
                                        <i class="fas fa-paper-plane"></i> Validasi
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data surat aktif kuliah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.getElementById('status').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            @if (session('success'))
                swal({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    button: "OK",
                });
        @endif
        </script>
    @endpush
@endsection