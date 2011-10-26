<?php

define('_MD_PICO_FILTERS_XOOPSTPLINITWEIGHT',0);
define('_MD_PICO_FILTERS_XOOPSTPLISINSECURE',1); // only admins/moderators can use it

include_once XOOPS_ROOT_PATH.'/class/template.php' ;

function pico_xoopstpl( $mydirname , $text , $content4assign )
{
	global $xoopsTpl ;

	$tpl = new XoopsTpl() ;
	//$tpl->plugins_dir[] = dirname(dirname(__FILE__)).'/smarty_plugins' ;
	array_unshift( $tpl->plugins_dir , dirname(dirname(__FILE__)).'/smarty_plugins' ) ; // pico plugin has the first priority

	if( is_object( @$xoopsTpl ) ) {
		$tpl->assign( $xoopsTpl->get_template_vars() ) ;
		if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$root =& XCube_Root::getSingleton() ;
			$context =& $root->getContext();
			$themeName = $context->getThemeName();
			$tpl->assign('xoops_theme', $themeName);
			$tpl->assign('xoops_imageurl', XOOPS_THEME_URL . "/${themeName}/");
			$tpl->assign('xoops_themecss', xoops_getcss($themeName));
		}
	}
	$tpl->assign( 'mydirname' , $mydirname ) ;
	$tpl->assign( 'mod_url' , XOOPS_URL.'/modules/'.$mydirname ) ;
	$tpl->assign( 'content' , $content4assign ) ;

	// assign special session
	$session_prefix = $mydirname . '_' . @$content4assign['id'] . '_' ;
	$session4assign = array() ;
	foreach( array_keys( @$_SESSION ) as $index ) {
		if( strncmp( $index , $session_prefix , strlen( $session_prefix ) ) === 0 ) {
			$session4assign[ substr( $index , strlen( $session_prefix ) ) ] = $_SESSION[ $index ] ;
		}
	}
	$tpl->assign( 'session' , $session4assign ) ;

	$temp_file = XOOPS_COMPILE_PATH.'/'.substr(md5(XOOPS_DB_PREFIX.@$content4assign['id']),16).$mydirname.'_temp_resource' ;
	if( ! $text || $text != @file_get_contents( $temp_file ) ) {
		$fw = fopen( $temp_file , 'wb' ) ;
		fwrite( $fw , $text ) ;
		fclose( $fw ) ;
		$tpl->clear_compiled_tpl( 'file:'.$temp_file ) ;
	}
	$text = $tpl->fetch( 'file:'.$temp_file ) ;
//	unlink( $temp_file ) ;

	return $text ;
}

?>