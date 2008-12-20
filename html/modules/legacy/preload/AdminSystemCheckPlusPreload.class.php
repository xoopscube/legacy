<?php
/**
 AdminSystemCheckPlus Preload version 1.0 by wanikoo
 ( http://www.wanisys.net/ )
*/

if (!defined('XOOPS_ROOT_PATH')) exit();

//New language constants
if (!defined('_AD_LEGACY_XCLEGACYVERSION')) {
define('_AD_LEGACY_XCLEGACYVERSION', "XC Legacy Version");
}
if (!defined('_AD_LEGACY_XCVERSION')) {
define('_AD_LEGACY_XCVERSION', "XOOPS Cube Version");
}
if (!defined('_AD_LEGACY_SYSTEMINFO')) {
define('_AD_LEGACY_SYSTEMINFO', "Site/System Info");
}
if (!defined('_AD_LEGACY_PHPVERSION')) {
define('_AD_LEGACY_PHPVERSION', "PHP Version");
}
if (!defined('_AD_LEGACY_MYSQLVERSION')) {
define('_AD_LEGACY_MYSQLVERSION', "MYSQL Version");
}
if (!defined('_AD_LEGACY_OS')) {
define('_AD_LEGACY_OS', "Operating System");
}
if (!defined('_AD_LEGACY_SERVER')) {
define('_AD_LEGACY_SERVER', "Server");
}
if (!defined('_AD_LEGACY_USERAGENT')) {
define('_AD_LEGACY_USERAGENT', "User Agent");
}
if (!defined('_AD_LEGACY_PHPSETTING')) {
define('_AD_LEGACY_PHPSETTING', "PHP Setting");
}
if (!defined('_AD_LEGACY_PHPSETTING_SM')) {
define('_AD_LEGACY_PHPSETTING_SM', "Safe Mode");
}
if (!defined('_AD_LEGACY_PHPSETTING_DE')) {
define('_AD_LEGACY_PHPSETTING_DE', "Display Errors");
}
if (!defined('_AD_LEGACY_PHPSETTING_SOT')) {
define('_AD_LEGACY_PHPSETTING_SOT', "Short Open Tags");
}
if (!defined('_AD_LEGACY_PHPSETTING_FU')) {
define('_AD_LEGACY_PHPSETTING_FU', "File Uploads");
}
if (!defined('_AD_LEGACY_PHPSETTING_FU_UMAX')) {
define('_AD_LEGACY_PHPSETTING_FU_UMAX', "Upload Max File Size:");
}
if (!defined('_AD_LEGACY_PHPSETTING_FU_PMAX')) {
define('_AD_LEGACY_PHPSETTING_FU_PMAX', "Post Max Size:");
}
if (!defined('_AD_LEGACY_PHPSETTING_MQ')) {
define('_AD_LEGACY_PHPSETTING_MQ', "Magic Quotes");
}
if (!defined('_AD_LEGACY_PHPSETTING_RG')) {
define('_AD_LEGACY_PHPSETTING_RG', "Register Globals");
}
if (!defined('_AD_LEGACY_PHPSETTING_OB')) {
define('_AD_LEGACY_PHPSETTING_OB', "Output Buffering");
}
if (!defined('_AD_LEGACY_PHPSETTING_SAS')) {
define('_AD_LEGACY_PHPSETTING_SAS', "Session auto start");
}
if (!defined('_AD_LEGACY_PHPSETTING_XML')) {
define('_AD_LEGACY_PHPSETTING_XML', "XML enabled");
}
if (!defined('_AD_LEGACY_PHPSETTING_ZLIB')) {
define('_AD_LEGACY_PHPSETTING_ZLIB', "Zlib enabled");
}
if (!defined('_AD_LEGACY_PHPSETTING_MB')) {
define('_AD_LEGACY_PHPSETTING_MB', "Mbstring enabled");
}
if (!defined('_AD_LEGACY_PHPSETTING_ICONV')) {
define('_AD_LEGACY_PHPSETTING_ICONV', "Iconv available");
}
if (!defined('_AD_LEGACY_PHPSETTING_ON')) {
define('_AD_LEGACY_PHPSETTING_ON', "On");
}
if (!defined('_AD_LEGACY_PHPSETTING_OFF')) {
define('_AD_LEGACY_PHPSETTING_OFF', "Off");
}

//This preload is just an example 
//to show you how to design/decorate your html/admin.php (front page of admin-section) using a preload!  
//anyway...
//all contents of this preload will appear next to default system-check of html/admin.php
//please refer to /html/admin.php

//you can determine which should be displayed!
//display(1) or not display(0): Welcome message
if (!defined('XC_ADMINSYSTEMCHECK_WELCOME')) define('XC_ADMINSYSTEMCHECK_WELCOME', 1);
//display(1) or not display(0): Site/System Info
if (!defined('XC_ADMINSYSTEMCHECK_SYSTEMINFO')) define('XC_ADMINSYSTEMCHECK_SYSTEMINFO', 1);
//display(1) or not display(0): PHP Settings
if (!defined('XC_ADMINSYSTEMCHECK_PHPSETTING')) define('XC_ADMINSYSTEMCHECK_PHPSETTING', 1);
//display(1) or not display(0): Wating(pending) contents
if (!defined('XC_ADMINSYSTEMCHECK_WAITING')) define('XC_ADMINSYSTEMCHECK_WAITING', 0);
//display(1) or not display(0): Full PHP Info!
if (!defined('XC_ADMINSYSTEMCHECK_PHPINFO')) define('XC_ADMINSYSTEMCHECK_PHPINFO', 0);

class Legacy_AdminSystemCheckPlusPreload extends XCube_ActionFilter
{
	function preBlockFilter()
	{

	$root=&XCube_Root::getSingleton();
	$root->mDelegateManager->add("Legacypage.Admin.SystemCheck", "Legacy_AdminSystemCheckPlusPreload::SystemCheckPlus", XCUBE_DELEGATE_PRIORITY_NORMAL+1);

	}

	function SystemCheckPlus()
	{
		$root =& XCube_Root::getSingleton();
		////////////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_WELCOME) {
		//ex) 
		//direct output(0), output with legacy_dummy.html(1), output with legacy_admin_welcome.html(2)   
		$type = 2;
		//Umm...Just example!!
		//please customize it to design/decorate your html/admin.php
		
		if ( $type == 0 ) {
		
		$welcome = '<b>Welcome to XOOPS Cube Legacy!!</b><br />Have a nice time!!';
		echo $welcome;
		}//type0 if
		
		elseif ( $type == 1 ) {
		
		$welcome = '<b>Welcome to XOOPS Cube Legacy!!</b><br />Have a nice and happy time!!';
		$attributes = array();
		$attributes['dummy_content'] = $welcome;
		$template = XOOPS_LEGACY_PATH."/templates/legacy_dummy.html";
		Legacy_AdminSystemCheckPlusPreload::display_message($attributes, $template, $return = false);
		}//type1 if
		
		elseif ( $type == 2 ) {
		
		//you must prepare your own legacy_admin_welcome.html
		if ( file_exists(XOOPS_LEGACY_PATH . "/admin/templates/legacy_admin_welcome.html") ) {
		//it's just a example! please customize it!
		$welcome_title = 'Welcome Message!';
		$welcome_msg = array();
		$welcome_msg[] = 'Welcome to XOOPS Cube Legacy!!';
		$welcome_msg[] = 'Have a nice and happy time!!';
		$attributes = array();
		$attributes['title'] = $welcome_title;
		$attributes['messages'] = $welcome_msg;
		$template = XOOPS_LEGACY_PATH."/admin/templates/legacy_admin_welcome.html";
		Legacy_AdminSystemCheckPlusPreload::display_message($attributes, $template, $return = false);
		}//file_exists if
		}//type2 if

		}
		////////////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_SYSTEMINFO) {

		$systeminfo_message = array();

		$systeminfo_message[] = _AD_LEGACY_XCLEGACYVERSION." : ".XOOPS_VERSION;
		$systeminfo_message[] = _MD_AM_DTHEME." : ".$root->mContext->mXoopsConfig['theme_set'];
		$systeminfo_message[] = _MD_AM_DTPLSET." : ".$root->mContext->mXoopsConfig['template_set'];
		$systeminfo_message[] = _MD_AM_LANGUAGE." : ".$root->mContext->mXoopsConfig['language'];
		
		$debugmode = intval($root->mContext->mXoopsConfig['debug_mode']);
		if ( $debugmode == 0 ) {
		$systeminfo_message[] = _MD_AM_DEBUGMODE." : "._MD_AM_DEBUGMODE0;
		}
		elseif ( $debugmode == 1) {
		$systeminfo_message[] = _MD_AM_DEBUGMODE." : "._MD_AM_DEBUGMODE1;
		}
		elseif ( $debugmode == 2 ) {
		$systeminfo_message[] = _MD_AM_DEBUGMODE." : "._MD_AM_DEBUGMODE2;
		}
		elseif ( $debugmode == 3 ) {
		$systeminfo_message[] = _MD_AM_DEBUGMODE." : "._MD_AM_DEBUGMODE3;
		}

		$systemconfig = array();
		$systemconfig['phpversion'] = phpversion();
		$db = &$root->mController->getDB();
		$result = $db->query("SELECT VERSION()");
		list($mysqlversion) = $db->fetchRow($result);
		$systemconfig['mysqlversion'] = $mysqlversion; 
		$systemconfig['os'] = substr( php_uname(), 0, 7 );
		$systemconfig['server'] = $_SERVER['SERVER_SOFTWARE'];
		$systemconfig['useragent'] = $_SERVER['HTTP_USER_AGENT'];

		$systeminfo_message[] = _AD_LEGACY_OS." : ".$systemconfig['os'];
		$systeminfo_message[] = _AD_LEGACY_SERVER." : ".$systemconfig['server'];
		$systeminfo_message[] = _AD_LEGACY_USERAGENT." : ".$systemconfig['useragent'];
		$systeminfo_message[] = _AD_LEGACY_PHPVERSION." : ".$systemconfig['phpversion'];
		$systeminfo_message[] = _AD_LEGACY_MYSQLVERSION." : ".$systemconfig['mysqlversion'];

		xoops_result($systeminfo_message,_AD_LEGACY_SYSTEMINFO,'tips');

		}//systeminfo if
		
		
		/////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_PHPSETTING) {

		$phpsetting_message = array();

		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SM." : ".(ini_get('safe_mode')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_DE." : ".(ini_get('display_errors')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SOT." : ".(ini_get('short_open_tag')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_FU." : ".(ini_get('file_uploads')? _AD_LEGACY_PHPSETTING_ON." ( "._AD_LEGACY_PHPSETTING_FU_UMAX.ini_get('upload_max_filesize').", "._AD_LEGACY_PHPSETTING_FU_PMAX.ini_get('post_max_size')." )" : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_MQ." : ".(ini_get('magic_quotes_gpc')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_RG." : ".(ini_get('register_globals')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_OB." : ".(ini_get('output_buffering')? _AD_LEGACY_PHPSETTING_ON : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SAS." : ".ini_get('session.auto_start');
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_XML." : ".(extension_loaded('xml')? _YES : _NO);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_ZLIB." : ".(extension_loaded('zlib')? _YES : _NO);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_MB." : ".(extension_loaded('mbstring')? _YES : _NO);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_ICONV." : ".(function_exists('iconv')? _YES : _NO);

		xoops_result($phpsetting_message,_AD_LEGACY_PHPSETTING,'tips');

		}
		
		
		/////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_WAITING) {
		$modules = array();
    		XCube_DelegateUtils::call('Legacyblock.Wating.Show', new XCube_Ref($modules));
		$attributes = array();
		$attributes['block']['modules'] = $modules;
		$template = XOOPS_ROOT_PATH."/modules/legacy/templates/blocks/legacy_block_waiting.html";
		$result = Legacy_AdminSystemCheckPlusPreload::display_message($attributes, $template, $return = true);
		xoops_result($result, _MI_LEGACY_BLOCK_WAITING_NAME);
		}//waiting if
		
		
		////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_PHPINFO) {
		//some code borrowed from joomla
		ob_start();
		phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
		$phpinfo = ob_get_contents();
		ob_end_clean();
		preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
		$output = preg_replace('#<table#', '<table class="outer""', $output[1][0]);
		$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
		$output = preg_replace('#border="0" cellpadding="3" width="600"#', '', $output);
		$output = preg_replace('#<hr />#', '', $output);
		$output = preg_replace('#class="e"#', 'class="even"', $output);
		$output = preg_replace('#class="h"#', 'class="odd"', $output);
		$output = preg_replace('#class="v"#', 'class="even"', $output);
		$output = preg_replace('#class="p"#', 'class="odd"', $output);
		$output = str_replace('<div class="center">', '', $output);
		$output = str_replace('</div>', '', $output);	
		$attributes = array();
		$attributes['dummy_content'] = $output;
		$template = XOOPS_ROOT_PATH."/modules/legacy/templates/legacy_dummy.html";
		Legacy_AdminSystemCheckPlusPreload::display_message($attributes, $template, $return = false);
		}//phpinfo if
		/////////////////////////////////
	}

	function display_message($attributes = array(), $template="", $return = false)
	{
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem($root->mContext->mBaseRenderSystemName);
		$renderTarget =& $renderSystem->createRenderTarget('main');
		$renderTarget->setAttribute('legacy_module', 'legacy');
		$renderTarget->setTemplateName($template);
		foreach (array_keys($attributes) as $attribute) {
		$renderTarget->setAttribute($attribute, $attributes[$attribute]);
		}
		$renderSystem->render($renderTarget);
		if ($return == true ) { 
		$ret = $renderTarget->getResult();
		return $ret;
		}
		else {
		print $renderTarget->getResult();
		}
	}

	
}

?>