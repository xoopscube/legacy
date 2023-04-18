<?php

require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');

/**
 * Smarty date_new modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_new<br>
 * Input:<br>
 *         - $thatTime: input date string
 * @param string $thatTime
 * @return string
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_new($thatTime)
{
	$thatTime = smarty_make_timestamp($thatTime);
	$now      = time();

	$interval  = defined('_XOOPS_DATE_NEW_INTERVAL') ? _XOOPS_DATE_NEW_INTERVAL : '2 weeks';
	$newString = defined('_XOOPS_DATE_NEW_STRING')   ? _XOOPS_DATE_NEW_STRING   : 'new';

	if ( strtotime("+$interval", $thatTime) >= $now ) {
		return sprintf('<span class="dateNew">%s</span>', $newString);
	}

	return '';
}


