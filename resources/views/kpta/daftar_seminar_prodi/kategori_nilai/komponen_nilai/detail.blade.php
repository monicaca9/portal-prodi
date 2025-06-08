@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> KOMPONEN PENILAIAN - {{ $data->jenisSeminar->nm_jns_seminar }} ({{ $data->prodi->nm_lemb.' ('.$data->prodi->jenjang->nm_jenj_didik.')' }})</h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <tbody>
                    {!! tableRow('Nama Seminar',$data->jenisSeminar->nm_jns_seminar) !!}
                    {!! tableRow('Nama Kategori Penilaian',$data_kategori_nilai->nm_kategori_nilai) !!}
                    {!! tableRow('Keterangan Kategori Penilaian',$data_kategori_nilai->keterangan) !!}
                    {!! tableRow('Urutan Kategori Penilaian',$data_kategori_nilai->urutan) !!}
                </tbody>
            </table>
        </div>
        <hr>
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR KOMPONEN PENILAIAN SEMINAR</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Komponen Nilai</th>
                                <th>Keterangan</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($komponen_nilai AS $no=>$each_komponen)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_komponen->nm_komponen_nilai }}</td>
                                <td>{{ $each_komponen->keterangan }}</td>
                                <td>{{ $each_komponen->urutan }}</td>
                                <td>
                                        {!! buttonEditMultipleId('daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai.ubah',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($data_kategori_nilai->id_list_kategori_nilai), Crypt::encrypt($each_komponen->id_list_komponen_nilai)],'Ubah Komponen') !!}
                                        {!! buttonDeleteMultipleId('daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai.delete',[Crypt::encrypt($data->id_seminar_prodi), Crypt::encrypt($data_kategori_nilai->id_list_kategori_nilai), Crypt::encrypt($each_komponen->id_list_komponen_nilai)],'Hapus Komponen') !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <form action="{{ route('daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai.simpan',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($data_kategori_nilai->id_list_kategori_nilai)]) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"><i class="fa fa-list-alt"></i> TAMBAH KOMPONEN PENILAIAN SEMINAR</h3>
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
                        {!! FormInputSelect('id_komponen_nilai','Pilih Komponen Nilai Seminar',true,true,$list_komponen_nilai) !!}
                    </div>
                    <div class="form-new">
                        {!! FormInputText('nm_komponen_nilai','Nama Komponen Penilaian Seminar ','text',null,['required'=>true]) !!}
                        {!! FormInputText('keterangan', 'Keterangan', 'text', null) !!}
                    </div>
                    {!! FormInputText('urutan','Urutan','number',null,['required'=>true]) !!}
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        {!! buttonBack( route('daftar_seminar_prodi.detail_kategori',Crypt::encrypt($data->id_seminar_prodi))) !!}
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
                $('#nm_komponen_nilai').prop('required', true);
                $('#keterangan').prop('required', false);
                $('#id_komponen_nilai').prop('required', false);
            } else {
                $('.form-old').show();
                $('.form-new').hide();
                $('#nm_komponen_nilai').prop('required', false);
                $('#keterangan').prop('required', false);
                $('#id_komponen_nilai').prop('required', true);
            }
        }
    })
</script>
@endpush