{!! FormInputText('id_peran','ID Peran','number',$data->id_peran,['required'=>true,'properties'=>['min'=>3000],'readonly'=>true]) !!}
{!! FormInputText('nm_peran','Nama Peran','text',$data->nm_peran,['required'=>true]) !!}
<div class="form-group row">
    <label class="col-sm-2">Butuh SK?<span style="color:red;"> *</span></label>
    <div class="col-sm-10">
        <div class="form-check form-inline">
            <input type="checkbox" class="form-check-input" name="a_perlu_sk" id="a_perlu_sk" value="1" {{ $data->a_perlu_sk==1?'checked':'' }}>
            <label class="form-check-label" for="a_perlu_sk"> Ya, Butuh SK</label> &nbsp;
        </div>
    </div>
</div>
