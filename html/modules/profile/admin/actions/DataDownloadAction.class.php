<?php
/**
 * @package user
 * @version $Id: DataDownloadAction.class.php,v 1.1 2007/08/01 02:34:42 kilica Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();
require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

class Profile_Admin_DataDownloadAction extends Profile_AbstractListAction
{
	// TODO : make sure max user number
	protected $max_user = 65535;

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('data');
		return $handler;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=DataDownload";
	}

	function executeViewIndex(&$render)
	{
		$render->setTemplateName("data_download.html");
		$handler = $this->_getHandler();
		$render->setAttribute('profileCount', $handler->getCount());
		$userHandler = xoops_getmodulehandler('users', 'user');
		$render->setAttribute('userCount', $userHandler->getCount());
		$start = xoops_getrequest('start') ? xoops_getrequest('start') : 0;
		$count = $userHandler->getCount();
		$nav = new XoopsPageNav($count, $this->max_user, $start, "start", 'action=DataDownload');
		$render->setAttribute('start',$start);
		$render->setAttribute('end',$this->max_user + $start);
		$render->setAttribute('nav',$nav->renderNav());
	}

	function getDefaultView()
	{
		return PROFILE_FRAME_VIEW_INDEX;
	}
	private function setExportData( &$rowObj, $key, $value ){
		//if($rowObj->get('data_type')=='date'){
		switch ($key){
			case "user_regdate":
			case "last_login" :
				$value = $value ? formatTimestamp($value, 'Y-n-j H:i') : '';
				break;
		}
		if (preg_match('/[,"\r\n]/', $value)) {
			$value = preg_replace('/"/', "\"\"", $value);
		}
		$value = "\"$value\"";
		return $value;
	}

	// CSV data download
	function execute()
	{
		global $xoopsModuleConfig;
		$start = xoops_getrequest('start') ? xoops_getrequest('start') : 0;

		$filename = sprintf('%s_Profile_data_List.csv', date( "YmdHis", time()) );
		$text = '';
		$field_line = '';

		// Get user table keys
		$userHandler =& xoops_getmodulehandler('users', 'user');
		$user_tmp = $userHandler->create();
		// Get group table keys
		$groupHandler =& xoops_getmodulehandler('groups_users_link', 'user');
		$group_tmp = $groupHandler->create();
		// Get profile_data table keys
		$profHandler =& $this->_getHandler();
		$prof_tmp = $profHandler->create();

		$criteria = new CriteriaElement();
		$criteria->setSort('uid');
		$criteria->setStart($start);
		$criteria->setLimit($this->max_user);
		$userArr = $userHandler->getObjects($criteria);
		if (count($userArr)==0){
			return PROFILE_FRAME_VIEW_INDEX;
		}
		foreach (array_keys($user_tmp->gets()) as $key){
			$_f = '_MD_USER_LANG_'.strtoupper($key);
			$field_line .= (defined($_f) ? constant($_f) : $key).",";
		}
		$field_line .= "groupid,";
		$handler = xoops_getmodulehandler('definitions');
		$labels = $handler->getLabel();
		foreach ($labels as $key){
			if ($key!="uid") $field_line .= $key.",";
		}
		$field_line .= "\n";
		foreach ($userArr as $userRow){
			$export_data = '';
			/*
			 * Output User
			 */
			$row = $userRow->gets();
			foreach ($row as $key=>$value){
				$export_data .= $this->setExportData($userRow,$key,$value) . ',';
			}
			/*
			 * Output Group
			 */
        	$groupRows =& $groupHandler->getObjects(new Criteria('uid', $userRow->get('uid')));
        	$groupStr = "";
        	foreach($groupRows as $groupRow){
				$groupStr .= $groupRow->get('groupid') . '|';
			}
			$export_data .= '"'.$groupStr.'",';
        	$profRow =& $profHandler->getObjects(new Criteria('uid', $userRow->get('uid')));
			/*
			 * Output Profile
			 */
			foreach ($profRow as $obj) {
				foreach($obj->gets() as $key=>$value){
      				if($key!="uid"){
						$export_data .= $this->setExportData($profRow,$key,$value) . ',';
      				}
				}
			}
			$text .= trim($export_data, ',')."\n";
		}
		$text = $field_line.$text;

		/// japanese
		if (strncasecmp($GLOBALS['xoopsConfig']['language'], 'ja', 2)===0){
			mb_convert_variables('SJIS', _CHARSET, $text);
		}

		if( preg_match('/firefox/i' , xoops_getenv('HTTP_USER_AGENT')) ){
			header("Content-Type: application/x-csv");
		}else{
			header("Pragma: public");
			header("Content-Type: application/octet-stream");
		}

		header("Content-Disposition: attachment ; filename=\"{$filename}\"") ;
		while ( ob_get_level() > 0 ) {
    		ob_end_clean();
		}
		exit($text);
	}
}

