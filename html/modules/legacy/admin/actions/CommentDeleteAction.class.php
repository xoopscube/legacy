<?php
/**
 *
 * @package Legacy
 * @version $Id: CommentDeleteAction.class.php,v 1.4 2008/09/25 15:11:33 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/CommentAdminDeleteForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/actions/CommentEditAction.class.php";

class Legacy_CommentDeleteAction extends Legacy_AbstractDeleteAction
{
	function _getId()
	{
		return isset($_REQUEST['com_id']) ? xoops_getrequest('com_id') : 0;
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('comment');
		$handler->mDeleteSuccess->add(array(&$this, "doDelete"));
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_CommentAdminDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		//
		// Lazy load
		//
		$this->mObject->loadUser();
		$this->mObject->loadModule();
		$this->mObject->loadStatus();

		//
		// Load children and load their module and commentater.
		//
		$handler =& xoops_getmodulehandler('comment');
		$criteria =new Criteria('com_pid', $this->mObject->get('com_id'));
		$children =& $handler->getObjects($criteria);

		if (count($children) > 0) {
			foreach (array_keys($children) as $key) {
				$children[$key]->loadModule();
				$children[$key]->loadUser();
			}
		}

		$render->setTemplateName("comment_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('children', $children);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=CommentList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=CommentList", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=CommentList");
	}

	function doDelete($comment)
	{
		//
		// Adjust user's post count.
		//
		if ($comment->get('com_status') != 1 && $comment->get('com_uid') > 0) {
			$handler =& xoops_gethandler('member');

			//
			// TODO We should adjust the following lines and handler's design.
			// We think we should not use getUser() and updateUserByField in XCube 2.1.
			//
			$user =& $handler->getUser($comment->get('com_uid'));
			if (is_object($user)) {
				$count = $user->get('posts');
			
				if($count > 0) {
					$handler->updateUserByField($user, 'posts', $count - 1);
				}
			}
		}
		
		//
		// callback
		//
		$comment_config = Legacy_CommentEditAction::loadCallbackFile($comment);
		
		if ($comment_config == false) {
			return;
		}
		
		$function = $comment_config['callback']['update'];
		
		if (function_exists($function)) {
			$criteria = new CriteriaCompo(new Criteria('com_modid', $comment->get('com_modid')));
			$criteria->add(new Criteria('com_itemid', $comment->get('com_itemid')));
			$criteria->add(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
			
			$handler =& xoops_gethandler('comment');
			$commentCount = $handler->getCount($criteria);
			
			call_user_func($function, $comment->get('com_id'), $commentCount);
		}
	}
}

?>
