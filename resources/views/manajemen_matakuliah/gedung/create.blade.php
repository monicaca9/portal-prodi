@if(is_null($prodi))
    <?php
        $sms_list = collect(\DB::SELECT("
            SELECT
                tsms.id_sms,
                CASE WHEN tjp.id_jenj_didik IS NOT NULL AND tsms.id_jenj_didik!=99 THEN
                    CONCAT(tjs.nm_jns_sms,' ',tsms.nm_lemb,' (',tjp.nm_jenj_didik,')')
                    ELSE CONCAT(tjs.nm_jns_sms,' ',tsms.nm_lemb)
                END AS nm_lemb
            FROM pdrd.sms AS tsms
            JOIN ref.jenis_sms AS tjs ON tjs.id_jns_sms=tsms.id_jns_sms
            LEFT JOIN ref.jenjang_pendidikan AS tjp ON tjp.id_jenj_didik=tsms.id_jenj_didik AND tjp.expired_date IS NULL
            WHERE tsms.soft_delete=0
            ORDER BY tjs.id_jns_sms ASC, tsms.nm_lemb ASC
        "))->pluck('nm_lemb','id_sms')->toArray();
    ?>
    {!! FormInputSelect('id_sms','Lembaga',$sms_list,true) !!}
@else
    <input type="hidden" name="id_sms" value="{{ $prodi->id_induk_sms }}">
@endif
{!! FormInputText('nm_gedung','Nama Gedung','text',null,['required'=>true]) !!}
{!! FormInputText('alamat_gedung','Alamat Gedung','text',null,['required'=>true]) !!}
{!! FormInputText('panjang','Panjang Gedung','text') !!}
{!! FormInputText('lebar','Lebar Gedung','text') !!}
{!! FormInputText('luas','Luas Gedung','text') !!}
{!! FormInputText('lintang','Lintang Lokasi Gedung','text') !!}
{!! FormInputText('bujur','Bujur Lokasi Gedung','text') !!}
