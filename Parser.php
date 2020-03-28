<?php

require 'utils.php';
require 'phpQuery.php';

class Parser{
    public $data;
    private $query, $region;
    /** @var Mysqli $mysqli */
    private $mysqli;
    public function __construct($mysqli, $query = "Опалубка", $region = "moskva_i_mo")
    {
        $this->query = $query;
        $this->region = $region;
        $this->mysqli = $mysqli;
    }

    public function parse($from_page = 1, $update = 1)
    {

        $page = $from_page;
        while (1) {
        $url = "https://m.avito.ru/api/9/items?key=af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir&" .
            "query=" . $this->query . "&" .
            "locationId=107620&sort=date&" .
            "categoryId=19&amp;params[44]=144&" .
            "page=" . $page . "&" .
            "lastStamp=" . (time() - 100) . "&" .
            "display=list&" .
            "limit=50";
        $data = get_content($url);
        $data = json_decode($data, 1);
        $data = $data['result']['items'];
        if(!count($data))
            return;
            foreach ($data as $item) {
                $item = $item['value'];
                $id = $item['id'];
                $title = $item['title'];
                $href = $item['uri_mweb'];
                $time = $item['time'];
                $phone_data = "https://m.avito.ru/api/1/items/" .
                    "$id/phone?" .
                    "key=af0deccbgcgidddjgnvljitntccdduijhdinfgjgfjir";
                $q = $this->query;
                echo "$title";
                if(!mysqli_num_rows($this->mysqli->query("select * from ads where ad_id = $id and query = '$q'"))){
                    if(!mysqli_num_rows($this->mysqli->query("select * from ads where ad_id = $id"))) {
                        $this->mysqli->query("insert into ads values(0, '$q', '$id', '$title', '$href', '$phone_data', '$time', '')");
                        echo " Added<BR>";
                    }
                } else
                    if($update)
                        return;
                echo "<BR>";

            }
            $page++;
            sleep(0.5);
        }
    }

}


