@extends('template.default')
@if(isset($nim))
    @include('__partial.datatable')
@endif

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-universal-access"></i> AKUN MAHASISWA</h3>
        </div>
        <form action="" class="form-horizontal form-inside">
        <div class="card-body">
            {!! FormInputText('nim','Nomor Induk Mahasiswa',true,(isset($nim)?$nim:null)) !!}
        </div>
            <div class="card-footer pull-right">
                <button type="submit" class="btn btn-primary btn-flat btn-sm"><i class="fa fa-search"></i> Cari Akun</button>
            </div>
        </form>
    </div>
    @if(isset($nim))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> DAFTAR PENCARIAN AKUN MAHASISWA</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table-data">
                        <thead>
                        <tr>
                            <th>NO</th>
                            <th>NIM</th>
                            <th>NAMA</th>
                            <th>ASAL PRODI</th>
                            <th>STATUS KEAKTIFAN</th>
                            <th>IPK</th>
                            <th>Total SKS</th>
                            <th>STATUS AKUN</th>
                            <th>AKSI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data AS $no=>$each_data)
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ $each_data->nim }}</td>
                                <td>{{ $each_data->nm_pd }}</td>
                                <td>{{ $each_data->prodi }}</td>
                                <td>{{ is_null($each_data->ket_keluar)?$each_data->nm_stat_mhs:$each_data->ket_keluar }}</td>
                                <td>{!! !is_null($each_data->ipk)?$each_data->ipk.'<br>(Semester: '.$each_data->nm_smt.')':null !!}</td>
                                <td>{{ !is_null($each_data->total_sks)?$each_data->total_sks.' sks':null }}</td>
                                <td>
                                    @if(is_null($each_data->id_pengguna))
                                        <span class="badge badge-info">Belum dibuatkan</span>
                                    @else
                                        @if($each_data->a_aktif==1)
                                            <span class="badge badge-success">Akun Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Akun Non-Aktif</span>
                                        @endif
                                        @if(is_null($each_data->last_active))
                                            <br><span class="badge badge-info">Belum pernah login</span>
                                        @else
                                            <br><span class="badge badge-info">Terakhir login {{ tglWaktuIndonesia($each_data->last_active) }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($each_data->id_stat_mhs=='A')
                                        @if(is_null($each_data->id_pengguna))
                                            @if(check_akses('akun_mahasiswa.create_akun'))
                                                <a href="{{ route('akun_mahasiswa.create_akun',Crypt::encrypt($each_data->id_pd)) }}" class="btn btn-flat btn-xs btn-primary"><i class="fa fa-plus-circle"></i> Buat Akun</a>
                                            @endif
                                        @else
                                            @if($each_data->a_aktif==1)
                                                <a href="{{ route('manajemen_akses.pengguna.ubah_aktif',Crypt::encrypt($each_data->id_pengguna)) }}" class="btn btn-flat btn-danger btn-xs">TIDAK AKTIF</a>
                                            @else
                                                <a href="{{ route('manajemen_akses.pengguna.ubah_aktif',Crypt::encrypt($each_data->id_pengguna)) }}" class="btn btn-flat btn-success btn-xs">AKTIF</a>
                                            @endif
                                            {!! buttonDelete('akun_mahasiswa.delete',Crypt::encrypt($each_data->id_pengguna),'Hapus Akun') !!}
                                        @endif
                                    @else
                                        @if(is_null($each_data->id_pengguna))
                                            <span class="badge badge-danger">Tidak bisa dibuatkan akun</span>
                                        @else
                                            {!! buttonDelete('akun_mahasiswa.delete',Crypt::encrypt($each_data->id_pengguna),'Hapus Akun') !!}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script>
        $(function () {
            $('#nim').on('change',function() {
                var role = $(this).val();
                if(role!='') {
                    $('form.form-inside').submit();
                } else {
                    window.location.href = "{{ route('akun_mahasiswa') }}";
                }
            });
        })
    </script>
@endpush
