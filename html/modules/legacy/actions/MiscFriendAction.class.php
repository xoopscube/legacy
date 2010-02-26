<?php
/**
 *
 * @package Legacy
 * @version $Id: MiscFriendAction.class.php,v 1.4 2008/09/25 15:12:07 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/MiscFriendForm.class.php";

class Legacy_MiscFriendAction extends Legacy_Action
{
	var $mActionForm = null;
	var $mMailer = null;
	
	function hasPermission(&$controller, &$xoopsUser)
	{
		return is_object($xoopsUser);
	}

	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_MiscFriendForm();
		$this->mActionForm->prepare();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mActionForm->load($xoopsUser);
		return LEGACY_FRAME_VIEW_INPUT;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return LEGACY_FRAME_VIEW_INPUT;
		}
		
		$root =& XCube_Root::getSingleton();
		
		$this->mMailer =& getMailer();
		$this->mMailer->setTemplate("tellfriend.tpl");
		$this->mMailer->assign("SITENAME", $root->mContext->getXoopsConfig('sitename'));
		$this->mMailer->assign("ADMINMAIL", $root->mContext->getXoopsConfig('adminmail'));
		$this->mMailer->assign("SITEURL", XOOPS_URL . '/');
		
		$this->mActionForm->update($this->mMailer);
		
		$root->mLanguageManager->loadPageTypeMessageCatalog("misc");
		
		$this->mMailer->setSubject(sprintf(_MSC_INTSITE, $root->mContext->getXoopsConfig('sitename')));
		
		return $this->mMailer->send() ? LEGACY_FRAME_VIEW_SUCCESS : LEGACY_FRAME_VIEW_ERROR;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_misc_friend.html");
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_misc_friend_success.html");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_misc_friend_error.html");
		$render->setAttribute('xoopsMailer', $this->mMailer);
	}
}

?>
