<style>
    html{
        font-family: Arial;
    }
    a{
        color: #4444cc;
    }
    .phone{
        color: #dd2222;
    }
    .date{
        color: #888;
    }
    .today{
        background-color: #ddf;
        padding-top: 10px;
        padding-left: 10px;
        margin-bottom: 15px;
    }
</style>
<form>
    <?php
include 'CONSTS.php';
    if(isset($_GET['q']))
        $q = $_GET['q'];
    else
        $q = pow(2, count($DATA)) - 1;
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $num = $q;
    for ($i = 0; $i < count($DATA); $i++){
        echo "<input type=\"checkbox\" value=" .( count($DATA) - $i - 1) .
        ($num >= pow(2, count($DATA) - $i - 1) ? " checked" : "") .
            "> " . $DATA[count($DATA) - $i - 1] . "<br>";
        if($num >= pow(2, count($DATA) - $i - 1))
        $num -= pow(2, count($DATA) - $i - 1);
}
    if(isset($_GET['new']))
        $new = $_GET['new'];
    else
        $new = 0;
    $c2 = $new ? "checked" : "";
    $c1 = !$new ? "checked" : "";

    echo "<p>Поиск по: </p>
    Всем<input type=\"radio\" name=\"find\" id=\"radio_all\" $c1>   Новичкам<input name=\"find\" type=\"radio\" id=\"radio_new\" $c2><br><br>"
    ?>

    <input type="hidden" name="q" id="sum" value="<?echo $q?>">
    <input type="button" value="Поиск" id="go_button">
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $('#go_button').click(function (e) {
        let sum = 0;
        $('input[type=checkbox]').each(function(index) {

            console.log($(this).val() + "  " + $(this).is(':checked'));
            sum += $(this).is(':checked') ? Math.pow(2,  $(this).val()) : 0;
        });
        let New = $("#radio_all").is(':checked') ? 0 : 1;
        let url = "?q=" + sum + "&new=" + New;

        window.location = url;

    })
</script>

<?php
require 'utils.php';
require 'bd.php';

if(isset($_GET['p']))
{
    $p = $_GET['p'];
}
else
    $p = -1;

$data = get_data($q, $p, $offset, $new);

$i = 1;
$c = $data['count'];
echo "<h4>" . ($offset + 1) . " / $c</h4>";
echo "<div class='today'>";
$ended = 0;
foreach($data['elements'] as $element){
    $time = $element['time'];
    if(date("d.m.Y") != date("d.m.Y", $time)){
        if(!$ended){
            $ended = 1;
            echo "</div>";
        }
    }
    $date = date("d.m.Y H:i", $time);
    $phone = $element['phone'];
    $count = mysqli_num_rows($mysqli->query("select * from ads where phone = '$phone'"));
    $query = $element['query'];
    $href = $element['href'];
    $title = $element['title'];
    echo "[" . $query . "] <a href=https://avito.ru$href target='_blank'>$title</a> - " .
        "<a class='phone' href='?p=$phone'>$phone</a> ($count) " .
        "<span class='date'>$date</span><br><br>";

}
$o1 = max(0, $offset - 50);
$o2 = min($c, $offset + 50);
echo $offset > 0 ? "<a href='?q=$q&p=$p&offset=$o1&new=$new'>Назад</a> " : " ";
echo $offset + 50 < $c ? "<a href='?q=$q&p=$p&offset=$o2&new=$new'>Вперед</a>" : ""
?>

