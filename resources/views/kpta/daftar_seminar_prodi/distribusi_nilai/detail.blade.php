@extends('template.default')
@include('__partial.datatable')
@include('__partial.select2')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-list"></i> DISTRIBUSI NILAI SEMINAR - {{ $data->jenisSeminar->nm_jns_seminar }} ({{ $data->prodi->nm_lemb.' ('.$data->prodi->jenjang->nm_jenj_didik.')' }})</h3>
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
                <h3 class="card-title"><i class="fa fa-list"></i> DAFTAR DISTRIBUSI NILAI SEMINAR</h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peran</th>
                                <th>Bobot Penilaian (%)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distribusi_nilai as $no=>$each_distribusi)
                            <?php
                            $peran_seminar = config('mp.data_master.peran_seminar');
                            $nama_peran = $peran_seminar[$each_distribusi->peran];
                            // dd($nama_peran);
                            ?>
                            <tr>
                                <td>{{ $no + 1}}</td>
                                <td>{{ $nama_peran }} {{$each_distribusi->urutan}}</td>
                                <td>{{number_format($each_distribusi->persentase,2)}}</td>
                                <td>
                                    {!! buttonEditMultipleId('daftar_seminar_prodi.detail_distribusi_nilai.ubah',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_distribusi->id_distribusi_nilai)],'Ubah Distribusi Nilai') !!}
                                    <?php  ?>
                                    {!! buttonDeleteMultipleId('daftar_seminar_prodi.detail_distribusi_nilai.delete',[Crypt::encrypt($data->id_seminar_prodi),Crypt::encrypt($each_distribusi->id_distribusi_nilai)],'Hapus Distribusi Nilai') !!}
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <form action="{{ route('daftar_seminar_prodi.detail_distribusi_nilai.simpan', Crypt::encrypt($data->id_seminar_prodi)) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title"><i class="fa fa-list-alt"></i> TAMBAH DISTRIBUSI PENILAIAN SEMINAR</h3>
                    <div class="card-tools">
                        <button type="submit" class="btn btn-xs btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div><!-- /.card-header -->
                <div class="card-body">
                    {!! FormInputSelect('peran_urutan', 'Pilih Jabatan', true, true, $list_jabatan) !!}
                    {!! FormInputText('persentase', 'Bobot Penilaian (%)', 'number', null, ['required'=>true]) !!}
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        {!! buttonBack(route('daftar_seminar_prodi')) !!}
    </div>
</div>
@endsection