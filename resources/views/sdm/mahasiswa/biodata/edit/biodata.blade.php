<form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
    @csrf
    @method('PUT')
    <input type="hidden" name="kode" value="biodata">
    <div class="card card-body">
        <div class="form-group row">
            <label for="foto" class="col-sm-2 col-form-label">Foto <span class="text-danger">*</span></label>
            <div class="col-sm-2">
                @if(is_null($mhs->id_blob))
                    <img src="{{ asset('images/blank-profile.png') }}" alt="foto">
                @else
                    <?php $foto = DB::table('dok.large_object')->where('id_blob',$mhs->id_blob)->first(); ?>
                    <img src="data:{{$foto->mime_type}};base64,{{stream_get_contents($foto->blob_content)}}" width="200" alt="foto">
                @endif
            </div>
            <div class="offset-sm-1 col-sm-7">
                <input type="file" name="foto" id="foto" class="form-control" aria-describedby="fotoHelpBlock">
                <small id="fotoHelpBlock" class="form-text text-muted">
                    Pas Foto harus formal dengan ukuran 3x4 max 1MB.
                </small>
            </div>
        </div>
    </div>
    {!! FormInputStatic('Nama Lengkap <i class="text-danger">*</i>',$mhs->nm_pd) !!}
    {!! FormInputStatic('Nomor Induk Mahasiswa <i class="text-danger">*</i>',$info->nim) !!}
    {!! FormInputStatic('Asal Program Studi <i class="text-danger">*</i>',$info->prodi) !!}
    <hr>
    {!! FormInputText('nik','NIK','text',$mhs->nik,['required'=>true,'placeholder'=>'Tuliskan Nomor Induk Penduduk yang ada pada KTP','helper'=>'jika dari luar Negeri isikan passport anda']) !!}
    {!! FormInputSelect('jk','Jenis Kelamin',true,false,['L'=>'Laki-laki','P'=>'Perempuan'],$mhs->jk) !!}
    {!! FormInputText('tmpt_lahir','Tempat Lahir','text',$mhs->tmpt_lahir,['required'=>true,'placeholder'=>'Tuliskan Tempat Lahir anda sesuai KTP']) !!}
    {!! FormInputText('tgl_lahir','Tanggal Lahir','text',$mhs->tgl_lahir,['required'=>true,'placeholder'=>'Tuliskan Tanggal Lahir anda sesuai KTP','properties'=>['autocomplete'=>'off'],'readonly'=>true]) !!}
    {!! FormInputSelect('id_agama','Agama',true,true,$agama,$mhs->id_agama) !!}
    <hr>
    {!! FormInputText('jln','Alamat','text',$mhs->jln,['required'=>true,'placeholder'=>'Contoh: Perum Korpri Blok XX No. 99']) !!}
    <div class="form-group row">
        <label for="rt" class="offset-2 col-sm-1 col-form-label">RT</label>
        <div class="col-sm-1">
            <input name="rt" id="rt" type="text" class="form-control {{($errors->has('rt')?" is-invalid":"")}}" value="{{ (!is_null($mhs->rt))?$mhs->rt:old('rt') }}" placeholder="RT">
            @if($errors->has('rt'))
                <div class="invalid-feedback">
                    {{ $errors->first('rt') }}
                </div>
            @endif
        </div>
        <label for="rw" class="offset-sm-1 col-sm-1 col-form-label">RW</label>
        <div class="col-sm-1">
            <input name="rw" id="rw" type="text" class="form-control {{($errors->has('rw')?" is-invalid":"")}}" value="{{ (!is_null($mhs->rw))?$mhs->rw:old('rw') }}" placeholder="RW">
            @if($errors->has('rw'))
                <div class="invalid-feedback">
                    {{ $errors->first('rw') }}
                </div>
            @endif
        </div>
        <label for="nm_dsn" class="offset-sm-1 col-sm-1 col-form-label">Dusun</label>
        <div class="col-sm-3">
            <input name="nm_dsn" id="nm_dsn" type="text" class="form-control {{($errors->has('nm_dsn')?" is-invalid":"")}}" value="{{ (!is_null($mhs->nm_dsn))?$mhs->nm_dsn:old('nm_dsn') }}" placeholder="Dusun">
            @if($errors->has('nm_dsn'))
                <div class="invalid-feedback">
                    {{ $errors->first('nm_dsn') }}
                </div>
            @endif
        </div>
    </div>
    {!! FormInputText('ds_kel','Desa/Kelurahan','text',$mhs->ds_kel) !!}
    {!! FormInputText('kode_pos','Kode Pos','number',$mhs->kode_pos,['properties'=>['oninput'=>'javascript:if(this.value.length>this.maxLength)this.value=this.value.slice(0,this.maxLength);','maxlength'=>5]]) !!}
    {!! FormInputSelect('id_wil','Kota/Kabupaten',true,true,$kota_kab,$mhs->id_wil) !!}
    {!! FormInputSelect('id_kewarganegaraan','Kewarganegaraan',true,true,$negara,$mhs->id_kewarganegaraan) !!}
    {!! FormInputText('tlpn_rumah','No. Telepon Rumah','number',$mhs->tlpn_rumah) !!}
    {!! FormInputText('tlpn_hp','No. HP','number',$mhs->tlpn_hp,['required'=>true]) !!}
    <hr>
    <button type="submit" class="btn btn-success btn-flat btn-block">SIMPAN</button>
    @if(!is_null($mhs->id_wil))
        <hr>
        <div class="pull-right">
            <a href="{{ url(route('biodata.ubah').'?tab=keluarga') }}" class="btn btn-flat btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></a>
        </div>
    @endif
</form>

