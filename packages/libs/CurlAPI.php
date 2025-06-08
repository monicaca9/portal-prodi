<?php

if( !function_exists( 'curlApi' ) ){
    function curlApi($method, $url, $fields_string, $token)
    {
        if (extension_loaded('curl') === true)
        {
            $header = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 100000);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            if ($result === false) {
                $info = curl_getinfo($ch);
                curl_close($ch);
                die('error occured during curl exec. Info: ' . var_export($info));
            }
            curl_close ($ch);
        } else {
            ini_set("allow_url_fopen", 1);
            $result = file_get_contents($url);

        }

        // dd($result);
        $obj = json_decode($result, TRUE);

        return $obj;

    }
}


if( !function_exists('generate_token_onedata')) {
    function generate_token_onedata($method, $form_url)
    {
        $token = null;
        $form_token = data_get_token_form_onedata();
        $get_token = curlApi($method, $form_url, $form_token, $token);
        $token = $get_token['data']['token_bearer'];
        return $token;
    }
}

if( !function_exists('data_get_token_form_onedata')) {
    function data_get_token_form_onedata()
    {
        return json_encode([
            "id_aplikasi" => ENV('ID_APLIKASI'),
            "username" => ENV('WS_USERNAME'),
            "password" => ENV('WS_PASSWORD')
        ]);
    }
}