<?php
require 'bd.php';
require 'utils.php';

$q = $mysqli->query("select id, image_info from ads where phone = '' and ad_id != '' ");
get_content('https://avito.ru');
while($row = mysqli_fetch_array($q)){
    $url = $row[1];
    $id = $row[0];
    $data = get_content($url);
    echo $data . "<BR><BR>";
    $data = json_decode($data, 1);
    $data = $data['result']['action']['uri'];
    $data = substr($data, strlen($data) - 11, 11);
    echo $data . "<BR><BR><BR><BR>";
    $mysqli->query("update ads set phone = '$data' where id = $id");
    sleep(0.3);
}
