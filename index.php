<?php

require_once 'get_wifi.php';

require_once 'schedule_helper.php';

require_once 'bin_cal.php';


enum Mode: string
{
    case schedule = 'schedule';
    case on = 'on';
    case off = 'off';
}

$wifi = get_wifi(WLAN_NAME);

$cal_from_unifi = clean_cal(unifi2cal($wifi->schedule_with_duration));

// var_dump($wifi->enabled);

if ($wifi->enabled) {
    if (count($cal_from_unifi) == 0) {
        $current_mode = Mode::on;
    } else {
        $current_mode = Mode::schedule;
    }
} else {
    $current_mode = Mode::off;
}



if ($current_mode == Mode::schedule) {
    $cal = $cal_from_unifi;
} else {
    $cal = json_decode(file_get_contents('schedule.json'), false);
    // print(var_export($cal));
}
$bin_cal = new bin_cal();
$bin_cal->from_cal($cal);


?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
<script>
turning_on = "none"
change_css = false
saving = false
mode_current = "<?php echo($current_mode->value); ?>"
mode_original = mode_current
bincal_original = []

document.addEventListener('DOMContentLoaded', function() {
   bincal_original = div2bincal();
}, false);

function unloader(e) {
    if(!saving){
        var confirmationMessage = 'Unsaved Changes!';
        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    }
}

function something_changed(){
    getmode();
    if(mode_current != mode_original || bincal_original != div2bincal()){
        if (!change_css) {
            document.getElementById("title").style.setProperty("background-color", "#d10000");
            document.getElementById("button").style.setProperty("display", "revert");
            change_css = true;
            window.addEventListener("beforeunload", unloader);
        }
    }else{
        if (change_css) {
            colour = document.getElementById("content").style.getPropertyValue("background-color");
            document.getElementById("title").style.setProperty("background-color", colour);
            document.getElementById("button").style.setProperty("display", "none");
            change_css = false;
            window.removeEventListener("beforeunload", unloader);
        }
    }

}

function schedule_change(e, id) {
    if(e.buttons%2 == 1){
        is_on = document.getElementById(id).classList.contains('on')

        if (is_on) {
            document.getElementById(id).classList.remove('on')
            document.getElementById(id).classList.add('off')
            turning_on = "no"

            something_changed()
        } else {
            document.getElementById(id).classList.remove('off')
            document.getElementById(id).classList.add('on')
            turning_on = "yes"

            something_changed()
        }
    }
}

function schedule_enter(e, id) {
    if(e.buttons%2 == 1){
        if (turning_on == "yes") {
            document.getElementById(id).classList.remove('off')
            document.getElementById(id).classList.add('on')
        }else{
            if(turning_on == "no"){
                document.getElementById(id).classList.remove('on')
                document.getElementById(id).classList.add('off')
            }
        }
        something_changed();
    }else{
        turning_on = "none"
    }
}

function save(){
    getmode();

    xhttp = new XMLHttpRequest();
    xhttp.open("POST", "update_wifi.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("mode="+mode_current+"&bincal="+div2bincal());
    saving = true;
    setTimeout(function(){
        window.location.reload();
    }, 1000);
}

function getmode() {
    if (document.getElementById("schedule").checked) {
        mode_current = "schedule"
    } else {
        if(document.getElementById("always_on").checked){
            mode_current = "on"
        } else {
            mode_current = "off"
        }
    }
}

function div2bincal(){
    retval = ""
    for (let i = 0; i < <?php echo(7*24*HOUR_SUBDIVISIONS);?>; i++) {
        if(document.getElementById(i).classList.contains('on')){
            retval += "1,"
        }else{
            retval += "0,"
        }
    }
    return retval.slice(0,-1);
}
</script>
</head>
<body>
<div class="root">
    <div class="save"><div class="button" id="button" onclick="save();">Save</div></div>
    <div class="box title" id="title">
        Student WiFi Tool
    </div>
    <div class="box content" id="content">
        <div class="option calendar" style="cursor:auto;
            <?php echo $current_mode == Mode::schedule?"border-width: 5px; padding: 35px;":"padding: 40px;";?>
            ">
            <input type="radio" onclick="something_changed();" id="schedule" name="setting" value="schedule" <?php echo $current_mode == Mode::schedule?"checked":"";?>>
            <label for="schedule">Schedule</label>
            <table class="time">
                <?php
                    echo "<tr>";
                    for ($j=0; $j < 7; $j++) {
                        echo '<td class="cal head">'. jd2dayofweek($j, 2) . '</td>';
                    }
                    echo "</tr>";
                    for ($i=0; $i < HOUR_SUBDIVISIONS*24; $i++) {
                        echo "<tr>";
                        for ($j=0; $j < 7; $j++) {
                            $id = $i+$j*HOUR_SUBDIVISIONS*24;
                            $colour = ($bin_cal->active[$id])?"on":"off";
                            echo '<td id="'.$id.'" class="cal time '.$colour.'" onmouseenter="schedule_enter(event,'.$id.')" onmousedown="schedule_change(event, '.$id.');">'. floor($i/HOUR_SUBDIVISIONS) .':' . sprintf('%02d', ($i%HOUR_SUBDIVISIONS)*(60/HOUR_SUBDIVISIONS)) . '</td>';
                        }
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
        <div class="option"style="
            <?php echo $current_mode == Mode::on?"border-width: 5px; padding: 35px;":"padding: 40px;";?>
            ">
            <input type="radio" onclick="something_changed();" id="always_on" name="setting" value="always_on" <?php echo $current_mode == Mode::on?"checked":"";?>>
            <label for="always_on">On</label>
        </div>
        <div class="option"style="
            <?php echo $current_mode == Mode::off?"border-width: 5px; padding: 35px;":"padding: 40px;";?>
            ">
            <input type="radio" onclick="something_changed();" id="always_off" name="setting" value="always_off" <?php echo $current_mode == Mode::off?"checked":"";?>>
            <label for="always_off">Off</label>
        </div>
    </div>
</div>
</body>
</html>