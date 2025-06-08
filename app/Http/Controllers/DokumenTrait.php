<?php

namespace App\Http\Controllers;

use App\Models\Dok\Dokumen;
use Illuminate\Http\Request;

trait DokumenTrait
{
    public function simpan_dokumen($input)
    {
        $file = $input['file'];
        $size = $file->getSize();
        if ($size <= 1000000) {
            $mime = $file->getClientMimeType();
            $nama_asli = $file->getClientOriginalName();
            $bytea = base64_encode(file_get_contents($file->getPathName()));
            $dokumen = new Dokumen();
            $dokumen->fill($dokumen->prepare([
                'id_jns_dok'    => $input['id_jns_dok'],
                'nm_dok'        => $input['nm_dok'],
                'ket_dok'       => $input['ket_dok'],
                'file_dok'      => $bytea,
                'wkt_unggah'    => currDateTime(),
                'url'           => $input['url'],
                'media_type'    => $mime,
                'file_name'     => $nama_asli,
            ]))->save();
            return $dokumen->id_dok;
        } else {
            alert()->error('Foto melebihi 1MB')->persistent('OK');
            return redirect()->back();
        }
    }
}
