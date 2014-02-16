<?php
/**
 *
 * @package Legacy
 * @version $Id: install_siteInit.inc.php,v 1.3 2008/09/25 15:12:19 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
include_once '../mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';

$current_timezone = date('O');
$current_timediff = floatval(substr($current_timezone,0,1).(substr($current_timezone,1,2) + substr($current_timezone,3,2)/60));
$wizard->assign('current_timediff', $current_timediff);

$wizard->assign('timediffs', XoopsLists::getTimeZoneList());
$wizard->assign('timezones', array (
	'-12'  => 'Kwajalein',
	'-11'  => 'Pacific/Midway',
	'-10'  => 'Pacific/Honolulu',
	'-9'   => 'America/Adak',
	'-8'   => 'America/Anchorage',
	'-7'   => 'America/Los_Angeles',
	'-6'   => 'America/Denver',
	'-5'   => 'America/Guayaquil',
	'-4.5' => 'America/Caracas',
	'-4'   => 'America/New_York',
	'-3'   => 'America/Halifax',
	'-2'   => 'Atlantic/South_Georgia',
	'-1'   => 'Atlantic/Cape_Verde',
	'0'    => 'Atlantic/Azores',
	'1'    => 'Europe/Dublin',
	'2'    => 'Europe/Belgrade',
	'3'    => 'Asia/Kuwait',
	'3.5'  => 'Asia/Tehran',
	'4'    => 'Asia/Muscat',
	'4.5'  => 'Asia/Kabul',
	'5'    => 'Asia/Ashgabat',
	'5.5'  => 'Asia/Kolkata',
	'5.75' => 'Asia/Kathmandu',
	'6'    => 'Asia/Dhaka',
	'6.5'  => 'Asia/Rangoon',
	'7'    => 'Asia/Jakarta',
	'8'    => 'Asia/Krasnoyarsk',
	'9'    => 'Asia/Tokyo',
	'9.5'  => 'Australia/Darwin',
	'10'   => 'Asia/Yakutsk',
	'11'   => 'Australia/Canberra',
	'12'   => 'Pacific/Fiji',
	'13'   => 'Pacific/Tongatapu'));

$wizard->render('install_siteInit.tpl.php');
?>
