<?php
//TMP
// require_once 'get_wifi.php';
// $workhorse = get_wifi_demo();
// $cal = clean_cal(unifi2cal($workhorse->schedule_with_duration));

// var_dump($cal);

// var_dump($workhorse->schedule_with_duration);



/* Time format:
    0        = Monday,  0H00M
    23*60+59 = Monday, 23H59M
    6*1440   = Sunday,  0H00M

    Mod 7*1440 = 10080
*/


class ui_schedule_event{
    public string $name;
    /**
     * @var string[]
     */
    public array $start_days_of_week;
    public int $start_hour;
    public int $start_minute;
    public int $duration_minutes;

    public function __construct(int $start_time, int $end_time){
        $this->name = "";
        $this->start_minute = $start_time%60;
        $this->start_hour = (($start_time-$this->start_minute)/60)%(24);
        $this->start_days_of_week = [jd2dayofweek(($start_time - $this->start_hour * 60 - $this->start_minute)/(60*24))];
        $this->duration_minutes = $end_time - $start_time + 1;
    }
}

class event
{
    public $start;
    public $end;

    public function __construct(int $start_time, int $end_time)
    {
        $this->start = $start_time;
        $this->end   = $end_time;
    }
}

/**
 * @param ui_schedule_event[] $data
 * @return event[]
 */
function unifi2cal(array $data)
{
    
    // var_dump($data);
    $retval = array();
    foreach ($data as $ui_event) {
        $event_start = $ui_event->start_hour*60+$ui_event->start_minute;
        foreach ($ui_event->start_days_of_week as $day) {
            $day_offset = 1440 * dayofweek2jd($day);
            array_push($retval,NEW event($event_start+$day_offset,$event_start+$day_offset+$ui_event->duration_minutes-1));
        }
        // echo("<br>");
    }
    return $retval;
}
/**
 * @param event[] $cal
 * @return ui_schedule_event[]
 */
function cal2unifi(array $cal){
    $retval = [];
    foreach ($cal as $event) {
        $retval[] = new ui_schedule_event($event->start, $event->end);
    }
    return $retval;
}

/**
 * @param event[] $events
 * @return event[]
 */
function sort_cal(array $events){
    usort($events, function(event $a, event $b){ return ($a->start <=> $b->start);});
    return $events;
}

/**
 * @param event[] $cal
 * @return event[]
 */
function clean_cal(array $cal){
    if(count($cal) == 0)
        return $cal;

    $cal = sort_cal($cal);
    $last_event_index = count($cal) - 1;
    if ($cal[$last_event_index]->end >= 10080) {
        array_unshift($cal, NEW event(0,$cal[$last_event_index]->end-10080));
        $cal[$last_event_index+1]->end = 10079;
    }
    return $cal;
}

function dayofweek2jd(string $day): int
{
    switch ($day) {
        case 'mon':
            return 0;
        case 'tue':
            return 1;
        case 'wed':
            return 2;
        case 'thu':
            return 3;
        case 'fri':
            return 4;
        case 'sat':
            return 5;
        default:
            return 6;
    }
}

function jd2dayofweek(int $day): string
{
    switch ($day) {
        case 0:
            return 'mon';
        case 1:
            return 'tue';
        case 2:
            return 'wed';
        case 3:
            return 'thu';
        case 4:
            return 'fri';
        case 5:
            return 'sat';
        default:
            return 'sun';
    }
}