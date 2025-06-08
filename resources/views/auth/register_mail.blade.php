<!DOCTYPE html>
<html>
<body>
	<p>Salam hormat {{ $info_pd->nm_pd.' ('.$info_pd->nim.')' }} dari Program Studi {{ $info_pd->prodi }}</p>
	<p>Langkah selanjutnya, silakan klik tautan di bawah ini untuk melakukan aktivasi akun PORTAL-PRODI Anda.</p>

	<div style="text-align: center;">
		<h1><a class="btn btn-sm btn-primary fwbold" href="{{url('/auth/aktivasi/'.Crypt::encrypt($data)) }}"><i class=" "></i> AKTIVASI AKUN</a></h1>
	</div>
	<p>atau dengan copy paste link berikut:</p>
	<p>{{url('/auth/aktivasi/'.Crypt::encrypt($data)) }}</p>

	<p>Jika anda tidak merasa melakukan registrasi pada aplikasi PORTAL-PRODI, mohon tidak melanjutkan aktivasi pada link tersebut dan silahkan abaikan pesan ini.</p>
	<p>Terima kasih atas perhatian dan kerjasamanya.</p>
</body>
{{--<footer>--}}
{{--		<p>	Gedung Rektorat (Lt.3)<br>--}}
{{--            Sekretariat Penerimaan Mahasiswa Baru<br>--}}
{{--            Jl. Prof. Dr. Sumantri Brojonegoro No. 1<br>--}}
{{--            Bandar Lampung, 35145--}}
{{--		</p>--}}
{{--	</footer>--}}
</html>
