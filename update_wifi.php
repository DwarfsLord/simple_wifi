<?php
require_once 'schedule_helper.php';

require_once 'bin_cal.php';

require_once 'set_wifi.php';

$bin_cal = new bin_cal();
$bin_cal->from_cs($_POST["bincal"]);

$cal = $bin_cal->to_cal();

file_put_contents("schedule.json",json_encode($cal));

if ($_POST["mode"] == "on") {
    $res = set_wifi(WLAN_NAME, true, []);
}elseif($_POST["mode"] == "off"){
    $res = set_wifi(WLAN_NAME, false, []);
}else{
    $res = set_wifi(WLAN_NAME, true, cal2unifi( $cal));
}
// echo(var_export(cal2unifi($cal)));

// echo("\n");

var_dump($res);

?>