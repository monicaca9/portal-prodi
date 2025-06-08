@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> SYARAT SEMINAR - {{ $data->jenisSeminar->nm_jns_seminar }} ({{ $data->prodi->nm_lemb.' ('.$data->prodi->jenjang->nm_jenj_didik.')' }})</h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tbody>
                    {!! tableRow('Urutan Seminar',$data->urutan) !!}
                    {!! tableRow('Jumlah Pembimbing',$data->jmlh_pembimbing.' Pembimbing') !!}
                    {!! tableRow('Jumlah Penguji',$data->jmlh_penguji.' Penguji') !!}
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR SYARAT SEMINAR</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Syarat</th>
                                <th>Keterangan</th>
                                <th>Urutan</th>
                                <th>File Syarat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($syarat AS $no=>$each_syarat)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_syarat->nm_syarat_seminar }}</td>
                                <td>{{ $each_syarat->keterangan }}</td>
                                <td class="text-center">{{ $each_syarat->urutan }}</td>
                                <td class="text-center">
                                    <a href="{{route('daftar_seminar_prodi.detail.daftar_dokumen',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_syarat->id_list_syarat)])}}" class="btn btn-flat btn-info btn-xs">
                                        {{ $each_syarat->total_dokumen.' File' }}</a>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center">
                                        {!! buttonEditMultipleId('daftar_seminar_prodi.detail.ubah',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_syarat->id_list_syarat)],'Ubah Syarat') !!}
                                        {!! buttonDeleteMultipleId('daftar_seminar_prodi.detail.delete',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_syarat->id_list_syarat)],'Hapus Syarat') !!}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <form action="{{ route('daftar_seminar_prodi.detail.simpan',Crypt::encrypt($data->id_seminar_prodi)) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"><i class="fa fa-list-alt"></i> TAMBAH SYARAT SEMINAR</h3>
                    <div class="card-tools">
                        <button type="submit" class="btn btn-xs btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2" for="a_input_baru">Tambah Baru<span style="color:red;"> *</span></label>
                        <div class="col-sm-10">
                            <div class="form-check form-inline">
                                <input type="checkbox" class="form-check-input" name="a_input_baru" id="a_input_baru" value="1">
                                <label class="form-check-label" for="a_input_baru"> Form Input Baru</label> &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="form-old">
                        {!! FormInputSelect('id_syarat_seminar','Pilih Syarat Seminar',true,true,$list_syarat) !!}
                    </div>
                    <div class="form-new">
                        {!! FormInputText('nm_syarat_seminar','Syarat Seminar','text',null, ['required'=>true]) !!}
                        {!! FormInputText('keterangan','Keterangan Syarat','text',null) !!}
                    </div>
                    {!! FormInputText('urutan','Urutan','number',null,['required'=>true]) !!}
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('daftar_seminar_prodi')) !!}
    </div>
</div>
@endsection

@push('js')
<script>
    $(function() {
        cek_atrr($('#a_input_baru').prop("checked"));
        $('#a_input_baru').click(function() {
            cek_atrr($(this).prop("checked"));
        });

        function cek_atrr(checkbox_val) {
            if (checkbox_val == true) {
                $('.form-old').hide();
                $('.form-new').show();
                $('#nm_syarat_seminar').prop('required', true);
                $('#keterangan').prop('required', false);
                $('#id_syarat_seminar').prop('required', false);
            } else {
                $('.form-old').show();
                $('.form-new').hide();
                $('#nm_syarat_seminar').prop('required', false);
                $('#keterangan').prop('required', false);
                $('#id_syarat_seminar').prop('required', true);
            }
        }
    })
</script>
@endpush