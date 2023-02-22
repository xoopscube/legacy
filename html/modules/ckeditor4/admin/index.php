<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/ckeditor4/class/Ckeditor4Utiles.class.php';

$mid = Ckeditor4_Utils::getMid();
if (defined('LEGACY_BASE_VERSION')) {
    $dash = XOOPS_URL . '/admin.php';
	$pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id=';
	$help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=ckeditor4';
}
?>
    <div class="adminnavi">
        <a href="<?php echo $dash ?>">Dashboard</a>
        »» <span class="adminnaviTitle" aria-current="page"><a href="./index.php">CKEditor</a></span>
    </div>

    <nav class="adminavi">
        <a href="<?php echo $pref . $mid ?>" class="adminavi-item"><?php echo _PREFERENCES ?></a>
        <a href="<?php echo $help ?>" class="adminavi-item"><?php echo _HELP ?></a>
    </nav>
<h3>CKEditor 4 for XCL</h3>

<?php
require_once XOOPS_ROOT_PATH . "/footer.php";
