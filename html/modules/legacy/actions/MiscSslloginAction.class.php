<?php
/**
 *
 * @package Legacy
 * @version $Id: MiscSslloginAction.class.php,v 1.5 2008/09/25 15:12:07 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * @public
 * @todo This action should be implemented by base. We must move it to user.
 */
class Legacy_MiscSslloginAction extends Legacy_Action
{
	function execute(&$controller, &$xoopsUser)
	{
		return LEGACY_FRAME_VIEW_INDEX;
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		//
		// Because this action's template uses USER message catalog, load it.
		//
		$root =& $controller->mRoot;
	
		$config_handler =& xoops_gethandler('config');
		$moduleConfigUser =& $config_handler->getConfigsByDirname('user');
	
		if($moduleConfigUser['use_ssl'] == 1 && ! empty($_POST[$moduleConfigUser['sslpost_name']])){
			session_id($_POST[$moduleConfigUser['sslpost_name']]);
		}
	
		$render->setTemplateName("legacy_misc_ssllogin.html");
		$render->setAttribute("message", XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_LOGIN_SUCCESS, $xoopsUser->get('uname')));
	}
}

?>
