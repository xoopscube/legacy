<?php
/**
 * @package pm
 * @version $Id: ReadAction.class.php,v 1.3 2008/06/22 11:27:45 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * [Notice]
 * This class has been checked in Alpha4. But, this class doesn't go along the
 * latest cubson style. And some problems (using core handler, naming rule and
 * etc) are there. Pm module is one of the most old code in Legacy.
 */
class Pm_ReadAction extends Pm_AbstractAction
{
	/***
	 * A instance of the current private-message.
	 * 
	 * @var XoopsPrivmessage
	 */
	var $mPrivMessage = null;
	
	/***
	 * A instance of the user that has sent the current private-message.
	 * 
	 * @var XoopsUser
	 */
	var $mSendUser = null;

	/***
	 * A instance of previous private-message form the current private-message.
	 * 
	 * @var XoopsPrivmessage
	 */
	var $mPreviousMessage = null;
	
	/***
	 * A instance of next private-message form the current private-message.
	 * 
	 * @var XoopsPrivmessage
	 */
	var $mNextMessage = null;
	
	function getDefaultView(&$controller,&$xoopsUser)
	{
		//
		// Request Check without ActionForm
		//
		$msg_id = intval(xoops_getrequest('msg_id'));

		//
		// Load private message object
		//
		$pmHandler =& xoops_gethandler('privmessage');
		$this->mPrivMessage =& $pmHandler->get($msg_id);

		if (!is_object($this->mPrivMessage)) {
			return PM_FRAME_VIEW_ERROR;
		}

		//
		// Check read permission and the sender.
		//
		if ($this->mPrivMessage->getVar('to_userid') != $xoopsUser->getVar('uid')) {
			return PM_FRAME_VIEW_ERROR;
		}

		$this->mSendUser =& $this->mPrivMessage->getFromUser();

		//
		// Get previous and next message
		//
		$criteria =& new CriteriaCompo();
		$criteria->add(new Criteria('msg_id', $this->mPrivMessage->getVar('msg_id'), "<"));
		$criteria->add(new Criteria('to_userid', $xoopsUser->get('uid')));
		$criteria->setLimit(1);
		$criteria->setSort('msg_time');
		$criteria->setOrder('DESC');
		$t_objArr =& $pmHandler->getObjects($criteria);
		if (count($t_objArr) > 0 && is_object($t_objArr[0])) {
			$this->mPreviousMessage =& $t_objArr[0];
		}
		unset($t_objArr);
		unset($criteria);

		$criteria =& new CriteriaCompo();
		$criteria->add(new Criteria('msg_id', $this->mPrivMessage->getVar('msg_id'), ">"));
		$criteria->add(new Criteria('to_userid', $xoopsUser->get('uid')));
		$criteria->setLimit(1);
		$criteria->setSort('msg_time');
		$t_objArr =& $pmHandler->getObjects($criteria);
		if (count($t_objArr) > 0 && is_object($t_objArr[0])) {
			$this->mNextMessage =& $t_objArr[0];
		}

		//
		// If this message is unread, Raise read flag.
		//
		if (!$this->mPrivMessage->isRead()) {
			$pmHandler->setRead($this->mPrivMessage);
		}

		return PM_FRAME_VIEW_INDEX;
	}
	
	/***
	 * When a user click 'delete' button, this member function is called by
	 * POST request. Forward delete one action.
	 */
	function execute(&$controller, &$xoopsUser)
	{
		$controller->executeForward(XOOPS_URL . "/readpmsg.php?action=DeleteOne&msg_id=" . xoops_getrequest('msg_id'));
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/viewpmsg.php", 1, _MD_PM_ERROR_ACCESS);
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("readpmsg.html");
		$render->setAttribute("thisUser", $xoopsUser);
		if (is_object($this->mSendUser) && $this->mSendUser->isActive()) {
			$render->setAttribute("sendUser", $this->mSendUser);
		}
		$render->setAttribute("privMessage", $this->mPrivMessage);
		$render->setAttribute("previousMessage", $this->mPreviousMessage);
		$render->setAttribute("nextMessage", $this->mNextMessage);
		$render->setAttribute("anonymous", $controller->mRoot->mContext->getXoopsConfig('anonymous'));
	}
}

?>