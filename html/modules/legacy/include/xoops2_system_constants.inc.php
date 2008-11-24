<?php
/**
 *
 * @package XOOPS2
 * @version $Id: xoops2_system_constants.inc.php,v 1.3 2008/09/25 15:12:38 kilica Exp $
 * @copyright Copyright (c) 2000 XOOPS.org  <http://www.xoops.org/>
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * This file defines constants which were defined in system module of XOOPS2.
 * Legacy module already has removed all codes which depend on these constants.
 * This file may be loaded by some developers who need these constants.
 * 
 */

if (!defined('XOOPS_SYSTEM_GROUP')) {
	define('XOOPS_SYSTEM_GROUP', 1);
	define('XOOPS_SYSTEM_USER', 2);
	define('XOOPS_SYSTEM_PREF', 3);
	define('XOOPS_SYSTEM_MODULE', 4);
	define('XOOPS_SYSTEM_BLOCK', 5);
	// define('XOOPS_SYSTEM_THEME', 6);
	define('XOOPS_SYSTEM_FINDU', 7);
	define('XOOPS_SYSTEM_MAILU', 8);
	define('XOOPS_SYSTEM_IMAGE', 9);
	define('XOOPS_SYSTEM_AVATAR', 10);
	define('XOOPS_SYSTEM_URANK', 11);
	define('XOOPS_SYSTEM_SMILE', 12);
	define('XOOPS_SYSTEM_BANNER', 13);
	define('XOOPS_SYSTEM_COMMENT', 14);
	define('XOOPS_SYSTEM_TPLSET', 15);
}

?>
