<?php

namespace App\Http\Requests\Ref;

use Illuminate\Foundation\Http\FormRequest;

class TahunAjaranRequest extends FormRequest
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
//        $thn_ajaran = substr($this->nm_thn_ajaran,0,4);
//        dd($this->request);
//        if($this->method() == "PATCH" || $this->method() == "PUT") {
//            $id = Crypt::decrypt($this->id);
//            $tipe = 'sometimes|min:2|unique:pgsql.referensi.nilai_satuan,nm_satuan,'.$id.',id_nilai_satuan';
//        } else {
//            $tipe = 'required|min:2|unique:pgsql.referensi.nilai_satuan,nm_satuan';
//        }
        return [
            'nm_thn_ajaran' => 'required|string|size:9',
            'tgl_mulai'     => 'required|date',
            'tgl_selesai'   => 'required|date'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'nm_thn_ajaran' => 'Nama Tahun Ajaran',
            'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai',
        ];
    }
}
