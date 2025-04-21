<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_escape.php,v 1.3 2008/09/25 15:12:37 kilica Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license GPL v2.0
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     xoops_escape
 * Author:   nobunobu
 * Purpose:  Escape the string according to escapement type(XOOPS custom version)
 * @param string
 * @param show|edit|plain|link
 * @return string
 *
 * Examples: {$msg|xoops_escape}
 * -------------------------------------------------------------
 */

function smarty_modifier_xoops_escape($string, $esc_type = 'show')
{
    static $textFilter;
    if (!isset($textFilter)) {
        $root =& XCube_Root::getSingleton();
        $textFilter = $root->getTextFilter();
    }
    switch ($esc_type) {
        case 'show':
            return $textFilter->toShow($string);

        case 'edit':
            return $textFilter->toEdit($string);

        case 'plain':
        case 'link':
            return htmlspecialchars($string, ENT_QUOTES);

        default:
            return $string;
    }
}
