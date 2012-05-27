<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__)."/DataUploadAction.class.php";

class Profile_Admin_DataUploadDoAction extends Profile_Admin_DataUploadAction
{
	function execute()
	{
		/// back
		if (isset($_POST['back'])){
			return $this->getDefaultView($controller, $xoopsUser);
		}
		/// csv file check
		if (isset($_SESSION['import_csv_upload_data']) &&
			count($_SESSION['import_csv_upload_data'])){
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView();
	}
	/*
	 *  Insert & Update for groups_users_link table
	 */
	function insertGroups(&$handler,$uid,$groupIds)
	{
		$flag = true;
		// delete before insert
		if ($uid){
			$force = true;
			$oldLinkArr = $handler->getObjects(new Criteria('uid', $uid), $force);
			$oldGroupidArr = array();
			foreach (array_keys($oldLinkArr) as $key) {
				$handler->delete($oldLinkArr[$key], $force);
			}
		}
		// insert gid
		foreach ($groupIds as $gid) {
			if ($gid){
				$link = $handler->create();
				$link->set('groupid', $gid);
				$link->set('uid', $uid);
				$flag &= $handler->insert($link, true);
			}
		}
		return $flag;
	}
	function executeViewSuccess(&$controller,&$render)
	{
		$csv_data = $_SESSION['import_csv_upload_data'];
		
		// Get user table keys
		$userHandler = xoops_getmodulehandler('users', 'user');
		$user_tmp = $userHandler->create();
		$userKey = array_keys($user_tmp->gets());
		
		// Get group table keys
		$groupHandler = xoops_getmodulehandler ('groups_users_link', 'user');
		$groupKey = array("groupid");		
		
		$profile_handler = xoops_getmodulehandler('data');
		$defHandler = xoops_getmodulehandler('definitions');
		$defArr = $defHandler->getDefinitions(false);
		// Set key to restore
		$import_key = array_merge($userKey,$groupKey);
		foreach (array_keys($defArr) as $key){
			$import_key[] = $key;
		}
		foreach ($csv_data as $data){
			$uid = NULL;
			if ($data['is_new'] || $data['userUpdate'] || $data['groupUpdate'] || $data['profUpdate']
			 || $data['userCreate'] || $data['groupCreate'] || $data['profCreate'] ){
				adump($data);
				$userUpdate = $groupUpdate = $profUpdate = False;
				// get uid
				$uid = $data['value'][0]['var'];
				if ($data['userUpdate']){
					$userObj = $userHandler->get($uid);
					$userUpdate = TRUE;
				} elseif ($data['userCreate']) {
					$userObj = $userHandler->create();
					$userUpdate = TRUE;
				}
				if ($data['profCreate']){
					$profObj = $profile_handler->create();
					$profUpdate = TRUE;
				}elseif ($data['profUpdate']){
					$profObj = $profile_handler->get($uid);
					$profUpdate = TRUE;
				}
				foreach ($import_key as $i=>$key){
					$value = $data['value'][$i]['var'];
					$field = $data['value'][$i]['field'];
					if(!isset($value)) continue;
					if (in_array($field,$userKey)){
						if($key=="pass" && !(ctype_xdigit($value) && strlen($value)==32) ){
							$value = md5($value);
						}
						if($userUpdate){
							$userObj->setVar($key, $value);
						}	
					} elseif (in_array($field,$groupKey)){
						$groupIds = explode("|",$value);
						$groupUpdate = TRUE;	
					} else{
						if($profUpdate){
							if($value) $profObj->setVar($key, $value);
						}	
					}
				}
				if ($userUpdate) $userHandler->insert($userObj);
				if (is_null($uid)){
					$uid = $userObj->getVar('uid');
				} 
				$profObj->setVar('uid', $uid);
				if ($groupUpdate) $this->insertGroups($groupHandler,$uid,$groupIds);
				if ($profUpdate) $profile_handler->insert($profObj);
			}
		}
		unset($_SESSION['import_csv_upload_data']);
		echo "User,Group,Profile Insert and Update Done.";
		//$controller->executeRedirect("./index.php?action=DefinitionsList", 1, _AD_PROFILE_DATA_UPLOAD_DONE);
	}
}
