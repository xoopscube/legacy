<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchResultsAction.class.php,v 1.3 2008/09/25 15:12:06 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/forms/SearchResultsForm.class.php";

define('LEGACY_SEARCH_RESULT_MAXHIT', 5);
define('LEGACY_SEARCH_SHOWALL_MAXHIT', 20);

class Legacy_SearchResultsAction extends Legacy_Action
{
	var $mActionForm = null;
	var $mSearchResults = array();
	var $mModules = array();
	
	var $mConfig = array();
	
	function prepare(&$controller, &$xoopsUser)
	{
		$root =& $controller->mRoot;
		$root->mLanguageManager->loadPageTypeMessageCatalog('search');
		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
		
		$handler =& xoops_gethandler('config');
		$this->mConfig =& $handler->getConfigsByCat(XOOPS_CONF_SEARCH);
		
		$this->_setupActionForm();
	}
	
	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_SearchResultsForm($this->mConfig['keyword_min']);
		$this->mActionForm->prepare();
	}
	
	function hasPermission(&$controller, &$xoopsUser)
	{
		if ($this->mConfig['enable_search'] != 1) {
			$controller->executeRedirect(XOOPS_URL . '/', 3, _MD_LEGACY_ERROR_SEARCH_NOT_ENABLED);
			return false;
		}
		return true;
	}
	
	function _getMaxHit()
	{
		return LEGACY_SEARCH_RESULT_MAXHIT;
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$root =& $controller->mRoot;
		$service =& $root->mServiceManager->getService("LegacySearch");
		
		if (is_object($service)) {
			$client =& $root->mServiceManager->createClient($service);
			$this->mModules = $client->call('getActiveModules', array());
		}
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return LEGACY_FRAME_VIEW_INDEX;
		}

		//
		// TODO ErrorHandling
		//
		if (is_object($service)) {
			$this->mActionForm->update($params);
			
			$handler =& xoops_gethandler('module');
			foreach ($this->_getSelectedMids() as $mid) {
				$t_module =& $handler->get($mid);
				if (is_object($t_module)) {
					$module = array();
					
					$module['mid'] = $mid;
					$module['name'] = $t_module->get('name');
					
					$params['mid'] = $mid;
					$module['results'] = $this->_doSearch($client, $xoopsUser, $params);
					
					if (count($module['results']) > 0) {
						$module['has_more'] = (count($module['results']) >= $this->_getMaxHit()) ? true : false;
						$this->mSearchResults[] = $module;
					}
				}
			}
		}
		else {
			return LEGACY_FRAME_VIEW_ERROR;
		}

		return LEGACY_FRAME_VIEW_INDEX;
	}
	
	function _doSearch(&$client, &$xoopsUser, &$params)
	{
		$root =& XCube_Root::getSingleton();
		$timezone = $root->mContext->getXoopsConfig('server_TZ') * 3600;
		
		$results = $client->call('searchItems', $params);
		
		return $results;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName($this->_getTemplateName());
	
		$render->setAttribute('actionForm', $this->mActionForm);
			
		$render->setAttribute('searchResults', $this->mSearchResults);
		$render->setAttribute('moduleArr', $this->mModules);

		//
		// If the request include $mids, setAttribute it. If it don't include, 
		// setAttribute $mid or $this->mModules.
		//		
		$render->setAttribute('selectedMidArr', $this->_getSelectedMids());
		$render->setAttribute('searchRuleMessage', @sprintf(_SR_KEYTOOSHORT, $this->mConfig['keyword_min']));
	}
	
	function _getTemplateName()
	{
		return "legacy_search_results.html";
	}
	
	function _getSelectedMids()
	{
		$ret = $this->mActionForm->get('mids');
		if (!count($ret)) {
			foreach ($this->mModules as $module) {
				$ret[] = $module['mid'];
			}
		}
		
		return $ret;
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL . '/');
	}
}

?>
