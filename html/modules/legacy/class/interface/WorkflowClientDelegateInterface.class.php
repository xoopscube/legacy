<?php
/**
 * This Interface is generated by Cube tool.
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     code generator
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Interface of workflow client delegate
 * Modules which uses Legacy_Workflow must implement this interface.
 * Legacy_Workflow module must be unique.
 * You can get its dirname by constant LEGACY_WORKFLOW_DIRNAME
**/
interface Legacy_iWorkflowClientDelegate
{
    /**
     * getClientList	Legacy_WorkflowClient.GetClientList
     * Get client module's dirname and dataname(tablename)
     *
     * @param mixed[]	&$list
     *  $list[]['dirname']	client module dirname
     *  $list[]['dataname']	client module dataname(tablename)
     *
     * @return	void
     */
    public static function getClientList(/*** mixed[] ***/ &$list);

    /**
     * updateStatus Legacy_WorkflowClient.UpdateStatus
     * Update client module's status(publish, rejected, etc).
     *
     * @param string	&$result
     * @param string	$dirname	client module dirname
     * @param string	$dataname	client module dataname(tablename)
     * @param int		$data_id	client module primary key
     * @param Enum		$status Lenum_WorkflowStatus
     *
     * @return	void
     */
    public static function updateStatus(/*** string ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $data_id, /*** Enum ***/ $status);
}
