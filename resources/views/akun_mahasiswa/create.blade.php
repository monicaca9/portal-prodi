<input type="hidden" name="id_pd" value="{{ $data->id_pd }}">

<table class="table table-striped">
    <tbody>
    {!! tableRow('Nama',$data->nm_pd) !!}
    {!! tableRow('Nomor Induk Mahasiswa',$data->nim) !!}
    {!! tableRow('Asal Prodi',$data->prodi) !!}
    {!! tableRow('Status Keaktifan',is_null($data->ket_keluar)?$data->nm_stat_mhs:$data->ket_keluar) !!}
    </tbody>
</table>
<hr>
<div class="alert alert-info">
    <strong>INFO:</strong> Isikan email aktif yang akan menjadi username mahasiswa/peserta didik untuk login ke aplikasi.<br>
    Akses aplikasi akan dikirimkan ke email mahasiswa ybs.
</div>
{!! FormInputText('email','Email Aktif','email',null,['required'=>true]) !!}
