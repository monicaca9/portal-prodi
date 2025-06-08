<!DOCTYPE html>
<html>
<head>
	<title>Berita Acara Seminar Hasil</title>
	<style type="text/css">
	@page {
   size: 21cm 29.7cm;
   margin-top: 20px;
   margin-bottom: 0cm;
   border: 1px solid blue;

}
		.judul{
			text-align: center;
		}
		.page_break { page-break-before: always; }


		.body{
			   font-family: arial;
		}
	</style>
</head>
<body>

	<table width="100%" border="0" cellspacing="-20" cellpadding="-20">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="130" height="130"></td>
			<td width="97%" style="padding-left: -500px; padding-top: 0">

				<p class="judul">KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN
	<br>UNIVERSITAS LAMPUNG <br>	
	FAKULTAS TEKNK<br><strong>
	JURUSAN TEKNIK ELEKTRO<br>
	PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
	<span style="font-size: 11px">
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 Telp.(0721)701609 ext 219, 220</span></P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>BERITA ACARA SEMINAR HASIL</u> <br>
	NO.:    63/UN26.15.07/PP.05.02/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>
	<p>Pada hari  <?= $hari ?>, <?= date('j', strtotime($data->tgl_mulai)) .' '. $bulan ?>.  telah dilaksanakan Seminar Hasil atas nama mahasiswa :</p>


<table >
	<tr>
		<td width="23%">Nama</td>
		<td width="2%">:</td>
		<td width="75%"><?= $profil->nm_pd ?></td>
	</tr>

	<tr>
		<td>NPM</td>
		<td>:</td>
		<td>{{ $profil->nim}}</td>
	</tr>
	<tr>
		<td>Pukul</td>
		<td>:</td>
		<td>{{ $waktu}} s.d. Selesai</td>
	</tr>
		<tr>
		<td>Tempat</td>
		<td>:</td>
		<td>{{ $gedung->nm_gedung}} / {{ $ruang->nm_ruang}}</td>
	</tr>
	<tr>
		<td valign="top">Judul Tugas Akhir</td>
		<td valign="top">:</td>
		<td valign="top">{{ $data->judul_akt_mhs}}</td>
	</tr>
</table>
<p>Dengan haisl sebagai berikut :</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="300px" align="center">JABATAN</th>
		<th width="100px" align="center">NILAI</th>
		<th width="110px" align="center">PROSENTASE</th>
		<th width="130px" align="center">NILAI AKHIR</th>
	</tr><tr>
		<td align="center">1</td>
		<td>Pembimbing Utama/Ketua</td>
		<td></td>
		<td align="center">50%</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Penguji Utama</td>
		<td></td>
		<td align="center">30%</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Pembimbing Pendamping/Sekretaris</td>
		<td></td>
		<td align="center">20%</td>
		<td></td>
	</tr>
</table>
<br><br>

<table  width="100%">
	<tr>
		<td  width="25%">Nilai Akhir Rata-rata</td>
		<td  width="5%">:</td>
		<td  width="70%"></td>
	</tr><tr>
		<td  width="25%">Huruf Mutuf</td>
		<td  width="5%">:</td>
		<td  width="70%"></td>
	</tr><tr>
		<td  width="25%">Perbaikan</td>
		<td  width="5%">:</td>
		<td  width="70%"></td>
	</tr>
	
</table>
<br><br>
<p>Demikian berita acara ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
<br>

<table  width="100%">
	<tr>
		<td width="30%" align="center">Pembimbing Pendamping/ Sekretairis</td>
		
		<td width="30%"align="center">Penguji Utama,</td>
		<td width="40%"align="center">Pembimbing Utama/Ketua</td>
	</tr>
	<tr>
		<td><br><br><br><br><br></td>
		<td></td>
		<td></td>
	</tr>

	<tr>
		<td align="center">____________________________</td>
		
		<td align="center">____________________________</td>
		<td align="center">____________________________</td>
	</tr>
		<tr>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	

</table>



<br><br>
<p>Catatan :</p>
<table width="60%" style="font-size: 13px">
	<tr>
		
		<td width="10%" align="left">A &#60 76 <br> B+ 70 &#60 76 <br> B 66 - &#60 64 <</td>
		<td width="10%" align="left">C+ 60 -&#60 66</td>
		
	</tr>
	
	

</table>





<div class="page_break"></div>



	<table width="100%" border="0" cellspacing="-20" cellpadding="-20">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="130" height="130"></td>
			<td width="97%" style="padding-left: -500px; padding-top: 0">

				<p class="judul">KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN
	<br>UNIVERSITAS LAMPUNG <br>	
	FAKULTAS TEKNK<br><strong>
	JURUSAN TEKNIK ELEKTRO<br>
	PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
	<span style="font-size: 11px">
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 Telp. 0721-785508 Fax. 0721-785508</span></P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>BERITA ACARA SEMINAR HASIL</u> <br>
	NO.:    74/UN26.15.07/PP.05.02/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>


<table >
	<tr>
		<td width="23%">Nama</td>
		<td width="2%">:</td>
		<td width="75%">{{ $profil->nm_pd}}</td>
	</tr>

	<tr>
		<td>NPM</td>
		<td>:</td>
		<td>{{ $profil->nim}}</td>
	</tr>
	<tr>
		<td>Pukul</td>
		<td>:</td>
		<td>{{ $waktu}} s.d. Selesai</td>
	</tr>
		<tr>
		<td>Tempat</td>
		<td>:</td>
		<td>{{ $gedung->nm_gedung}} / {{ $ruang->nm_ruang}}</td>
	</tr>
	<tr>
		<td valign="top">Judul Tugas Akhir</td>
		<td valign="top">:</td>
		<td valign="top">{{ $data->judul_akt_mhs}}</td>
	</tr>
</table>
<p>A. Nilai Makalah</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td>6</td>
		<td>Bahasa</td>
		<td></td>
	</tr><tr>
		<td>7</td>
		<td>Pustaka</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata A</td>
		<td></td>
	</tr>
</table>


	<p>B. Nilai Presentasi :</p>
<table border="1" cellspacing="0">
	<tr>
		<th  width="40px">NO.</th>
		<th  width="350px">ASPEK YANG DINILAI</th>
		<th  width="100px">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Penampilan</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Penyajian</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Penguasaan Materi</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Mutu Jawaban</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata B</td>
		<td></td>
	</tr>
</table>

<table >
	<tr>
		<td rowspan="3">Nilai Rata-rata Keseluruhan =</td>
		<td  align="center">A+B</td>
		<td rowspan="3"> =</td>
		<td  rowspan="3" >___________</td>
	</tr>
	<tr  align="center">
		
		<td valign="top">_______</td>
		
	</tr>
	<tr align="center">
		<td>2</td>
	</tr>
</table>

<table align="right">
	<tr>
		<td>Bandar Lampung, <?= date('j') . ' ' . $bulan . ' ' . date('Y'); ?></td>
	</tr>
	<tr>
		<td>Pembimbing Pendamping</td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>_______________________________</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<div class="page_break"></div>



	<table width="100%" border="0" cellspacing="-20" cellpadding="-20">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="130" height="130"></td>
			<td width="97%" style="padding-left: -500px; padding-top: 0">

				<p class="judul">KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN
	<br>UNIVERSITAS LAMPUNG <br>	
	FAKULTAS TEKNK<br><strong>
	JURUSAN TEKNIK ELEKTRO<br>
	PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
	<span style="font-size: 11px">
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 Telp. 0721-785508 Fax. 0721-785508</span></P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>BERITA ACARA SEMINAR HASIL</u> <br>
	NO.:    74/UN26.15.07/PP.05.02/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>


<table >
	<tr>
		<td width="23%">Nama</td>
		<td width="2%">:</td>
		<td width="75%">{{ $profil->nm_pd}}</td>
	</tr>

	<tr>
		<td>NPM</td>
		<td>:</td>
		<td>{{ $profil->nim}}</td>
	</tr>
	<tr>
		<td>Pukul</td>
		<td>:</td>
		<td>{{ $waktu}} s.d. Selesai</td>
	</tr>
		<tr>
		<td>Tempat</td>
		<td>:</td>
		<td>{{ $gedung->nm_gedung}} / {{ $ruang->nm_ruang}}</td>
	</tr>
	<tr>
		<td valign="top">Judul Tugas Akhir</td>
		<td valign="top">:</td>
		<td valign="top">{{ $data->judul_akt_mhs}}</td>
	</tr>
</table>
<p>A. Nilai Makalah</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td>6</td>
		<td>Bahasa</td>
		<td></td>
	</tr><tr>
		<td>7</td>
		<td>Pustaka</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata A</td>
		<td></td>
	</tr>
</table>


	<p>B. Nilai Presentasi :</p>
<table border="1" cellspacing="0">
	<tr>
		<th  width="40px">NO.</th>
		<th  width="350px">ASPEK YANG DINILAI</th>
		<th  width="100px">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Penampilan</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Penyajian</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Penguasaan Materi</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Mutu Jawaban</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata B</td>
		<td></td>
	</tr>
</table>

<table >
	<tr>
		<td rowspan="3">Nilai Rata-rata Keseluruhan =</td>
		<td  align="center">A+B</td>
		<td rowspan="3"> =</td>
		<td  rowspan="3" >___________</td>
	</tr>
	<tr  align="center">
		
		<td valign="top">_______</td>
		
	</tr>
	<tr align="center">
		<td>2</td>
	</tr>
</table>

<table align="right">
	<tr>
		<td>Bandar Lampung, <?= date('j') . ' ' . $bulan . ' ' . date('Y'); ?></td>
	</tr>
	<tr>
		<td>Penguji Utama</td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>_______________________________</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<div class="page_break"></div>



	<table width="100%" border="0" cellspacing="-20" cellpadding="-20">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="130" height="130"></td>
			<td width="97%" style="padding-left: -500px; padding-top: 0">

				<p class="judul">KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN
	<br>UNIVERSITAS LAMPUNG <br>	
	FAKULTAS TEKNK<br><strong>
	JURUSAN TEKNIK ELEKTRO<br>
	PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
	<span style="font-size: 11px">
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 Telp. 0721-785508 Fax. 0721-785508</span></P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>BERITA ACARA SEMINAR HASIL</u> <br>
	NO.:    74/UN26.15.07/PP.05.02/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>


<table >
	<tr>
		<td width="23%">Nama</td>
		<td width="2%">:</td>
		<td width="75%">{{ $profil->nm_pd}}</td>
	</tr>

	<tr>
		<td>NPM</td>
		<td>:</td>
		<td>{{ $profil->nim}}</td>
	</tr>
	<tr>
		<td>Pukul</td>
		<td>:</td>
		<td>{{ $waktu}} s.d. Selesai</td>
	</tr>
		<tr>
		<td>Tempat</td>
		<td>:</td>
		<td>{{ $gedung->nm_gedung}} / {{ $ruang->nm_ruang}}</td>
	</tr>
	<tr>
		<td valign="top">Judul Tugas Akhir</td>
		<td valign="top">:</td>
		<td valign="top">{{ $data->judul_akt_mhs}}</td>
	</tr>
</table>
<p>A. Nilai Makalah</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td>6</td>
		<td>Bahasa</td>
		<td></td>
	</tr><tr>
		<td>7</td>
		<td>Pustaka</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata A</td>
		<td></td>
	</tr>
</table>


	<p>B. Nilai Presentasi :</p>
<table border="1" cellspacing="0">
	<tr>
		<th  width="40px">NO.</th>
		<th  width="350px">ASPEK YANG DINILAI</th>
		<th  width="100px">NILAI</th>
	</tr><tr>
		<td>1</td>
		<td>Penampilan</td>
		<td></td>
	</tr><tr>
		<td>2</td>
		<td>Penyajian</td>
		<td></td>
	</tr><tr>
		<td>3</td>
		<td>Penguasaan Materi</td>
		<td></td>
	</tr><tr>
		<td>4</td>
		<td>Mutu Jawaban</td>
		<td></td>
	</tr><tr>
		<td>5</td>
		<td>Metodologi</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Jumlah</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">Nilai Rata-rata B</td>
		<td></td>
	</tr>
</table>

<table >
	<tr>
		<td rowspan="3">Nilai Rata-rata Keseluruhan =</td>
		<td  align="center">A+B</td>
		<td rowspan="3"> =</td>
		<td  rowspan="3" >___________</td>
	</tr>
	<tr  align="center">
		
		<td valign="top">_______</td>
		
	</tr>
	<tr align="center">
		<td>2</td>
	</tr>
</table>

<table align="right">
	<tr>
		<td>Bandar Lampung, <?= date('j') . ' ' . $bulan . ' ' . date('Y'); ?></td>
	</tr>
	<tr>
		<td>Pembimbing Utama</td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr><tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>_______________________________</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>




</body>
</html>

