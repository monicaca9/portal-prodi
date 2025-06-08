@extends('template.default')
@include('__partial.datatable')

@section('content')


<div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building"></i> Cuaca Hari Ini</h3>
        </div><!-- /.card-header -->
        <a class="weatherwidget-io" href=#" data-label_1="BANDAR LAMPUNG" data-label_2="WEATHER" data-theme="pure" >Cuaca BANDAR LAMPUNG </a>
<script>
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');
</script>
        </div>
    </div>
    <script>
    </script>


@endsection
