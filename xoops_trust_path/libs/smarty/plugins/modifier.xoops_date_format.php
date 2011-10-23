<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_date_format.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     xoops_date_format
 * Purpose:  format datestamps via strftime ( use xoops timestamp )
 * Input:    time: input date unixtime
 *           format: strftime format for output
 * -------------------------------------------------------------
 */
function smarty_modifier_xoops_date_format($time, $format="%b %e, %Y")
{
	if($time && is_numeric($time)) {
		return strftime ( $format, xoops_getUserTimestamp ( $time ) );
	}
	return;
}

?>
