@include('__partial.datatable')
@include('__partial.ckeditor')
@include('__partial.bootstrap-icon')

{!! FormInputText('nm_menu','Nama Menu','text',null,['required'=>true]) !!}
{!! FormInputText('nm_file','Nama File','text',null,['required'=>true]) !!}

