<?php
require_once '../../../mainfile.php';
$root = XCube_Root::getSingleton();
$root->mController->executeForward($root->mContext->mModule->getAdminIndex());
?>