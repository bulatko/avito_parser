<?php
include 'CONSTS.php';

function get_content($url, $data = null, $headers = [], $file_name = '')
{

    $ch = curl_init($url);
    if ($data != null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        if($file_name){
            curl_setopt($ch, CURLOPT_INFILE, fopen($file_name, 'r'));
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
    $res = curl_exec($ch);
    echo curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return $res;
}

function check_captcha($url){
    $solved = 0;
    while(!$solved) {
        $data = get_content($url);
        if (stristr($data, 'form-captcha-image js-form-captcha-image')) {

            $doc = phpQuery::newDocument($data);
            $captcha = $doc->find('img.form-captcha-image.js-form-captcha-image')->attr('src');
            $img = get_content("http://avito.ru$captcha");
            $file = "test.jpg";
            $fle = fopen($file, 'w+');
            fwrite($fle, $img);
            fclose($fle);
            $solved = solve_captcha();
            if($solved)
                return $solved;
            continue;
        } else
        return $data;
    }
}

function solve_captcha(){
    $key = "df7f6748457508408e68b406aefa0122";
    $rc_in = "http://rucaptcha.com/in.php";
    $data = [
        "key" => $key,
        "json" => 1
    ];

    $res = get_content($rc_in, $data, [], 'test.jpg');
    $resp = json_decode($res, 1);
    sleep(5.5);
    $id = $resp['request'];
    $rc_res = "http://rucaptcha.com/res.php?key=" . $key . "&action=get&json=1&id=" . $id;
    $r = get_content($rc_res);
    $r = json_decode($r, 1);
    while ($r['status'] == 0) {
        sleep(3);
        $r = get_content($rc_res);
        $r = json_decode($r, 1);
        if ($r['request'] == "ERROR_CAPTCHA_UNSOLVABLE")
            return 0;
    }
    return $r['request'];

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

function get_data($q, $p = -1, $offset = 0, $new = 0)
{
    /*
SELECT ads.* FROM ads
where ads.phone in(SELECT phone from ads GROUP BY phone HAVING min(time) > 1585400124)
     */

    global $DATA, $mysqli;
    $get_data = [];
    $n = $q;
    for ($i = 0; $i < count($DATA); $i++) {
        if ($n >= pow(2, count($DATA) - $i - 1)) {
            $get_data[] = $DATA[count($DATA) - $i - 1];
            $n -= pow(2, count($DATA) - $i - 1);
        }
    }
    $i = 0;
    $q = 'select * from ads where (';
    foreach ($get_data as $data) {
        if ($i)
            $q .= 'or ';
        $q .= "query = '$data' ";
        $i++;
    }
    $q .= ')' . ($p != -1 ? " and phone = " . $p : " and phone != 0");
    if($new){
        $time = time() - 5 * 3600 * 24;
        $q .= " and time > $time and phone not in (select phone from ads where time < $time)";
    }
    $q .= " order by time desc";
    $data = [];
    $data['count'] = mysqli_num_rows($mysqli->query($q));
    $q .= " limit $offset, 50";
    $q = $mysqli->query($q);
    $elts = [];
    while ($row = mysqli_fetch_array($q)) {
        $element = ['query' => $row[1],
            'title' => $row[3],
            'href' => $row[4],
            'time' => $row[6] + 3 * 3600,
            'phone' => $row[7]];
        $elts[] = $element;
    }
    $data['elements'] = $elts;
    return $data;
}