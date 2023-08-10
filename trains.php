<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); //임시
date_default_timezone_set('Asia/Seoul');

$line = $_REQUEST['line'];

if ($line != 1) {
    echo '{"error":true,"msg":"Invalid Line"}';
    exit;
}

$data = read_time_table($line);
$stn_names = [ //2호선은 열차별 시간표가 공개되어 있지 않아요
    ['계양', '귤현', '박촌', '임학', '계산', '경인교대입구', '작전', '갈산', '부평구청', '부평시장', '부평', '동수', '부평삼거리', '간석오거리', '인천시청', '예술회관', '인천터미널', '문학경기장', '선학', '신연수', '원인재', '동춘', '동막', '캠퍼스타운', '테크노파크', '지식정보단지', '인천대입구', '센트럴파크', '국제업무지구', '송도달빛축제공원']
][$line - 1];

$result = [];

$now = 60*60*date('H') + 60*date('i') + date('s');

foreach($data as $train => $times) {
    $time = $times[count($times) - 1]['time'];
    $time = time2sec($time);
    
    if ($time < $now) continue;
    $time = time2sec($times[0]['time']);
    if ($now < $time) continue;

    $info = [];
    $info['no'] = $train;

    $train = (int)$train;
    $ud = $train % 2 == 0 ? 'up' : 'dn';
    $stn = get_train_pos($times, $now);

    $info['updn'] = $ud;
    $info['stn'] = $stn;
    $result[] = $info;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

function get_train_pos($times, $now) {
    for ($n = count($times) - 1; $n >=0; $n--) {
        $time = time2sec($times[$n]['time']);
        if ($time == $now) return $times[$n]['stn'];
        if ($time < $now) return $times[$n + 1]['stn'];
    }
    return -1;
}

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
    if ($day == 0 || $day == 6) $file_name = 'timetable/line'.$line.'_ends.json';
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