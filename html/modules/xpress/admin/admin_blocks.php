<?php 

include_once('./../../../include/cp_header.php');

global $xoopsModule;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$module_dir = $mydirname;
//$module_dir = $xoopsModule->getInfo('dirname');

if (file_exists(XOOPS_ROOT_PATH . '/modules/altsys/admin/index.php')){
	header("Location: ".XOOPS_URL."/modules/altsys/admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=$module_dir");	
} else if (file_exists(XOOPS_ROOT_PATH . '/modules/legacy/admin/index.php')){
	header("Location: ".XOOPS_URL."/modules/legacy/admin/index.php?action=BlockList");	
} else {
	header("Location: ".XOOPS_URL."/modules/system/admin.php?fct=blocksadmin&selmod=".$xoopsModule->getVar("mid"));
}
?>