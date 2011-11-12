<?php

require_once "../../mainfile.php";
require_once XOOPS_ROOT_PATH . "/header.php";
require_once XOOPS_MODULE_PATH . "/user/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();

$actionName = isset($_GET['action']) ? trim($_GET['action']) : "default";

$moduleRunner = new User_ActionFrame(false);
$moduleRunner->setActionName($actionName);

$root->mController->mExecute->add(array(&$moduleRunner, 'execute'));

$root->mController->execute();

require_once XOOPS_ROOT_PATH . "/footer.php";

?>
