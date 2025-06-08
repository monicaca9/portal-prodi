<?php

namespace App\Http\Requests\ManAkses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class PeranRequest extends FormRequest
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
            $id_peran   = 'required|min:4|unique:pgsql.man_akses.peran,id_peran,'.$id.',id_peran';
            $nm_peran   = 'required|min:6|max:50';
        } else {
            $id_peran   = 'required|min:4|unique:pgsql.man_akses.peran,id_peran';
            $nm_peran   = 'required|min:6|max:50';
        }
        return [
            'id_peran'      => $id_peran,
            'nm_peran'      => $nm_peran
        ];
    }
}
