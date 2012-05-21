<?php
/**
 * @package user
 * @version $Id: DataDownloadAction.class.php,v 1.1 2007/08/01 02:34:42 kilica Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();
require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";

class Profile_Admin_DataDownloadAction extends Profile_AbstractListAction
{
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
		$handler =& $this->_getHandler();
		$count = $handler->getCount();
		$render->setAttribute('profileCount', $count);
	}
	
	function getDefaultView()
	{
		return PROFILE_FRAME_VIEW_INDEX;
	}
	private function setExportData( &$rowObj, $key, $value ){
		//if($rowObj->get('type')=='date'){
		//	$value = $value ? formatTimestamp($value, 'Y/n/j H:i') : '';
		//}
		if (preg_match('/[,"\r\n]/', $value)) {
			$value = preg_replace('/"/', "\"\"", $value);
		}
		$value = "\"$value\"";
		return $value;
	}
	
	// CSV data download
	function execute()
	{
		$filename = sprintf('%s_Profile_data_List.csv', $GLOBALS['xoopsConfig']['sitename']);
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
		$userArr = $userHandler->getObjects($criteria);
		if (count($userArr)==0){
			return PROFILE_FRAME_VIEW_INDEX;
		}
		foreach (array_keys($user_tmp->gets()) as $key){
			$field_line .= $key.",";
		}
		$field_line .= "groupid,";
		foreach (array_keys($prof_tmp->gets()) as $key){
			if($key!="uid") $field_line .= $key.",";
		}
		$field_line .= "\n";
		foreach ($userArr as $userRow){
			$export_data = '';
			foreach ($userRow->gets() as $key=>$value){
				$export_data .= $this->setExportData($userRow,$key,$value) . ',';
			}
        	$groupRows =& $groupHandler->getObjects(new Criteria('uid', $userRow->get('uid')));
        	$groupStr = "";
        	foreach($groupRows as $groupRow){
				$groupStr .= $groupRow->get('groupid') . '|';
			}
			$export_data .= '"'.$groupStr.'",';
        	$profRow =& $profHandler->getObjects(new Criteria('uid', $userRow->get('uid')));

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
			header("Content-Type: application/vnd.ms-excel");
		}
		
		
		header("Content-Disposition: attachment ; filename=\"{$filename}\"") ;
		exit($text);
	}
}

?>
