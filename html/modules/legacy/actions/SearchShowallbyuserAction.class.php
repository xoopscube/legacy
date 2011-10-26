<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchShowallbyuserAction.class.php,v 1.4 2008/09/25 15:12:06 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/actions/SearchShowallAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/SearchShowallbyuserForm.class.php";

class Legacy_SearchShowallbyuserAction extends Legacy_SearchShowallAction
{
	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_SearchShowallbyuserForm(0);
		$this->mActionForm->prepare();
	}
	
	function _getTemplateName()
	{
		return "legacy_search_showallbyuser.html";
	}
	
	function _getSelectedMids()
	{
		$ret = array();
		$ret[] = $this->mActionForm->get('mid');
		
		return $ret;
	}
	
	function _doSearch(&$client, &$xoopsUser, &$params)
	{
		return $client->call('searchItemsOfUser', $params);
	}

	function _getMaxHit()
	{
		return LEGACY_SEARCH_SHOWALL_MAXHIT;
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		parent::executeViewIndex($controller, $xoopsUser, $render);
		
		$handler =& xoops_gethandler('user');
		$user =& $handler->get($this->mActionForm->get('uid'));
		
		$render->setAttribute('targetUser', $user);
		
		$prevStart = $this->mActionForm->get('start') - LEGACY_SEARCH_SHOWALL_MAXHIT;
		if ($prevStart < 0) {
			$prevStart = 0;
		}

		$render->setAttribute('prevStart', $prevStart);
		$render->setAttribute('nextStart', $this->mActionForm->get('start') + LEGACY_SEARCH_SHOWALL_MAXHIT);
	}
}

?>
