<?php
/**
 *
 * @package Legacy
 * @version $Id: install_siteInit.inc.php,v 1.3 2008/09/25 15:12:19 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
include_once '../mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';

$current_timezone = date('O');
$current_timediff = floatval(substr($current_timezone,0,1).(substr($current_timezone,1,2) + substr($current_timezone,3,2)/60));
$wizard->assign('current_timediff', $current_timediff);

$wizard->assign('timediffs', XoopsLists::getTimeZoneList());

$wizard->render('install_siteInit.tpl.php');
?>
