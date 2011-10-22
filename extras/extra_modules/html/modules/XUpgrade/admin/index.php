<?php

require_once "../../../mainfile.php";
require_once XOOPS_ROOT_PATH . "/header.php";

$root =& XCube_Root::getSingleton();
$root->mController->executeForward($root->mController->getHelpViewUrl($root->mContext->mModule->mXoopsModule));

require_once XOOPS_ROOT_PATH . "/footer.php";

?>
