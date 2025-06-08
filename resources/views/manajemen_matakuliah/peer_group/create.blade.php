<input type="hidden" name="id_sms" value="{{ session()->get('login.peran.id_organisasi') }}">
{!! FormInputText('nm_peer_group','Nama Peer Group','text',null,['required'=>true]) !!}
