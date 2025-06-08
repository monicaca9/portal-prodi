{{-- <div class="body-template">
    <div class="header" style="display: flex; width: 100%;">
        <div style="">
            <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Fakultas"
                style="width: 3cm; height: 2.59cm; object-fit: contain;">
        </div>
        <div style="text-align: center; padding-left: 10px;">
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
                <td>: {{ $data->letterNumber->number ?? '' }}/{{ $data->letterNumber->code ?? '' }}/{{ $data->letterNumber->year ?? '' }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: 1 (satu) berkas</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Permohonan Pembuatan Surat Keterangan Aktif Kuliah</td>
            </tr>
        </table>


        <p style="padding-top: 10px;">Yth. Wakil Dekan Bidang Kemahasiswaan dan Alumni<br />
            Fakultas Teknik Universitas Lampung<br />
            di Bandar Lampung
        </p>
        <p >Sehubungan dengan keperluan mahasiswa sebagai berikut:
        </p>
    </div>
    <div class="section" style="padding-top: 0px; !important;">
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
        </table>

        <p>Dengan ini kami mengajukan permohonan pembuatan Surat Keterangan Aktif Kuliah untuk keperluan
            {{ $data->purpose }}.</p>

        <p>Sebagai bahan pertimbangan, bersama ini kami lampirkan:</p>
        <ol style="margin-left: 0; padding-left: 1em; text-align: left;">
            <li>Form Surat Keterangan Aktif Kuliah 2 lembar</li>
            <li>Fotokopi Slip UKT Terakhir 1 lembar</li>
        </ol>

        <p>Demikian atas perhatian dan kerjasamanya diucapkan terima kasih.</p>
    </div>




    <strong>
        <div class="signature-area">
           <div class="right-side">
               <table style="width: 100%; border-collapse: collapse;">
                   <tr>
                       <td style="width: 36%">Bandar Lampung,</td>
                       <td>&emsp;&emsp;&emsp;{{ \Carbon\Carbon::parse($data->month_year)->translatedFormat('F Y') }}</td>
                   </tr>
                   <tr>
                       <td colspan="2">Ketua Jurusan,</td>
                   </tr>
                   <tr>
                       <td colspan="2" style="height: 80px; padding-bottom:10px;"></td>
                   </tr>
                   <tr>
                       <td colspan="2">Herlinawati, S.T., M.T.</td>
                   </tr>
                   <tr>
                       <td colspan="2">NIP. 197103141999032001</td>
                   </tr>
               </table>
           </div>
           <div class="clear-float"></div>
       </div>
       </strong>

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

            .header {
                text-align: justify !important;
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
                padding-top: 10px;
                padding-left: 20px;
                padding-right: 20px;
            }
        </style>
    </div> --}}