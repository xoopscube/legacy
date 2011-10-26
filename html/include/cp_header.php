<?php 
/**
 *
 * @package Legacy
 * @version $Id: cp_header.php,v 1.3 2008/09/25 15:12:45 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Legacy project for |
 |   keeping compatibility with XOOPS 2.0.x <http://www.xoops.org>        |
 *------------------------------------------------------------------------*/

if (!defined('XOOPS_ROOT_PATH')) {
	//
	// Strange code? This file is used from files in admin directories having no include "mainfile.php".
	// Ummm..., such uses is deprecated in Legacy.
	//
	if (!file_exists("../../../mainfile.php")) exit();
	
	require_once "../../../mainfile.php";
}

if (!defined('XOOPS_CPFUNC_LOADED')) require_once XOOPS_ROOT_PATH . "/include/cp_functions.php";

//
// [Special Mission] Additional CHECK!!
// Old modules may call this file from other admin directory.
// In this case, the controller does not have Admin Module Object.
//
$root =& XCube_Root::getSingleton();

require_once XOOPS_ROOT_PATH . "/modules/legacy/kernel/Legacy_AdminControllerStrategy.class.php";
$strategy =new Legacy_AdminControllerStrategy($root->mController);

$root->mController->setStrategy($strategy);
$root->mController->setupModuleContext();
$root->mController->_mStrategy->setupModuleLanguage();	//< Umm...

//
// TODO
//

?>
