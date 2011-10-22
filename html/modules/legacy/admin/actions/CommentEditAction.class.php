<?php
/**
 *
 * @package Legacy
 * @version $Id: CommentEditAction.class.php,v 1.7 2008/09/25 15:11:52 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/CommentAdminEditForm.class.php";
require_once XOOPS_ROOT_PATH . "/include/comment_constants.php";

class Legacy_CommentEditAction extends Legacy_AbstractEditAction
{
	/**
	 * Override. At first, call _setupObject().
	 */
	function prepare(&$controller, &$xoopsUser)
	{
		$this->_setupObject();
		$this->_setupActionForm();
	}
	
	function _getId()
	{
		return isset($_REQUEST['com_id']) ? intval(xoops_getrequest('com_id')) : 0;
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('comment');
		return $handler;
	}
	
	function isEnableCreate()
	{
		return false;
	}

	/**
	 * Choose appropriate ActionForm by the value of com_status.
	 */
	function _setupActionForm()
	{
		if ($this->mObject->get('com_status') == XOOPS_COMMENT_PENDING) {
			$this->mActionForm =new Legacy_PendingCommentAdminEditForm();
			$this->mObjectHandler->mUpdateSuccess->add(array(&$this, "doApprove"));
			$this->mObjectHandler->mUpdateSuccess->add(array(&$this, "doUpdate"));
		}
		else {
			$this->mActionForm =new Legacy_ApprovalCommentAdminEditForm();
			$this->mObjectHandler->mUpdateSuccess->add(array(&$this, "doUpdate"));
		}
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$this->mObject->loadUser();
		$this->mObject->loadModule();
		$this->mObject->loadStatus();
		
		$render->setTemplateName("comment_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		
		$subjectHandler =& xoops_gethandler('subjecticon');
		$subjectIconArr =& $subjectHandler->getObjects();
		
		$render->setAttribute('subjectIconArr', $subjectIconArr);

		$statusHandler =& xoops_getmodulehandler('commentstatus');
		if ($this->mObject->get('com_status') == XOOPS_COMMENT_PENDING) {
			$statusArr =& $statusHandler->getObjects();
		}
		else {
			$statusArr = array();
			$statusArr[0] =& $statusHandler->get(XOOPS_COMMENT_ACTIVE);
			$statusArr[1] =& $statusHandler->get(XOOPS_COMMENT_HIDDEN);
		}
		
		$render->setAttribute('statusArr', $statusArr);
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

	/**
	 * @static
	 * @return Return array as the informations of comments. If $comment has fatal status, return false.
	 */
	function loadCallbackFile(&$comment)
	{
		$handler =& xoops_gethandler('module');
		$module =& $handler->get($comment->get('com_modid'));
		
		if (!is_object($module)) {
			return false;
		}
		
		$comment_config = $module->getInfo('comments');
		
		if (!isset($comment_config['callbackFile'])) {
			return false;
		}
			
		//
		// Load call-back file
		//
		$file = XOOPS_MODULE_PATH . "/" . $module->get('dirname') . "/" . $comment_config['callbackFile'];
		if (!is_file($file)) {
			return false;
		}
		
		require_once $file;
		
		return $comment_config;
	}
	
	function doApprove($comment)
	{
		$comment_config = Legacy_CommentEditAction::loadCallbackFile($comment);

		if ($comment_config == false) {
			return;
		}
		
		$function = $comment_config['callback']['approve'];
		
		if (function_exists($function)) {
			call_user_func($function, $comment);
		}
		
		$handler =& xoops_gethandler('member');

		//
		// TODO We should adjust the following lines and handler's design.
		// We think we should not use getUser() and updateUserByField in XCube 2.1.
		//
		$user =& $handler->getUser($comment->get('com_uid'));
		if (is_object($user)) {
			$handler->updateUserByField($user, 'posts', $user->get('posts') + 1);
		}
	}
	
	function doUpdate($comment)
	{
		//
		// call back
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
			
			call_user_func_array($function, array($comment->get('com_itemid'), $commentCount, $comment->get('com_id')));
		}
	}
}

?>
