<?php
/**
 * *
 *  * Old theme functions
 *  *
 *  * @package    Legacy
 *  * @subpackage core
 *  * @author     Original Authors: Minahito
 *  * @author     Other Authors : Kazumi Ono (aka onokazu)
 *  * @copyright  2005-2020 The XOOPSCube Project
 *  * @license    Legacy : GPL 2.0
 *  * @license    Cube : https://github.com/xoopscube/xcl/blob/master/BSD_license.txt
 *  * @version    v 1.1 2007/05/15 02:34:18 minahito, Release: @package_230@
 *  * @link       https://github.com/xoopscube/xcl
 * *
 */

// These are needed when viewing old modules (that don't use Smarty template files) when a theme that use Smarty templates are selected.

// function_exists check is needed for inclusion from the admin side

if (!function_exists('opentable')) {
    function OpenTable($width='100%')
    {
        echo '<table width="'.$width.'" cellspacing="0" class="outer"><tr><td class="even">';
    }
}

if (!function_exists('closetable')) {
    function CloseTable()
    {
        echo '</td></tr></table>';
    }
}

if (!function_exists('themecenterposts')) {
    function themecenterposts($title, $content)
    {
        echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer"><tr><td class="head">'.$title.'</td></tr><tr><td><br>'.$content.'<br></td></tr></table>';
    }
}
