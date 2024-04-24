<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once "../mainfile.php";

$error = false;
$g_webmasters = isset($g_webmasters) ? intval($g_webmasters) : 0;
$g_users = isset($g_users) ? intval($g_users) : 0;
$g_anonymous = isset($g_anonymous) ? intval($g_anonymous) : 0;

if (empty($g_webmasters) || empty($g_users) || empty($g_anonymous)) {
    $error = true;
} else {
    include_once "./class/mainfilemanager.php";
    $title = _INSTALL_L88;
    $mm = new mainfile_manager("../mainfile.php");
    $mm->setRewrite('XOOPS_GROUP_ADMIN', $g_webmasters);
    $mm->setRewrite('XOOPS_GROUP_USERS', $g_users);
    $mm->setRewrite('XOOPS_GROUP_ANONYMOUS', $g_anonymous);

    $ret = $mm->doRewrite();
    if (!$ret) {
        $content = _INSTALL_L60;
        include './install_tpl.php';
        exit();
    }
}
if (false != $error) {
    $b_back = [];
    $content = _INSTALL_L162;
    include './install_tpl.php';
    //break;
}

include_once './class/dbmanager.php';

$dbm = new db_manager;
if (!$dbm->query("ALTER TABLE ".$dbm->prefix("newblocks")." ADD dirname VARCHAR(50) NOT NULL, ADD func_file VARCHAR(50) NOT NULL, ADD show_func VARCHAR(50) NOT NULL, ADD edit_func VARCHAR(50) NOT NULL")) {
}
$result = $dbm->queryFromFile('./sql/upgrade/'.((XOOPS_DB_TYPE === 'mysqli')? 'mysql' : XOOPS_DB_TYPE).'.structure.sql');
$content = $dbm->report();

if (!$result) {
    $content .= "<p>"._INSTALL_L135."</p>\n";
    $b_back = [];
} else {
    $content .= "<p>"._INSTALL_L136."</p>\n";
    $b_next = ['updateConfig', _INSTALL_L14];
}

include './install_tpl.php';
