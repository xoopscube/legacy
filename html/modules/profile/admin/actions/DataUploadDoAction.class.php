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
		if (isset($_SESSION['profile_csv_upload_data']) &&
			count($_SESSION['profile_csv_upload_data'])){
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView();
	}
	
	
	function executeViewSuccess(&$controller,&$render)
	{
		$csv_data = $_SESSION['profile_csv_upload_data'];
		$profile_handler =& xoops_getmodulehandler('data');
		$defHandler =& xoops_getmodulehandler('definitions');
		$defArr =& $defHandler->getDefinitions(false);
		$profile_key[] = "uid";
		foreach (array_keys($defArr) as $key){
			$profile_key[] = $key;
		}		
		foreach ($csv_data as $data){
			if ($data['is_new'] || $data['update']){
				if ($data['update']){
					$prof =& $profile_handler->get($data['value'][0]['var']);
				} else {
					$prof =& $profile_handler->create();
				}
				foreach ($profile_key as $i=>$key){
					$value = $data['value'][$i]['var'];
					$prof->setVar($key, $value);
					echo $value;
				}
				$profile_handler->insert($prof);
			}
		}
		unset($_SESSION['profile_csv_upload_data']);
		
		$controller->executeRedirect("index.php", 1, _AD_PROFILE_DATA_UPLOAD_DONE);
	}
}
