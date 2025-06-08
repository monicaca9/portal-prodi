<?php
$data_kk = [];
if (!is_null($mhs->id_kk)) {
    if ($mhs->id_kk==0) {
        $data_kk[] = "NULL";
    } else {
        $data_kk = explode(', ',$mhs->id_kk);
    }
}
?>
<form action="{{ route('biodata.update') }}" enctype="multipart/form-data" class="form-horizontal" method="post">
    @csrf
    @method('PUT')
    <input type="hidden" name="kode" value="lainnya">
    {!! FormInputSelect('a_terima_kps','Menerima KPS?',true,true,[0=>'Tidak',1=>'Ya, Saya menggunakan KPS (Kartu Perlindungan Sosial)'],$mhs->a_terima_kps) !!}
    {!! FormInputText('no_kps','No. KPS','text',$mhs->no_kps,['placeholder'=>'Tuliskan Nomor Kartu Perlindungan Sosial anda (Jika ada)']) !!}
    {!! FormInputSelect('id_alat_transport','Alat Transportasi',true,true,$alat_transport,$mhs->id_alat_transport) !!}
    <div id="form_kk">
        <div class="form-group row">
            <label for="kk" class="col-sm-2 col-form-label">Kebutuhan Khusus <span class="text-danger">*</span></label>
            <div class="col-sm-10">
                @foreach($kebutuhan_khusus AS $each_kk)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $each_kk->id_kk }}" name="list_kk[]" id="{{ $each_kk->id_kk }}" {{ (in_array($each_kk->kode,$data_kk)?'checked':'') }}>
                        <label class="form-check-label" for="{{ $each_kk->id_kk }}">
                            {{ $each_kk->nm_kk }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <hr>
    <button type="submit" class="btn btn-success btn-flat btn-block">SIMPAN</button>
</form>
<hr>
<div class="clearfix">
    <a href="{{ url(route('biodata.ubah').'?tab=wali') }}" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
    @if(!is_null($mhs->id_alat_transport))
        <div class="pull-right">
            <form action="{{ route('biodata.validasi',Crypt::encrypt($mhs->id_pd)) }}" class="validasi_form" style="display: inline;" method="POST">
                @csrf
                <button class="btn btn-xs btn-primary btn-flat btn-validasi">
                    <i class="fa fa-check"></i> Simpan Permanen
                </button>
            </form>
            @push('js')
                <script>
                    $(document).ready(function () {
                        $('button.btn-validasi').on('click', function(e){
                            e.preventDefault();
                            var self = $(this);
                            swal({
                                title               : "Simpan Permanen Biodata Anda?",
                                text                : "Jika sudah disimpan permanen maka anda tidak bisa mengubah datanya kembali (Kecuali : Data Konsentrasi Prodi)",
                                icon                : "warning",
                                buttons: {
                                    cancel: {
                                        text: "Batal",
                                        value: null,
                                        closeModal: true,
                                        visible: true,
                                    },
                                    text: {
                                        text: "Ya, Simpan Permanen Biodata!",
                                        value: true,
                                        visible: true,
                                        closeModal: false,
                                    }
                                },
                                dangerMode         : true,
                            }).then((willDelete) => {
                                if (willDelete) {
                                    self.parents(".validasi_form").submit();
                                }
                            })
                        });
                    })
                </script>
            @endpush
        </div>
    @endif
</div>
