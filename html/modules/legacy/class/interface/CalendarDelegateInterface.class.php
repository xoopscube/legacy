<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit();
}

/**
 * Interface of calendar delegate
**/
interface Legacy_iCalendarDelegate
{
	/**
	 * getCalendarEvents	Legacy_Calendar.GetCalendarEvents
	 *
	 * @param Legacy_AbstractCalendarObject[] &$event
	 * @param int $start
	 * @param int $end
	 * @param int $uid
	 *
	 * @return	void
	 */	
	public static function getCalendarEvents(/*** mix[] ***/ &$event, /*** int ***/ $start, /*** int ***/ $end, /*** int ***/ $uid);

}

?>
