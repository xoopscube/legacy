<?php
/**
 *
 * @package Legacy
 * @version $Id: SiteClose.class.php,v 1.5 2008/09/25 15:12:38 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * The action filter for the site close procedure.
 */
class Legacy_SiteClose extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		if ($this->mRoot->mContext->getXoopsConfig('closesite') == 1) {
			$this->mController->mSetupUser->add("Legacy_SiteClose::callbackSetupUser", XCUBE_DELEGATE_PRIORITY_FINAL);
			$this->mRoot->mDelegateManager->add("Site.CheckLogin.Success", array(&$this, "callbackCheckLoginSuccess"));
		}
	}

	/**
	 * Checks whether the site is closed now, and whether all of must modules
	 * have been installed. This function is called through delegates.
	 * @var XoopsUser &$xoopsUser
	 * @see preBlockFilter()
	 */
	function callbackSetupUser(&$principal, &$controller, &$context)
	{
		$accessAllowFlag = false;
		$xoopsConfig = $controller->mRoot->mContext->getXoopsConfig();
		
		if (!empty($_POST['xoops_login'])) {
			$controller->checkLogin();
			return;
		} else if (@$_GET['op']=='logout') { // GIJ
			$controller->logout();
			return;
		} elseif (is_object($context->mXoopsUser)) {
			foreach ($context->mXoopsUser->getGroups() as $group) {
				if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
					$accessAllowFlag = true;
					break;
				}
			}
		}

		if (!$accessAllowFlag) {
			require_once XOOPS_ROOT_PATH . '/class/template.php';
			$xoopsTpl =new XoopsTpl();
			$xoopsTpl->assign(array('xoops_sitename' => htmlspecialchars($xoopsConfig['sitename']),
									   'xoops_isuser' => is_object( $context->mXoopsUser ),//GIJ
									   'xoops_themecss' => xoops_getcss(),
									   'xoops_imageurl' => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
									   'lang_login' => _LOGIN,
									   'lang_username' => _USERNAME,
									   'lang_password' => _PASSWORD,
									   'lang_siteclosemsg' => $xoopsConfig['closesite_text']
									   ));
									   
			$xoopsTpl->compile_check = true;
			
			// @todo filebase template with absolute file path
			$xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/legacy/templates/legacy_site_closed.html');
			exit();
		}
	}
	
	/**
	 * When the user logs in successfully, checks whether the user belongs to
	 * the special group which is allowed to login. This function is called
	 * through delegates.
	 * @var XoopsUser &$xoopsUser
	 * @see preBlockFilter
	 */
	function callbackCheckLoginSuccess(&$xoopsUser)
	{
		//
		// This check is not needed. :)
		//
		if (!is_object($xoopsUser)) {
			return;
		}

		// Site close
		if ($this->mRoot->mContext->getXoopsConfig('closesite')) {
			$accessAllowed = false;

			foreach ($xoopsUser->getGroups() as $group) {
				if (in_array($group, $this->mRoot->mContext->getXoopsConfig('closesite_okgrp')) || ($group == XOOPS_GROUP_ADMIN)) {
					$accessAllowed = true;
					break;
				}
			}

			if (!$accessAllowed) {
				$this->mController->executeRedirect(XOOPS_URL . '/', 1, _NOPERM);
			}
		}
	}
}

?>
