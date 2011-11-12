<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Legacy_AbstractGroupObject
**/
abstract class Legacy_AbstractGroupObject extends XoopsSimpleObject
{
	const PRIMARY = 'group_id';
	const DATANAME = 'group';

	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('group_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('posttime', XOBJ_DTYPE_INT, time(), false);
	}

	/**
	 * getPrimary
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function getPrimary()
	{
		return self::PRIMARY;
	}

	/**
	 * getDataname
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function getDataname()
	{
		return self::DATANAME;
	}

	/**
	 * isMember
	 * 
	 * @param	int		$uid
	 * @param	Enum	$rank	Lenum_GroupRank
	 * 
	 * @return	int[]
	**/
	abstract public function isMember(/*** int ***/ $uid, /*** Enum ***/ $rank=Lenum_GroupRank::REGULAR);

	/**
	 * countMembers
	 * 
	 * @param	Enum	$rank	Lenum_GroupRank
	 * 
	 * @return	int
	**/
	abstract public function countMembers(/*** Enum ***/ $rank=Lenum_GroupRank::ASSOCIATE);

	/**
	 * renderUri
	 * 
	 * @param	string	$action
	 * 
	 * @return	string
	 */
	public function renderUri($action=null)
	{
		return Legacy_Utils::renderUri($this->getDirname(), $this->getDataname(), $this->get($this->getPrimary()), $action);
	}
}

?>
