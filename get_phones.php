<?php
require 'bd.php';
require 'utils.php';

$q = $mysqli->query("select id, image_info from ads where phone = '' and ad_id != '' ");

while($row = mysqli_fetch_array($q)){
    $url = $row[1];
    $id = $row[0];
    $data = get_content($url);
    echo $data;
    break;

}
