<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
require_once "../../../mainfile.php";
require_once XOOPS_MODULE_PATH. '/fileManager/admin/menu.php' ;
require_once XOOPS_MODULE_PATH. '/fileManager/class/ActionFrame.class.php';

$root =& XCube_Root::getSingleton();
$root->mController->executeHeader();

$actionName = isset($_GET['action']) ? trim($_GET['action']) : "index";   

$moduleRunner =& new FileManager_ActionFrame(true);
$moduleRunner->setActionName($actionName);

$root->mController->mExecute->add(array(&$moduleRunner, 'execute'));
$root->mController->execute();

$xoopsLogger=&$root->mController->getLogger();
$xoopsLogger->stopTime();

//$root->mController->executeView();


$out = ob_get_contents();
$root->mController->executeView();
echo $out;

?>
