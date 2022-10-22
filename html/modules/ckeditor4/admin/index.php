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
	$pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id=';
	$help = '<li><a href="' . XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=ckeditor4">' . _HELP . '</a></li>';
} else {
	$pref = XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=';
	$help = '';
}
?>

<h3>CKEditor 4 for XCL</h3>

<hr>

<ul>
	<li><a href="<?php echo $pref . $mid ?>"><?php echo _PREFERENCES ?></a></li>
	<?php echo $help ?>
</ul>

<?php
require_once XOOPS_ROOT_PATH . "/footer.php";
