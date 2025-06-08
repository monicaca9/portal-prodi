@extends('template.default')

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-envelope"></i> Form Ubah Mail Server</h3></div>
        <form method="POST" action="{{route('mail_server.simpan')}}" class="form-horizontal">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-3"></div>
                    <div class="col-md-4 text-center">
                        <h4>Data Mail Server Lama</h4>
                    </div>
                    <div class="col-md-5 text-center">
                        <h4>Data Mail Server Baru</h4>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Driver</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! env('MAIL_DRIVER') !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="text" name="driver" placeholder="Tulis driver mail server disini..."></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Host</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! env('MAIL_HOST') !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="text" name="host" placeholder="Tulis alamat mail server disini"></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Port</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! env('MAIL_PORT') !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="text" name="port" placeholder="Tulis port mail server disini"></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Username</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! env('MAIL_USERNAME') !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="text" name="username" placeholder="Tulis username mail server disini"></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Password</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! str_repeat('•', strlen(env('MAIL_PASSWORD'))) !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="password" name="password" placeholder="Tulis password / app password mail server disini"></div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3"><label class="control-label">Encryption</label></div>
                    <div class="col-md-4 text-center"><p class="form-control-plaintext">{!! (is_null(env('MAIL_ENCRYPTION'))?'-':env('MAIL_ENCRYPTION')) !!}</p></div>
                    <div class="col-md-5 text-center"><input class="form-control" type="text" name="encryption" placeholder="Tulis 'null' jika tidak ada enkripsi"></div>
                </div>
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-success"><i class="fa fa-save"></i> SIMPAN</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-send-o"></i> Testing Mail Server</h3></div>
        <form method="POST" action="{{route('mail_server.testing')}}" class="form-horizontal">
            @csrf
            <div class="card-body">
                {!! FormInputText('email_target','Email Target','email') !!}
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-primary"><i class="fa fa-send"></i> KIRIM EMAIL TESTING</button>
                </div>
            </div>
        </form>
    </div>
@endsection
