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
	 * @param mix[] &$event see hCalendar
	 *	 $event['dirname']	module directory name
	 *	 $event['summary']
	 *	 $event['location']
	 *	 $event['url']
	 *	 $event['dtstart']	unixtime
	 *	 $event['dtend']	unixtime
	 *	 $event['description']
	 *	 $event['cat_id']	Legacy_Category id
	 *	 $event['geo']['latitude']
	 *	 $event['geo']['longitude']
	 * @param int $start
	 * @param int $end
	 * @param int $uid
	 *
     * @return  void
	 */	
	abstract public function getCalendarEvents(/*** mix[] ***/ &$event, /*** int ***/ $start, /*** int ***/ $end, /*** int ***/ $uid);

}

?>
