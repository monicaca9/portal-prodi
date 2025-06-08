<!DOCTYPE html>
<html>
<head>
	<title>Berita Acara Kerja Praktek</title>
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
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 Telp. 0721-785508 Fax. 0721-785508</span></P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>BERITA ACARA SEMINAR KERJA PRAKTEK</u> <br>
	NOMOR.:    74/UN26.15.07/PP.05.02/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>
	<p>Pada hari ini <strong><?= $hari ?>, <?= date('j', strtotime($data->tgl_mulai)) .' '. $bulan ?>. </strong> telah dilaksanakan Seminar Kerja Praktek atas nama mahasiswa :</p>


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
<p>Dengan hasil sebagai berikut :</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="300px" align="center">JABATAN</th>
		<th width="100px" align="center">NILAI</th>
		<th width="110px" align="center">PROSENTASE</th>
		<th width="130px" align="center">NILAI AKHIR</th>
	</tr><tr>
		<td align="center">1</td>
		<td>Pembimbing</td>
		<td></td>
		<td align="center">60%</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Pembimbing Lapangan</td>
		<td></td>
		<td align="center">40%</td>
		<td></td>
	</tr>
</table>
<br><br>

<table  width="100%">
	<tr>
		<td  width="25%">Nilai Akhir Rata-rata</td>
		<td  width="5%">:</td>
		<td  width="70%">......................................................................................................................</td>
	</tr><tr>
		<td  width="25%">Huruf Mutuf</td>
		<td  width="5%">:</td>
		<td  width="70%">......................................................................................................................</td>
	</tr><tr>
		<td  width="25%">Perbaikan</td>
		<td  width="5%">:</td>
		<td  width="70%">......................................................................................................................</td>
	</tr>
	
</table>
<br><br>
<p>Demikian berita acara ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
<br>

<table  width="100%">
	<tr>
		<td width="45%" align="center">Pembimbing</td>
		
		<td width="45%"align="center">Penguji</td>
	</tr>
	<tr>
		<td><br><br><br><br><br></td>
		<td></td>
		<td></td>
	</tr>

	<tr>
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
		<td width="3%" align="left">Nilai</td>
		<td width="15%" align="left">A &#60 76 <br> B+ 70 -&#60 76 <br> B 66 -&#60 76 <br> C+ 60 -&#60 66</td>
		
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
	<strong><u>DAFTAR NILAI SEMINAR KERJA PRAKTEK</u> <br>
	NOMOR.:    74/UN26.15.07/PP.05.02/2021</strong><br>
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
		<td align="center">1</td>
		<td>Pembahasan</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Bahasa</td>
		<td></td>
	</tr><tr>
		<td align="center">3</td>
		<td>Teknik Penyusunan</td>
		<td></td>
	</tr><tr>
		<td align="center">4</td>
		<td>Kegunaan Praktis</td>
		<td></td>
	</tr><tr>
		<td align="center">5</td>
		<td>Pembagian Materi</td>
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
	<strong><u>DAFTAR NILAI SEMINAR KERJA PRAKTEK</u> <br>
	NOMOR.:    74/UN26.15.07/PP.05.02/2021</strong><br>
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
		<td align="center">1</td>
		<td>Pembahasan</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Bahasa</td>
		<td></td>
	</tr><tr>
		<td align="center">3</td>
		<td>Teknik Penyusunan</td>
		<td></td>
	</tr><tr>
		<td align="center">4</td>
		<td>Kegunaan Praktis</td>
		<td></td>
	</tr><tr>
		<td align="center">5</td>
		<td>Pembagian Materi</td>
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
		<td>Penguji</td>
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

