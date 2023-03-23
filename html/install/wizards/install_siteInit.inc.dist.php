<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once '../mainfile.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

echo '<h2>wizard/siteInit.inc.dist</h2>';

$current_timezone = date( 'O' );
$current_timediff = (float) ( $current_timezone[0] . ( substr( $current_timezone, 1, 2 ) + substr( $current_timezone, 3, 2 ) / 60 ) );

$wizard->assign( 'current_timediff', $current_timediff );

$wizard->assign( 'timediffs', XoopsLists::getTimeZoneList() );

$wizard->render( 'install_siteInit.tpl.php' );
