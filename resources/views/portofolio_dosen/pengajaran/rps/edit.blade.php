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
                    <h3 class="card-title"><i class="fa fa-pencil"></i> Ubah Deskripsi Mata Kuliah</h3>
                </div>
                <form action="{{ route('pelaksanaan_pendidikan.pengajaran.update_desc_mk',Crypt::encrypt($mk->id_mk)) }}" enctype="multipart/form-data" class="form-horizontal" method="post">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        {!! FormInputText('desc_mk','Deskripsi Mata Kuliah','text',$mk->desc_mk) !!}
                        <button type="submit" class="btn btn-warning pull-right btn-flat"><i class="fa fa-pencil"></i> Ubah</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
