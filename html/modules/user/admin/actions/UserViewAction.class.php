<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractViewAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/UserRecountForm.class.php";

class User_UserViewAction extends User_AbstractViewAction
{
	var $mActionForm = null;

	/**
	 * @var XCube_Delegate
	 */
	var $mGetUserPosts = null;
	
	function User_UserViewAction()
	{
		parent::User_AbstractViewAction();
		$this->mGetUserPosts =& new XCube_Delegate();
		$this->mGetUserPosts->register('User_UserViewAction.GetUserPosts');
	}
	
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		parent::prepare($controller, $xoopsUser, $moduleConfig);
		$this->mActionForm =& new User_RecountForm();
		$this->mActionForm->prepare();
	}
	
	function _getId()
	{
		return xoops_getrequest('uid');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('users');
		return $handler;
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		if (is_object($this->mObject)) {
			$this->mActionForm->load($this->mObject);
		}
		
		$ret = parent::getDefaultView($controller, $xoopsUser);
		
		//
		// Because this class implemented 'execute()', convet the status here.
		//
		if ($ret == USER_FRAME_VIEW_SUCCESS) {
			return USER_FRAME_VIEW_INDEX;
		}
		else {
			return $ret;
		}
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return USER_FRAME_VIEW_ERROR;
		}

		$this->mActionForm->load($this->mObject);
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		//
		// Do 'recount'
		//
		$posts = 0;
		$this->mGetUserPosts->call(new XCube_Ref($posts), $this->mObject);
		
		$handler =& xoops_getmodulehandler('users');
		return $handler->insert($this->mObject) ? USER_FRAME_VIEW_SUCCESS
		                                        : USER_FRAME_VIEW_ERROR;
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_view.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		
		$render->setAttribute('rank', $this->mObject->getRank());
		
		$handler =& xoops_gethandler('timezone');
		$timezone =& $handler->get($this->mObject->get('timezone_offset'));
		$render->setAttribute('timezone', $timezone);
		
		//
		// TODO dirty code... :(
		//
		$umodeOptions = array("nest" => _NESTED, "flat" => _FLAT, "thread" => _THREADED);
		$render->setAttribute('umode', $umodeOptions[$this->mObject->get('umode')]);

		$uorderOptions = array(0 => _OLDESTFIRST, 1 => _NEWESTFIRST);
		$render->setAttribute('uorder', $uorderOptions[$this->mObject->get('uorder')]);
		
		//
		// Notifications. (TODO Also dirty...)
		//
		$controller->mRoot->mLanguageManager->loadPageTypeMessageCatalog('notification');
		require_once XOOPS_ROOT_PATH . "/include/notification_constants.php";

		$methodOptions = array(XOOPS_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
		                         XOOPS_NOTIFICATION_METHOD_PM => _NOT_METHOD_PM,
		                         XOOPS_NOTIFICATION_METHOD_EMAIL => _NOT_METHOD_EMAIL
		                   );
		$render->setAttribute('notify_method', $methodOptions[$this->mObject->get('notify_method')]);
		
		$modeOptions = array(XOOPS_NOTIFICATION_MODE_SENDALWAYS => _NOT_MODE_SENDALWAYS,
		                       XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
		                       XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT => _NOT_MODE_SENDONCEPERLOGIN
		                 );
		$render->setAttribute('notify_mode', $modeOptions[$this->mObject->get('notify_mode')]);
		
		//XCL2.2 TEST:Profile_Service
		$root =& $controller->mRoot;
		$service = $root->mServiceManager->getService("Profile_Service");
		$client = $root->mServiceManager->createClient($service);
		if (is_object($client)) {
			$definitions = $client->call('getDefinitions', array());
			$render->setAttribute('definitions', $definitions);
		
			$data = $client->call('getProfile', array('uid'=>$this->mObject->get('uid')));
			$render->setAttribute('data', $data);
		}
		//XCL2.2 TEST END:Profile_Service
		
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=UserView&uid=" . $this->mObject->get('uid'), 1, _AD_USER_MESSAGE_RECOUNT_SUCCESS);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=UserList", 1, _AD_USER_ERROR_CONTENT_IS_NOT_FOUND);
	}
}

?>
