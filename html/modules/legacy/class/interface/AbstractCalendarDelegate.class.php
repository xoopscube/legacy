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
abstract class Legacy_AbstractCalendarDelegate
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
	abstract public function getCalendarEvents(/*** mix[] ***/ &$event, /*** int ***/ $start, /*** int ***/ $end, /*** int ***/ $uid);

}

?>
