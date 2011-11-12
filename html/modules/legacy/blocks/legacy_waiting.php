<?php
/**
 *
 * @package Legacy
 * @version $Id: legacy_waiting.php,v 1.3 2008/09/25 15:12:12 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x <http://www.xoops.org>        |
 *------------------------------------------------------------------------*/

function b_legacy_waiting_show() {
    $modules = array();
    XCube_DelegateUtils::call('Legacyblock.Waiting.Show', new XCube_Ref($modules));
    $block['modules'] = $modules;
    return $block;
}
?>
