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
	
	
	/// CSVファイルを出力する
	function execute()
	{
		$filename = sprintf('%s_Profile_data_List.csv', $GLOBALS['xoopsConfig']['sitename']);
		$text = '';
		$field_line = 'uid,';
		
		$handler =& $this->_getHandler();
		$defHandler =& xoops_getmodulehandler('definitions');
		$defArr =& $defHandler->getDefinitions(false);
	
		$criteria = new CriteriaElement();
		$criteria->setSort('uid');
		$dataArr = $handler->getObjects($criteria);
		if (count($dataArr)==0){
			return PROFILE_FRAME_VIEW_INDEX;
		}
		foreach (array_keys($defArr) as $key){
			$field_line .= $var['label'].",";
		}
		$field_line .= "\n";
		
		foreach ($dataArr as $profile){
			$profile_data = '';
			foreach ($profile->gets() as $key=>$value){
				if($defArr[$key]->get('type')=='date'){
					$value = $value ? formatTimestamp($value, 'Y/n/j H:i') : '';				}
				if (preg_match('/[,"\r\n]/', $value)) {
					$value = preg_replace('/"/', "\"\"", $value);
					$value = "\"$value\"";
				}
				$profile_data .= $value . ',';
			}
			$text .= trim($profile_data, ',')."\n";
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
