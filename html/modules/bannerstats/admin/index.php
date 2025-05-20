<?php
require_once "../../../mainfile.php";
// require_once XOOPS_ROOT_PATH . "/include/cp_header.php";
require_once "../../../header.php";
// --- ADD THESE LINES TO LOAD YOUR CORE CLASS FILES ---
require_once XOOPS_MODULE_PATH . "/bannerstats/class/Banner.class.php";       // Defines Bannerstats_BannerObject and Bannerstats_BannerHandler
require_once XOOPS_MODULE_PATH . "/bannerstats/class/BannerClient.class.php"; // Defines Bannerstats_BannerclientObject and Bannerstats_BannerclientHandler
require_once XOOPS_MODULE_PATH . "/bannerstats/class/BannerFinish.class.php"; // Defines Bannerstats_BannerfinishObject and Bannerstats_BannerfinishHandler
// --- END ADDED LINES ---


require_once XOOPS_MODULE_PATH . "/bannerstats/class/ActionFrame.class.php";

$root =& XCube_Root::getSingleton();

// Check module admin permission
$moduleHandler =& xoops_gethandler('module');
$module =& $moduleHandler->getByDirname('bannerstats');
$currentUser =& $root->mContext->mXoopsUser;

if (!is_object($currentUser) || !$currentUser->isAdmin($module->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// Render header
$root->mController->executeHeader();

// Create ActionFrame and execute
$actionFrame = new Bannerstats_ActionFrame(true);
$actionFrame->execute($root->mController);

// Render footer
// $root->mController->executeFooter();
require_once "../../../footer.php";