<?php
/**
 *
 * @package Legacy
 * @version $Id: NotifyListAction.class.php,v 1.6 2008/09/25 15:12:07 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/include/notification_functions.php";

require_once XOOPS_MODULE_PATH . "/legacy/forms/NotifyDeleteForm.class.php";

/***
 * @internal
 * List up notifications. This action is like notifications.php (when $op is
 * 'list').
 */
class Legacy_NotifyListAction extends Legacy_Action
{
	var $mModules = array();
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$root =& $controller->mRoot;
		$root->mLanguageManager->loadPageTypeMessageCatalog('notification');
		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
		
		$this->mActionForm =new Legacy_NotifyDeleteForm();
		$this->mActionForm->prepare();
	}
	
	function hasPermission(&$controller, &$xoopsUser)
	{
		return is_object($xoopsUser);
	}

	function getDefaultView(&$contoller, &$xoopsUser)
	{
		$criteria =new Criteria('not_uid', $xoopsUser->get('uid'));
		$criteria->setSort('not_modid, not_category, not_itemid');
		
		$handler =& xoops_gethandler('notification');
		$notificationArr =& $handler->getObjects($criteria);

		$moduleHandler =& xoops_gethandler('module');
		
		$prev_modid = -1;
		$prev_category = -1;
		$prev_item = -1;
		foreach ($notificationArr as $notify) {
			$t_modid = $notify->get('not_modid');
			
			$module =& $moduleHandler->get($t_modid);
			if (!is_object($module)) {
				continue;
			}
			
			if ($t_modid != $prev_modid) {
				$prev_modid = $t_modid;
				$prev_category = -1;
				$prev_item = -1;
				
				$this->mModules[$t_modid] = array (
					'id' => $t_modid,
					'name' => $module->getShow('name'),
					'categories' => array()
				);


				//
				// [ToDo] (Original)
				// note, we could auto-generate the url from the id
				// and category info... (except when category has multiple
				// subscription scripts defined...)
				// OR, add one more option to xoops_version 'view_from'
				// which tells us where to redirect... BUT, e.g. forums, it
				// still wouldn't give us all the required info... e.g. the
				// topic ID doesn't give us the ID of the forum which is
				// a required argument...
				
				//
				// Get the lookup function, if exists
				//
				$notifyConfig = $module->getInfo('notification');
				$lookupFunc = '';
				if (!empty($notifyConfig['lookup_file'])) {
					$t_filepath = XOOPS_ROOT_PATH . '/modules/' . $module->get('dirname') . '/' . $notifyConfig['lookup_file'];
					if (file_exists($t_filepath)) {
						require_once $t_filepath;
						if (!empty($notifyConfig['lookup_func']) && function_exists($notifyConfig['lookup_func'])) {
							$lookupFunc = $notifyConfig['lookup_func'];
						}
					}
				}
			}
			
			$t_category = $notify->get('not_category');
			if ($t_category != $prev_category) {
				$prev_category = $t_category;
				$prev_item = -1;
				$categoryInfo =& notificationCategoryInfo($t_category, $t_modid);
			}
			
			$t_item = $notify->get('not_itemid');
			if ($t_item != $prev_item) {
				$prev_item = $t_item;
				if (!empty($lookupFunc)) {
					$itemInfo = $lookupFunc($t_category, $t_item);
				}
				else {
					$itemInfo = array ('name' => '[' . _NOT_NAMENOTAVAILABLE . ']', 'url' => '');
				}
				$this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item] = array(
					'id' => $t_item,
					'name' => $itemInfo['name'],
					'url' => $itemInfo['url'],
					'notifications' => array()
				);
			}
			
			$eventInfo =& notificationEventInfo($t_category, $notify->get('not_event'), $notify->get('not_modid'));
			$this->mModules[$t_modid]['categories'][$t_category]['items'][$t_item]['notifications'][] = array(
				'id' => $notify->get('not_id'),
				'module_id' => $notify->get('not_modid'),
				'category' => $notify->get('not_category'),
				'category_title' => $categoryInfo['title'],
				'item_id' => $notify->get('not_itemid'),
				'event' => $notify->get('not_event'),
				'event_title' => $eventInfo['title'],
				'user_id' => $notify->get('not_uid')
			);
		}
		
		return LEGACY_FRAME_VIEW_INDEX;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_notification_list.html");
		
		$render->setAttribute('modules', $this->mModules);
		$render->setAttribute('currentUser', $xoopsUser);
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=NotifyList");
	}
}

?>
