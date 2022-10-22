<?php

if ( ! defined( 'XOOPS_MODULE_PATH' ) ) {
	define( 'XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules' );
}
if ( ! defined( 'XOOPS_MODULE_URL' ) ) {
	define( 'XOOPS_MODULE_URL', XOOPS_URL . '/modules' );
}

$check_dir = [
	XOOPS_MODULE_PATH . '/' . $mydirname . '/cache',
	XOOPS_MODULE_PATH . '/' . $mydirname . '/cache/tmb',
	XOOPS_TRUST_PATH . '/uploads/xelfinder'
];

$dir_res = [];

foreach($check_dir as $dir){
	$dir = rtrim($dir, '/');
	if (is_writable($dir)) {
		$dir .= ' <span style="color:green;font-weight:bold;">OK</span>';
	} else {
		$dir .= ' <span style="color:red;font-weight:bold;">NG</span>';
	}
	$dir_res[] = $dir;
}

$dir_res = '<div class="tips"><ul><li>'.join('</li><li>', $dir_res).'</li></ul></div>';

if (isset($_POST) && ! empty($_POST['session_table_fix'])) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();
	$db->query('ALTER TABLE `'.$db->prefix('session').'` CHANGE `sess_data` `sess_data` MEDIUMBLOB NOT NULL');
}

include_once XOOPS_TRUST_PATH.'/modules/xelfinder/class/xoops_elFinder.class.php';
$xelf = new xoops_elFinder($mydirname);
$stype = $xelf->getSessionTableType();
if ($stype !== 'mediumblob' && $stype !== 'longblob') {
	$form = '<form method="post" style="display:inline;"><input type="hidden" name="session_table_fix" value="1"><input type="submit" value="Change"></form>';
	$sResult = '<div class="success">Session table type is "'.$stype.'" <span style="color:red;font-weight:bold;">Recomend change type to "mediumblob"</span><br>'.$form.'</div>>';
} else {
	$sResult = '<div class="success">Session table type is "'.$stype.'" <span style="color:green;font-weight:bold;">OK</span></div>';
}

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;

echo '<h2>'.$xoopsModule->getVar('name').'</h2>' ;
echo '<h3>Checking for directory and file write permissions</h3>';
echo $dir_res;
echo '<h3>Check Session table</h3>';
echo '<div>'.$sResult.'</div>';

xoops_cp_footer();
