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
 * Interface of workflow client delegate
 * Modules which uses Legacy_Workflow must implement this interface.
**/
interface Legacy_iWorkflowClientDelegate
{
	/**
	 * getClientList
	 * Get client module's dirname and dataname(tablename)
	 *
	 * @param mixed[]	&$list
	 *  $list[]['dirname']
	 *  $list[]['dataname']
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** mixed[] ***/ &$list);

	/**
	 * updateStatus Legacy_WorkflowClient.UpdateStatus
	 * Update client module's status(publish, rejected, etc).
	 *
	 * @param string	&$result
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$data_id
	 * @param Enum		$status Lenum_WorkflowStatus
	 *
	 * @return	void
	 */ 
	public static function updateStatus(/*** string ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id, /*** Enum ***/ $status);
}

?>
