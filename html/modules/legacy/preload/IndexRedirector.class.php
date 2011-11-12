<?php
/**
 *
 * @package Legacy
 * @version $Id: IndexRedirector.class.php,v 1.3 2008/09/25 15:12:44 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_IndexRedirector extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		$this->mController->mRoot->mDelegateManager->add("Legacypage.Top.Access", array(&$this, "redirect"));
	}

	function redirect()
	{
		$startPage = $this->mRoot->mContext->getXoopsConfig('startpage');
		if ($startPage != null && $startPage != "--") {
			$handler =& xoops_gethandler('module');
			$module =& $handler->get($startPage);
			if (is_object($module) && $module->get('isactive')) {
				$this->mController->executeForward(XOOPS_URL . '/modules/' . $module->getShow('dirname') . '/');
			}
		}
	}
}

?>
