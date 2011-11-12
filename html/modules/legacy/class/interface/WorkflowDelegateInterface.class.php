<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit();
}

/**
 * Interface of workflow delegate
 * Legacy_Workflow module must be unique.
 * You can get its dirname by constant LEGACY_WORKFLOW_DIRNAME
**/
interface Legacy_iWorkflowDelegate
{
	/**
	 * addItem	Legacy_Workflow.AddItem
	 *
	 * @param string $title
	 * @param string $dirname	client module dirname
	 * @param string $dataname	client module dataname
	 * @param int	 $data_id	client module primary key
	 * @param string $url		client data's uri
	 *
	 * @return	void
	 */ 
	public static function addItem(/*** string ***/ $title, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id, /*** string ***/ $url);

	/**
	 * deleteItem	Legacy_Workflow.DeleteItem
	 *
	 * @param string $dirname	client module dirname
	 * @param string $dataname	client module dataname
	 * @param int	 $data_id	client module primary key
	 *
	 * @return	void
	 */ 
	public static function deleteItem(/*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id);

	/**
	 * getHistory	Legacy_Workflow.GetHistory
	 *
	 * @param mix[] &$historyArr
	 *	$hisotryArr['step']
	 *	$hisotryArr['uid']
	 *	$hisotryArr['result']
	 *	$hisotryArr['comment']
	 *	$hisotryArr['posttime']
	 * @param string $dirname	client module dirname
	 * @param string $dataname	client module dataname
	 * @param int	 $data_id	client module primary key
	 *
	 * @return	void
	 */ 
	public static function getHistory(/*** mix[] ***/ &$historyArr, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id);
}

?>
