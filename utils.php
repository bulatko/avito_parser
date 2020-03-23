<?php


function get_content($url, $data = [], $getlink = null)
{

    $ch = curl_init($url);
    if ($data != null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . 'cookie.txt');
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
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