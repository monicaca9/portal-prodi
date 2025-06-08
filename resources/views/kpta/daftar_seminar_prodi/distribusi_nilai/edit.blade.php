{!! FormInputSelect('peran_urutan', 'Pilih Jabatan', true, true, $list_jabatan, $selected_value ) !!}
{!! FormInputText('persentase','Urutan','number',explode('.', $data->persentase)[0],['required'=>true]) !!}
