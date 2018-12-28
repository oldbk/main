<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.11.2015
 */

namespace components\Helper;


class TimeHelper
{
    public static function isExpire($value, $period)
    {
        $last_plus_month = new \DateTime();
        $last_plus_month->setTimestamp($value)
            ->modify($period);

        $current_time = new \DateTime();

        return $last_plus_month < $current_time;
    }

    public static function prettyTime($start_timestamp = null, $end_timestamp = null, $seconds = false, $format = [])
    {
		$start_datetime = new \DateTime();
		if($start_timestamp !== null) {
			$start_datetime->setTimestamp($start_timestamp);
		}

		$end_datetime = new \DateTime();
		if($end_timestamp !== null) {
			$end_datetime->setTimestamp($end_timestamp);
		}
		if($end_datetime->getTimestamp() - $start_datetime->getTimestamp() < 60) {
			return 'менее минуты';
		}

		$interval = $end_datetime->diff($start_datetime);

		$time_type = [
			'm' => isset($format['m']) ? $format['m'] : '%m мес.',
			'd' => isset($format['d']) ? $format['d'] : '%d дн.',
			'h' => isset($format['h']) ? $format['h'] : '%h ч.',
			'i' => isset($format['i']) ? $format['i'] : '%i мин.',
		];
		if($seconds) {
			$time_type['s'] = isset($format['s']) ? $format['s'] : '%s сек.';
		}
		$format_arr = [];
		foreach($time_type as $property => $format) {
			if($interval->{$property} != 0) {
				$format_arr[] = $format;
			}
		}
		if(empty($format_arr)) {
			return null;
		}

		return $interval->format(implode(' ', $format_arr));
    }
}