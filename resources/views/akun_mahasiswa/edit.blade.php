@extends('template.default')
{{--@include('__partial.date')--}}
@include('__partial.ckeditor')
@include('__partial.datetime')
@include('__partial.select2')
{{--@include('__partial.fontawesome_picker')--}}
@include('__partial.bootstrap-icon')

@section('content')
    <div class="row">
        <section class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-pencil"></i> Ubah Menu</h3>
                </div>
                <form action="{{ route('manajemen_akses.menu.update',Crypt::encrypt($id)) }}" enctype="multipart/form-data" class="form-horizontal" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        {!! FormInputSelect('id_aplikasi','Aplikasi',true,true,$aplikasi,$data->id_aplikasi) !!}
                        {!! FormInputText('nm_menu','Nama Menu','text',$data->nm_menu,['required'=>true]) !!}
                        {!! FormInputText('nm_file','Nama File','text',$data->nm_file,['required'=>true]) !!}
                        <div class="form-group row">
                            <label for="icon" class="col-sm-2 col-form-label">Ikon Menu</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <button class="btn btn-secondary" id="icon-button" role="iconpicker" data-icon="{{ $data->icon }}"></button>
                                    </span>
                                    <input type="text" name="icon" id="icon" class="form-control {{($errors->has('icon')?" is-invalid":"")}}" value="{{ (!is_null($data->icon))?$data->icon:old('icon') }}" placeholder="Icon menu">
                                </div>
                                @if($errors->has('icon'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('icon') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {!! FormInputText('urutan_menu', 'Urutan', 'number', $data->urutan_menu,['properties'=>['min'=>0]]) !!}
                        <div class="form-group row">
                            <label class="col-sm-2">Status<span style="color:red;"> *</span></label>
                            <div class="col-sm-10">
                                <div class="form-check form-inline">
                                    <input type="checkbox" class="form-check-input" name="a_aktif" id="a_aktif" value="1" {{ $data->a_aktif==1?'checked':'' }}>
                                    <label class="form-check-label" for="a_aktif"> Apakah aktif?</label> &nbsp;
                                    <input type="checkbox" class="form-check-input" name="a_tampil" id="a_tampil" value="1" {{ $data->a_tampil==1?'checked':'' }}>
                                    <label class="form-check-label" for="a_tampil"> Apakah tampil?</label> &nbsp;
                                </div>
                            </div>
                        </div>
                        {!! FormInputSelect('id_group_menu','Parent Menu',false,true,$parent_menu,$data->id_group_menu) !!}
                    </div>
                    <div class="card-footer">
                        <a href="{{ url(route('manajemen_akses.menu').'?app_kode='.$data->id_aplikasi) }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                        <button type="submit" class="btn btn-warning pull-right btn-flat"><i class="fa fa-pencil"></i> Ubah</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="row">
        <section class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-user"></i> Hak Akses Peran</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Peran</th>
                                <th>Hak Akses</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($hak_akses AS $each_hak_akses)
                                <tr>
                                    <td>{{ $each_hak_akses->peran->nm_peran }}</td>
                                    <form action="{{ route('manajemen_akses.menu.update_hak_menu',[Crypt::encrypt($data->id_menu),Crypt::encrypt($each_hak_akses->id_peran)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="form-check form-inline">
                                                        <label class="form-check-label" for="update_insert_{{ $each_hak_akses->id_peran }}"><input class="form-check-input" type="checkbox" name="a_boleh_insert" id="update_insert_{{ $each_hak_akses->id_peran }}" value="1" {{ $each_hak_akses->a_boleh_insert==1?'checked':'' }}> Insert &nbsp;</label>
                                                        <label class="form-check-label" for="update_delete_{{ $each_hak_akses->id_peran }}"><input class="form-check-input" type="checkbox" name="a_boleh_delete" id="update_delete_{{ $each_hak_akses->id_peran }}" value="1" {{ $each_hak_akses->a_boleh_delete==1?'checked':'' }}> Delete &nbsp;</label>
                                                        <label class="form-check-label" for="update_update_{{ $each_hak_akses->id_peran }}"><input class="form-check-input" type="checkbox" name="a_boleh_update" id="update_update_{{ $each_hak_akses->id_peran }}" value="1" {{ $each_hak_akses->a_boleh_update==1?'checked':'' }}> Update &nbsp;</label>
                                                        <label class="form-check-label" for="update_sanggah_{{ $each_hak_akses->id_peran }}"><input class="form-check-input" type="checkbox" name="a_boleh_sanggah" id="update_sanggah_{{ $each_hak_akses->id_peran }}" value="1" {{ $each_hak_akses->a_boleh_sanggah==1?'checked':'' }}> Sanggah &nbsp;</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-success btn-xs btn-flat">Simpan</button>
                                    </form>
                                    {!! buttonDeleteMultipleId('manajemen_akses.menu.delete_hak_menu',[Crypt::encrypt($data->id_menu),Crypt::encrypt($each_hak_akses->id_peran)],'Expired Hak Menu') !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="row">
        <section class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-user-plus"></i> Tambah Hak Akses Peran</h3>
                </div>
                <form action="{{ route('manajemen_akses.menu.simpan_hak_menu') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <input type="hidden" name="id_menu" value="{{ $data->id_menu }}">
                        {!! FormInputSelect('id_peran','Peran',true,true,$list_peran) !!}
                        <div class="form-group row">
                            <label class="col-sm-2">Hak Akses<span style="color:red;"> *</span></label>
                            <div class="col-sm-10">
                                <div class="form-check form-inline">
                                    <input type="checkbox" class="form-check-input" name="a_boleh_insert" id="a_boleh_insert" value="1">
                                    <label class="form-check-label" for="a_boleh_insert"> Insert</label> &nbsp;
                                    <input type="checkbox" class="form-check-input" name="a_boleh_delete" id="a_boleh_delete" value="1">
                                    <label class="form-check-label" for="a_boleh_delete"> Delete</label> &nbsp;
                                    <input type="checkbox" class="form-check-input" name="a_boleh_update" id="a_boleh_update" value="1">
                                    <label class="form-check-label" for="a_boleh_update"> Update</label> &nbsp;
                                    <input type="checkbox" class="form-check-input" name="a_boleh_sanggah" id="a_boleh_sanggah" value="1">
                                    <label class="form-check-label" for="a_boleh_sanggah"> Sanggah</label> &nbsp;
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url(route('manajemen_akses.menu').'?app_kode='.$data->id_aplikasi) }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary pull-right btn-flat"><i class="fa fa-pencil"></i> Tambah Hak Akses</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
