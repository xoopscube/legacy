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
 * Lenum_Status
**/
interface Lenum_Status
{
	const DELETED = 0;
	const REJECTED = 2;
	const PROGRESS = 5;
	const PUBLISHED = 9;
}
/**
 * Lenum_WorkflowStatus
**/
interface Lenum_WorkflowStatus
{
	const DELETED = 0;
	const BLOCKED = 1;
	const REJECTED = 2;
	const PROGRESS = 5;
	const FINISHED = 9;
}
/**
 * Lenum_GroupRank
**/
class Lenum_GroupRank
{
	const GUEST = 0;
	const ASSOCIATE = 2;
	const REGULAR = 5;
	const STAFF = 7;
	const OWNER = 9;

	public static function getList()
	{
		return array(
			self::GUEST => _GROUP_RANK_GUEST,
			self::ASSOCIATE => _GROUP_RANK_ASSOCIATE,
			self::REGULAR => _GROUP_RANK_REGULAR,
			self::STAFF => _GROUP_RANK_STAFF,
			self::OWNER => _GROUP_RANK_OWNER
		);
	}
}


class Lenum_ImageType
{
	const GIF = 1;
	const JPG = 2;
	const PNG = 3;

	public static function getName(/*** Enum ***/ $ext)
	{
		switch($ext){
		case self::GIF:
			return 'gif';
			break;
		case self::JPG:
			return 'jpg';
			break;
		case self::PNG:
			return 'png';
			break;
		}
	}
}

?>
