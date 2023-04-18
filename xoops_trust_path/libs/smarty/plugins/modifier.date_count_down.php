<?php

require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');

/**
 * Smarty date_count_down modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_count_down<br>
 * Input:<br>
 *         - $deadLineDateTime: input date string
 *         - $currentDateTime: a datetime to count down
 * @param string $deadLineDateTime
 * @param string $currentDateTime
 * @return string
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_count_down($deadLineDateTime, $currentDateTime = null)
{
	$deadLineDateTime = smarty_make_timestamp($deadLineDateTime);
	$currentDateTime  = smarty_make_timestamp($currentDateTime);
	$interval         = $deadLineDateTime - $currentDateTime;

	// 言語定数が定義されている場合は言語定数を使う
	$daysFormat     = defined('_DATE_COUNT_DOWN_DAYS_FORMAT')      ? _DATE_COUNT_DOWN_DAYS_FORMAT      : '%u日';
	$hoursFormat    = defined('_DATE_COUNT_DOWN_HOURS_FORMAT')     ? _DATE_COUNT_DOWN_HOURS_FORMAT     : '%u時間';
	$minuteFormat   = defined('_DATE_COUNT_DOWN_MINUTE_FORMAT')    ? _DATE_COUNT_DOWN_MINUTE_FORMAT    : '%u分';
	$pastDateFormat = defined('_DATE_COUNT_DOWN_PAST_DATE_FORMAT') ? _DATE_COUNT_DOWN_PAST_DATE_FORMAT : 'Y-m-d H:i:s';

	$oneDay    = 86400;
	$oneHour   = 3600;
	$oneMinute = 60;

	// 残りN日
	if ( $interval >= $oneDay ) {
		$leftDays = ceil($interval / $oneDay);
		return sprintf($daysFormat, $leftDays);
	}

	// 残りN時間
	if ( $interval >= $oneHour ) {
		$leftHours = ceil($interval / $oneHour);
		return sprintf($hoursFormat, $leftHours);
	}

	// 残りN分
	if ( $interval >= 0 ) {
		$leftMinutes = ceil($interval / $oneMinute);
		return sprintf($minuteFormat, $leftMinutes);
	}

	// 過去
	return date($pastDateFormat, $deadLineDateTime);
}


