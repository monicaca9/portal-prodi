@include('__partial.bootstrap-icon')
{!! FormInputSelect('id_aplikasi','Aplikasi',true,true,$aplikasi,$app_kode) !!}
{!! FormInputText('nm_menu','Nama Menu','text',null,['required'=>true]) !!}
{!! FormInputText('nm_file','Nama File','text',null,['required'=>true]) !!}
{!! FormInputSelect('id_peran','Peran',true,true,$peran) !!}
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
<div class="form-group row">
    <label for="icon" class="col-sm-2 col-form-label">Ikon Menu</label>
    <div class="col-sm-10">
        <div class="input-group">
            <span class="input-group-prepend">
                <button class="btn btn-secondary" id="icon-button" role="iconpicker" data-icon="{{ old('icon') }}"></button>
            </span>
            <input type="text" name="icon" id="icon" class="form-control {{($errors->has('icon')?" is-invalid":"")}}" value="{{ old('icon') }}" placeholder="Icon menu">
        </div>
        @if($errors->has('icon'))
            <div class="invalid-feedback">
                {{ $errors->first('icon') }}
            </div>
        @endif
    </div>
</div>

{!! FormInputText('urutan_menu', 'Urutan', 'number', 0,['properties'=>['min'=>0]]) !!}
<div class="form-group row">
    <label class="col-sm-2">Status<span style="color:red;"> *</span></label>
    <div class="col-sm-10">
        <div class="form-check form-inline">
            <input type="checkbox" class="form-check-input" name="a_aktif" id="a_aktif" value="1">
            <label class="form-check-label" for="a_aktif"> Apakah aktif?</label> &nbsp;
            <input type="checkbox" class="form-check-input" name="a_tampil" id="a_tampil" value="1">
            <label class="form-check-label" for="a_tampil"> Apakah tampil?</label> &nbsp;
        </div>
    </div>
</div>
{!! FormInputSelect('id_group_menu','Parent Menu',false,true,$parent_menu) !!}
