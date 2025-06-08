<!DOCTYPE html>
<html>
<head>
	<title>Berita Acara Seminar Proposal</title>
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
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>Telepon (0721) 704947 Faksmile (0721) 704947 <br>Laman http://eng.unila.ac.id/</P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>DAFTAR NILAI SEMINAR PROPOSAL</u> <br>
	No.:    /UN.26.15.07/PP.07.02.01/2021</strong><br>
</p>
</td>
		</tr>
	</table>
	
	<br>


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
<p>A. Nilai Makalah Seminar Proposal :</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td align="center">1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td align="center">3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td align="center">4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td align="center">5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td align="center">6</td>
		<td>Bahasa</td>
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


	<p>B. Nilai Presentasi Seminar Proposal :</p>
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
		<td colspan="2">Nilai Rata-rata A</td>
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
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>Telepon (0721) 704947 Faksmile (0721) 704947 <br>Laman http://eng.unila.ac.id/</P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>DAFTAR NILAI SEMINAR PROPOSAL</u> <br>
	No.:    /UN.26.15.07/PP.07.02.01/2021</strong><br>
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
<p>A. Nilai Makalah Seminar Proposal :</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td align="center">1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td align="center">3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td align="center">4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td align="center">5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td align="center">6</td>
		<td>Bahasa</td>
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


	<p>B. Nilai Presentasi Seminar Proposal :</p>
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
		<td colspan="2">Nilai Rata-rata A</td>
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
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>Telepon (0721) 704947 Faksmile (0721) 704947 <br>Laman http://eng.unila.ac.id/</P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>DAFTAR NILAI SEMINAR PROPOSAL</u> <br>
	No.:    /UN.26.15.07/PP.07.02.01/2021</strong><br>
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
<p>A. Nilai Makalah Seminar Proposal :</p>
<table border="1" cellspacing="0">
	<tr>
		<th width="40px" align="center">NO.</th>
		<th width="350px" align="center">ASPEK YANG DINILAI</th>
		<th width="100px" align="center">NILAI</th>
	</tr><tr>
		<td align="center">1</td>
		<td>Originalitas / Keaslian</td>
		<td></td>
	</tr><tr>
		<td align="center">2</td>
		<td>Dasar / Landasan Pemilihan Judul</td>
		<td></td>
	</tr><tr>
		<td align="center">3</td>
		<td>Ketajaman penulisan / Tujuan</td>
		<td></td>
	</tr><tr>
		<td align="center">4</td>
		<td>Tinjauan Pustaka</td>
		<td></td>
	</tr><tr>
		<td align="center">5</td>
		<td>Metodologi</td>
		<td></td>
	</tr><tr>
		<td align="center">6</td>
		<td>Bahasa</td>
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


	<p>B. Nilai Presentasi Seminar Proposal :</p>
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
		<td colspan="2">Nilai Rata-rata A</td>
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
	Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>Telepon (0721) 704947 Faksmile (0721) 704947 <br>Laman http://eng.unila.ac.id/</P>



</td>
		</tr>
		<tr >
			<td colspan="2"><hr>
			<p class="judul">
	<strong><u>DAFTAR NILAI SEMINAR PROPOSAL</u> <br>
	No.:    /UN.26.15.07/PP.07.02.01/2021</strong><br>
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
<p>Dengan hasil sebagai berikut :</p>
<table border="1" cellspacing="0" width="100%">
	<tr>
		<th width="5%" align="center">NO.</th>
		<th width="30%" align="center">JABATAN</th>
		<th width="15%" align="center">NILAI</th>
		<th width="25%" align="center">PRESENTASE</th>
		<th width="25%" align="center">NILAI AKHIR</th>
	</tr>
	<tr>
		<td width="5%" align="center">1</td>
		<td width="30%" align="left">Pembimbing Utama/Ketua</td>
		<td width="15%" align="center"></td>
		<td width="25%" align="center">50%</td>
		<td width="25%" align="center"></td>
	</tr>
	<tr>
		<td width="5%" align="center">2</td>
		<td width="30%" align="left">Pembimbing Pendamping/Sekretaris</td>
		<td width="15%" align="center"></td>
		<td width="25%" align="center">20%</td>
		<td width="25%" align="center"></td>
	</tr>

	<tr>
		<td width="5%" align="center">3</td>
		<td width="30%" align="left">Penguji Utama</td>
		<td width="15%" align="center"></td>
		<td width="25%" align="center">30%</td>
		<td width="25%" align="center"></td>
	</tr>

</table>

<br>
	
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

<p>Demikian berita acara ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>

<table  width="100%">
	<tr>
		<td width="35%" align="center">Pembimbing Pendamping/ Sekretaris</td>
		<td width="30%" align="center">Penguji Utama</td>
		<td width="35%"align="center">Pendamping Utama/Ketua</td>
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

<table width="60%" style="font-size: 10px">
	<tr>
		<td width="5%" align="left">Nilai</td>
		<td width="10%" align="left">A => 76 <br> B 66 - 71</td>
		<td width="10%" align="left"><p>B+ 71 -&#60 76  <br>C+ 61 -&#60 66</p></td>
		<td width="10%" align="left">C 56 -&#60 61</td>
	</tr>
	
	

</table>





</body>
</html>

