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
 * Legacy_Status
**/
interface Legacy_Status
{
	const DELETED = 0;
	const REJECTED = 2;
	const PROGRESS = 5;
	const PUBLISHED = 9;
}
/**
 * Legacy_ProgressStatus
**/
interface Legacy_ProgressStatus
{
	const DELETED = 0;
	const REJECTED = 2;
	const PROGRESS = 5;
	const FINISHED = 9;
}

interface Legacy_TextareaEditorEnum
{
	const BBCODE = 0;	//default
	const HTML = 1;
	const NONE = 2;
}

?>
