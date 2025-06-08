<?php

namespace App\Http\Controllers;

use App\Models\Dok\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DokumenController extends Controller
{
    public function view_dokumen($id)
    {
        $id_dok = Crypt::decrypt($id);
        $dokumen = Dokumen::findorfail($id_dok);
        if( !is_null($dokumen->file_dok) ){
            $content = base64_decode(stream_get_contents($dokumen->file_dok));

            return response($content)
                ->header('Content-Type',$dokumen->media_type)
                ->header('Content-Disposition', "inline; filename={$dokumen->file_name}");
        }
        else{
            abort(404);
        }
    }
}
