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
$current_timediff = floatval(substr($current_timezone, 0, 1).(substr($current_timezone, 1, 2) + substr($current_timezone, 3, 2)/60));
$wizard->assign('current_timediff', $current_timediff);

$wizard->assign('timediffs', XoopsLists::getTimeZoneList());
$wizard->assign('timezones', array(
    '-12'  => 'Etc/GMT+12',
    '-11'  => 'Etc/GMT+11',
    '-10'  => 'Etc/GMT+10',
    '-9'   => 'Etc/GMT+9',
    '-8'   => 'Etc/GMT+8',
    '-7'   => 'Etc/GMT+7',
    '-6'   => 'Etc/GMT+6',
    '-5'   => 'Etc/GMT+5',
    '-4.5' => 'America/Caracas',
    '-4'   => 'Etc/GMT+4',
    '-3.5' => 'America/St_Johns',
    '-3'   => 'Etc/GMT+3',
    '-2'   => 'Etc/GMT+2',
    '-1'   => 'Etc/GMT+1',
    '0'    => 'Etc/GMT0',
    '1'    => 'Etc/GMT-1',
    '2'    => 'Etc/GMT-2',
    '3'    => 'Etc/GMT-3',
    '3.5'  => 'Asia/Tehran',
    '4'    => 'Etc/GMT-4',
    '4.5'  => 'Asia/Kabul',
    '5'    => 'Etc/GMT-5',
    '5.5'  => 'Asia/Calcutta',
    '5.75' => 'Asia/Kathmandu',
    '6'    => 'Etc/GMT-6',
    '6.5'  => 'Asia/Rangoon',
    '7'    => 'Etc/GMT-7',
    '8'    => 'Etc/GMT-8',
    '9'    => 'Etc/GMT-9',
    '9.5'  => 'Australia/Darwin',
    '10'   => 'Etc/GMT-10',
    '11'   => 'Etc/GMT-11',
    '12'   => 'Etc/GMT-12',
    '13'   => 'Etc/GMT-13',
    '14'   => 'Etc/GMT-14'));

$wizard->render('install_siteInit.tpl.php');
