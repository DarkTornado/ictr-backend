<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); //임시

$line = $_REQUEST['line'];
$data = read_time_table($line);

$result = [];
echo $data;

function read_time_table($line) {
    $day = date('w');

    /* 주말/평일 구분 */
    if ($day == 0 || $day == 6) $file_name = 'timetable/line.'.$line.'_ends.json';
    else $file_name = 'timetable/line'.$line.'_days.json';

    /* 시간표 파일 읽기 */
    $fp = fopen($file_name, 'r');
    $size = filesize($file_name);
    if ($size > 0){
        $value = fread($fp, $size);
        fclose($fp);
        return $value;
        return json_decode($value, true);
    } else {
        echo '{"error":true,"msg":"Cannot read file"}';
        fclose($fp);
        exit;
    }
}


?>