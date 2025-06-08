@push('css')
<link href="{{asset('node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" media="screen">
@endpush

@push('js')
<script type="text/javascript" src="{{asset('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        $('#tgl_seminar_baru').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_mulai').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_sk').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });
        $('#tgl_sk_tugas').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_selesai').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_kirim').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
        });

        $('#tanggal_lahir').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_lahir').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_lahir_ayah').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_lahir_ibu').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_lahir_wali').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });

        $('#tgl_sk_seminar_baru').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: true,
            todayHighlight: 1,
            forceParse: false,
            weekStart: 1,
            startView: 2,
        });
    });
</script>
@endpush
