<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>SURAT KETERANGAN AKTIF KULIAH</title>
</head>

<body
    style="font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1; text-align: justify; margin: 0.4cm;">
    <div style="width: 100%; text-align: center; padding-top: 20px;">
        <p style="margin: 0;"><strong>SURAT PERMOHONAN AKTIF KULIAH</strong></p>
    </div>

    <div style="padding-top: 10px; margin-left: 16px; margin-right: 16px; margin-top: 10px;">
        <p>Saya yang bertanda tangan dibawah ini :</p>
    </div>

    <div style="padding-top: 0px; margin-left: 16px; margin-right: 16px;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td style="width: 24%;">Nama</td>
                <td>: {{ $data->name }}</td>
            </tr>
            <tr>
                <td>NPM</td>
                <td>: {{ $data->student_number }}</td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>: {{ $data->department }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $data->study_program }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $data->semester }}</td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>: {{ $data->academic_year }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $data->address }}</td>
            </tr>
        </table>

        <p style="padding-top: 20px;">
            Bersama dengan surat permohonan ini kami mengajukan syarat untuk dapat dibuatkan Surat Pengantar Aktif
            Kuliah ke Wakil Dekan Bid.Kemahasiswaan dan Alumni untuk dipergunakan sebagai syarat {{ $data->purpose }}.
        </p>

        <p>Demikian surat permohonan ini saya buat, atas perhatiannya saya ucapkan terima kasih.</p>
    </div>

    <div style="padding-top: 10px; margin-left: 16px; margin-right: 16px;">
        {{-- Bagian Atas: Dua Kolom --}}
        <table style="width: 100%; margin-top: 30px;">
            <tr>
                <!-- Kolom Kiri: Pembimbing Akademik -->
                {{-- width: 60%: Menempatkan kolom kiri agar lebih lebar --}}
                <td style="width: 60%; text-align: left;">
                    <table style="width: 100%; text-align: left;">
                        <tr>
                            <td>Mengetahui,</td>
                        </tr>
                        <tr>
                            <td>Pembimbing Akademik</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 80px">
                                {{-- Periksa apakah $advisorQrCode TIDAK kosong, Kalau variabel ADA ISINYA, maka kodenya di dalam blok if akan dijalankan. --}}
                                @if (!empty($advisorQrCode))
                                    <img src="{{ $advisorQrCode }}" alt="QR Signature" width="100">
                                @else
                                    {{-- Bagian else kosong → tidak menampilkan apa pun. Jadi kalau $advisorQrCode kosong, gambar QR tidak muncul --}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ $data->academic_advisor_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>NIP. {{ $data->academic_advisor_nip ?? '..............' }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Kolom Kanan: Pemohon (Mahasiswa) -->
                <td style="width: 40%; text-align: left;">
                    <table style="width: 100%; text-align: left;">
                        <tr>
                            <td>Bandar Lampung, {{ tglIndonesia($data->created_at) }}</td>
                        </tr>
                        <tr>
                            <td>Pemohon (Mahasiswa)</td>
                        </tr>
                        <tr>
                            <td style="height: 80px;">
                                @if ($data->signature && file_exists(public_path('storage/' . str_replace('public/', '', $data->signature))))
                                    @php
                                        $path = public_path('storage/' . str_replace('public/', '', $data->signature));
                                        $type = pathinfo($path, PATHINFO_EXTENSION);
                                        $data_img = base64_encode(file_get_contents($path));
                                        $src = "data:image/$type;base64,$data_img";
                                    @endphp
                                    <img src="{{ $src }}" alt="Signature" style="height: 80px;">
                                @endif

                            </td>
                        </tr>
                        <tr>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <td>NPM. {{ $data->student_number }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Membuat isi tabel tengah bawah tetap rata kiri tapi posisinya di tengah halaman. --}}
        <div style="padding-top: 40px; text-align: center; margin-top: 20px;">
            {{-- inline-block = Biar tabelnya: Nggak 100% melebar kalau isinya pendek, jadi ngikut ukuran isinya
            Bisa rapi di tengah (karena text-align: center; di div luar). --}}
            <div style="display: inline-block;">
                <table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto; margin-top: 20px;">
                    <tr>
                        <td>Menyetujui,</td>
                    </tr>
                    <tr>
                        <td style=" text-align: left;">Ketua Program Studi Teknik Informatika</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 80px;">
                            @if (!empty($headOfProgramQrCode))
                                <img src="{{ $headOfProgramQrCode }}" alt="QR Signature" width="100">
                            @else

                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left;">
                            @if (!empty($data->signature->createdBySdm->nm_sdm))
                                {{ $data->signature->createdBySdm->nm_sdm }}
                            @else
                                Yessi Mulyani, S.T., M.T.
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left;">
                            @if (!empty($data->signature->createdBySdm->nip))
                                {{ $data->signature->createdBySdm->nip }}
                            @else
                                NIP 197312262000122001
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
