<?php
/**
 * @package user
 * @version $Id: LostPassMailBuilder.class.php,v 1.3 2007/07/20 03:03:39 nobunobu Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * This class commands a builder to build mail. It's a kind of builder pattern,
 * and made for separating the building logic and the business logic.
 */
class User_LostPassMailDirector
{
	var $mBuilder;
	var $mXoopsUser;
	var $mXoopsConfig;
	var $mExtraVars;
	
	function User_LostPassMailDirector(&$builder, &$user, &$xoopsConfig, $extraVars = array())
	{
		$this->mBuilder =& $builder;
		$this->mXoopsUser =& $user;
		$this->mXoopsConfig =& $xoopsConfig;
		$this->mExtraVars = $extraVars;
	}
	
	function contruct()
	{
		$this->mBuilder->setToUsers($this->mXoopsUser, $this->mXoopsConfig);
		$this->mBuilder->setFromEmail($this->mXoopsUser, $this->mXoopsConfig);
		$this->mBuilder->setSubject($this->mXoopsUser, $this->mXoopsConfig);
		$this->mBuilder->setTemplate();
		$this->mBuilder->setBody($this->mXoopsUser, $this->mXoopsConfig,$this->mExtraVars);
	}
}

/***
 * @internal
 * This class is a builder for User_LostPassMailDirector and the base class,
 * and the base class for other builders. Use lostpass1.tpl as the template.
 * That's the first mail at procedure of the password regenerated, and written
 * about the special URL which generates new password.
 */
class User_LostPass1MailBuilder
{
	var $mMailer;
	
	function User_LostPass1MailBuilder()
	{
		$this->mMailer =& getMailer();
		$this->mMailer->useMail();
	}

	function setToUsers($user, $xoopsConfig)
	{
		$this->mMailer->setToUsers($user);
	}

	function setFromEmail($user, $xoopsConfig)
	{
		$this->mMailer->setFromEmail($xoopsConfig['adminmail']);
		$this->mMailer->setFromName($xoopsConfig['sitename']);
	}
	
	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(sprintf(_MD_USER_LANG_NEWPWDREQ, $xoopsConfig['sitename']));
	}

	/**
	 * Set template file itself.
	 */
	function setTemplate()
	{
		$root =& XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(XOOPS_MODULE_PATH . '/user/language/' . $language . '/mail_template/');
		$this->mMailer->setTemplate("lostpass1.tpl");
	}
	
	function setBody($user,$xoopsConfig,$extraVars)
	{
		$this->mMailer->assign("SITENAME", $xoopsConfig['sitename']);
		$this->mMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
		$this->mMailer->assign("SITEURL", XOOPS_URL . "/");
		$this->mMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
		$this->mMailer->assign("NEWPWD_LINK", XOOPS_URL . "/lostpass.php?email=" . $user->getShow('email') . "&code=" . substr($user->get("pass"), 0, 5));
	}
		
	function &getResult()
	{
		return $this->mMailer;
	}
}

/***
 * @internal
 * This class is a builder which uses lostpass2.tpl as the template. That's the
 * second mail at procedure of the password regenerated, and written about the
 * new generated password of the user.
 */
class User_LostPass2MailBuilder extends User_LostPass1MailBuilder
{
	function setTemplate()
	{
		$root=&XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$this->mMailer->setTemplateDir(XOOPS_MODULE_PATH . '/user/language/' . $language . '/mail_template/');
		$this->mMailer->setTemplate("lostpass2.tpl");
	}

	function setSubject($user, $xoopsConfig)
	{
		$this->mMailer->setSubject(sprintf(_MD_USER_LANG_NEWPWDREQ, $xoopsConfig['sitename']));
	}

	function setBody($user, $xoopsConfig, $extraVars)
	{
		$this->mMailer->assign("SITENAME", $xoopsConfig['sitename']);
		$this->mMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
		$this->mMailer->assign("SITEURL", XOOPS_URL . "/");
		$this->mMailer->assign("IP", $_SERVER['REMOTE_ADDR']);
		$this->mMailer->assign("NEWPWD", $extraVars['newpass']);
	}
}
?>
