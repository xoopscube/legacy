<?php
/**
 * @package user
 * @version $Id: RegistMailBuilder.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * This class commands a builder to build mail. It's a kind of builder pattern,
 * and made for separating the building logic and the business logic.
 */
class User_UserRegistMailDirector
{
	/***
	 * @var User_RegistUserActivateMailBuilder
	 */
	var $mBuilder;
	
	/***
	 * @var XoopsUser
	 */
	var $mUser;
	
	var $mXoopsConfig;
	
	var $mUserConfig;

	function User_UserRegistMailDirector(&$builder, &$user, $xoopsConfig, $userConfig)
	{
		$this->mBuilder =& $builder;
		
		$this->mUser =& $user;
		$this->mXoopsConfig =$xoopsConfig;
		$this->mUserConfig = $userConfig;
	}
	
	/***
	 * With setting variables to a builder, triggers building the mail of a
	 * builder.
	 */
	function contruct()
	{
		$this->mBuilder->setTemplate();
		$this->mBuilder->setToUsers($this->mUser, $this->mUserConfig);
		$this->mBuilder->setFromEmail($this->mXoopsConfig);
		$this->mBuilder->setSubject($this->mUser, $this->mXoopsConfig);
		$this->mBuilder->setBody($this->mUser, $this->mXoopsConfig);
	}
}

/***
 * @internal
 * This class is a builder for User_UserRegistMailDirector and the base class,
 * and the base class for other builders. Use register.tpl as the template.
 * That's the first mail at procedure of the user registration, and written
 * about the special URL which activates the registration application.
 */
class User_RegistUserActivateMailBuilder
{
	var $mMailer;
	
	function User_RegistUserActivateMailBuilder()
	{
		$this->mMailer =& getMailer();
		$this->mMailer->useMail();
	}

	/***
	 * Set the template itself.
	 */
	function setTemplate()
	{
		$root=&XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/user/language/' . $language . '/mail_template/');
		$this->mMailer->setTemplate('register.tpl');
	}

	function setToUsers($user, $userConfig)
	{
		$this->mMailer->setToUsers($user);
	}
	
	function setFromEmail($xoopsConfig)
	{
		$this->mMailer->setFromEmail($xoopsConfig['adminmail']);
		$this->mMailer->setFromName($xoopsConfig['sitename']);
	}
	
	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(@sprintf(_MD_USER_LANG_USERKEYFOR, $user->getShow('uname')));
	}

	function setBody($user,$xoopsConfig)
	{
		$this->mMailer->assign('SITENAME', $xoopsConfig['sitename']);
		$this->mMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
		$this->mMailer->assign('SITEURL', XOOPS_URL . '/');
		$this->mMailer->assign('USERACTLINK', XOOPS_URL . '/user.php?op=actv&uid=' . $user->getVar('uid') . '&actkey=' . $user->getShow('actkey'));
	}
	
	function &getResult()
	{
		return $this->mMailer;
	}
}

/***
 * @internal
 * This class is a builder which uses adminactivate.tpl as the template. The
 * mail which this class builds, requests activating the new registration for
 * administrators.
 */
class User_RegistUserAdminActivateMailBuilder extends User_RegistUserActivateMailBuilder
{
	function setTemplate()
	{
		$root=&XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/user/language/' . $language . '/mail_template/');
		$this->mMailer->setTemplate('adminactivate.tpl');
	}

	function setToUsers($user, $userConfig)
	{
		$memberHandler=&xoops_gethandler('member');
		$this->mMailer->setToGroups($memberHandler->getGroup($userConfig['activation_group']));
	}
	
	function setFromUser($xoopsConfig)
	{
		$this->mMailer->setFromEmail($xoopsConfig['adminmail']);
		$this->mMailer->setFromName($xoopsConfig['sitename']);
	}
	
	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(@sprintf(_MD_USER_LANG_USERKEYFOR,$user->getVar('uname')));
	}

	function setBody($user, $xoopsConfig)
	{
		parent::setBody($user,$xoopsConfig);
		$this->mMailer->assign('USERNAME', $user->getVar('uname'));
		$this->mMailer->assign('USEREMAIL', $user->getVar('email'));
		$this->mMailer->assign('USERACTLINK', XOOPS_URL . '/user.php?op=actv&uid=' . $user->getVar('uid') . '&actkey=' . $user->getVar('actkey'));
	}
}

/***
 * @internal
 * [Notice]
 * Uncompleted?
 * 
 * @todo Implement setTemplate()
 */
class User_RegistUserNotifyMailBuilder extends User_RegistUserActivateMailBuilder
{
	function setTemplate()
	{
	}

	function setToUsers($user, $userConfig)
	{
		$memberHandler=&xoops_gethandler('member');
		$this->mMailer->setToGroups($memberHandler->getGroup($userConfig['new_user_notify_group']));
	}
	
	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(@sprintf(_MD_USER_LANG_NEWUSERREGAT, $xoopsConfig['sitename']));
	}

	function setBody($user, $xoopsConfig)
	{
		$this->mMailer->setBody(@sprintf(_MD_USER_LANG_HASJUSTREG, $user->getVar('uname')));
	}
}

/***
 * @internal
 * This class is a builder which uses activated.tpl as the template. The mail
 * which this class builds, reports completed activation for a new user.
 */
class User_RegistAdminCommitMailBuilder extends User_RegistUserActivateMailBuilder
{
	function setTemplate()
	{
		$root=&XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/user/language/' . $language . '/mail_template/');
		$this->mMailer->setTemplate('activated.tpl');
	}
	
	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(@sprintf(_MD_USER_LANG_YOURACCOUNT, $xoopsConfig['sitename']));
	}
}

?>