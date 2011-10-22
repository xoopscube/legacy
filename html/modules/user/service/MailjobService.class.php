<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class Mailjob_ArrayOfInt extends XCube_ObjectArray
{
	function getClassName()
	{
		return "int";
	}
}

class User_MailjobService extends XCube_Service
{
	var $mServiceName = "User_MailjobService";
	var $mNameSpace = "User_Mailjob";
	var $mClassName = "User_MailjobService";

	/**
	 * @public
	 */
	function prepare()
	{
		$this->addFunction(S_PUBLIC_FUNC('int addMailjob(string title, text body, string from_name, string from_email, bool is_pm, bool is_mail, Mailjob_ArrayOfInt uidArr)'));
		//$this->addFunction(S_PUBLIC_FUNC('int sendMailjob(int mailjob_id, int uid)'));
	}

	/**
	 * @public
	 * add new mailjob
	 */
	function addMailjob()
	{
		require_once XOOPS_MODULE_PATH . "/user/admin/forms/MailjobServiceEditForm.class.php";
	
		$root =& XCube_Root::getSingleton();
	
		$uidArr = $root->mContext->mRequest->getRequest('uidArr');
	
		//prepare mailjob object
		$handler =& xoops_getmodulehandler('mailjob', 'user');
		$obj = $handler->create();
		$obj->set('title', $root->mContext->mRequest->getRequest('title'));
		$obj->set('body', $root->mContext->mRequest->getRequest('body'));
		$obj->set('from_name', $root->mContext->mRequest->getRequest('from_name'));
		$obj->set('from_email', $root->mContext->mRequest->getRequest('from_email'));
		$obj->set('is_pm', $root->mContext->mRequest->getRequest('is_pm'));
		$obj->set('is_mail', $root->mContext->mRequest->getRequest('is_mail'));
	
		//validate
		$actionForm = new User_MailjobServiceEditForm();
		$actionForm->prepare();
		$actionForm->load($obj);
		$actionForm->validate();
		if($actionForm->hasError()){
			return 0;
		}
	
		//insert mailjob to DB
		if(! $handler->insert($obj)){
			return 0;
		}
	
		//insert mailjob_link
		$linkHandler =& xoops_getmodulehandler('mailjob_link', 'user');
		foreach ($uidArr as $uid) {
			$linkObj =& $linkHandler->create();
			$linkObj->set('mailjob_id', $obj->get('mailjob_id'));
			$linkObj->set('uid', $uid);
			$linkHandler->insert($linkObj);
		}
	
		return $obj->get('mailjob_id');
	}
/*
	function sendMailjob()
	{
		$root =& XCube_Root::getSingleton();
		$mailjob_id = intval($root->mContext->mRequest->getRequest('mailjob_id'));
		$uid = intval($root->mContext->mRequest->getRequest('mailjob_id'));
	
		$handler =& xoops_getmodulehandler('mailjob', 'user');
		$mailjobObj =& $handler->get($mailjjob_id);
	
		$userHandler =& xoops_gethandler('user');
		$userObj =& $userHandler->get($uid);
	
		if ($mailjobObj->get('is_pm')) {
			$mailjobObj->mSend->add(array(&$this, "sendPM"));
		}

		if ($mailjobObj->get('is_mail')) {
			$mailjobObj->mSend->add(array(&$this, "sendMail"));
		}

		$mailjobObj->send($userObj);
		
		return $mailjobObj->loadUserCount();
	}
*/
}


?>