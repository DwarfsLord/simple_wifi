<?php

require_once 'config.php';

require_once 'schedule_helper.php';

define('MINUTES', 60/HOUR_SUBDIVISIONS);

class bin_cal
{
    /**
     * @var bool[]
     */
    public $active;

    public function __construct()
    {
        $this->active = array_fill(0, HOUR_SUBDIVISIONS*24*7, false);
    }

    /**
     * @param event[] $cal
     */
    public function from_cal(array $cal)
    {
        if (count($cal) == 0) {
            $this->active = array_fill(0, HOUR_SUBDIVISIONS*24*7, true);
            return;
        }
        $cal_i = 0;
        $on = true;
        for ($i=0; $i < HOUR_SUBDIVISIONS*24*7; $i++) {
            if ($on) {
                if ($cal_i < count($cal) && $cal[$cal_i]->start <= $i * MINUTES) {
                    $on = false;
                }
            } else {
                if ($cal_i < count($cal) && $cal[$cal_i]->end <= $i * MINUTES) {
                    $on = true;
                    $cal_i += 1;
                }
            }
            $this->active[$i] = $on;
        }
    }

    public function from_cs(string $input)
    {
        $input_array = explode(",", $input);
        $this->active = [];
        foreach ($input_array as $value) {
            $this->active[] = intval($value);
        }
    }

    /**
     * @return event[]
     */
    public function to_cal()
    {
        $retval = [];
        $last_active = true;
        $start_time  = 0;

        foreach ($this->active as $key => $value) {
            if (!$value) {
                if ($last_active) {
                    $start_time = $key * MINUTES;
                    $last_active = false;
                }
            } else {
                if (!$last_active) {
                    $retval[] = new event($start_time, ($key * MINUTES)-1);
                    $last_active = true;
                }
            }
        }

        if (!$last_active) {
            $retval[] = new event($start_time, (7*24*60)-1);
        }

        return $retval;
    }
}
