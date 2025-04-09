<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.5.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

require '../../mainfile.php';
define('_MY_DIRNAME', basename(__DIR__));
define('_MY_MODULE_PATH', XOOPS_MODULE_PATH.'/'._MY_DIRNAME.'/');
define('_MY_MODULE_URL', XOOPS_MODULE_URL.'/'._MY_DIRNAME.'/');

require _MY_MODULE_PATH.'kernel/ModController.class.php';

$root = XCube_Root::getSingleton();
$root->mController->executeHeader();

$modrun = new ModController();
$root->mController->mExecute->add([$modrun, 'execute']);
$root->mController->execute();

$root->mController->executeView();
