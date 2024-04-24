<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (file_exists( '../mainfile.php')) {
    include_once '../mainfile.php';
}

include_once './class/settingmanager.php';

$sm = new setting_manager();

$sm->readConstant();
$wizard->setContent( $sm->editform() );
$wizard->render();
