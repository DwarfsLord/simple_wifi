<?php
/**
 * Hospot Editor On/Off/Schedule
 *
 * contributed by:
 * description:    example basic PHP script to update WLAN settings of a device when using a controller version 5.5.X or higher
 *                 where set_ap_radiosettings() throws an error
 */

/**
 * using the composer autoloader
 */
require_once 'vendor/autoload.php';



//---- CUSTOM HELPER
function wlan_key_from_name(array $datas, string $name):int
{
    foreach ($datas as $key => $data) {
        if ($data->name == $name) {
            return $key;
        }
    }
}

function setup_ui_client():UniFi_API\Client{
    /**
     * include the config file (place your credentials etc. there if not already present)
     * see the config.template.php file for an example
     */
    require_once 'config_unifi.php';

    $site_id = 'default';
    $unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion, false);
    $set_debug_mode   = $unifi_connection->set_debug(false);
    if($set_debug_mode != true)
      echo "debug mode error";
    $loginresults     = $unifi_connection->login();
    if($loginresults != true)
      echo "login error: " . $loginresults;
    return $unifi_connection;
}

function get_wifi(string $wifi_name)
{
    $unifi_connection = setup_ui_client();
    $data             = $unifi_connection->list_wlanconf();

    $workhorse        = $data[wlan_key_from_name($data, $wifi_name)];

    return $workhorse;
}

// echo "<pre>";
// echo var_export(get_wifi());

function get_wifi_demo(){
    $retval = (object) array(
        '_id' => '614b6ef629688b05ca077324',
        'ap_group_ids' => 
       array (
         0 => '614b379929688b05ca075f34',
       ),
        'enabled' => true,
        'fast_roaming_enabled' => false,
        'hide_ssid' => false,
        'name' => 'workhorse',
        'networkconf_id' => '6149f63c29688b09b01a808e',
        'pmf_mode' => 'disabled',
        'usergroup_id' => '6149f63c29688b09b01a808f',
        'wlan_band' => 'both',
        'wpa_enc' => 'ccmp',
        'x_passphrase' => 'QED911;:!63',
        'wpa3_support' => false,
        'wpa3_transition' => false,
        'wpa3_fast_roaming' => false,
        'wpa3_enhanced_192' => false,
        'group_rekey' => 0,
        'uapsd_enabled' => false,
        'mcastenhance_enabled' => false,
        'no2ghz_oui' => false,
        'bss_transition' => true,
        'proxy_arp' => false,
        'l2_isolation' => false,
        'b_supported' => false,
        'optimize_iot_wifi_connectivity' => true,
        'dtim_mode' => 'default',
        'minrate_ng_enabled' => false,
        'minrate_ng_data_rate_kbps' => 1000,
        'minrate_ng_advertising_rates' => false,
        'minrate_na_enabled' => false,
        'minrate_na_data_rate_kbps' => 6000,
        'minrate_na_advertising_rates' => false,
        'mac_filter_enabled' => false,
        'mac_filter_policy' => 'allow',
        'mac_filter_list' => 
       array (
       ),
        'radius_mac_auth_enabled' => false,
        'radius_macacl_format' => 'none_lower',
        'security' => 'wpapsk',
        'wpa_mode' => 'wpa2',
        'radius_das_enabled' => false,
        'site_id' => '6149f63729688b09b01a807d',
        'iapp_enabled' => false,
        'x_iapp_key' => 'd49e659e587c25d938554a0a99e37ea6',
        'dtim_ng' => 1,
        'dtim_na' => 3,
        'schedule_with_duration' => 
       array (
         0 => 
         (object) array(
            'name' => 'Morning Lectures',
            'start_days_of_week' => 
           array (
             0 => 'mon',
             1 => 'tue',
             2 => 'wed',
             3 => 'thu',
             4 => 'fri',
           ),
            'start_hour' => 9,
            'start_minute' => 15,
            'duration_minutes' => 180,
         ),
         1 => 
         (object) array(
            'name' => 'Evening Lectures',
            'start_days_of_week' => 
           array (
             0 => 'mon',
             1 => 'tue',
             2 => 'wed',
             3 => 'thu',
           ),
            'start_hour' => 19,
            'start_minute' => 30,
            'duration_minutes' => 90,
         ),
         2 => 
         (object) array(
            'name' => 'Night',
            'start_days_of_week' => 
           array (
             0 => 'mon',
             1 => 'tue',
             2 => 'wed',
             3 => 'thu',
             4 => 'sun',
           ),
            'start_hour' => 22,
            'start_minute' => 30,
            'duration_minutes' => 390,
         ),
         3 => 
         (object) array(
            'name' => 'Night',
            'start_days_of_week' => 
           array (
             0 => 'fri',
             1 => 'sat',
           ),
            'start_hour' => 23,
            'start_minute' => 30,
            'duration_minutes' => 330,
         ),
       ),
        'name_combine_enabled' => false,
        'p2p' => false,
        'p2p_cross_connect' => false,
        'radius_macacl_empty_password' => false,
        'rrm_enabled' => false,
        'tdls_prohibit' => false,
        'vlan_enabled' => false,
        'auth_cache' => false,
        'bc_filter_enabled' => false,
        'bc_filter_list' => 
       array (
       ),
        'country_beacon' => false,
        'dpi_enabled' => false,
        'element_adopt' => false,
        'is_guest' => false,
        'dtim_6e' => 3,
        'wlan_bands' => 
       array (
         0 => '2g',
         1 => '5g',
       ),
        'schedule_enabled' => false,
        'setting_preference' => 'manual',
        'minrate_setting_preference' => 'auto',
        'sae_groups' => 
       array (
       ),
        'sae_psk' => 
       array (
       ),
        'schedule' => 
       array (
       ),
    );

    return $retval;
}


/* Schedule:
 ["schedule_enabled"]=> bool(false)
 ["schedule"]=> array(0) { }
 ["schedule_with_duration"]=> array(4) { [0]=> object(stdClass)#14 (5) { ["name"]=> string(16) "Morning Lectures" ["start_days_of_week"]=> array(5) { [0]=> string(3) "mon" [1]=> string(3) "tue" [2]=> string(3) "wed" [3]=> string(3) "thu" [4]=> string(3) "fri" } ["start_hour"]=> int(9) ["start_minute"]=> int(15) ["duration_minutes"]=> int(180) } [1]=> object(stdClass)#15 (5) { ["name"]=> string(16) "Evening Lectures" ["start_days_of_week"]=> array(4) { [0]=> string(3) "mon" [1]=> string(3) "tue" [2]=> string(3) "wed" [3]=> string(3) "thu" } ["start_hour"]=> int(19) ["start_minute"]=> int(30) ["duration_minutes"]=> int(90) } [2]=> object(stdClass)#16 (5) { ["name"]=> string(5) "Night" ["start_days_of_week"]=> array(5) { [0]=> string(3) "mon" [1]=> string(3) "tue" [2]=> string(3) "wed" [3]=> string(3) "thu" [4]=> string(3) "sun" } ["start_hour"]=> int(22) ["start_minute"]=> int(30) ["duration_minutes"]=> int(390) } [3]=> object(stdClass)#17 (5) { ["name"]=> string(5) "Night" ["start_days_of_week"]=> array(2) { [0]=> string(3) "fri" [1]=> string(3) "sat" } ["start_hour"]=> int(23) ["start_minute"]=> int(30) ["duration_minutes"]=> int(330) } }
*/

//------ UNUSED


// /**
//  * the MAC address of the access point to modify
//  */
// $ap_mac = '<enter MAC address>';

// /**
//  * power level for 2.4GHz
//  */
// $ng_tx_power_mode = 'low';

// /**
//  * channel for 2.4GHz
//  */
// $ng_channel = 6;

// /**
//  * power level for 5GHz
//  */
// $na_tx_power_mode = 'medium';

// /**
//  * channel for 5GHz
//  */
// $na_channel = 44;

// $radio_table      = $data[0]->radio_table;
// $device_id        = $data[0]->device_id;

// foreach ($radio_table as $radio) {
//     if ($radio->radio === 'ng') {
//         $radio->tx_power_mode = $ng_tx_power_mode;
//         $radio->channel = $ng_channel;
//     }

//     if ($radio->radio === 'na') {
//         $radio->tx_power_mode = $na_tx_power_mode;
//         $radio->channel = $na_channel;
//     }
// }

// $update_device = $unifi_connection->set_device_settings_base($device_id, ['radio_table' => $radio_table]);

// if (!$update_device) {
//     $error = $unifi_connection->get_last_results_raw();
//     echo json_encode($error, JSON_PRETTY_PRINT);
// }

// /**
//  * provide feedback in json format
//  */
// echo json_encode($update_device, JSON_PRETTY_PRINT);
