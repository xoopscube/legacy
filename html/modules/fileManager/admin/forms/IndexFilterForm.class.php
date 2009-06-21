<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . '/fileManager/class/AbstractFilterForm.class.php';

define('FILEMANAGER_SORT_KEY_FILENAME' , 1);
define('FILEMANAGER_SORT_KEY_FILETYPE' , 2);
define('FILEMANAGER_SORT_KEY_FILESIZE' , 3);
define('FILEMANAGER_SORT_KEY_FILEDATE' , 4);

define('FILEMANAGER_SORT_KEY_DEFAULT', FILEMANAGER_SORT_KEY_FILENAME);

class FileManager_IndexFilterForm extends FileManager_AbstractFilterForm
{
	var $mSortKeys = array(
		FILEMANAGER_SORT_KEY_FILENAME => 'file_name',
		FILEMANAGER_SORT_KEY_FILETYPE => 'file_type',
		FILEMANAGER_SORT_KEY_FILESIZE => 'file_statsize',
		FILEMANAGER_SORT_KEY_FILEDATE => 'file_time',
	);

	function getDefaultSortKey()
	{
		return FILEMANAGER_SORT_KEY_DEFAULT;
	}

}

?>
