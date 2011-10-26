<?php
/**
 *
 * @package Legacy
 * @version $Id: header.php,v 1.3 2008/09/25 15:10:26 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x <http://www.xoops.org>        |
 *------------------------------------------------------------------------*/

if (!defined('XOOPS_ROOT_PATH')) exit();

$root=&XCube_Root::getSingleton();
if(!is_object($root->mController)) exit();

$root->mController->executeHeader();
?>
