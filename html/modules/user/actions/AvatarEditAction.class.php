<?php
/**
 * @package user
 * @version $Id: AvatarEditAction.class.php,v 1.3 2007/08/23 13:04:21 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_PageNavigator.class.php";

require_once XOOPS_MODULE_PATH . "/user/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/forms/AvatarEditForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/forms/AvatarSelectForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/forms/AvatarFilterForm.class.php";

/***
 * @internal
 * This action handles the uploaded avatar image file.
 * 
 * Users who are allowed to upload, can upload custom avatars and select system
 * avatars. So this action has to implement both of uploading and selecting.
 * In the case of GET request, this action shows two forms to changing a avatar
 * for specified user. One of form shows upload-form. Anther form shows the list
 * of system avatars and the page navigator of the list.
 * 
 * @see User_AvatarEditForm
 * @see User_AvatarSelectForm
 */
class User_AvatarEditAction extends User_AbstractEditAction
{
	/***
	 * @var int
	 */
	var $mAvatarWidth = 0;
	
	/***
	 * @var int
	 */
	var $mAvatarHeight = 0;
	
	/***
	 * @var int
	 */
	var $mAvatarMaxfilesize = 0;
	
	/***
	 * @var int
	 */
	var $_mMinPost = 0;
	
	/***
	 * @var bool
	 */
	var $_mAllowUpload = false;
	
	/***
	 * @var User_AvatarFilterForm
	 */
	var $mFilter;

	/***
	 * Preset avatar object collection.
	 */	
	var $mSystemAvatars = array();
	
	/***
	 * Other action form for AvatarSelect.
	 * @var User_AvatarSelectForm
	 */
	var $mAvatarSelectForm = null;
	
	/***
	 * Fetch conditions from $moduleConfig and set these to member properties.
	 * And, by the member property mConfig of the base class, any member
	 * functions of this class can access $moduleConfig.
	 * 
	 * @todo The limit may be not completed, yet.
	 */
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mAvatarWidth = $moduleConfig['avatar_width'];
		$this->mAvatarHeight = $moduleConfig['avatar_height'];
		$this->mAvatarMaxfilesize = $moduleConfig['avatar_maxsize'];
		
		$this->_mMinPost = $moduleConfig['avatar_minposts'];
		$this->_mAllowUpload = $moduleConfig['avatar_allow_upload'];

		parent::prepare($controller, $xoopsUser, $moduleConfig);
	}

	function _getId()
	{
		return isset($_REQUEST['uid']) ? intval(xoops_getrequest('uid')) : 0;
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('users', 'user');
		return $handler;
	}

	/***
	 * This class uses AvatarUploadForm class. It requests three condition
	 * which are width limit, height limit and filesize limit.
	 * 
	 * @todo We may have to hand three parameters to constructor.
	 */
	function _setupActionForm()
	{
		$this->mActionForm =& new User_AvatarEditForm();
		$this->mActionForm->prepare($this->mAvatarWidth, $this->mAvatarHeight, $this->mAvatarMaxfilesize);
	}
	
	function isEnableCreate()
	{
		return false;
	}

	/***
	 *  Return true. This action should not be used by a guest user.
	 */
	function isSecure()
	{
		return true;
	}
	
	/***
	 * Check whether a current user can access this action.
	 * 1) A specified user has to exist.
	 * 2) A current user has to equal the specified user, or a current user has
	 *    to be a administrator.
	 */
	function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
	{
		if (!is_object($this->mObject)) {
			return false;
		}

		if ($controller->mRoot->mContext->mUser->isInRole('Module.user.Admin')) {
			return true;
		}
		elseif ($this->mObject->get('uid') == $xoopsUser->get('uid')) {
			// Check count of the system avatar.
			$handler =& xoops_getmodulehandler('avatar', 'user');
			$criteria =& new Criteria('avatar_type', 'S');
			if ($handler->getCount($criteria) > 0)
				return true;
			
			return ($this->mObject->get('posts') >= $this->_mMinPost);
		}
		
		return false;
	}

	/***
	 * This override method looks like the same method of ListAction, and tries
	 * to get system avatars. After, it will call base class.
	 */
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$navi =& new XCube_PageNavigator(XOOPS_URL . "/edituser.php?op=avatarform&amp;uid=" . $xoopsUser->get('uid'), XCUBE_PAGENAVI_START);
		$handler =& xoops_getmodulehandler('avatar', 'user');
		
		$this->mSystemAvatars[] =& $handler->createNoavatar();
		
		$this->mFilter =& new User_AvatarFilterForm($navi, $handler);
		$this->mFilter->fetch();
		
		$criteria = $this->mFilter->getCriteria();
		$t_avatarArr =& $handler->getObjects($criteria);
		foreach (array_keys($t_avatarArr) as $key) {
			$this->mSystemAvatars[] =& $t_avatarArr[$key];
		}
		
		$this->mAvatarSelectForm =& new User_AvatarSelectForm();
		$this->mAvatarSelectForm->prepare();
		
		$this->mAvatarSelectForm->load($this->mObject);
		
		return parent::getDefaultView($controller, $xoopsUser);
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		if ($this->_mMinPost > 0 && $this->mObject->get('posts') < $this->_mMinPost) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		$this->mActionForm->load($this->mObject);
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
	
		if($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
	
		$this->mActionForm->update($this->mObject);
		
		return $this->_doExecute($this->mObject) ? USER_FRAME_VIEW_SUCCESS
		                                         : USER_FRAME_VIEW_ERROR;
	}

	/***
	 * 1) Save avatar file which has been uploaded.
	 * 2) If old avatar file exists, remove it.
	 * 3) Insert a data to DB with calling base class method.
	 */
	function _doExecute()
	{
		if ($this->mActionForm->mFormFile != null) {
			if (!$this->mActionForm->mFormFile->saveAs(XOOPS_UPLOAD_PATH)) {
				return false;
			}
		}
		
		if ($this->mActionForm->mOldAvatarFilename != null && $this->mActionForm->mOldAvatarFilename != "blank.gif") {
			$avatarHandler =& xoops_getmodulehandler('avatar', 'user');
			$criteria =& new Criteria('avatar_file', $this->mActionForm->mOldAvatarFilename);
			$avatarArr =& $avatarHandler->getObjects($criteria);
			if (count($avatarArr) > 0 && is_object($avatarArr[0]) && $avatarArr[0]->get('avatar_type') == 'C') {
				$avatarHandler->delete($avatarArr[0]);
			}
		}
		
		if (parent::_doExecute()) {
			$avatar =& $this->mActionForm->createAvatar();
			if ($avatar != null) {
				$avatar->set('avatar_name', $this->mObject->get('uname'));
				$avatarHandler =& xoops_getmodulehandler('avatar', 'user');
				$avatarHandler->insert($avatar);
				
				$linkHandler =& xoops_getmodulehandler('avatar_user_link', 'user');
				$linkHandler->deleteAllByUser($this->mObject);
				
				$link =& $linkHandler->create();
				$link->set('user_id', $this->mObject->get('uid'));
				$link->set('avatar_id', $avatar->get('avatar_id'));
				
				$linkHandler->insert($link);
			}
			
			return true;
		}
		else {
			return false;
		}
	}

	function executeViewInput(&$controller,&$xoopsUser,&$render)
	{
		$render->setTemplateName("user_avatar_edit.html");
		$render->setAttribute("actionForm", $this->mActionForm);
		$render->setAttribute("thisUser", $this->mObject);

		$render->setAttribute("allowUpload", $this->_mAllowUpload && $this->mObject->get('posts') >= $this->_mMinPost);
		$render->setAttribute("avatarWidth", $this->mAvatarWidth);
		$render->setAttribute("avatarHeight", $this->mAvatarHeight);
		$render->setAttribute("avatarMaxfilesize", $this->mAvatarMaxfilesize);

		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute("systemAvatars", $this->mSystemAvatars);
		$render->setAttribute("avatarSelectForm", $this->mAvatarSelectForm);
	}
	
	function executeViewSuccess(&$controller, &$xoopsUser, $render)
	{
		$controller->executeForward(XOOPS_URL . "/userinfo.php?uid=" . $this->mActionForm->get('uid'));
	}

	function executeViewError(&$controller,&$xoopsUser,&$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/userinfo.php?uid=" . $this->mActionForm->get('uid'), 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}
}

?>