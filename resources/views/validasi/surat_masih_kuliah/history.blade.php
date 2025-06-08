@extends('template.default')
@include('__partial.datatable_class')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope"></i> RIWAYAT PENGAJUAN SURAT MASIH KULIAH</h3>

        </div>
        <div class="card-body">
            <form method="GET" id="filterForm" action="{{ route('validasi.surat_masih_kuliah.history') }}">
                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-3 d-flex flex-column">
                        <label for="status" class="form-label fw-semibold text-muted mb-2">Status</label>
                        <select id="status" name="status" class="form-select rounded border border-primary"
                            style="padding: 0.5rem 0.75rem;">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                            <option value="menunggu" {{ strtolower(request('status')) == 'menunggu' ? 'selected' : '' }}>
                                Menunggu</option>
                            <option value="proses" {{ strtolower(request('status')) == 'proses' ? 'selected' : '' }}>Proses
                            </option>
                            <option value="disetujui" {{ strtolower(request('status')) == 'disetujui' ? 'selected' : '' }}>
                                Disetujui</option>
                            <option value="ditolak" {{ strtolower(request('status')) == 'ditolak' ? 'selected' : '' }}>
                                Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label for="created_start" class="form-label fw-semibold text-muted">Dari</label>
                        <input type="date" id="created_start" name="created_start" class="form-control"
                            value="{{ request('created_start') }}">
                    </div>
                    <div class="col-md-1">
                        <label for="created_end" class="form-label fw-semibold text-muted">Sampai</label>
                        <input type="date" id="created_end" name="created_end" class="form-control"
                            value="{{ request('created_end') }}">
                    </div>
                    <div class="col-md-5 d-flex gap-2 justify-content-start">
                        <button type="submit" class="btn btn-secondary px-4 shadow-sm mx-1" id="filterBtn">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>

                        <a href="{{ route('administrasi.surat_masih_kuliah.history') }}"
                            class="btn btn-outline-secondary px-4 shadow-sm mx-1">
                            <i class="fas fa-undo-alt me-1"></i> Reset
                        </a>
                    </div>
                    @php
                        $userRole = session('login.peran.id_peran');
                    @endphp

                    @if ($userRole == 6)
                        <div class="col-md-2 d-flex ms-auto justify-content-end">
                            <button type="button" class="btn btn-success px-4 shadow-sm mx-1" id="exportBtn">
                                <i class="fa fa-file-excel me-1"></i> Export
                            </button>
                        </div>
                    @endif
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-letter">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Administrasi</th>
                            <th>Tanggal Ajuan</th>
                            <th>Tanggal Selesai</th>
                            <th>Umur Ajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stillStudyLetters as $no => $letter)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $letter->name }}</td>
                                <td>Surat Masih Kuliah</td>
                                <td>{{ isset($letter->created_at) ? tglIndonesia($letter->created_at) : '-' }}</td>
                                <td>
                                    @if (!empty($letter->status_updated_at) && strtotime($letter->status_updated_at))
                                        {{ tglIndonesia($letter->status_updated_at) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $letter->time_diff }}</td>
                                @php
                                    $rejectedNotes = collect([
                                        $letter->adminValidation,
                                        $letter->advisorSignature,
                                        $letter->headOfProgramSignature,
                                        $letter->headOfDepartmentSignature,
                                    ])
                                        ->filter(function ($signature) {
                                            return $signature && $signature->status === 'ditolak';
                                        })
                                        ->pluck('notes')
                                        ->filter()
                                        ->all();
                                @endphp

                                <td>
                                    {{ ucfirst($letter->status) }}
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
                                <td>
                                    <a href="{{ route('validasi.surat_masih_kuliah.download-pdf', ['id' => Crypt::encrypt($letter->id)]) }}"
                                        class="btn btn-success btn-flat mx-3">
                                        <i class="fa fa-download"></i> Unduh
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada letter surat masih kuliah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('validasi.surat_masih_kuliah') }}" class="btn btn-default btn-flat"><i
                    class="fa fa-arrow-left"></i> Kembali</a>
        </div>

    </div>
    @push('js')
        <script>
            document.getElementById('status').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('exportBtn').addEventListener('click', function() {
                const form = document.getElementById('filterForm');
                const urlParams = new URLSearchParams(new FormData(form)).toString();
                const exportUrl = "{{ route('validasi.surat_masih_kuliah.excel') }}";
                window.location.href = exportUrl + '?' + urlParams;
            });
        </script>
    @endpush
@endsection