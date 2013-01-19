<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__) . "/DataUploadAction.class.php";
require_once XOOPS_MODULE_PATH . "/profile/admin/class/GroupsUsersLink.php";
require_once XOOPS_MODULE_PATH . "/profile/admin/class/ImportCsv.php";

class Profile_Admin_DataUploadDoAction extends Profile_Admin_DataUploadAction
{
	var $user_key = array();
	var $group_key = array();
	var $userObjects = array();
	var $groupObjects = array();
	var $profObjects = array();
	var $csvFName = null;
	protected $root;

	public function __construct()
	{
		$this->root = XCube_Root::getSingleton();
	}


	function &_getHandler($tableName = 'data')
	{
		$handler = xoops_getmodulehandler($tableName);
		return $handler;
	}

	function execute()
	{
		/// back
		if (isset($_POST['back'])) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		/// csv file check
		if (isset($_SESSION['csvfilename']) &&
			count($_SESSION['csvfilename'])
		) {
			$this->csvFName = $_SESSION['csvfilename'];
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView();
	}

	/**
	 * @param $userHandler
	 * @param $profile_handler
	 * @param $import_key
	 * @param $userKey
	 * @param $data
	 * @throws RuntimeException
	 */
	private function arrayToDb(&$userHandler,&$profHandler,&$groupHandler,&$import_key,&$userKey,&$data)
	{
		// Set CSV uid first
		$uid = intval($data['value'][0]['var']);
		$defaultInfo = array(
			'user_regdate' => time(),
			'level' => 1,
			'timezone_offset' => 9,
			'theme' => 'blue_theme',
			'umode' => 'flat',
			'notify_method' => 1
		);
		$defKeys = array_keys($defaultInfo);
		if ($data['is_new'] || $data['userUpdate'] || $data['groupUpdate'] || $data['profUpdate']
			|| $data['userCreate'] || $data['groupCreate'] || $data['profCreate']
		) {
			$addNew = $userUpdate = $groupUpdate = $profUpdate = False;
			if ($data['userUpdate']) {
				$userObj = $userHandler->get($uid);
				$userUpdate = TRUE;
			} elseif ($data['userCreate']) {
				$userObj = $userHandler->create();
				$userUpdate = TRUE;
				$addNew = true;
				$data['profCreate'] = 1;
			}
			if ($data['profCreate'] || $data['profUpdate']) {
				$profObj = $profHandler->create();
				$profUpdate = TRUE;
			}
			foreach ($import_key as $i => $key) {
				$value = $data['value'][$i]['var'];
				$field = $data['value'][$i]['field'];
				if (is_null($value) || is_null($field)) continue;
				if (in_array($field, $userKey)) {
					if ($key == "pass" && !(ctype_xdigit($value) && strlen($value) == 32)) {
						$value = md5($value);
					}
					if ($userUpdate) {
						if (preg_match("/date/", $key)) {
							$value = strtotime($value);
						}
						// in case of INSERT then check the default parameter setting
						if ($addNew) {
							if (in_array($key, $defKeys)) {
								$value = $defaultInfo[$key];
							}
						}
						$userObj->setVar($key, $value);
					}
				} elseif ( $field === "groupid" ) {
					$groupIds = explode("|", $value);
					$groupUpdate = TRUE;
				} else {
					if ($profUpdate) {
						if ($value) {
							if (strpos($key,'birthday') !== false || strpos($key,'_date') !== false || strpos($key,'wedding_anniversary') !== false) {
								$value = str_replace('/','-',$value);
								preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/',$value,$match);
								$value = sprintf("%04d-%02d-%02d", $match[1], $match[2], $match[3]);
							}
							$profObj->setVar($key, $value);
						}
					}
				}
			}
			if ($userUpdate){
				if ($userHandler->insert($userObj)){
					if(empty($uid) || $uid==0){
						$uid = $userHandler->getCurrentUid();
						$profObj->setVar('uid', $uid);
					}
				}
			}
			/*
			 *
			 * for profile
			 */
			if ($profUpdate) {
				if ($data['profUpdate']) {
					$profHandler->deleteAllByUserId($uid);
				}
				$profObj->setVar('uid', $uid);
				$profHandler->insert($profObj);
			}
			/*
			 * for groups_users_link
			 */
			if ($groupUpdate){
				$gul = new groupsUsersLink();
				$gul->insertGroups($uid, $groupIds);
			}
			return $uid;
		}
		return null;
	}


	/**
	 * @param $render
	 * @throws RuntimeException
	 */
	function executeViewSuccess(&$controller,&$render)
	{
		$render->setTemplateName("data_upload_done.html");
		$csvFName = $this->csvFName;
		$profHandler = xoops_getmodulehandler('data');

		// Get user table keys
		$userHandler = xoops_getmodulehandler('users', 'user');
		$user_tmp = $userHandler->create();
		$userKey = array_keys($user_tmp->gets());

		// Get group table keys
		$groupHandler = xoops_getmodulehandler('groups_users_link', 'user');
		$group_tmp = $groupHandler->create();
		$groupKey = array("groupid");

		// Get profile_definitions fields
		$profHandler = $this->_getHandler();
		$defHandler = $this->_getHandler('definitions');
		$defArr = $defHandler->getDefinitions(false);

		// Set key to restore
		$import_key = $userKey;
		$import_key[] = "groupid";
		foreach ($defArr as $key => $val){
			if ($key!="uid") $import_key[] = $key;
		}
		if (function_exists('mb_detect_encoding')){
			$csv_encoding = "SJIS-WIN";
		}else{
			$csv_encoding = '';
		}
		$lineCount=0;
		$fp = fopen($csvFName, 'r');
		$iCsv = new importCsv($userKey,$groupKey);
		while (!feof($fp)) {
			$_line = $iCsv->loadCSV($fp, $csv_encoding);
			if ($lineCount > 0 && $_line) {
				$csvData = $iCsv->loadOneLineToArray($_line,$import_key,$userHandler,$groupHandler,$profHandler);
				$uid = $this->arrayToDb(
					$userHandler,$profHandler,$groupHandler,$import_key,$userKey,$csvData
				);
			}
			$lineCount++;
		}
		fclose($fp);
		unset($this->csvFName);
		$controller->executeRedirect("./index.php?action=DefinitionsList", 1, _AD_PROFILE_DATA_UPLOAD_DONE);
	}
}