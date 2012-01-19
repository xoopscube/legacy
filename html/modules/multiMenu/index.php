<?php
require '../../mainfile.php';
include XOOPS_ROOT_PATH."/header.php";
/************************************************* Reserved Process
$mydirname = basename( dirname( __FILE__ ) ) ;
$mymodpath = XOOPS_ROOT_PATH."/modules/$mydirname" ;
$model = isset($_GET['action']) ? htmlspecialchars($_GET['action'],ENT_QUOTES) : "categoryView";
$model = isset($_POST['action']) ? htmlspecialchars($_POST['action'],ENT_QUOTES) : $model;
$ctrl = isset($_GET['ctrl']) ? $model . "_" . htmlspecialchars($_GET['ctrl'],ENT_QUOTES) : "";
$ctrl = isset($_POST['ctrl']) ? $model . "_" . htmlspecialchars($_POST['ctrl'],ENT_QUOTES) : $ctrl;
***************************************************/
/*
 * Model
 */
/************************************************* Reserved Process
$myclass = $mymodpath . "/class/" . $model . ".php";
require( $myclass );
$handler = new ActionHandler($ctrl);
if ( !$ret = $handler->load() ){
	echo $handler->debug(); die;
}
***************************************************/
/*
 * Ctrl
 */
/************************************************* Reserved Process
if ( $ctrl ){
	if ( !$ret = $handler->$ctrl() ){
		echo $handler->debug(); die;
	}
}
***************************************************/
/*
 * View
 */
/************************************************* Reserved Process
$handler->assignRecords();
$xoopsOption['template_main'] = $mydirname ."_". $model . ".html" ;
***************************************************/
include( XOOPS_ROOT_PATH.'/footer.php' ) ;
?>