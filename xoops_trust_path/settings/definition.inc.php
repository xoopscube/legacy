<?php
/**
 *
 * @package Legacy
 * @version $Id: definition.inc.php,v 1.3 2008/09/25 15:12:47 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
// Enum
define("XOOPS_SIDEBLOCK_LEFT",0);
define("XOOPS_SIDEBLOCK_RIGHT",1);
define("XOOPS_SIDEBLOCK_BOTH",2);
define("XOOPS_CENTERBLOCK_LEFT",3);
define("XOOPS_CENTERBLOCK_RIGHT",4);
define("XOOPS_CENTERBLOCK_CENTER",5);
define("XOOPS_CENTERBLOCK_ALL",6);
define("XOOPS_BLOCK_INVISIBLE",0);
define("XOOPS_BLOCK_VISIBLE",1);

define("XOOPS_MATCH_START",0);
define("XOOPS_MATCH_END",1);
define("XOOPS_MATCH_EQUAL",2);
define("XOOPS_MATCH_CONTAIN",3);

// Smarty
define("SMARTY_DIR", XOOPS_TRUST_PATH."/libs/smarty/");
define("XOOPS_COMPILE_PATH", XOOPS_TRUST_PATH."/templates_c");

// Path
define("XOOPS_CACHE_PATH", XOOPS_TRUST_PATH."/cache");
define("XOOPS_MODULE_PATH", XOOPS_ROOT_PATH."/modules");
define("XOOPS_UPLOAD_PATH", XOOPS_ROOT_PATH."/uploads");
define("XOOPS_THEME_PATH", XOOPS_ROOT_PATH."/themes");
define("XOOPS_LIBRARY_PATH", XOOPS_TRUST_PATH."/libs");

// URL
define("XOOPS_MODULE_URL", XOOPS_URL."/modules");
define("XOOPS_UPLOAD_URL", XOOPS_URL."/uploads");
define("XOOPS_THEME_URL", XOOPS_URL."/themes");

define("XOOPS_LEGACY_PROC_NAME", "legacy");


// USER
define("XCUBE_CORE_USER_MODULE_NAME","user");
define("XCUBE_CORE_USER_UTILS_CLASS","UserAccountUtils");	// not use


define("XCUBE_CORE_PM_MODULE_NAME","pm");

define('LEGACY_SYSTEM_COMMENT', 14);

//
// A name of the render-system used by the embedded template of XoopsForm.
//
define('XOOPSFORM_DEPENDENCE_RENDER_SYSTEM', 'Legacy_RenderSystem');


?>
