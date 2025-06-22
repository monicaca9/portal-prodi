{{-- {{ dd(get_defined_vars()) }} --}}

@include('administrasi.surat_masih_kuliah.still_study_letter_request_template', ['data' => $data, 'advisorQrCode' => $advisorQrCode ?? null])
@include('administrasi.surat_masih_kuliah.still_study_letter_application_template', ['data' => $data, 'pathImageLogo' => $pathImageLogo, 'headOfDepartementQrCode' => $headOfDepartementQrCode ?? null])
@include('administrasi.surat_masih_kuliah.still_study_letter_template', ['data' => $data, 'pathImage' => $pathImage])
