<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');
/**
 * Smarty date_weekday modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_weekday<br>
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string $datetime
 * @param bool $isLong
 * @return string
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_weekday($datetime, $isLong = false)
{
	$shortWeekdayNames = [0 => defined('_XOOPS_WEEKDAY_SUN') ? _XOOPS_WEEKDAY_SUN : '日', 1 => defined('_XOOPS_WEEKDAY_MON') ? _XOOPS_WEEKDAY_MON : '月', 2 => defined('_XOOPS_WEEKDAY_TUE') ? _XOOPS_WEEKDAY_TUE : '火', 3 => defined('_XOOPS_WEEKDAY_WED') ? _XOOPS_WEEKDAY_WED : '水', 4 => defined('_XOOPS_WEEKDAY_THU') ? _XOOPS_WEEKDAY_THU : '木', 5 => defined('_XOOPS_WEEKDAY_FRI') ? _XOOPS_WEEKDAY_FRI : '金', 6 => defined('_XOOPS_WEEKDAY_SAT') ? _XOOPS_WEEKDAY_SAT : '土'];

	$longWeekdayNames = [0 => defined('_XOOPS_WEEKDAY_SUNDAY')    ? _XOOPS_WEEKDAY_SUNDAY    : '日曜日', 1 => defined('_XOOPS_WEEKDAY_MONDAY')    ? _XOOPS_WEEKDAY_MONDAY    : '月曜日', 2 => defined('_XOOPS_WEEKDAY_TUESDAY')   ? _XOOPS_WEEKDAY_TUESDAY   : '火曜日', 3 => defined('_XOOPS_WEEKDAY_WEDNESDAY') ? _XOOPS_WEEKDAY_WEDNESDAY : '水曜日', 4 => defined('_XOOPS_WEEKDAY_THURSDAY')  ? _XOOPS_WEEKDAY_THURSDAY  : '木曜日', 5 => defined('_XOOPS_WEEKDAY_FRIDAY')    ? _XOOPS_WEEKDAY_FRIDAY    : '金曜日', 6 => defined('_XOOPS_WEEKDAY_SATURDAY')  ? _XOOPS_WEEKDAY_SATURDAY  : '土曜日'];

	$datetime = smarty_make_timestamp($datetime);
	$weekday  = date('w', $datetime);

	if ( $isLong ) {
		return $longWeekdayNames[$weekday];
	}

	return $shortWeekdayNames[$weekday];
}

