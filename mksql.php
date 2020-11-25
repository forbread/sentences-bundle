<?php
/**
 * Author: koma<komazhang@foxmail.com>
 * Date: 10/7/18
 */

set_time_limit(0);

$dataBasePath  = "sentences/";

$host = "127.0.0.1";
$port = 3306;
$username = "root";
$password = "chu123456";
$poetryDb = "hitokotos";

$db = mysqli_connect($host, $username, $password, $poetryDb, $port);
if (mysqli_connect_error()) {
    die("Connect Error: ".mysqli_connect_errno());
}
if (!mysqli_set_charset($db, "utf8")) {
    die("Error loading character set utf8: ".mysqli_error($db));
}

mkSQL();

//============================= 执行函数区
function mkSQL() {
    
    mkHitokotoData(); // 汉字
}



function mkHitokotoData() {
    global $dataBasePath;

    doExecute('delete from hitokoto');

    $total = 0;
    $num = 0;
    $categorty = ['a','b','c','d','e','f','g','h','i','j','k','l'];
    do {
        $fileName = $dataBasePath.''.$categorty[$num].'.json';
        if (!file_exists($fileName)) break;

        $dataJson = file_get_contents($fileName);
        $dataArray = json_decode($dataJson, true);
        $total += count($dataArray);
        printf("start process song ci data file: %s, current total data num: %d\n", $fileName, $total);
        $sql = "insert into hitokoto(uuid, hitokoto, type, froms, from_who, creator, creator_uid, reviewer, commit_from, assessor, owner, created_at, length) values ";
        $value = '';
        foreach ($dataArray as $val) {
            $v = '("'.$val['uuid'].'","'.$val['hitokoto'].'","'.$val['type'].'","'.$val['from'].'","'.$val['from_who'].'","'.$val['creator'].'","'.$val['creator_uid'].'","'.$val['reviewer'].'","'.$val['commit_from'].'","'.$val['assessor'].'","'.$val['owner'].'","'.$val['created_at'].'","'.$val['length'].'")';
            $value .= $value == '' ? $v : ','.$v;
        }
        doExecute($sql.$value);
        $num += 1;
    } while(true);
    $res = doQuery('select count(*) as total from hitokoto');
    $row = $res->fetch_assoc();
    printf("DB idiom total num: %d\n", $row['total']);
}
 

//============================= 公用函数区
function doExecute($sql) {
    global $db;
    if (!$db->query($sql)) {
        die("Query Error: ".mysqli_error($db));
    }
}

function doQuery($sql) {
    global $db;

    $res = $db->query($sql);
    if (!$res) {
        die("Query Error: ".mysqli_error($db));
    }

    return $res;
}

function trimStr($str) {
    return str_replace(["\\", "\"", "\'"], ["", "", ""], $str);
}