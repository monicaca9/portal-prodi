<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RPS</title>

  <style>
th {
  
  padding: 10px;
}

td {
  padding: 10px;
}

</style>
</head>

<body>
<div class="card-body">
<table width="100%" border="1px" cellspacing="-20" cellpadding="0" style="border: 1px solid black; border-collapse: collapse; ">
  <thead>
    <tr>
      <td width="3%"><img src="https://upload.wikimedia.org/wikipedia/id/f/ff/Logo_UnivLampung.png" width="100" height="100" style="padding:20px;" ></td>
			<td width="97%" style="padding-left: -500px; padding-top: 0">
        
      <p class="judul" style="text-align:center">KEMENENTRIAN PENDIDIKAN DAN KEBUDAYAAN
        <br>UNIVERSITAS LAMPUNG <br>	
        FAKULTAS TEKNK<br><strong>
          JURUSAN TEKNIK ELEKTRO<br>
          PROGRAM STUDI TEKNIK INFORMATIKA</strong><br>
        </P>
        
      </td>
    </thead>
    <tbody>
      <tr >
        <td colspan="2">
          <p class="rps" style="text-align:center"> Rencana Pembelajaran Semester</p>
        </td>
      </tr>
    </tr>
  </tbody>
</table>

<table width="100%" border="1px" cellspacing="-20" cellpadding="0" style="border: 1px solid black; border-collapse: collapse; ">
  <thead style="text-align:center" >
    <tr>
      <td>Nama Matkul</td>
      <td>Kode Matkul</td>
      <td>SKS Matkul</td>
      <td rowspan="2">{!! config('mp.data_master.metode.'.$metode->metode) !!}</td>
    </tr>
  </thead>
  <tbody style="text-align:center">
    <tr>
      <td>{{$mk->nm_mk}}</td>
      <td>{{$mk->kode_mk}}</td>
      <td>{{$mk->sks_mk}}</td>
      
    </tr>
  </tbody>
</table>

<table width="100%" border="1px" cellspacing="-20" cellpadding="0" style="border: 1px solid black; border-collapse: collapse; text-align:center; ">
<tr>
  <th rowspan="2">Otorisasi</th>
  <td>Penyusun RPS</td>
</tr>
<tr>
  <td>{{$dosen->nm_sdm}}</td>
</tr>
</table>

<table width="100%" border="1px" cellspacing="-20" cellpadding="0" style="border: 1px solid black; border-collapse: collapse; font-size:90%">
<thead>
  <tr>
    <th>CPL-PRODI<br>(Capaian Pembelajaran Lulusan Program Studi)<br>Yang Dibebankan Pada Mata Kuliah</th>
    <th>Deskripsi CPL</th>
  </tr>
</thead>
<tbody>
@foreach($cpl_mk AS $each_data)
                            <tr>
                                <th>{{ $each_data->cpl->nm_cpl }}</th>
                                <td  style="padding:10px">{{ $each_data->cpl->desc_cpl }}</td>
                              
                            </tr>
                        @endforeach
</tbody>
<thead>
  <tr>
    <th>CPMK ( Capaian Pembelajaran Mata Kuliah)</th>
    <th>Deskripsi CPMK</th>
  </tr>
</thead>
<tbody>
@foreach($cpmk AS $no=>$cpmks)
                            <tr>
                                <th>CPMK - {{ $no+1 }}</th>
                                <td style="padding:10px">{{ $cpmks->cpmk }}</td>
                            </tr>
                        @endforeach
</tbody>
<tbody>
  <tr>
    <th>Daftar Pustaka</th>
    <td style ="padding:10px">
    @foreach($dapusmk as $no=>$dapusmks)
    [{{ $no+1 }}]  {{  $dapusmks->penulis }} ({{ $dapusmks->tahun }}), {{$dapusmks->judul}}<br>
    @endforeach
  </td>
  </tr>
</tbody>
<tbody>
  <tr>
  <th>Dosen Pengampu</th>
  <td style="padding:10px">
  @foreach($matkulrps as $no=>$mkrps)
      {{ $no+1 }}.  {{$mkrps->nm_sdm}}<br>
      @endforeach
    </td>
  </tr>
</tbody>
</table>
<br>
<hr>
<div style="overflow-x: auto;"  >
  <table width="100%" border="1px" cellspacing="-20" cellpadding="0" style="border: 1px solid black; border-collapse: collapse; table-layout: fixed; word-wrap: break-word;">
    <thead>    
      <tr >
        <th>Minggu-ke</th>
        <th>Tujuan Khusus</th>
        <th>Pokok Bahasan</th>
        <th>Referensi</th>
        <th>Sub Pokok Bahasan</th>
        <th>Metode</th>
        <th>Media</th>
        <th>Aktifitas Penugasan</th>
        <th>Bobot</th>
      </tr>
    </thead>
    
    <tbody style="padding:10px; overflow-x: auto; word-wrap: break-word">
      @foreach ($rpsdata as $rpsdatas)
      <tr>
        <th>      
          {!! $rpsdatas->minggu_ke !!}
        </th>
        <td>  
          {!! $rpsdatas->tujuan_khusus !!}
        </td>
        <td>
          {!! $rpsdatas->pokok_bahasan !!} 
        </td>
        <td>
          {!! $rpsdatas->referensi !!}
        </td>
        <td>
          {!! $rpsdatas->sub_pokok_bahasan !!}
        </td>
        <td>
          {!! $rpsdatas->metode !!}
        </td>
      <td>
        {!! $rpsdatas->media !!}
      </td>
      <td>
        {!! $rpsdatas->akt_penugasan !!}
      </td>
      @if($rpsdatas->bobot == null)
                                        <td> </td>
                                        @else
                                        <td>{!! $rpsdatas->bobot !!}%</td>
                                        @endif
    </tr>
    @endforeach
  </tbody>
</table>
</div>
<br>
<br>
<br>
<hr>
<div>
<h4>Kriteria Penilaian</h4>
<table width="25%" border="1px" cellspacing="-20" cellpadding="0" style="height=70px; border: 1px solid black; border-collapse: collapse; text-align:center; ">
<tr>
  <th>Nilai Akhir</th>
  <th>Huruf Mutu</th>
  <th>Konversi</th>
</tr>
<tr>
  <td>>76</td>
  <td>A</td>
  <td>4</td>
</tr>
<tr>
  <td>71-75</td>
  <td>B+</td>
  <td>3,5</td>
</tr>
<tr>
  <td>66-70</td>
  <td>B</td>
  <td>3</td>
</tr>
<tr>
  <td>61-65</td>
  <td>C+</td>
  <td>2,5</td>
</tr>
<tr>
  <td>56-60</td>
  <td>C</td>
  <td>2</td>
</tr>
<tr>
  <td>51-55</td>
  <td>D</td>
  <td>1</td>
</tr>
<tr>
  <td>&lt;51</td>
  <td>E</td>
  <td>0</td>
</tr>
</table>
</div>

<h4>Disahkan Pada : {{ $waktu_aktif->wkt_aktif }}</h4>
</div>
</body>
</html>
