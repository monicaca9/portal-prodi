@extends('template.default')
@include('__partial.datatable_class')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope"></i> ADMINISTRASI SURAT AKTIF KULIAH</h3>
            <div class="card-tools">

                <a href="{{ route('administrasi.surat_aktif_kuliah.history') }}"
                    class="btn btn-flat btn-secondary btn-sm ml-2">
                    <i class="fas fa-history"></i> Riwayat Ajuan
                </a>

                {!! buttonAdd('administrasi.surat_aktif_kuliah.add', 'Tambah Ajuan') !!}

            </div>
        </div>

        <div class="row my-4 mx-4 align-items-start">
    <!-- Keterangan Surat -->
    <div class="col-md-6">
        <div>
            <strong>Surat Keterangan Aktif Kuliah dapat digunakan untuk keperluan:</strong>
            <br>a. Pengajuan Beasiswa (sebutkan nama beasiswanya)
            <br>b. Kehilangan Kartu Tanda Mahasiswa (KTM)
            <br>c. Kehilangan Slip UKT (sebutkan Slip UKT semester berapa saja)
            <br>d. Kehilangan Sertifikat PKKMB
        </div>
    </div>

    <!-- Legenda Status -->
    <div class="col-md-6 text-end"> <!-- Tambahkan 'text-end' di sini -->
        <div class="mb-1">
            <label class="form-label fw-semibold text-muted">Legenda</label>
        </div>
        <div class="border border-secondary rounded p-2 d-inline-block"
            style="font-size: 1rem; color: #555; max-width: fit-content;">
            <div class="d-flex flex-wrap gap-3">
                @foreach ([['color' => 'gray', 'icon' => 'fa-clock', 'label' => 'Menunggu'], 
                           ['color' => 'green', 'icon' => 'fa-check', 'label' => 'Disetujui'], 
                           ['color' => 'red', 'icon' => 'fa-times', 'label' => 'Ditolak']] as $item)
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-2"
                            style="width: 1.25rem; height: 1.25rem; background-color: {{ $item['color'] }};">
                            <i class="fa {{ $item['icon'] }}" style="font-size: 0.75rem; color: white;"></i>
                        </div>
                        <span>{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keperluan Surat</th>
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

                                <td>{{ $letter->purpose }}</td>

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
                                                @endif
                                            </div>
                                        </li>
                                    </ul>
                                </td>

                                <td>
                                    <a href="{{ route('administrasi.surat_aktif_kuliah.detail', ['id' => Crypt::encrypt($letter->id)]) }}"
                                        class="btn btn-xs btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form
                                        action="{{ route('administrasi.surat_aktif_kuliah.submit', ['id' => Crypt::encrypt($letter->id)]) }}"
                                        method="POST" style="display:inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-xs btn-success"
                                            {{ $letter->status !== 'dibuat' ? 'disabled aria-disabled=true tabindex=-1' : '' }}>
                                            <i class="fas fa-paper-plane"></i> Ajukan
                                        </button>
                                    </form>

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
        @if (session('success'))
            <script>
                swal({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    button: "OK",
                });
            </script>
        @endif
    @endpush
@endsection