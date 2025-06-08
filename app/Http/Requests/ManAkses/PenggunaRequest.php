<?php

namespace App\Http\Requests\ManAkses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class PenggunaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->_method=='PUT') {
            $id = Crypt::decrypt($this->id);
            $username   = 'required|min:6|unique:pgsql.man_akses.pengguna,username,'.$id.',id_pengguna';
            $password   = 'sometimes|confirmed';
        } else {
            $username   = 'required|min:6|unique:pgsql.man_akses.pengguna,username';
            $password   = 'required|min:6|confirmed';
        }
        return [
            'username'      => $username,
            'nm_pengguna'   => 'required|min:8',
            'password'      => $password
        ];
    }
}
