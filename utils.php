<?php


function get_content($url, $data = null, $headers = [])
{

    $ch = curl_init($url);
    if ($data != null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . 'cookie.txt');
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function check_captcha($url){
    $data = get_content($url);
    if(stristr($data, 'form-captcha-image js-form-captcha-image')){
        $doc = phpQuery::newDocument($data);
        $captcha = $doc->find('img.form-captcha-image.js-form-captcha-image')->attr('src');
        $img = get_content("http://avito.ru$captcha");
        $file = "test.jpg";
        $fle = fopen($file, 'w+');
        fwrite($fle, $img);
        fclose($fle);
        return 0;
    }
    return $data;
}

function solve_captcha(){
    $captcha = get_content("test.jpg");
    $url = "https://www.avito.ru/blocked";
    $data = ['captcha' => $captcha,
        'submit' => ''];
    $res = get_content($url, $data);
    $res = json_decode($res, 1);
    if(isset($res['image64']))
        return 1;
    else return 0;

}


function phoneDemixer($t, $e) {
    preg_match_all("/[0-9a-f]+/", $e, $i);
    $i = $i[0];
    $n = implode( "",$t % 2 == 0 ? array_reverse($i) : $i);
    $r = strlen($n);
    $a = "";
    for($o = 0; $o < $r; $o++)
        if($o % 3 == 0)
            $a .= substr($n, $o, 1);

    return $a;
}