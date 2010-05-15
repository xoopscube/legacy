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
 * Lenum_ProgressStatus
**/
interface Lenum_ProgressStatus
{
	const DELETED = 0;
	const REJECTED = 2;
	const PROGRESS = 5;
	const FINISHED = 9;
}
/**
 * Lenum_GroupRank
**/
interface Lenum_GroupRank
{
	const GUEST = 0;
	const ASSOCIATE = 2;
	const REGULAR = 5;
	const STAFF = 7;
	const OWNER = 9;
}


?>
