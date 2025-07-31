<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>SURAT KETERANGAN MASIH KULIAH</title>
    <style>
        @page {
            padding: 1cm;
        }

        .body-template {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1;
            margin-top: 0.71cm !important;
            margin-bottom: 0.49cm !important;
            margin-left: 1.25cm !important;
            margin-right: 0.5cm !important;
            text-align: justify;
        }

        .center-text {
            text-align: center;
        }

        .header {
            text-align: center;
        }

        .header {
            text-align: justify !important;
        }

        .signature-area {
            padding-top: 20px;
            width: 100%;
        }

        .signature-area .right-side {
            float: right;
            text-align: start;
            width: 58%;
        }

        .clear-float {
            clear: both;
        }

        .section {
            padding-top: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
</head>

<body class="body-template">
    <div class="header" style="display: table; width: 100%;">
        <div style="display: table-cell; vertical-align: middle; width: 3.2cm;">
            <img src="{{ $pathImageLogo }}" alt="Logo Universitas" style="width: 3cm; height: 2.59cm; display: block;">
        </div>
        <div style="display: table-cell; vertical-align: middle; text-align: center; padding-left: 10px;">
            <p>
                KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI<br />UNIVERSITAS LAMPUNG<br />
                FAKULTAS TEKNIK<br />
                JURUSAN TEKNIK ELEKTRO<br />
                Jalan Prof. Soemantri Brojonegoro No. 1 Bandar Lampung 35145<br /> Telepon (0721) 704947 Faksimile
                (0721) 704947<br />
                Laman: elektro.unila.ac.id Email: jte@eng.unila.ac.id
            </p>
        </div>
    </div>

    <div style="width: 100%; height: 2px; background-color: black;"></div>
    <div class="section" style="padding-top: 10px !important;">

        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="width: 16%">Nomor</td>
                <td>:
                    {{ $data->letterNumber->nomor ?? '' }}/{{ $data->letterNumber->kode ?? '' }}/{{ $data->numberLetter->year ?? '' }}
                </td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: 1 (satu) berkas</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Permohonan Pembuatan Surat Keterangan Masih Kuliah</td>
            </tr>
        </table>


        <p style="padding-top: 10px;">Yth. Wakil Dekan Bidang Kemahasiswaan dan Alumni<br />
            Fakultas Teknik Universitas Lampung<br />
            di Bandar Lampung
        </p>
        <p>Sehubungan dengan keperluan mahasiswa sebagai berikut:
        </p>
    </div>
    <div class="section" style="padding-top: 0px; !important;">
        <table style="width: 100%; border-collapse: collapse">
            <tr>
                <td style="width: 24%">Nama</td>
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
                <td>Tahun Akademik</td>
                <td>: {{ $data->thn_akademik }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $data->alamat }}</td>
            </tr>
        </table>

        <p>Dengan ini kami mengajukan permohonan pembuatan Surat Keterangan Masih Kuliah untuk keperluan
            {{ $data->tujuan }}.</p>

        <p>Sebagai bahan pertimbangan, bersama ini kami lampirkan:</p>
        <ol style="margin-left: 0; padding-left: 1.2em; text-align: left;">
            <li>Form Surat Keterangan Masih Kuliah 2 lembar</li>
            <li>Fotokopi Slip UKT Terakhir 1 lembar</li>
            <li>Fotokopi KP4 Orang Tua (bagi PNS aktif)/ Fotokopi SK Pensiun (bagi pensiun PNS)/
                Fotokopi Surat keterangan Kerja Orang Tua (bagi Swasta) tahun terbaru 1 Lembar</li>
        </ol>

        <p>Demikian atas perhatian dan kerjasamanya diucapkan terima kasih.</p>
    </div>


    <strong>
        <div class="signature-area">
            <div class="right-side">
                <<table style="width: 100%; text-align: left;">
                        <tr>
                            <td>Bandar Lampung, {{ tglIndonesia($data->tgl_create) }}</td>
                        </tr>
                    <tr>
                        <td colspan="2">Ketua Jurusan,</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 80px;">
                            @if (!empty($headOfDepartementQrCode))
                                <img src="{{ $headOfDepartementQrCode }}" alt="QR Signature" width="100">
                            @else
                                
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            @if (!empty($data->validasi->createdBySdm->nm_sdm))
                                {{ $data->validasi->createdBySdm->nm_sdm }}
                            @else
                                Herlinawati, S.T., M.T.
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            @if (!empty($data->validasi->createdBySdm->nip))
                                {{ $data->validasi->createdBySdm->nip }}
                            @else
                                NIP. 197103141999032001
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="clear-float"></div>
        </div>
    </strong>
</body>

</html>
