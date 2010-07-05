<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Lecat_Utils
**/
class Lecat_Utils
{
	/**
	 * &getXoopsHandler
	 * 
	 * @param	string	$name
	 * @param	bool  $optional
	 * 
	 * @return	XoopsObjectHandler
	**/
	public static function &getXoopsHandler(/*** string ***/ $name,/*** bool ***/ $optional = false)
	{
		// TODO will be emulated xoops_gethandler
		return xoops_gethandler($name,$optional);
	}

	/**
	 * &getModuleHandler
	 * 
	 * @param	string	$name
	 * @param	string	$dirname
	 * 
	 * @return	XoopsObjectHandleer
	**/
	public static function &getModuleHandler(/*** string ***/ $name,/*** string ***/ $dirname)
	{
		// TODO will be emulated xoops_getmodulehandler
		return xoops_getmodulehandler($name,$dirname);
	}

	/**
	 * getEnv
	 * 
	 * @param	string	$key
	 * 
	 * @return	string
	**/
	public static function getEnv(/*** string ***/ $key)
	{
		return getenv($key);
	}

	/**
	 * getInheritPermission
	 * 
	 * @param	string	$dirname
	 * @param	int[]  $catPath
	 * @param	int  $groupid
	 * 
	 * @return	string
	**/
	public function getInheritPermission(/*** string ***/ $dirname, /*** int[] ***/$catPath, /*** int ***/ $groupId=0)
	{
		$handler = Legacy_Utils::getModuleHandler('permit', $dirname);
		//check if the category has permission in order
		foreach(array_keys($catPath) as $key){
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('cat_id', $catPath[$key]));
			if(intval($groupId)>0){
				$criteria->add(new Criteria('groupid', $groupId));
			}
			$objs = $handler->getObjects($criteria);
			if(count($objs)>0) return $objs;
		}
	}

	/**
	 * getActionList
	 * 
	 * @param	string	$dirname
	 * 
	 * @return	string[]
	**/
	public static function getActionList(/*** string ***/ $dirname)
	{
		$handler = xoops_gethandler('config');
		$conf = $handler->getConfigsByDirname($dirname);
	
		$actions = $conf['actions'];
		return isset($actions) ? unserialize($actions) : array('key'=>array('viewer','poster','manager'),'title'=>array('Viewer', 'Poster', 'Manager'),'default'=>array(1,1,0));
	}
}

?>
