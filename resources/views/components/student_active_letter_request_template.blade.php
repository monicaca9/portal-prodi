{{-- <div class="body-template">
    <div class="header">
        <img src="{{ asset('images/kop_fakultas.png') }}" alt="Logo Fakultas" width="100%">
    </div>

    <div class="center-text">
        <strong>SURAT KETERANGAN AKTIF KULIAH</strong><br />
        Nomor:&emsp;&emsp;/{{ $data->letterNumber->code ?? '' }}/{{ $data->letterNumber->year ?? '' }}
    </div>

    <div class="section">
        <p>Dekan Fakultas Teknik Universitas Lampung menerangkan bahwa:</p>

        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="width: 24%">Nama</td>
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
                <td>Tahun Akademik</td>
                <td>: {{ $data->academic_year }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $data->address }}</td>
            </tr>
            <tr>
                <td>Keperluan</td>
                <td>: {{ $data->purpose }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p>
            Adalah benar mahasiswa tersebut terdaftar dan aktif sebagai
            mahasiswa di Fakultas Teknik Universitas Lampung.<br>
            Demikian surat keterangan ini dibuat untuk dapat digunakan
            sebagaimana mestinya.
        </p>

    </div>

    <div class="signature-area">
        <div class="right-side">
            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="width: 32%">Dikeluarkan di</td>
                    <td>: Bandar Lampung</td>
                </tr>
                <tr>
                    <td>Pada tanggal</td>
                    <td>: &emsp;&emsp;{{ $data->month_year ? \Carbon\Carbon::parse($data->month_year)->translatedFormat('F Y') : '' }}</td>
                </td>
                </tr>
                <tr>
                    <td colspan="2">a.n. Dekan</td>
                </tr>
                <tr>
                    <td colspan="2">Wakil Dekan Bidang Kemahasiswaan dan Alumni,</td>
                </tr>
                <td colspan="2" style="height: 80px; padding-bottom:10px;"></td>
                <tr>
                    <td colspan="2">Dr. Eng. Ageng Sadnowo R, S.T., M.T.</td>
                </tr>
                <tr>
                    <td colspan="2">NIP 196902281998031001</td>
                </tr>
            </table>
        </div>
        <div class="clear-float"></div>
    </div>

    <style>
        @page {
            padding: 1cm;
        }

        .body-template {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1;
            padding: 0.71cm 0.5cm 0.49cm 1.25cm;
            text-align: justify;
            width: 794px;
            height: 1123px;
            align-self: center;
            background-color: white;
        }

        .center-text {
            text-align: center;
        }

        .header {
            text-align: center;
        }

        .signature-area {
            padding-top: 10px;
            width: 100%;
        }

        .signature-area .right-side {
            float: right;
            text-align: start;
            width: 50%;
        }

        .clear-float {
            clear: both;
        }

        .section {
            padding-top: 20px;
            padding-left: 10px;
            padding-right: 10px;
        }
    </style>
</div> --}}