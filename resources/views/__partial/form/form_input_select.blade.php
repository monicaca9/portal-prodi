<div class="form-group row">
    <label for="{{ $fieldname }}" class="col-sm-{{ !is_null($column) ? $column : 2 }} col-form-label">
        {!! $label . ($required === true ? '<span style="color:red;"> *</span>' : '') !!}
    </label>

    <div class="col-sm-{{ !is_null($column) ? (12 - $column) : 10 }}">
        <select 
            name="{{ $fieldname }}" 
            id="{{ $fieldname }}"
            class="form-control{{ $errors->has($fieldname) ? ' is-invalid' : '' }}"
            @if($required) required @endif
            @if(isset($attr) && is_array($attr))
                @foreach($attr as $key => $value)
                    {{ $key }}="{{ $value }}"
                @endforeach
            @endif
        >
            @if($default === true)
                <option value="" selected>-Pilih-</option>
            @endif

            @foreach($list as $key => $value)
                <option value="{{ $key }}"
                    {{ (!is_null($data) || old($fieldname)) && $key == (!is_null($data) ? $data : old($fieldname)) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>

        @if($errors->has($fieldname))
            <div class="invalid-feedback">
                {{ $errors->first($fieldname) }}
            </div>
        @endif

        @if(!is_null($helper))
            <small class="form-text text-muted">{{ $helper }}</small>
        @endif
    </div>
</div>
