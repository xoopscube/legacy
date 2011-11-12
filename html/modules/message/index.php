<?php
require '../../mainfile.php';
define ('_MY_DIRNAME', basename(dirname(__FILE__)));
define ('_MY_MODULE_PATH', XOOPS_MODULE_PATH.'/'._MY_DIRNAME.'/');
define ('_MY_MODULE_URL', XOOPS_MODULE_URL.'/'._MY_DIRNAME.'/');

require _MY_MODULE_PATH.'kernel/ModController.class.php';

$root = XCube_Root::getSingleton();
$root->mController->executeHeader();

$modrun = new ModController();
$root->mController->mExecute->add(array($modrun, 'execute'));
$root->mController->execute();

$root->mController->executeView();
?>