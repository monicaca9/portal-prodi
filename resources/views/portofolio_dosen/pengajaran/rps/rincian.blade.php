<div class="table-responsive">
    <h4>Rincian MK</h4>
    <table class="table table-striped table-hover">
        <tbody>
        {!! tableRow('Kode MK',$mk->kode_mk) !!}
        {!! tableRow('Nama MK',$mk->nm_mk) !!}
        {!! tableRow('SKS MK',$mk->sks_mk) !!}
        {!! tableRow('Jenis MK',config('mp.data_master.jenis_matkul.'.$mk->jns_mk)) !!}
        {!! tableRow('Metode',config('mp.data_master.metode.'.$ajuan->metode)) !!}
        </tbody>
    </table>
</div>
<hr>
<h4>Rincian RPS Minggu ke-{{ $data->minggu_ke_baru }}</h4>
{!! FormInputTextareaCKeditor('tujuan_khusus_baru','Tujuan Khusus Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->tujuan_khusus_baru) !!}
{!! FormInputTextareaCKeditor('pokok_bahasan_baru','Pokok Bahasan Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->pokok_bahasan_baru) !!}
{!! FormInputTextareaCKeditor('referensi_baru','Referensi Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->referensi_baru) !!}
{!! FormInputTextareaCKeditor('sub_pokok_bahasan_baru','Sub Pokok Bahasan Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->sub_pokok_bahasan_baru) !!}
<br>

<div>
    
    {!! FormInputTextareaCKeditor('metode_baru','Metode Baru <button class="btn btn-xs btn-primary btn-flat btn-validasi"><i class="fa fa-info-circle" aria-hidden="true"></i></button> <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->metode_baru) !!}
    @push('js')
    <script>
                            $(document).ready(function () {
                                $('button.btn-validasi').on('click', function(e){
                                    e.preventDefault();
                                    var self = $(this);
                                    swal({
                                        title               : "Metode yang anda pilih adalah {{config('mp.data_master.metode.'.$ajuan->metode)}}",
                                        text                : "Untuk melihat contoh, mohon kembali ke halaman sebelumnya",
                                        icon                : "info",
                                        buttons: {
                                            cancel: {
                                                text: "Batal",
                                                value: null,
                                                closeModal: true,
                                                visible: true,
                                            },
                                        },
                                    })
                                });
                            })
                        </script>
                        @endpush
</span></h5>
</div>
<br>
{!! FormInputTextareaCKeditor('media_baru','Media Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->media_baru) !!}
{!! FormInputTextareaCKeditor('akt_penugasan_baru','Aktifitas Penugasan Baru <br> <h10 style="color:Tomato;">text, tidak boleh lebih dari 2000 character</h10>',true,$data->akt_penugasan_baru) !!}
{!! FormInputText('bobot','Bobot Nilai',$data->bobot) !!}
