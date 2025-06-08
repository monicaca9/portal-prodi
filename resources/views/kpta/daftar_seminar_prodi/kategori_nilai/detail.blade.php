@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> KATEGORI PENILAIAN - {{ $data->jenisSeminar->nm_jns_seminar }} ({{ $data->prodi->nm_lemb.' ('.$data->prodi->jenjang->nm_jenj_didik.')' }})</h3>
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
                <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR KATEGORI PENILAIAN SEMINAR</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori Nilai</th>
                                <th>Keterangan</th>
                                <th>Urutan</th>
                                <th>Komponen Penilaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori_nilai AS $no=>$each_kategori)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_kategori->nm_kategori_nilai }}</td>
                                <td>{{ $each_kategori->keterangan }}</td>
                                <td class="text-center">{{ $each_kategori->urutan }}</td>
                                <td class="text-center">
                                    <a href="{{route('daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_kategori->id_list_kategori_nilai)])}}" class="btn btn-flat btn-info btn-xs">
                                        {{ $each_kategori->total_komponen_nilai.' Komponen' }}</a>
                                </td>
                                <td>
                                    {!! buttonEditMultipleId('daftar_seminar_prodi.detail_kategori.ubah',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_kategori->id_list_kategori_nilai)],'Ubah Kategori') !!}
                                    {!! buttonDeleteMultipleId('daftar_seminar_prodi.detail_kategori.delete',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_kategori->id_list_kategori_nilai)],'Hapus Kategori') !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <form action="{{ route('daftar_seminar_prodi.detail_kategori.simpan',Crypt::encrypt($data->id_seminar_prodi)) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"><i class="fa fa-list-alt"></i> TAMBAH KATEGORI PENILAIAN SEMINAR</h3>
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
                        {!! FormInputSelect('id_kategori_nilai','Pilih Kategori Nilai Seminar',true,true,$list_kategori_nilai) !!}
                    </div>
                    <div class="form-new">
                        {!! FormInputText('nm_kategori_nilai','Nama Kategori Penilaian Seminar ','text',null,['required'=>true]) !!}
                        {!! FormInputText('keterangan', 'Keterangan', 'text', null) !!}
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
                $('#nm_kategori_nilai').prop('required', true);
                $('#keterangan').prop('required', false);
                $('#id_kategori_nilai').prop('required', false);
            } else {
                $('.form-old').show();
                $('.form-new').hide();
                $('#nm_kategori_nilai').prop('required', false);
                $('#keterangan').prop('required', false);
                $('#id_kategori_nilai').prop('required', true);
            }
        }
    })
</script>
@endpush