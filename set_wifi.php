<?php

require_once 'get_wifi.php';


/**
 * @param ui_schedule_event[] $schedule
 */
function set_wifi(string $wifi_name, bool $enabled, $schedule){
    $unifi_connection = setup_ui_client();
    $data             = $unifi_connection->list_wlanconf();

    $wifi_id = $data[wlan_key_from_name($data, $wifi_name)]->_id;

    // $unifi_connection->set_wlansettings_base($wifi_id, 

    var_dump($schedule);
    var_dump(json_decode(json_encode($schedule),1));

    return $unifi_connection->set_wlansettings_base($wifi_id, ["enabled" => $enabled, "schedule_with_duration" => json_decode(json_encode($schedule),1)]);
}