<?php

namespace App\Http\Controllers\Kpta;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Kpta\SeminarProdi;
use App\Http\Controllers\Controller;
use App\Models\Kpta\DistribusiNilaiSeminar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class DistribusiNilaiSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request, $id)
    // {
    //     $id_seminar_prodi = Crypt::decrypt($id);
    //     $data = SeminarProdi::findorfail($id_seminar_prodi);
    //     $input = $request->all();
    //     $input['id_seminar_prodi'] = $data->id_seminar_prodi;
    //     $input['urutan']
    // }
    public function store(Request $request, $id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findOrFail($id_seminar_prodi);
        $input = $request->all();
        $input['id_seminar_prodi'] = $id_seminar_prodi;
        $peran_urutan = $request->input('peran_urutan');

        $peran_map = array_flip(config('mp.data_master.peran_seminar'));
        $default_urutan = 1;
        $last_dash_pos = strrpos($peran_urutan, '-');
        if ($last_dash_pos === false) {
            $peran = $peran_urutan;
            $urutan = $default_urutan;
        } else {
            $peran = substr($peran_urutan, 0, $last_dash_pos);

            $urutan = (int) substr($peran_urutan, $last_dash_pos + 1);
        }
        $peran_numerik = $peran_map[$peran];
        $input['peran'] = $peran_numerik;
        $input['urutan'] = $urutan;

        $data = new DistribusiNilaiSeminar();
        $data->fill($data->prepare($input))->save();

        alert()->success('Data Berhasil Disimpan')->persistent('OK');

        return redirect()->route('daftar_seminar_prodi.detail_distribusi_nilai', Crypt::encrypt($id_seminar_prodi));
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, $id_distribusi)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_distribusi_nilai = Crypt::decrypt($id_distribusi);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_distribusi_nilai = DistribusiNilaiSeminar::findorfail($id_distribusi_nilai);
        $distribusi_nilai = DB::table('kpta.distribusi_nilai')
            ->where('soft_delete', 0)
            ->where('id_seminar_prodi', $id_seminar_prodi)
            ->orderByRaw('peran ASC, urutan ASC')
            ->get();

        $list_jabatan = [];
        $jmlh_pembimbing = $data->jmlh_pembimbing ?? 0;
        $jmlh_penguji = $data->jmlh_penguji ?? 0;

        for ($pembimbing = 1; $pembimbing <= $jmlh_pembimbing; $pembimbing++) {
            $list_jabatan["Pembimbing-$pembimbing"] = "Pembimbing $pembimbing";
        }

        for ($penguji = 1; $penguji <= $jmlh_penguji; $penguji++) {
            $list_jabatan["Penguji-$penguji"] = "Penguji $penguji";
        }

        if ($data->jenisSeminar && $data->jenisSeminar->a_tugas_akhir == 0) {
            $list_jabatan["Pembimbing Lapangan"] = "Pembimbing Lapangan";
        }

        foreach ($distribusi_nilai as $item) {
            $peran_seminar = config('mp.data_master.peran_seminar');
            $nama_peran = $peran_seminar[$item->peran] ?? null;

            if ($nama_peran) {
                if ($nama_peran === "Pembimbing Lapangan") {
                    $key = $nama_peran;
                } else {
                    $key = $item->urutan > 0 ? "$nama_peran-$item->urutan" : $nama_peran;
                }

                if (isset($list_jabatan[$key])) {
                    unset($list_jabatan[$key]);
                }
            }
        }

        $nama_peran = config('mp.data_master.peran_seminar')[$data_distribusi_nilai->peran] ?? 'Tidak Diketahui';
        $selected_value = $data_distribusi_nilai->urutan > 0 ? "$nama_peran-$data_distribusi_nilai->urutan" : $nama_peran;

        if (!array_key_exists($selected_value, $list_jabatan)) {
            $list_jabatan[$selected_value] = str_replace('-', ' ', $selected_value);
        }

        return view('__partial.form.edit', [
            'judul_halaman'      => 'Ubah Distribusi Nilai Seminar ' . $data->jenisSeminar->nm_jns_seminar,
            'route'              => 'daftar_seminar_prodi.detail_distribusi_nilai.update',
            'backLink'           => 'daftar_seminar_prodi.detail_distribusi_nilai',
            'form'               => 'kpta.daftar_seminar_prodi.distribusi_nilai.edit',
            'data'               => $data_distribusi_nilai,
            'id'                 => $id_distribusi_nilai,
            'list_jabatan'       => $list_jabatan,
            'param_form'         => $id_seminar_prodi,
            'selected_value'     => $selected_value

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, $id_distribusi)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_distribusi_nilai = Crypt::decrypt($id_distribusi);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_distribusi_nilai = DistribusiNilaiSeminar::findorfail($id_distribusi_nilai);
        $input = $request->all();
        $peran_urutan = $request->input('peran_urutan');

        $peran_map = array_flip(config('mp.data_master.peran_seminar'));
        $default_urutan = 1;
        $last_dash_pos = strrpos($peran_urutan, '-');
        if ($last_dash_pos === false) {
            $peran = $peran_urutan;
            $urutan = $default_urutan;
        } else {
            $peran = substr($peran_urutan, 0, $last_dash_pos);

            $urutan = (int) substr($peran_urutan, $last_dash_pos + 1);
        }
        $peran_numerik = $peran_map[$peran];
        $input['peran'] = $peran_numerik;
        $input['urutan'] = $urutan;

        $data_distribusi_nilai->fill($data_distribusi_nilai->prepare($input))->save();
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('daftar_seminar_prodi.detail_distribusi_nilai', Crypt::encrypt($id_seminar_prodi));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $id_distribusi)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_distribusi_nilai = Crypt::decrypt($id_distribusi);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_distribusi_nilai = DistribusiNilaiSeminar::findorfail($id_distribusi_nilai);
        $data_distribusi_nilai->drop();

        alert()->success('Data Berhasil Dihapus')->persistent('OK');
        return redirect()->back();    }
}
