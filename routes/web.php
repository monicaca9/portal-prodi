<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
Route::namespace('Auth')->group(function () {
    Route::get('auth/login', 'LoginController@showLoginForm')->name('login');
    Route::post('auth/login', 'LoginController@authenticate')->name('auth.login');
    Route::get('auth/login_sso', 'LoginController@authenticate_sso')->name('auth.login.sso');
    Route::get('auth/logout', 'LoginController@logout')->name('auth.logout');
    Route::get('auth/register', 'RegisterController@index')->name('auth.register');
    Route::post('auth/register', 'RegisterController@create')->name('auth.do_register');
    Route::get('auth/aktivasi/{id}', 'RegisterController@show')->name('auth.aktivasi');
    Route::post('auth/aktivasi/{id}', 'RegisterController@active')->name('auth.do_aktivasi');
    Route::get('auth/claim', 'LoginController@Claim')->name('auth.claim');
    Route::post('auth/claim', 'LoginController@Claim')->name('auth.do_claim');
    Route::get('auth/verifysso', 'LoginController@verifysso')->name('auth.verifysso');
    Route::post('auth/verfysso', 'LoginController@verifysso')->name('auth.do_verifysso');
});


Route::namespace('SuratAktifKuliah')->group(function () {
    $route_alias = 'sak';
    $controller = 'StudentActiveLetter';
    $url = $route_alias;
    Route::get($url . '/s/p/{code}', $controller . 'Controller@seeSignatureByShortCode')->name($route_alias . '.preview');
});

Route::namespace('SuratMasihKuliah')->group(function () {
    $route_alias = 'sak';
    $controller = 'StillStudyLetter';
    $url = $route_alias;
    Route::get($url . '/s/p/{code}', $controller . 'Controller@seeSignatureByShortCode')->name($route_alias . '.preview');
});




Route::middleware('auth_custom')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/{id}/distribusi_dosen_mahasiswa', 'DashboardController@distribusi_dosen_mahasiswa')->name('dashboard.distribusi_dosen_mahasiswa');
    Route::get('/{id}/distribusi_dosen_mahasiswa/{id_jns_seminar}/detail', 'DashboardController@show')->name('dashboard.distribusi_dosen_mahasiswa.detail');
    Route::get('dokumen/{id}/preview', 'DokumenController@view_dokumen')->name('dokumen.preview');
    //Route::get('/', function () {
    //    return view('beasiswa.dashboard');
    //})->name('dashboard');
    Route::get('biodata', 'BeasiswaController@biodata')->name('biodata');
    Route::get('daftar_beasiswa', 'BeasiswaController@daftar_beasiswa')->name('daftar_beasiswa');
    Route::get('daftar_beasiswa/daftar', 'BeasiswaController@daftar')->name('daftar_beasiswa.daftar');

    $route_alias = 'password';
    $controller = 'Pengaturan\\Password';
    $url = $route_alias;
    Route::get($url, $controller . 'Controller@index')->name($route_alias);
    Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');

    $route_alias = 'ubah_peran';
    $controller = 'Pengaturan\\ChangeRole';
    $url = $route_alias;
    Route::get($url, $controller . 'Controller@index')->name($route_alias);
    Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
    Route::get($url . '/{id}/update_menu', $controller . 'Controller@update')->name($route_alias . '.update');

    Route::group(['middleware' => 'auth_mp'], function () {
        Route::namespace('DataMaster')->prefix('data_master')->name('data_master.')->group(function () {
            $route_alias = 'tahun_ajaran';
            $controller = 'TahunAjaran';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'semester';
            $controller = 'Semester';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_list_ref = [
                'agama'                 => 'Agama',
                'alat_transportasi'     => 'AlatTransportasi',
                'bentuk_pendidikan'     => 'BentukPendidikan',
                'daftar_bank'           => 'DaftarBank',
                'fungsi_lab'            => 'FungsiLab',
                'ikatan_kerja_sdm'      => 'IkatanKerjaSdm',
                'jalur_daftar'          => 'JalurDaftar',
                'jenis_aktivitas_mhs'   => 'JenisAktivitasMhs',
                'jenis_beasiswa'        => 'JenisBeasiswa',
                'jenis_dokumen'         => 'JenisDokumen',
                'jenis_evaluasi'        => 'JenisEvaluasi',
                'jenis_keluar'          => 'JenisKeluar',
                'jenis_lembaga'         => 'JenisLembaga',
                'jenis_pendaftaran'     => 'JenisPendaftaran',
                'jenis_sdm'             => 'JenisSdm',
                'jenis_seminar'         => 'JenisSeminar',
                'jenis_tinggal'         => 'JenisTinggal',
                'jenjang_pendidikan'    => 'JenjangPendidikan',
                'jurusan'               => 'Jurusan',
                'kebutuhan_khusus'      => 'KebutuhanKhusus',
                'pekerjaan'             => 'Pekerjaan',
                'pembiayaan'            => 'Pembiayaan',
                'penghasilan'           => 'Penghasilan',
                'status_kepemilikan'    => 'StatusKepemilikan',
                'sumber_dana'           => 'SumberDana',
            ];
            foreach ($route_list_ref as $route_alias => $controller) {
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
                Route::get($url . '/lihat_table', $controller . 'Controller@show')->name($route_alias . '.detail');
                Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
                Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
                Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            }
        });
        Route::namespace('Mahasiswa')->group(function () {
            $route_alias = 'akun_mahasiswa';
            $controller = 'AkunMahasiswa';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/create_akun', $controller . 'Controller@create')->name($route_alias . '.create_akun');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
        });

        Route::namespace('KPTA')->group(function () {
            $route_alias = 'distribusi_dosen_mahasiswa';
            $controller = 'DistribusiDosenMahasiswa';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/create', $controller . 'Controller@create')->name($route_alias . '.create');
            Route::put($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'daftar_seminar_prodi';
            $controller = 'DaftarSeminarProdi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/create', $controller . 'Controller@create')->name($route_alias . '.create');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/syarat', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id}/kategori_nilai', $controller . 'Controller@kategori_nilai')->name($route_alias . '.detail_kategori');
            Route::get($url . '/{id}/distribusi_nilai', $controller . 'Controller@distribusi_nilai')->name($route_alias . '.detail_distribusi_nilai');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            Route::put($url . '/{id}/simpan_ba', $controller . 'Controller@store_ba')->name($route_alias . '.simpan_ba');
            Route::get($url . '/{id}/ubah_no_ba', $controller . 'Controller@edit_ba')->name($route_alias . '.ubah_ba');
            Route::put($url . '/{id}/update_no_ba', $controller . 'Controller@update_ba')->name($route_alias . '.update_ba');
            Route::delete($url . '/{id}/delete_ba_seminar', $controller . 'Controller@destroy_ba_seminar')->name($route_alias . '.delete_ba_seminar');

            $route_alias = 'daftar_seminar_prodi.detail';
            $controller = 'SyaratSeminar';
            $url = 'daftar_seminar_prodi/{id}/syarat';
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id_syarat}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id_syarat}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id_syarat}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            Route::get($url . '/{id_syarat}/daftar_dokumen', $controller . 'Controller@daftar_dokumen')->name($route_alias . '.daftar_dokumen');
            Route::post($url . '/{id_syarat}/daftar_dokumen/simpan', $controller . 'Controller@store_dokumen')->name($route_alias . '.daftar_dokumen.simpan');
            Route::delete($url . '/{id_syarat}/daftar_dokumen/{id_dok}/delete', $controller . 'Controller@hapus_dokumen')->name($route_alias . '.daftar_dokumen.hapus');

            $route_alias = 'daftar_seminar_prodi.detail_kategori';
            $controller = 'KategoriNilaiSeminar';
            $url = 'daftar_seminar_prodi/{id}/kategori_nilai';
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store_kategori')->name($route_alias . '.simpan');
            Route::get($url . '/{id_kategori_nilai}/ubah', $controller . 'Controller@edit_kategori')->name($route_alias . '.ubah');
            Route::put($url . '/{id_kategori_nilai}/update', $controller . 'Controller@update_kategori')->name($route_alias . '.update');
            Route::delete($url . '/{id_kategori_nilai}/delete', $controller . 'Controller@destroy_kategori')->name($route_alias . '.delete');
            Route::get($url . '/{id_kategori_nilai}/daftar_komponen_nilai', $controller . 'Controller@komponen_nilai')->name($route_alias . '.daftar_komponen_nilai');
            Route::post($url . '/{id_kategori_nilai}/daftar_komponen_nilai/simpan', $controller . 'Controller@store_komponen')->name($route_alias . '.daftar_komponen_nilai.simpan');
            Route::get($url . '/{id_kategori_nilai}/daftar_komponen_nilai/{id_komponen}/ubah', $controller . 'Controller@edit_komponen')->name($route_alias . '.daftar_komponen_nilai.ubah');
            Route::put($url . '/{id_kategori_nilai}/daftar_komponen_nilai/{id_komponen}/update', $controller . 'Controller@update_komponen')->name($route_alias . '.daftar_komponen_nilai.update');
            Route::delete($url . '/{id_kategori_nilai}/daftar_komponen_nilai/{id_komponen}/delete', $controller . 'Controller@destroy_komponen')->name($route_alias . '.daftar_komponen_nilai.delete');

            $route_alias = 'daftar_seminar_prodi.detail_distribusi_nilai';
            $controller = 'DistribusiNilaiSeminar';
            $url = 'daftar_seminar_prodi/{id}/distribusi_nilai';
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id_distribusi}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id_distribusi}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id_distribusi}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'berita_acara';
            $controller = 'BeritaAcaraSeminar';
            $url = $route_alias;
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');

            Route::namespace('SeminarProdi')->prefix('seminar_prodi')->name('seminar_prodi.')->group(function () {
                $route_alias = 'distribusi_peran_dosen';
                $controller = 'DistribusiPeranDosen';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/{id}/distribusi_peran_dosen/{id_jns_seminar}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');

                $route_alias = 'penilaian_seminar';
                $controller = 'PenilaianSeminar';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
                Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                Route::put($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.ubah');

                $route_alias = 'jangka_waktu_penyusunan';
                $controller = 'JangkaWaktuPenyusunan';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
                Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.ubah');
                Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            });


            $route_alias = 'konsentrasi_prodi';
            $controller = 'KonsentrasiProdi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
        });

        Route::namespace('PelaksanaanPendidikan')->prefix('pelaksanaan_pendidikan')->name('pelaksanaan_pendidikan.')->group(function () {
            $route_alias = 'pengajaran';
            $controller = 'Pengajaran';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/rps', $controller . 'Controller@rps_mahasiswa')->name($route_alias . '.pd');
            Route::get($url . '/{id_mk}/rps', $controller . 'Controller@rps_mahasiswa_detail')->name($route_alias . '.pd_show');
            Route::get($url . '/{id_mk}/show', $controller . 'Controller@show')->name($route_alias . '.show');
            Route::get($url . '/{id_mk}/viewpdf', $controller . 'Controller@view_pdf')->name($route_alias . '.viewpdf');
            Route::get($url . '/{id_mk}/edit_desc_mk', $controller . 'Controller@edit_desc_mk')->name($route_alias . '.edit_desc_mk');
            Route::put($url . '/{id_mk}/update_desc_mk', $controller . 'Controller@update_desc_mk')->name($route_alias . '.update_desc_mk');
            Route::get($url . '/{id_mk}/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::get($url . '/{id_mk}/tambah_rps_minggu', $controller . 'Controller@create_rps_minggu')->name($route_alias . '.tambah_rps_minggu');
            Route::post($url . '{id_mk}/simpan_rps_minggu', $controller . 'Controller@store_rps_minggu')->name($route_alias . '.simpan_rps_minggu');
            Route::get($url . '/{id_mk}/edit_rps_minggu', $controller . 'Controller@edit_rps_minggu')->name($route_alias . '.edit_rps_minggu');
            Route::put($url . '/{id_mk}/update_rps_minggu/{id_rps_minggu_mk}', $controller . 'Controller@update_rps_minggu')->name($route_alias . '.update_rps_minggu');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::post($url . '/{id_mk}/simpan_cpl_mk', $controller . 'Controller@store_cpl_mk')->name($route_alias . '.simpan_cpl_mk');
            Route::post($url . '/{id_mk}/simpan_daftar_pustaka_mk', $controller . 'Controller@store_daftar_pustaka_mk')->name($route_alias . '.simpan_daftar_pustaka_mk');
            Route::post($url . '/{id_mk}/simpan_cpmk', $controller . 'Controller@store_cpmk')->name($route_alias . '.simpan_cpmk');
            Route::delete($url . '/{id_mk}/delete_cpl_mk/{id_cpl_mk}', $controller . 'Controller@delete_cpl_mk')->name($route_alias . '.delete_cpl_mk');
            Route::delete($url . '/{id_mk}/delete_dapus/{id_daftar_pustaka_mk}', $controller . 'Controller@delete_daftar_pustaka_mk')->name($route_alias . '.delete_daftar_pustaka_mk');
            Route::delete($url . '/{id_mk}/delete_cpmk/{id_cpmk}', $controller . 'Controller@delete_cpmk')->name($route_alias . '.delete_cpmk');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'pengajaran.rps';
            $controller = 'Rps';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id_mk}/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::put($url . '/{id_mk}/simpan/{id_rps}', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::put($url . '/{id_mk}/simpan_permanen/{id_rps}', $controller . 'Controller@store_permanen')->name($route_alias . '.simpan_permanen');
            Route::post($url . '/{id_mk}/simpan_dokumen/{id_rps}', $controller . 'Controller@store_dokumen')->name($route_alias . '.simpan_dokumen');
            Route::get($url . '/{id_rps}/detail/{id_mk}', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id_mk}/{id_rps}/detail/{id_rincian}/detail_rincian', $controller . 'Controller@show_rincian')->name($route_alias . '.detail_rincian');
            Route::put($url . '/{id_mk}/{id_rps}/detail/{id_rincian}/update_rincian', $controller . 'Controller@update_rincian')->name($route_alias . '.update_rincian');
            Route::delete($url . '/{id_rps}/detail/{id_dok}/delete_dokumen', $controller . 'Controller@destroy_dokumen')->name($route_alias . '.delete_dokumen');
        });

        Route::namespace('ManajemenPerkuliahan')->group(function () {
            $route_alias = 'gedung_ruang';
            $controller = 'Gedung';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'gedung_ruang.detail_ruang';
            $controller = 'Ruang';
            $url = 'gedung_ruang/{id}/detail_ruang';
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id_ruang}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id_ruang}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id_ruang}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'kurikulum';
            $controller = 'Kurikulum';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'matakuliah';
            $controller = 'Matakuliah';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'peer_group';
            $controller = 'PeerGroup';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            Route::get($url . '/{id}/bidang', $controller . 'Controller@bidang')->name($route_alias . '.bidang');
            Route::post($url . '/{id}/bidang/simpan', $controller . 'Controller@simpan_bidang')->name($route_alias . '.bidang.simpan');
            Route::delete($url . '/{id}/bidang/{id_bidang}/delete', $controller . 'Controller@destroy_bidang')->name($route_alias . '.bidang.delete');
            Route::get($url . '/{id}/anggota', $controller . 'Controller@anggota')->name($route_alias . '.anggota');
            Route::post($url . '/{id}/anggota/simpan', $controller . 'Controller@simpan_anggota')->name($route_alias . '.anggota.simpan');
            Route::get($url . '/{id}/anggota/{id_anggota}/ubah_status', $controller . 'Controller@ubah_status_anggota')->name($route_alias . '.anggota.ubah_status');
            Route::get($url . '/{id}/anggota/{id_anggota}/ubah_ketua', $controller . 'Controller@ubah_ketua_anggota')->name($route_alias . '.anggota.ubah_ketua');
            Route::delete($url . '/{id}/anggota/{id_anggota}/delete', $controller . 'Controller@destroy_anggota')->name($route_alias . '.anggota.delete');

            $route_alias = 'tim_penjamin_mutu';
            $controller = 'TimPenjaminMutu';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            Route::get($url . '/{id}/anggota', $controller . 'Controller@anggota')->name($route_alias . '.anggota');
            Route::post($url . '/{id}/anggota/simpan', $controller . 'Controller@simpan_anggota')->name($route_alias . '.anggota.simpan');
            Route::get($url . '/{id}/anggota/{id_anggota}/ubah_status', $controller . 'Controller@ubah_status_anggota')->name($route_alias . '.anggota.ubah_status');
            Route::get($url . '/{id}/anggota/{id_anggota}/ubah_ketua', $controller . 'Controller@ubah_ketua_anggota')->name($route_alias . '.anggota.ubah_ketua');
            Route::delete($url . '/{id}/anggota/{id_anggota}/delete', $controller . 'Controller@destroy_anggota')->name($route_alias . '.anggota.delete');
        });

        Route::namespace('Validasi')->prefix('validasi')->name('validasi.')->group(function () {

            $route_alias = 'surat_aktif_kuliah';
            $controller = 'StudentActiveLetter';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.edit');
            Route::get($url . '/{id}/preview-pdf', $controller . 'Controller@previewPDF')
                ->name($route_alias . '.preview-pdf')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/{id}/download-pdf', $controller . 'Controller@downloadPDF')
                ->name($route_alias . '.download-pdf')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/history', $controller . 'Controller@history')
                ->name($route_alias . '.history')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/excel', $controller . 'Controller@exportExcel')
                ->name($route_alias . '.excel')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);

            
            $route_alias = 'surat_masih_kuliah';
            $controller = 'StillStudyLetter';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.edit');
            Route::get($url . '/{id}/preview-pdf', $controller . 'Controller@previewPDF')
                ->name($route_alias . '.preview-pdf')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/{id}/download-pdf', $controller . 'Controller@downloadPDF')
                ->name($route_alias . '.download-pdf')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/history', $controller . 'Controller@history')
                ->name($route_alias . '.history')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            Route::get($url . '/excel', $controller . 'Controller@exportExcel')
                ->name($route_alias . '.excel')
                ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);    




            $route_alias = 'pengajuan_seminar';
            $controller = 'PengajuanSeminar';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::put($url . '/{id}/update_jadwal_seminar', $controller . 'Controller@update_jadwal_seminar')->name($route_alias . '.update_jadwal_seminar');
            Route::get($url . '/{id}/detail/{id_syarat}/daftar_riwayat_verifikasi', $controller . 'Controller@daftar_riwayat_verifikasi')->name($route_alias . '.detail.daftar_riwayat_verifikasi');


            $route_alias = 'pengajuan_seminar_kaprodi';
            $controller = 'PengajuanSeminarKaprodi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::get($url . '/{id}/detail/{id_syarat}/daftar_riwayat_verifikasi', $controller . 'Controller@daftar_riwayat_verifikasi')->name($route_alias . '.detail.daftar_riwayat_verifikasi');

            $route_alias = 'rps_peer_group';
            $controller = 'RpsPeerGroup';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');

            $route_alias = 'riwayat_seminar';
            $controller = 'RiwayatSeminar';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/beritaacara', $controller . 'Controller@beritaacara')->name($route_alias . '.beritaacara');
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');

            $route_alias = 'riwayat_seminar_kaprodi';
            $controller = 'RiwayatSeminarKaprodi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');

            $route_alias = 'berita_acara';
            $controller = 'BeritaAcara';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');

            $route_alias = 'rps_tpmps';
            $controller = 'RpsTpmps';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');

            $route_alias = 'rps_admin_prodi';
            $controller = 'RpsAdminProdi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/sah', $controller . 'Controller@sah')->name($route_alias . '.sah');

            $route_alias = 'pengajuan_draft_usul';
            $controller = 'PengajuanDraftUsul';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
        });

        Route::namespace('ManajemenLab')->prefix('manajemen_lab')->name('manajemen_lab.')->group(function () {
            $route_alias = 'daftar_lab';
            $controller = 'DaftarLab';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
        });

        Route::namespace('DataProdi')->prefix('sdm')->name('sdm.')->group(function () {
            $route_alias = 'dosen';
            $controller = 'Dosen';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'mahasiswa';
            $controller = 'Mahasiswa';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/table', $controller . 'Controller@table')->name($route_alias . '.table');
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
        });

        Route::namespace('Pengaturan')->group(function () {
            $route_alias = 'mail_server';
            $controller = 'MailServer';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::post($url . '/testing', $controller . 'Controller@testing')->name($route_alias . '.testing');
        });

        Route::namespace('ManAkses')->prefix('manajemen_akses')->name('manajemen_akses.')->group(function () {
            $route_alias = 'pengguna';
            $controller = 'Pengguna';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::post($url . '/simpan_peran_pengguna', $controller . 'Controller@store_peran_pengguna')->name($route_alias . '.simpan_peran_pengguna');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::get($url . '/{id}/ubah_aktif', $controller . 'Controller@ubah_aktif')->name($route_alias . '.ubah_aktif');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            Route::delete($url . '/{id}/delete_peran', $controller . 'Controller@destroy_peran')->name($route_alias . '.delete_peran');

            $route_alias = 'menu';
            $controller = 'Menu';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::post($url . '/simpan_hak_menu', $controller . 'Controller@store_hak_menu')->name($route_alias . '.simpan_hak_menu');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::put($url . '/{id}/update_hak_menu/{id_peran}', $controller . 'Controller@update_hak_menu')->name($route_alias . '.update_hak_menu');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
            Route::delete($url . '/{id}/delete_hak_menu/{id_peran}', $controller . 'Controller@expired_hak_menu')->name($route_alias . '.delete_hak_menu');

            $route_alias = 'peran';
            $controller = 'Peran';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'hak_akses';
            $controller = 'HakAkses';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'aplikasi';
            $controller = 'Aplikasi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');

            $route_alias = 'unit_organisasi';
            $controller = 'UnitOrganisasi';
            $url = $route_alias;
            Route::get($url, $controller . 'Controller@index')->name($route_alias);
            Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
            Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
            Route::get($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
            Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
            Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
        });

        Route::get('biodata/ubah', 'Mahasiswa\\BiodataController@edit')->name('biodata.ubah');
        Route::put('biodata/update', 'Mahasiswa\\BiodataController@update')->name('biodata.update');
        Route::post('biodata/{id}/validasi', 'Mahasiswa\\BiodataController@validasi')->name('biodata.validasi');
        Route::post('pendaftaran_seminar/{id}/berita', 'Mahasiswa\\RiwayatSeminarController@store_berita_acara')->name('pendaftaran_seminar.berita_acara_seminar');

        Route::group(['middleware' => 'mahasiswa_auth'], function () {
            Route::namespace('Mahasiswa')->group(function () {
                $route_alias = 'riwayat_seminar';
                $controller = 'RiwayatSeminar';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
                Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');



                $route_alias = 'biodata';
                $controller = 'Biodata';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                Route::post($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
                Route::post($url . '/foto', $controller . 'Controller@foto')->name($route_alias . '.foto');

                $route_alias = 'pendaftaran_seminar';
                $controller = 'PendaftaranSeminar';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);
                Route::get($url . '/{id}/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                Route::post($url . '/{id}/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
                Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
                Route::get($url . '/{id}/beritaacara', $controller . 'Controller@beritaacara')->name($route_alias . '.beritaacara');

                Route::put($url . '/{id}/ubah', $controller . 'Controller@edit')->name($route_alias . '.ubah');
                Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
                Route::get($url . '/{id}/detail/{id_syarat}/daftar_dokumen', $controller . 'Controller@daftar_dokumen')->name($route_alias . '.detail.daftar_dokumen');
                Route::post($url . '/{id}/detail/{id_syarat}/daftar_dokumen/simpan', $controller . 'Controller@store_dokumen')->name($route_alias . '.detail.daftar_dokumen.simpan');
                Route::delete($url . '/{id}/detail/{id_syarat}/daftar_dokumen/{id_dok}/delete', $controller . 'Controller@hapus_dokumen')->name($route_alias . '.detail.daftar_dokumen.hapus');

                $route_alias = 'pendaftaran_seminar';
                $controller = 'RiwayatSeminar';
                $url = $route_alias;
                Route::get($url . '/daftar_ajuan_riwayat', $controller . 'Controller@index')->name($route_alias . '.daftar_ajuan_riwayat');
                Route::get($url . '/tambah_riwayat', $controller . 'Controller@create')->name($route_alias . '.tambah_riwayat');
                Route::put($url . '/simpan_riwayat', $controller . 'Controller@store')->name($route_alias . '.simpan_riwayat');
                Route::put($url . '/simpan_permanen_riwayat', $controller . 'Controller@store_permanen')->name($route_alias . '.simpan_permanen_riwayat');
                Route::post($url . '/simpan_dokumen', $controller . 'Controller@store_dokumen')->name($route_alias . '.simpan_dokumen');
                Route::get($url . '/{id}/detail_riwayat', 'RiwayatSeminarController@show')->name($route_alias . '.detail_riwayat');
                Route::delete($url . '/{id}/delete_riwayat', $controller . 'Controller@destroy')->name($route_alias . '.delete_riwayat');
                Route::delete($url . '/{id}/delete_dok_riwayat', $controller . 'Controller@hapus_dokumen')->name($route_alias . '.delete_dok_riwayat');

                $route_alias = 'informasi_dosen_mahasiswa';
                $controller = 'InformasiDosenMahasiswa';
                $url = $route_alias;
                Route::get($url, $controller . 'Controller@index')->name($route_alias);

                Route::namespace('TugasAkhir')->prefix('tugas_akhir')->name('tugas_akhir.')->group(function () {
                    $route_alias = 'sisa_waktu_penyusunan';
                    $controller = 'SisaWaktuPenyusunan';
                    $url = $route_alias;
                    Route::get($url, $controller . 'Controller@index')->name($route_alias);

                    $route_alias = 'pengajuan_draft_usul';
                    $controller = 'PengajuanDraftUsul';
                    $url = $route_alias;
                    Route::get($url, $controller . 'Controller@index')->name($route_alias);
                    Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                    Route::put($url . '/simpan', $controller . 'Controller@store')->name($route_alias . '.simpan');
                    Route::put($url . '/simpan_permanen_ajuan', $controller . 'Controller@store_permanen')->name($route_alias . '.simpan_permanen_ajuan');
                    Route::post($url . '/simpan_dokumen', $controller . 'Controller@store_dokumen')->name($route_alias . '.simpan_dokumen');
                    Route::get($url . '/{id}/detail', $controller . 'Controller@show')->name($route_alias . '.detail');
                    Route::delete($url . '/{id}/delete_dok_ajuan', $controller . 'Controller@hapus_dokumen')->name($route_alias . '.delete_dok_ajuan');
                    Route::delete($url . '/{id}/delete', $controller . 'Controller@destroy')->name($route_alias . '.delete');
                    Route::delete($url . '/{id}/delete_ajuan', $controller . 'Controller@destroy')->name($route_alias . '.delete_ajuan');
                });
            });
            Route::namespace('Administrasi')->prefix('administrasi')->name('administrasi.')->group(function () {
                Route::namespace('SuratAktifKuliah')->group(function () {
                    $route_alias = 'surat_aktif_kuliah';
                    $controller = 'StudentActiveLetter';
                    $url = $route_alias;

                    Route::get($url, $controller . 'Controller@index')->name($route_alias);
                    Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                    Route::post($url . '/add', $controller . 'Controller@store')->name($route_alias . '.add');
                    Route::get($url . '/{id}/detail', $controller . 'Controller@detail')->name($route_alias . '.detail');
                    Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.edit');
                    Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                    Route::put($url . '/{id}/submit', $controller . 'Controller@submit')->name($route_alias . '.submit')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/preview', $controller . 'Controller@preview')
                        ->name($route_alias . '.preview')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/preview-pdf', $controller . 'Controller@previewPDF')
                        ->name($route_alias . '.preview-pdf')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/download-pdf', $controller . 'Controller@downloadPDF')
                        ->name($route_alias . '.download-pdf')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/history', $controller . 'Controller@history')
                        ->name($route_alias . '.history')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                });

                Route::namespace('SuratMasihKuliah')->group(function () {
                    $route_alias = 'surat_masih_kuliah';
                    $controller = 'StillStudyLetter';
                    $url = $route_alias;

                    Route::get($url, $controller . 'Controller@index')->name($route_alias);
                    Route::get($url . '/tambah', $controller . 'Controller@create')->name($route_alias . '.tambah');
                    Route::post($url . '/add', $controller . 'Controller@store')->name($route_alias . '.add');
                    Route::get($url . '/{id}/detail', $controller . 'Controller@detail')->name($route_alias . '.detail');
                    Route::get($url . '/{id}/edit', $controller . 'Controller@edit')->name($route_alias . '.edit');
                    Route::put($url . '/{id}/update', $controller . 'Controller@update')->name($route_alias . '.update');
                    Route::put($url . '/{id}/submit', $controller . 'Controller@submit')->name($route_alias . '.submit')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/preview', $controller . 'Controller@preview')
                        ->name($route_alias . '.preview')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/preview-pdf', $controller . 'Controller@previewPDF')
                        ->name($route_alias . '.preview-pdf')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/{id}/download-pdf', $controller . 'Controller@downloadPDF')
                        ->name($route_alias . '.download-pdf')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
                    Route::get($url . '/history', $controller . 'Controller@history')
                        ->name($route_alias . '.history')
                        ->withoutMiddleware(\MP\ManAkses\AuthenticateMiddleware::class);
            });
        });
    });
});
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');