<?php
/**
 *
 * @package Legacy
 * @version $Id: ActSearchAction.class.php,v 1.4 2008/09/25 15:11:53 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_LEGACY_PATH."/admin/forms/ActionSearchForm.class.php";

class Legacy_ActionSearchArgs
{
	var $mKeywords;
	var $mRecords;

	function Legacy_ActionSearchArgs($words)
	{
		$this->setKeywords($words);
	}
	
	function setKeywords($words)
	{
		foreach (explode(" ", $words) as $word) {
			if( strlen($word) > 0) {
				$this->mKeywords[] = $word;
			}
		}
	}

	function getKeywords()
	{
		return $this->mKeywords;
	}

	function addRecord($moduleName, $url, $title, $desc = null)
	{
		$this->mRecords[] =new Legacy_ActionSearchRecord($moduleName, $url, $title, $desc);
	}
	
	function &getRecords()
	{
		return $this->mRecords;
	}
	
	/**
	 * @return bool
	 */
	function hasRecord()
	{
		return count($this->mRecords) > 0;
	}
}

/**
 * An item on one search record. This is a class as a structure.
 * 
 * @todo we may change it to Array.
 */
class Legacy_ActionSearchRecord
{
	var $mModuleName;
	var $mActionUrl;
	var $mTitle;
	var $mDescription;

	function Legacy_ActionSearchRecord($moduleName, $url, $title, $desc=null)
	{
		$this->mModuleName = $moduleName;
		$this->mActionUrl = $url;
		$this->mTitle = $title;
		$this->mDescription = $desc;
	}
}

/***
 * @internal
 *  Execute action searching. Now, it returns all of modules' results whether
 * the current user can access to.
 * 
 * @todo We should return the result by the current user's permission.
 */
class Legacy_ActSearchAction extends Legacy_Action
{
	var $mModules = array();
	var $mModuleRecords = null;
	var $mRecords = null;
	var $mActionForm = null;
	
	var $mSearchAction = null;
	
	function Legacy_ActSearchAction($flag)
	{
		parent::Legacy_Action($flag);
		
		$this->mSearchAction =new XCube_Delegate();
		$this->mSearchAction->add(array(&$this, 'defaultSearch'));
		$this->mSearchAction->register('Legacy_ActSearchAction.SearchAction');
	}

	function prepare(&$controller, &$xoopsUser)
	{
		parent::prepare($controller, $xoopsUser);

		$db=&$controller->getDB();

		$mod = $db->prefix("modules");
		$perm = $db->prefix("group_permission");
		$groups = implode(",", $xoopsUser->getGroups());
						 
		$sql = "SELECT DISTINCT ${mod}.mid FROM ${mod},${perm} " .
		       "WHERE ${mod}.isactive=1 AND ${mod}.mid=${perm}.gperm_itemid AND ${perm}.gperm_name='module_admin' AND ${perm}.gperm_groupid IN (${groups}) " .
		       "ORDER BY ${mod}.weight, ${mod}.mid";

		$result=$db->query($sql);
		
		$handler =& xoops_gethandler('module');
		while ($row = $db->fetchArray($result)) {
			$module =& $handler->get($row['mid']);
			$adapter =new Legacy_ModuleAdapter($module); // FIXMED
			
			$this->mModules[] =& $adapter;
			
			unset($module);
			unset($adapter);
		}
	}

	function hasPermission(&$controller, &$xoopsUser)
	{
		$permHandler =& xoops_gethandler('groupperm');
		return $permHandler->checkRight('module_admin', -1, $xoopsUser->getGroups());
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->_processActionForm();

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if($this->mActionForm->hasError()) {
			return LEGACY_FRAME_VIEW_INPUT;
		}

		$searchArgs =new Legacy_ActionSearchArgs($this->mActionForm->get('keywords'));
		$this->mSearchAction->call(new XCube_Ref($searchArgs));

		if ($searchArgs->hasRecord()) {
			$this->mRecords =& $searchArgs->getRecords();
			return LEGACY_FRAME_VIEW_SUCCESS;
		}
		else {
			return LEGACY_FRAME_VIEW_ERROR;
		}
	}
	
	function defaultSearch(&$searchArgs)
	{
		foreach (array_keys($this->mModules) as $key) {
			$this->mModules[$key]->doActionSearch($searchArgs);
		}
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
	
	function _processActionForm()
	{
		$this->mActionForm =new Legacy_ActionSearchForm();
		$this->mActionForm->prepare();
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_admin_actionsearch_success.html");
		$render->setAttribute("records", $this->mRecords);
		$render->setAttribute("actionForm", $this->mActionForm);
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_admin_actionsearch_input.html");
		$render->setAttribute("actionForm", $this->mActionForm);
	}
	
	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_admin_actionsearch_error.html");
		$render->setAttribute("actionForm", $this->mActionForm);
	}
}

?>
