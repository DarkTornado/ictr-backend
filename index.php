<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); //임시

$line = $_REQUEST['line'];
$data = read_time_table($line);
$stn_names = [
    ['계양', '귤현', '박촌', '임학', '계산', '경인교대입구', '작전', '갈산', '부평구청', '부평시장', '부평', '동수', '부평삼거리', '간석오거리', '인천시청', '예술회관', '인천터미널', '문학경기장', '선학', '신연수', '원인재', '동춘', '동막', '캠퍼스타운', '테크노파크', '지식정보단지', '인천대입구', '센트럴파크', '국제업무지구', '송도달빛축제공원'],
    ['검단오류', '왕길', '검단사거리', '마전', '완정', '독정', '검암', '검바위', '아시아드경기장', '서구청', '가정', '가정중앙시장', '석남', '서부여성회관', '인천가좌', '가재울', '주안국가산단', '주안', '시민공원', '석바위시장', '인천시청', '석천사거리', '모래내시장', '만수', '남동구청', '인천대공원', '운연']
][$line - 1];

$result = [];
for ($n = 0; $n < count($stn_names); $n++) {
    $result[$n] = ['stn' => $stn_names[$n], 'up' => 0, 'dn' => 0];
}

//var_dump($result);


function time2sec($time) {
    $t = explode(':', $time);
    $t[0] = (int)$t[0];
    $t[1] = (int)$t[1];
    if (count($t) == 3) $t[2] = (int)$t[2];
    else $t[2] = 0;
    return 60*60*$t[0] + 60*$t[1] + $t[2];
}

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
        return json_decode($value, true);
    } else {
        echo '{"error":true,"msg":"Cannot read file"}';
        fclose($fp);
        exit;
    }
}

?>