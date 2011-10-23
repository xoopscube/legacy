<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_escape.php,v 1.3 2008/09/25 15:12:37 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
 * Examples: {$msg|xoops_excape}
 * -------------------------------------------------------------
 */

function smarty_modifier_xoops_escape($string, $esc_type = 'show')
{
    $root =& XCube_Root::getSingleton();
    $textFilter =& $root->getTextFilter();
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
?>
