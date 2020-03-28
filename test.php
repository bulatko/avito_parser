<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'Parser.php';
require 'bd.php';

if(isset($_GET['captcha'])){
    $url = "https://m.avito.ru/api/1/items/1910022421/phone?key=af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir";
    $data = ['captcha' => $_GET['captcha'],
        'submit' => ''];
    $res = get_content($url, $data);
    echo "$res<br><br><br><br><br>";
}

$p = new Parser($mysqli);

$p->parse(1, 0);


