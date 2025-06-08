<input type="hidden" name="id_gedung" value="{{ $gedung->id_gedung }}">
{!! FormInputText('kode_ruang','Kode Ruang','text',null,['required'=>true]) !!}
{!! FormInputText('nm_ruang','Nama Ruang','text',null,['required'=>true]) !!}
{!! FormInputText('kapasitas_ruang','Kapasitas Ruang','number',1,['required'=>true]) !!}
