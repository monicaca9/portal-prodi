<form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
    @csrf
    @method('PUT')
    <div class="mt-2">
        <div class="alert alert-info">
            <i class="text-danger">*</i> Silahkan pilih Konsentrasi Prodi Anda.<br>
            Pastikan Anda memilih konsentrasi prodi sesuai dengan ketentuan dan semester yang berlaku di program studi Anda.
        </div>
    </div>
    <input type="hidden" name="kode" value="konsentrasi_prodi">
    <input type="hidden" name="id_pd" value='{{$info->id_pd}}'>
    {!! FormInputSelect('id_konsentrasi_prodi','Konsentrasi Prodi',false,true,$konsentrasi_prodi, $konsentrasi_prodi_pd->id_konsentrasi_prodi ?? null) !!}
    <hr>
    <button type="submit" class="btn btn-success btn-flat btn-block">SIMPAN</button>
    @if($konsentrasi_prodi_pd && !is_null($konsentrasi_prodi_pd->id_konsentrasi_prodi))
    <hr>
    <div class="pull-right">
        <a href="{{ url(route('biodata.ubah').'?tab=keluarga') }}" class="btn btn-flat btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></a>
    </div>
    @endif
</form>