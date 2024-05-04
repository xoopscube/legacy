<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    XCL 2.4.0
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

include __DIR__ . '/mymenu.php';
$xoopsTpl = new XoopsTpl();
$dbfile  = 'db:_custom_pico_index.html';
if (file_exists($dbfile)) {
    echo "The file $dbfile exists";
} else {
    echo "The file $dbfile does not exist";
}
//$test = $xoopsTpl->fetch($dbIndex);

if (defined('LEGACY_BASE_VERSION')) {
    $modname = $xoopsModule->getVar( 'name' ) ;
    $dash = XOOPS_URL . '/admin.php';
    $pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id=';
    $help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=ckeditor4';
}
echo '<h3>' . $xoopsModule->getVar( 'name' ) . '</h3>';
// echo $test;
?>
<h4>HTML template</h4>
    <p>echo $test;</p>

<?php
require_once XOOPS_ROOT_PATH . "/footer.php";
?>
