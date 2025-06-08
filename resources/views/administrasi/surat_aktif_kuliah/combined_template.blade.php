{{-- {{ dd(get_defined_vars()) }} --}}

@include('administrasi.surat_aktif_kuliah.student_active_letter_request_template', ['data' => $data, 'advisorQrCode' => $advisorQrCode ?? null])
@include('administrasi.surat_aktif_kuliah.student_active_letter_application_template', ['data' => $data, 'pathImageLogo' => $pathImageLogo, 'headOfDepartementQrCode' => $headOfDepartementQrCode ?? null])
@include('administrasi.surat_aktif_kuliah.student_active_letter_template', ['data' => $data, 'pathImage' => $pathImage])

@if ($data->supporting_document && file_exists(public_path('storage/' . str_replace('public/', '', $data->supporting_document))))
    @php
        $path = public_path('storage/' . str_replace('public/', '', $data->supporting_document));
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data_img = base64_encode(file_get_contents($path));
        $src = "data:image/$type;base64,$data_img";
    @endphp
    <div style="margin-top: 10px; text-align: center;">
        <img src="{{ $src }}" alt="Dokumen Pendukung" style="max-width: 100%; max-height: 100%; display: inline-block;">
    </div>
@endif