<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_formattimestamp.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     xoops_formattimestamp
 * Purpose:  format datestamps via strftime ( use xoops timestamp )
 * Input:    time: input date unixtime
 *           format: strftime format for output
 * -------------------------------------------------------------
 */
function smarty_modifier_xoops_formattimestamp($time, $format='s')
{
    if ($time && is_numeric($time)) {
        return formatTimestamp($time, $format);
    }
    return;
}
