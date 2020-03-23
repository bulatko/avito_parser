<?php
require 'utils.php';
require 'phpQuery.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$id = "1784126935";
$pkey = "15f6cce10qe42q75f29b11ee7c141809qb1ae141829fa9db35fqaf59bd9aff28q0410a1q4901143c7ae1cb98f5c24qd017cc2f5";
$url = "https://www.avito.ru/moskva_i_mo?s=104&q=%D0%9E%D0%BF%D0%B0%D0%BB%D1%83%D0%B1%D0%BA%D0%B0";

$data = get_content($url);
$doc = phpQuery::newDocument($data);
$f = $doc->find('div.snippet-horizontal.item.item_table.clearfix.' .
    'js-catalog-item-enum.item-with-contact.js-item-extended');
foreach ($f as $ad){
    $ad = pq($ad);
    $a = $ad->find("div.snippet-title-row.js-snippet-title-row a");
    $id = $ad->attr("data-item-id");
    $pkey = $ad->attr("data-pkey");
    $title = $a->text();
    $href = $a->attr("href");
    $button = $ad->find("button.js-item-extended-contacts.button.button-origin");
    $hash = $button->attr("data-search-hash");
    $pkey = phoneDemixer($id, $pkey);
    $phone_data = "https://www.avito.ru/items/phone/$id?" .
        "pkey=$pkey&" .
        "&h=36&vsrc=s&" .
        "searchHash";
    echo "<a href=$href>$title</a><br>" .
        "<a href=$phone_data>Телефон</a><br><br>";

}


