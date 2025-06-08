<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Models\SuratAktifKuliah\DataAdm;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;

class SuratAktifKuliahController extends Controller
{
    public function index()
{
    // Ambil data dari database
    $suratAktifKuliah = DataAdm::all();

    // Kirim data ke view
    return view('administrasi.surat_aktif_kuliah.index', compact('suratAktifKuliah'));
}

    public function create(PesertaDidik $pesertaDidik)
    {
    $prodi = GetProdiIndividu();
    $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);

    // Query ke tabel data_administrasi
    $data = DataAdm::where('id_pd', $profil->id_pd)->first();

        $data = new DataAdm();
        $data->fill([
            'id_pengajuan'    => Str::uuid(),
            'id_pd'           => $profil->id_pd,
            'nama'            => $profil->nm_pd,
            'npm'             => $profil->nim,
            'jurusan'         => $prodi->fakultas,
            'program_studi'   => $profil->prodi,
            'semester'        => $profil->total_sks,
            'tahun_akademik'  => $profil->nm_smt,
            'no_wa'           => $profil->tlpn_hp,
            'alamat'          => '',
            'keperluan'       => '',
            'ttd'             => '',
            'dosen_pa'        => '',

        ]);

    // Ambil daftar dosen PA (bisa sesuaikan query sesuai kebutuhan)
    $dosen_pa = DB::table('pdrd.sdm') // tambahkan nama schema sebelum tabel!
    ->pluck('nm_sdm', 'id_sdm') // ambil nama dosen PA & id nya
    ->toArray();

    return view('administrasi.surat_aktif_kuliah.create', compact('profil', 'prodi', 'data', 'dosen_pa'));
}

public function store(Request $request)
{
    // Validasi data input
    $request->validate([
        'id_pd' => 'required',
        'tahun_akademik' => 'required|string|max:100',
        'no_wa' => 'required|string|max:20',
        'alamat' => 'required|string',
        'keperluan' => 'required|string|max:255',
        'ttd' => 'required|string|max:100',
        'dosen_pa' => 'required|string|max:100',
    ]);

    // Cari profil mahasiswa
    $pesertaDidik = new PesertaDidik();
    $prodi = GetProdiIndividu();
    $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);

        $data = new DataAdm();
        $data->fill([
            'id_pengajuan'    => Str::uuid(),
            'id_pd'           => $request->id_pd,
            'nama'            => $request->nama,
            'npm'             => $request->npm,
            'jurusan'         => $request->jurusan,
            'program_studi'   => $request->program_studi,
            'semester'        => $request->semester,
            'tahun_akademik'  => $request->tahun_akademik,
            'no_wa'           => $request->no_wa,
            'alamat'          => $request->alamat,
            'keperluan'       => $request->keperluan,
            'ttd'             => $request->ttd,
            'dosen_pa'        => $request->dosen_pa,
        ]);

    // Simpan data
    $data->save();

    return redirect()->route('surat_aktif_kuliah')->with('success', 'Data berhasil disimpan!');
}

public function detail($id)
{
    $decryptedId = Crypt::decrypt($id);

    $data = DataAdm::where('id_pengajuan', $decryptedId)->first();

    // Cek apakah dosen_pa masih ID UUID (panjang 36 biasanya)
    if (strlen($data->dosen_pa) === 36) {
        $dosen_pa = DB::table('pdrd.sdm')
            ->where('id_sdm', $data->dosen_pa)
            ->value('nm_sdm');

        // Kalau ketemu, baru timpa
        if ($dosen_pa) {
            $data->dosen_pa = $dosen_pa;
        }
    }

    return view('administrasi.surat_aktif_kuliah.detail', compact('data'));
}

public function update(Request $request, $id)
{
    $data = DataAdm::findOrFail($id);

    $data->update($request->all());

    return redirect()->route('surat_aktif_kuliah.detail', ['id' => Crypt::encrypt($data->id_pengajuan)])
                     ->with('success', 'Data berhasil diperbarui.');
}


}
