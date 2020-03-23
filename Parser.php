<?php
require 'utils.php';
require 'phpQuery.php';
class Parser{
    public $data;
    private $query, $region, $until, $page = 1;
    /** @var Mysqli $mysqli */
    private $mysqli;
    public function __construct($query = "Опалубка", $region = "moskva_i_mo", $mysqli, $update = false)
    {
        $this->query = $query;
        $this->region = $region;
        $this->mysqli = $mysqli;
        if($update)
            $this->until = $this->get_last();
        else
            $this->until = '0';
    }
    private function get_last(){
        $last = mysqli_fetch_row($this->mysqli->query("select ad_id from ads order by id desc limit 1"))[0];
        return $last;
}
    public function parse(){
            $url = "https://www.avito.ru" . $this->region . "?s=104&q=" . $this->query . "&p=" . $this->page;
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
                $button = $ad->find("button.js-item-extended-contacts.button.button-origin")[0];
                $hash = $button->attr("data-search-hash");
                $pkey = phoneDemixer($id, $pkey);
                $phone_data = "https://www.avito.ru/items/phone/$id?" .
                    "pkey=$pkey&" .
                    "&h=36&vsrc=s&" .
                    "searchHash";
                echo "<a href=$href>$title</a><br>" .
                    "<a href=$phone_data>Телефон</a><br><br>";

            }
        }

}
?>

<button style="" class="js-item-extended-contacts
 item-extended-contacts

 button button-origin" data-item-id="1856863242"
        data-iscvsimpletestuser="true"
        data-item-category-id="114"
        data-search-hash="1zxl1x78tj8kskgkkwk400osgc8c0cg"
        data-from-block="0"
        data-marker="item-contact">
    Показать телефон
</button>

<script>
    phoneDemixer: function(t, e) {
        if (!e)
            return "";
        var o, i = e.match(/[0-9a-f]+/g), n = (t % 2 == 0 ? i.reverse() : i).join(""), r = n.length, a = "";
        for (o = 0; o < r; ++o)
            o % 3 == 0 && (a += n.substring(o, o + 1));
        return a
    }
</script>
