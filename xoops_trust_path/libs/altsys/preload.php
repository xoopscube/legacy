<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
$root = XCube_Root::getSingleton();
//admin page
if ($root->mController->_mStrategy) {
    if (strtolower(get_class($root->mController->_mStrategy)) == strtolower('Legacy_AdminControllerStrategy')) {
        include_once dirname(__FILE__).'/include/altsys_functions.php' ;
        // language file (modinfo.php)
        altsys_include_language_file('modinfo') ;
    }
}
// load altsys newly gticket class for other modules
require_once XOOPS_TRUST_PATH.'/libs/altsys/include/gtickets.php';
