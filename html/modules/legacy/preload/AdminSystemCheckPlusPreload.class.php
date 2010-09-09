<?php
/**
 AdminSystemCheckPlus Preload version 1.0 by wanikoo
 ( http://www.wanisys.net/ )
*/

if (!defined('XOOPS_ROOT_PATH')) exit();

//New language constants added to catalog
// (...)

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
//display(1) or not display(0): Waiting(pending) contents
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
		$systemconfig['server'] = xoops_getenv('SERVER_SOFTWARE');
		$systemconfig['useragent'] = xoops_getenv('HTTP_USER_AGENT');

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

		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SM." : ".(ini_get('safe_mode')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_MET." : ".(ini_get('max_execution_time')? ini_get('max_execution_time')." sec." : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_ML." : ".(ini_get('memory_limit')? ini_get('memory_limit')."b" : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_DE." : ".(ini_get('display_errors')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SOT." : ".(ini_get('short_open_tag')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_FU." : ".(ini_get('file_uploads')? _AD_LEGACY_PHPSETTING_ON." ( "._AD_LEGACY_PHPSETTING_FU_UMAX.ini_get('upload_max_filesize').", "._AD_LEGACY_PHPSETTING_FU_PMAX.ini_get('post_max_size')." )" : _AD_LEGACY_PHPSETTING_OFF);
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_MQ." : ".(ini_get('magic_quotes_gpc')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_RG." : ".(ini_get('register_globals')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_OB." : ".(ini_get('output_buffering')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_OBD." : ".(ini_get('open_basedir')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_UFO." : ".(ini_get('allow_url_fopen')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
		
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_DOM." : ".(extension_loaded('dom')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_EXIF." : ".(extension_loaded('exif')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_GTXT." : ".(extension_loaded('gettext')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_JSON." : ".(extension_loaded('json')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_XML." : ".(extension_loaded('xml')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_CRL." : ".(extension_loaded('curl')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_ZLIB." : ".(extension_loaded('zlib')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_SOAP." : ".(extension_loaded('soap')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_MB." : ".(extension_loaded('mbstring')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_ICONV." : ".(function_exists('iconv')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_GD." : ".(function_exists('gd_info')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>" );
	

		if( function_exists( 'gd_info' ) ) {
		$gd_info = gd_info() ;
		$phpsetting_message[] =  "GD Version: {$gd_info['GD Version']}" ;
		}
	
		if(function_exists('imagecreatetruecolor')) {
		$phpsetting_message[] = _AD_LEGACY_PHPSETTING_GD." Image create Truecolor" ;
		}

		xoops_result($phpsetting_message,_AD_LEGACY_PHPSETTING,'tips');

		}
		
		
		/////////////////////////////////////////
		if(XC_ADMINSYSTEMCHECK_WAITING) {
		$modules = array();
    		XCube_DelegateUtils::call('Legacyblock.Waiting.Show', new XCube_Ref($modules));
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
