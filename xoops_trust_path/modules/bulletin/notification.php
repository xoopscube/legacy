<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

global $xoopsConfig;

// language file
$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;
if( file_exists( "$mydirpath/language/$language/mail_template/" ) ) {
	// user customized language file
	$event_info['mail_template_dir'] = "$mydirpath/language/$language/mail_template/" ;
} else if( file_exists( "$mytrustdirpath/language/$language/mail_template/" ) ) {
	// default language file
	$event_info['mail_template_dir'] = "$mytrustdirpath/language/$language/mail_template/";
} else {
	// fallback english
	$event_info['mail_template_dir'] = "$mytrustdirpath/language/english/mail_template/";
}

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return bulletin_notify_base( $category, $item_id, "'.$mydirname.'" ) ;
}
' ) ;

if( ! function_exists( 'bulletin_notify_base' ) ) {


function bulletin_notify_base($category, $item_id, $mydirname)
{
	global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

	if (empty($xoopsModule) || $xoopsModule->getVar("dirname") != $mydirname) {	
		$module_handler =& xoops_gethandler("module");
		$module =& $module_handler->getByDirname($mydirname);
		$config_handler =& xoops_gethandler("config");
		$config =& $config_handler->getConfigsByCat(0,$module->getVar("mid"));
	} else {
		$module =& $xoopsModule;
		$config =& $xoopsModuleConfig;
	}

	if ($category=="global") {
		$item["name"] = "";
		$item["url"] = "";
		return $item;
	}

	global $xoopsDB;

	if ($category=="story") {
		// Assume we have a valid story id
		$sql = "SELECT title FROM ".$xoopsDB->prefix($mydirname."_stories") . " WHERE storyid = " . intval($item_id);
		$result = $xoopsDB->query($sql); // TODO: error check
		$result_array = $xoopsDB->fetchArray($result);
		$item["name"] = $result_array["title"];
		$item["url"] = XOOPS_URL . "/modules/" .$mydirname. "/index.php?page=article&amp;storyid=" . intval($item_id);
		return $item;
	}
}

}
?>