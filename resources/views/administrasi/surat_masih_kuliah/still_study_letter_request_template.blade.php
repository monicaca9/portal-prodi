<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>SURAT KETERANGAN MASIH KULIAH</title>
</head>

<body
    style="font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1; text-align: justify; margin: 0.4cm;">
    <div style="width: 100%; text-align: center; padding-top: 20px;">
        <p style="margin: 0;"><strong>SURAT PERMOHONAN MASIH KULIAH</strong></p>
    </div>

    <div style="padding-top: 10px; margin-left: 16px; margin-right: 16px; margin-top: 10px;">
        <p>Saya yang bertanda tangan dibawah ini :</p>
    </div>

    <div style="padding-top: 0px; margin-left: 16px; margin-right: 16px;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td style="width: 24%;">Nama</td>
                <td>: {{ $data->nama }}</td>
            </tr>
            <tr>
                <td>NPM</td>
                <td>: {{ $data->npm }}</td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>: {{ $data->jurusan }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>: {{ $data->prodi }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>: {{ $data->semester }}</td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>: {{ $data->thn_akademik }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $data->alamat }}</td>
            </tr>
        </table>

        <p style="padding-top: 20px;">
            Bersama dengan surat permohonan ini kami mengajukan syarat untuk dapat dibuatkan Surat Pengantar Masih
            Kuliah ke Wakil Dekan Bid.Kemahasiswaan dan Alumni untuk dipergunakan sebagai syarat {{ $data->purpose }}.
        </p>

        <p>Demikian surat permohonan ini saya buat, atas perhatiannya saya ucapkan terima kasih.</p>
    </div>

    <div style="padding-top: 10px; margin-left: 16px; margin-right: 16px;">
        <table style="width: 100%; margin-top: 30px;">
            <tr>
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
                                @if (!empty($advisorQrCode))
                                    <img src="{{ $advisorQrCode }}" alt="QR Signature" width="100">
                                @else
                                    
                                @endif
                            </td>

                        </tr>
                        <tr>
                            <td>{{ $data->dosen_pa_nama ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>NIP. {{ $data->dosen_pa_nip ?? '..............' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%; text-align: left;">
                    <table style="width: 100%; text-align: left;">
                        <tr>
                            <td>Bandar Lampung, {{ tglIndonesia($data->tgl_create) }}</td>
                        </tr>
                        <tr>
                            <td>Pemohon (Mahasiswa)</td>
                        </tr>
                        <tr>
                            <td style="height: 80px;">
                                @if ($data->validasi && file_exists(public_path('storage/' . str_replace('public/', '', $data->validasi))))
                                    @php
                                        $path = public_path('storage/' . str_replace('public/', '', $data->validasi));
                                        $type = pathinfo($path, PATHINFO_EXTENSION);
                                        $data_img = base64_encode(file_get_contents($path));
                                        $src = "data:image/$type;base64,$data_img";
                                    @endphp
                                    <img src="{{ $src }}" alt="Validasi" style="height: 80px;">
                                @endif

                            </td>
                        </tr>
                        <tr>
                            <td>{{ $data->nama }}</td>
                        </tr>
                        <tr>
                            <td>NPM. {{ $data->npm }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="padding-top: 40px; text-align: center; margin-top: 20px;">
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
                            @if (!empty($data->validasi->createdBySdm->nm_sdm))
                                {{ $data->validasi->createdBySdm->nm_sdm }}
                            @else
                                Yessi Mulyani, S.T., M.T.
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left;">
                            @if (!empty($data->validasi->createdBySdm->nip))
                                {{ $data->validasi->createdBySdm->nip }}
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
