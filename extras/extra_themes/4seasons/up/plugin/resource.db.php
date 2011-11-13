<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * Version:  for HD1.0
 * -------------------------------------------------------------
 */

function smarty_resource_db_systemTpl($tpl_name)
{
    // Replace Legacy System Template name to Legacy Module Template name
    static $patterns = null;
    static $replacements = null;
    if (!$patterns) {
        $root=&XCube_Root::getSingleton();
        $systemTemplates = explode(',',$root->getSiteConfig('Legacy_RenderSystem','SystemTemplate',''));
        $prefix = $root->getSiteConfig('Legacy_RenderSystem','SystemTemplatePrefix','legacy');
        $patterns = preg_replace('/^\s*([^\s]*)\s*$/e', '"/".preg_quote("\1","/")."/"', $systemTemplates);
        $replacements = preg_replace('/^\s*system_([^\s]*)\s*/', $prefix.'_\1', $systemTemplates);
    }
    if ($patterns) {
        $tpl_name = preg_replace($patterns, $replacements,$tpl_name);
    }
    return $tpl_name;
}

function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty)
{
	$tpl_name = smarty_resource_db_systemTpl($tpl_name);
	if ( !$tpl = smarty_resource_db_tplinfo( $tpl_name ) ) {
		return false;
	}
	if ( is_object( $tpl ) ) {
		$tpl_source = $tpl->getVar( 'tpl_source', 'n' );
	} else {
		$tpl_source = file_get_contents( $tpl ) ;
	}
	return true;
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	$tpl_name = smarty_resource_db_systemTpl($tpl_name);
	if ( !$tpl = smarty_resource_db_tplinfo( $tpl_name ) ) {
		return false;
	}
	if ( is_object( $tpl ) ) {
		$tpl_timestamp = $tpl->getVar( 'tpl_lastmodified', 'n' );
	} else {
		$tpl_timestamp = filemtime( $tpl );
	}
	return true;
}

function smarty_resource_db_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_db_trusted($tpl_name, &$smarty)
{
    // not used for templates
}

// return object(XoopsTplfile) or string(filepath)
function smarty_resource_db_tplinfo( $tpl_name )
{
	static $cache = array();
	global $xoopsConfig;

	// 1st, check the cache
	if ( isset( $cache[$tpl_name] ) ) {
		return $cache[$tpl_name];
	}

	$tplset = isset( $xoopsConfig['template_set'] ) ? $xoopsConfig['template_set']: 'default' ;
	$theme = isset( $xoopsConfig['theme_set'] ) ? $xoopsConfig['theme_set'] : 'default';

	// 2nd, check templates under themes/(theme)/templates/ (file template)
	$filepath = XOOPS_THEME_PATH . '/' . $theme . '/templates/' . $tpl_name ;
	if ( file_exists( $filepath ) ) {
		return $cache[$tpl_name] = $filepath ;
	}

	// 3rd, check templates under themes/(theme)/templates/(trust based template)
	@list( $dirname , $base_tpl_name ) = explode( '_' , $tpl_name , 2 ) ;
	$mytrustdirname = '' ;
	@include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ;
	if( $mytrustdirname && $base_tpl_name ) {
		$filepath = XOOPS_THEME_PATH . '/' . $theme . '/templates/' . $mytrustdirname . '/' . $base_tpl_name ;
		if ( file_exists( $filepath ) ) {
			return $cache[$tpl_name] = $filepath ;
		}
	}

	// 4th, find a DB template of the selected tplset
	$tplfile_handler =& xoops_gethandler('tplfile');
	$tplobj = $tplfile_handler->find( $tplset, null, null, null, $tpl_name, true);	if ( empty( $tplobj ) ) {
		// 5th, find a DB template in default tplset
		$tplobj = $tplfile_handler->find( 'default', null, null, null, $tpl_name, true);
		if( empty( $tplobj ) ) return false ;
	}
	return $cache[$tpl_name] = $tplobj[0];
}


?>
