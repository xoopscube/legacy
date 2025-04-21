<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_date_format.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
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
    if ($time && is_numeric($time)) {
        return strftime($format, xoops_getUserTimestamp($time));
    }
    return;
}
