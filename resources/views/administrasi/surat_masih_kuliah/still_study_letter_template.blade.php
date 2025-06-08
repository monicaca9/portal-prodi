<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>SURAT KETERANGAN MASIH KULIAH</title>
</head>

<body class="body-template">
    <div class="header">
        <img src="{{ $pathImage }}" alt="Logo Fakultas" width="100%">
    </div>

    <div class="center-text">
        <strong>SURAT KETERANGAN MASIH KULIAH</strong><br />
      <p>  Nomor: <span style="color: #FFFFFF;">...... </span>/UN26.15/{{ $data->numberLetter->year ?? '' }} </p>
    </div>

    <div class="section">
        <p>Dekan Fakultas Teknik Universitas Lampung menerangkan bahwa:</p>

        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="text-align: left; width: 26%">Nama</td>
                <td style="text-align: left">: {{ $data->name }}</td>
            </tr>
            <tr>
                <td style="text-align: left">NPM</td>
                <td style="text-align: left">: {{ $data->student_number }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Jurusan</td>
                <td style="text-align: left">: {{ $data->department }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Program Studi</td>
                <td style="text-align: left">: {{ $data->study_program }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Semester</td>
                <td style="text-align: left">: {{ $data->semester }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Tahun Akademik</td>
                <td style="text-align: left">: {{ $data->academic_year }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Alamat</td>
                <td style="text-align: left">: {{ $data->address }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Keperluan</td>
                <td style="text-align: left">: {{ $data->purpose }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p>
            Adalah benar mahasiswa tersebut terdaftar dan aktif sebagai
            mahasiswa di Fakultas Teknik Universitas Lampung dan biaya kuliahnya menjadi tanggungan dari:
        </p>

        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="text-align: left; width: 26%">Nama</td>
                <td style="text-align: left">: {{ $data->parent_name }}</td>
            </tr>
            <tr>
                <td style="text-align: left">NIP</td>
                <td style="text-align: left">: {{ $data->parent_nip }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Pangkat/Gol.</td>
                <td style="text-align: left">: {{ $data->parent_grade }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Pekerjaan</td>
                <td style="text-align: left">: {{ $data->parent_job }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Instansi/Tempat Kerja</td>
                <td style="text-align: left">: {{ $data->parent_institution }}</td>
            </tr>
            <tr>
                <td style="text-align: left">Alamat</td>
                <td style="text-align: left">: {{ $data->parent_address }}</td>
            </tr>
        </table>

        <p>
            Demikian surat keterangan ini dibuat untuk dapat digunakan
            sebagaimana mestinya.
        </p>
    </div>

    <div class="signature-area">
        <div class="right-side">
            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="text-align: left; width: 32%">
                        Dikeluarkan di
                    </td>
                    <td style="text-align: left">: Bandar Lampung</td>
                </tr>
                <tr>
                    <td style="text-align: left">Pada tanggal</td>
                    {{-- <td style="text-align: right">: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</td> --}}
                    <td>: <span style="color: #FFFFFF;">.............</span> {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('F Y') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left">a.n. Dekan</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left">
                        Wakil Dekan Bidang Kemahasiswaan dan Alumni,
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 80px"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left">
                        Dr. Eng. Ageng Sadnowo R, S.T., M.T.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left">
                        NIP 196902281998031001
                    </td>
                </tr>
            </table>
        </div>
        <div class="clear-float"></div>
    </div>
</body>
</html>