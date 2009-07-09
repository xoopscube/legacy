<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit();

class FileManagerTokenObject extends XoopsSimpleObject
{
	function FileManagerTokenObject()
	{
		$this->initVar('token', XOBJ_DTYPE_STRING, '', true, 32);
		$this->initVar('expire', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('uid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('ipaddress', XOBJ_DTYPE_STRING, '', true, 15);
	}
}

class FileManagerTokenHandler extends XoopsObjectGenericHandler
{
	var $mTable = 'filemanager_token';
	var $mPrimary = 'token';
	var $mClass = 'FileManagerTokenObject';

	function FileManagerTokenDataHandler(&$db) {
		parent::XoopsObjectGenericHandler($db);
	}

	function setToken($uploadToken, $expire, $uid, $ipAddress) {

		$handler =& xoops_getmodulehandler('token');
		// delete token
		$sql = sprintf("DELETE FROM `%s` WHERE `uid`=%s", $this->mTable, $uid );
		$this->db->queryF($sql);

		$obj = $handler->create();
		$obj->set('token' , $uploadToken);
		$obj->set('expire' , $expire);
		$obj->set('uid' , $uid);
		$obj->set('ipaddress' , $ipAddress);
		$ret =& $handler->insert($obj, true);
		return $ret;
	}

	// delete token
	function deleteToken($delTokenId = NULL) {
		$sql = sprintf("DELETE FROM `%s` WHERE `%s`=%s", $this->mTable, $this->mPrimary, $delTokenId );
		return $this->db->queryF($sql);
	}

}
?>