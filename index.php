
<style>
    html{
        font-family: Arial;
    }
    .phone{
        color: #dd2222;
    }
    .date{
        color: #888;
    }
</style>
<?php
require 'utils.php';
require 'bd.php';

if(isset($_GET['phone']))
{
    $p = $_GET['phone'];
    $q = $mysqli->query("select * from ads where ad_id != '' and phone = '$p' order by time desc");

}
else
$q = $mysqli->query("select * from ads where ad_id != '' and phone != '' order by time desc");
$i = 1;
while($row = mysqli_fetch_array($q)){
    $date = date("d.m.Y H:i", $row[6] + 3 * 3600);
    echo "$i) <a href=https://avito.ru$row[4] target='_blank'>$row[3]</a> - " .
        "<a class='phone' href='?phone=$row[7]'>$row[7]</a> " .
        "<span class='date'>$date</span><br><br>";
    $i++;

}