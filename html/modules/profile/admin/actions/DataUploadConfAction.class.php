<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__) . "/DataUploadAction.class.php";
require_once XOOPS_MODULE_PATH . "/profile/admin/class/ImportCsv.php";

class Profile_Admin_DataUploadConfAction extends Profile_Admin_DataUploadAction
{
	var $user_key = array();
	var $group_key = array();
	var $userObjects = array();
	var $groupObjects = array();
	var $profObjects = array();

	function &_getHandler($tableName='data')
	{
		$handler = xoops_getmodulehandler($tableName);
		return $handler;
	}

	function execute()
	{
		/// csv file check
		if (isset($_FILES['profile_csv_file']) &&
			$_FILES['profile_csv_file']['error'] == 0){
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView();
	}
	function executeViewSuccess(&$controller,&$render)
	{
		// success
		$render->setTemplateName("data_upload_conf.html");

		// csv data
		$csvData = array();
		$csvFName = XOOPS_TRUST_PATH . "/cache/".time().".csv";
		@move_uploaded_file( $_FILES['profile_csv_file']['tmp_name'], $csvFName );

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
		$criteria = new CriteriaElement();
		$criteria->setSort('uid');

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
				$csvData[] = $iCsv->loadOneLineToArray($_line,$import_key,$userHandler,$groupHandler,$profHandler);
			}
			$lineCount++;
		}
		fclose($fp);
		$render->setAttribute('import_fields', $import_key);
		$render->setAttribute('csv_data', $csvData, $iCsv->userObjects() );
		$render->setAttribute('lineCount', $lineCount-1 );
		$_SESSION['csvfilename'] = $csvFName;
	}
}
