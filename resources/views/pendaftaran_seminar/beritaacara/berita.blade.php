<!DOCTYPE html>
<html>

<head>
	<style type="text/css">
		@page {
			size: A4;
			margin: 5px 20px 5px 20px;
		}

		.judul {
			text-align: center;
		}

		.page_break {
			page-break-after: always;
		}

		body {
			margin: 10px;
			padding: 5px;
		}

		td,
		th {
			padding: 3px;
		}
	</style>
</head>

<body>

	@if ($komponen_nilai->isNotEmpty())
	@foreach ($komponen_nilai as $peran_dosen => $kategori_data)
	@php
	$rata_rata_nilai_komponen = 0;
	$total_nilai_kategori = 0;
	@endphp

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="125" height="125"></td>
			<td width="97%">
				<p class="judul">
					KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN<br>
					UNIVERSITAS LAMPUNG <br>
					FAKULTAS {{ $profil->fakultas }}<br>
					<strong>PROGRAM STUDI {{ $profil->nm_prodi }}</strong><br>
					Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>
					Telepon (0721) 704947 Faksmile (0721) 704947 <br>
					Laman <a href="http://eng.unila.ac.id/">http://eng.unila.ac.id/</a>
				</p>
			</td>
		</tr>
	</table>

	<hr>

	<p class="judul">
		<strong><u>DAFTAR NILAI SEMINAR {{ strtoupper($nm_jns_seminar) }}</u></strong><br>
		@if (!is_null($no_ba_seminar))
		No.: {{ $no_ba_seminar->no_ba_daftar_seminar . ' ' . $no_ba_seminar->kode_ba_daftar_seminar }}<br>
		@endif
	</p>

	<table>
		<tr>
			<td width="23%">Nama</td>
			<td width="2%">:</td>
			<td width="75%">{{ $profil->nm_pd }}</td>
		</tr>
		<tr>
			<td>NPM</td>
			<td>:</td>
			<td>{{ $profil->nim }}</td>
		</tr>
		<tr>
			<td>Pukul</td>
			<td>:</td>
			<td>{{ config('mp.data_master.waktu')[$data->waktu] }} s.d. Selesai</td>
		</tr>
		<tr>
			<td>Tempat</td>
			<td>:</td>
			<td>{{ $gedung->nm_gedung }} / {{ $ruang->nm_ruang }}</td>
		</tr>
		<tr>
			<td valign="top">Judul </td>
			<td valign="top">:</td>
			<td valign="top">{{ $data->judul_akt_mhs }}</td>
		</tr>
	</table>

	@foreach ($kategori_data as $kategori => $komponen_list)
	<p><strong>{{ chr(64 + $loop->iteration) }}. {{ $komponen_list->first()->nm_kategori_nilai }}</strong></p>

	<table width="100%" border="1" cellspacing="0">
		<tr>
			<th width="40px" align="center">NO.</th>
			<th width="350px" align="center">ASPEK YANG DINILAI</th>
			<th width="100px" align="center">NILAI</th>
		</tr>

		@php
		$total_nilai_komponen = 0;
		$jumlah_komponen = $komponen_list->count();
		@endphp

		@foreach ($komponen_list as $index => $komponen)
		<tr>
			<td align="center">{{ $index + 1 }}</td>
			<td>{{ $komponen->nm_komponen_nilai }}</td>
			<td align="center">{{ $komponen->skor }}</td>
		</tr>
		@php $total_nilai_komponen += $komponen->skor; @endphp
		@endforeach

		@php
		$rata_rata_nilai_komponen = number_format($total_nilai_komponen / $jumlah_komponen, 2);
		$total_nilai_kategori += $rata_rata_nilai_komponen;
		@endphp

		<tr>
			<td colspan="2">Jumlah</td>
			<td align="center">{{ number_format($total_nilai_komponen,2) }}</td>
		</tr>
		<tr>
			<td colspan="2">Nilai Rata-rata</td>
			<td align="center">{{ $rata_rata_nilai_komponen }}</td>
		</tr>
	</table>
	@endforeach

	@php $jumlah_kategori = $kategori_data->count(); @endphp

	<table>
		<tr>
			<td style="text-align:left;padding-left:5px;vertical-align:middle;">
				Nilai Rata-rata Keseluruhan =
			</td>
			<td style="text-align:center;padding:0 5px;">
				<span style="display:block;border-bottom:1px solid black;padding:2px 5px;">
					Total Rata-rata Kategori
				</span>
				<span>Jumlah Kategori Penilaian</span>
			</td>
			<td style="text-align:center;padding:0 5px;">
				=
				<span>&nbsp;</span>
			</td>
			<td style="text-align:center;padding:0 5px;">
				<span style="display:block;border-bottom:1px solid black;padding:2px 5px;">
					{{ number_format($total_nilai_kategori, 2) }}
				</span>
				<span>{{ $jumlah_kategori }}</span>
			</td>
			<td style="text-align:left;padding-left:5px;vertical-align:middle;">
				= {{ number_format($total_nilai_kategori / $jumlah_kategori, 2) }}
			</td>
		</tr>
	</table>



	<table style="float: right; margin-top: 20px; margin-right: 20px;">

		<tr>
			<td style="vertical-align: top; padding-bottom:50px; word-break: break-word; white-space: normal;">
				Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>

				{{ config('mp.data_master.peran_seminar')[$kategori_data->first()->first()->peran] . ' ' . $kategori_data->first()->first()->urutan }}
				<br>
				<br>
				<br>
				<br>
				<br>
				{{ $kategori_data->first()->first()->nm_sdm ?? $kategori_data->first()->first()->nm_pembimbing_luar_kampus ?? $kategori_data->first()->first()->nm_penguji_luar_kampus ?? $kategori_data->first()->first()->nm_pemb_lapangan ?? null }} <br>
				{{ $kategori_data->first()->first()->nip ? 'NIP. ' . $kategori_data->first()->first()->nip : '' }}
			</td>
		</tr>
	</table>

	@if (!$loop->last)
	<div class="page_break"></div>
	@endif
	@endforeach

	@else
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="125" height="125"></td>
			<td width="97%">
				<p class="judul">
					KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN<br>
					UNIVERSITAS LAMPUNG <br>
					FAKULTAS {{ $profil->fakultas }}<br>
					<strong>PROGRAM STUDI {{ $profil->nm_prodi }}</strong><br>
					Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>
					Telepon (0721) 704947 Faksmile (0721) 704947 <br>
					Laman <a href="http://eng.unila.ac.id/">http://eng.unila.ac.id/</a>
				</p>
			</td>
		</tr>
	</table>

	<hr>

	<p class="judul">
		<strong><u>DAFTAR NILAI SEMINAR {{ strtoupper($nm_jns_seminar) }}</u></strong><br>
		@if (!is_null($no_ba_seminar))
		No.: {{ $no_ba_seminar->no_ba_daftar_seminar . ' ' . $no_ba_seminar->kode_ba_daftar_seminar }}<br>
		@endif
	</p>

	<table>
		<tr>
			<td width="23%">Nama</td>
			<td width="2%">:</td>
			<td width="75%">{{ $profil->nm_pd }}</td>
		</tr>
		<tr>
			<td>NPM</td>
			<td>:</td>
			<td>{{ $profil->nim }}</td>
		</tr>
		<tr>
			<td>Pukul</td>
			<td>:</td>
			<td>{{ config('mp.data_master.waktu')[$data->waktu] }} s.d. Selesai</td>
		</tr>
		<tr>
			<td>Tempat</td>
			<td>:</td>
			<td>{{ $gedung->nm_gedung }} / {{ $ruang->nm_ruang }}</td>
		</tr>
		<tr>
			<td valign="top">Judul </td>
			<td valign="top">:</td>
			<td valign="top">{{ $data->judul_akt_mhs }}</td>
		</tr>
	</table>
	@endif

	<div class="page_break"></div>

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="125" height="125"></td>
			<td width="97%" style="padding-left: 10px;">
				<p class="judul">
					KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN<br>
					UNIVERSITAS LAMPUNG <br>
					FAKULTAS {{ $profil->fakultas }}<br>
					<strong>PROGRAM STUDI {{ $profil->nm_prodi }}</strong><br>
					Jalan Prof. Seomantri Brojonegoro No. 1 Bandar Lampung 35145 <br>
					Telepon (0721) 704947 Faksmile (0721) 704947 <br>
					Laman <a href="http://eng.unila.ac.id/">http://eng.unila.ac.id/</a>
				</p>
			</td>
		</tr>
	</table>
	<hr>

	<p class="judul">
		<strong><u>BERITA ACARA SEMINAR {{ strtoupper($nm_jns_seminar) }}</u></strong><br>
		@if (!is_null($no_ba_seminar))
		No.: {{ $no_ba_seminar->no_ba_daftar_seminar . ' ' . $no_ba_seminar->kode_ba_daftar_seminar }}<br>
		@endif
	</p>
	<br>
	<p> Pada hari ini, {{ config('mp.data_master.hari')[$data->hari]}}, tanggal {{tglIndonesia($data->tgl_mulai)}} telah dilaksanakan Seminar {{$nm_jns_seminar}} atas nama mahasiswa : </p>

	<table>
		<tr>
			<td width="23%">Nama</td>
			<td width="2%">:</td>
			<td width="75%">{{ $profil->nm_pd }}</td>
		</tr>
		<tr>
			<td>NPM</td>
			<td>:</td>
			<td>{{ $profil->nim }}</td>
		</tr>
		<tr>
			<td>Pukul</td>
			<td>:</td>
			<td>{{ config('mp.data_master.waktu')[$data->waktu] }} s.d. Selesai</td>
		</tr>
		<tr>
			<td>Tempat</td>
			<td>:</td>
			<td>{{ $gedung->nm_gedung }} / {{ $ruang->nm_ruang }}</td>
		</tr>
		<tr>
			<td valign="top">Judul</td>
			<td valign="top">:</td>
			<td valign="top">{{ $data->judul_akt_mhs }}</td>
		</tr>
	</table>
	<br>

	<p>Dengan hasil sebagai berikut :</p>

	<table width="100%" border="1" cellspacing="0">
		<tr>
			<th width="40px" align="center">NO.</th>
			<th width="350px" align="center">JABATAN</th>
			<th width="100px" align="center">NILAI</th>
			<th width="100px" align="center">PERSENTASE</th>
			<th width="100px" align="center">NILAI AKHIR</th>
		</tr>
		@php
		$total_nilai = 0;
		@endphp

		@if(!is_null($data_nilai_seminar) && !is_null($data_skor_kategori))
		@foreach ($data_skor_kategori as $no => $each_skor_komponen)
		@foreach ($data_distribusi_nilai as $distribusi)
		@if (
		$each_skor_komponen->peran == $distribusi->peran &&
		$each_skor_komponen->urutan == $distribusi->urutan
		)

		@php
		$nilai = $each_skor_komponen->skor * ($distribusi->persentase / 100);
		$total_nilai += $nilai;
		@endphp
		<tr>
			<td align="center">{{ $no + 1 }}</td>
			<td>{{ config('mp.data_master.peran_seminar')[$distribusi->peran] . ' ' . $distribusi->urutan}}</td>
			<td align="center">{{ $each_skor_komponen->skor }}</td>
			<td align="center">{{ number_format($distribusi->persentase,2) }} %</td>
			<td align="center">{{ number_format($nilai, 2) }}</td>
		</tr>
		@endif
		@endforeach
		@endforeach

	</table>
	<br>
	<table>
		<tr>
			<td valign="top">Nilai Akhir Rata-rata </td>
			<td valign="top">:</td>
			<td valign="top">{{ number_format($data_nilai_seminar->skor, 2) }}</td>

		</tr>
		<tr>
			<td valign="top">Huruf Mutu </td>
			<td valign="top">:</td>
		</tr>
		<tr>
			<td valign="top">Perbaikan </td>
			<td valign="top">:</td>
		</tr>
	</table>

	<br>
	<br>

	<table style="width: 100%; text-align: left;">
		@foreach ($data_skor_kategori->chunk(2) as $chunk)
		<tr>
			@foreach ($chunk as $kategori)
			<td style="vertical-align: top; padding-bottom:50px; word-break: break-word; white-space: normal;">
				{{ config('mp.data_master.peran_seminar')[$kategori->peran] . ' ' . $kategori->urutan }} <br>
				<br>
				<br>
				<br>
				<br>
				<br>
				{{ $kategori->nm_sdm ?? $kategori->nm_pembimbing_luar_kampus ?? 
                $kategori->nm_penguji_luar_kampus ?? $kategori->nm_pemb_lapangan ?? 'Tidak Ada Nama' }}</br>
				{{ $kategori->nip ? 'NIP. ' . $kategori->nip : '' }}
			</td>
			@endforeach
		</tr>
		@endforeach
	</table>
	@endif

</body>

</html>