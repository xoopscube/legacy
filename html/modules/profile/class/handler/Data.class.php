<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_DataObject extends XoopsSimpleObject
{
	/**
	 * @public
	 */
	function Profile_DataObject()
	{
		$handler =& xoops_getmodulehandler('definitions', 'profile');
		$def = $handler->getObjects();
	
		$this->initVar('uid', XOBJ_DTYPE_INT, '', false);
		foreach(array_keys($def) as $key){
			$this->initVar($def[$key]->get('field_name'), $def[$key]->getXObjType(), '', false);
		}
	}
}

class Profile_DataHandler extends XoopsObjectGenericHandler
{
	var $mTable = 'profile_data';
	var $mPrimary = 'uid';
	var $mClass = 'Profile_DataObject';

}

?>
