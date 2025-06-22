<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>SURAT KETERANGAN MASIH KULIAH</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            margin: 0.4cm;
        }

        .page {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .content {
            width: 100%;
            max-width: 17cm;
        }

        .text-center {
            text-align: center;
        }

        .logo {
            width: 3cm;
            height: 3cm;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-left: auto;
            margin-right: auto;
        }

        td:first-child {
            width: 30%;
        }

        .label {
            display: inline-block;
            width: 12px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="content">
            <div class="text-center">
                <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Universitas" class="logo">
                <p style="margin: 20px 0; font-weight: bold;">SURAT KETERANGAN MASIH KULIAH</p>
            </div>

            <table style="margin-top: 20px; text-align: left;">
                <tr>
                    <td>Nomor Surat</td>
                    <td><span class="label">:</span> {{ $data->numberLetter->number ?? '' }}/{{ $data->numberLetter->code ?? '' }}/{{ $data->numberLetter->year ?? '' }}</td>
                </tr>
                <tr>
                    <td>Perihal Surat</td>
                    <td><span class="label">:</span> Surat Keterangan Masih Kuliah</td>
                </tr>
                <tr><td colspan="2"></td></tr>
                <tr><td colspan="2">Yang Mengajukan</td></tr>
                <tr>
                    <td>Nama</td>
                    <td><span class="label">:</span> {{ $data->name }}</td>
                </tr>
                <tr>
                    <td>NPM</td>
                    <td><span class="label">:</span> {{ $data->student_number }}</td>
                </tr>
                <tr>
                    <td>Jurusan</td>
                    <td><span class="label">:</span> {{ $data->department }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td><span class="label">:</span> {{ $data->study_program }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td><span class="label">:</span> {{ $data->semester }}</td>
                </tr>
                <tr><td colspan="2"></td></tr>
                <tr><td colspan="2">Yang Menyetujui</td></tr>
                <tr>
                    <td>Nama</td>
                    <td><span class="label">:</span> {{ $name ?? '' }}</td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td><span class="label">:</span> {{ $nip }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
