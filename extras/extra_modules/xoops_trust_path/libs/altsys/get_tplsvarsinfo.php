<?php
// ------------------------------------------------------------------------- //
//                        get_tplsvarsinfo.php  (altsys)                     //
//                      - XOOPS templates admin module -                     //
//                        GIJOE <http://www.peak.ne.jp/>                     //
// ------------------------------------------------------------------------- //

error_reporting( 0 ) ;

include_once dirname(__FILE__).'/include/gtickets.php' ;
include_once dirname(__FILE__).'/include/altsys_functions.php' ;


// this page can be called only from altsys
if( $xoopsModule->getVar('dirname') != 'altsys' ) die( 'this page can be called only from altsys' ) ;


// language file
altsys_include_language_file( 'compilehookadmin' ) ;


$dw_snippets_dirname = 'files' ;
$site_name = @$_SERVER['HTTP_HOST'] ;
if( ! preg_match( '/^[0-9A-Za-z._-]+$/' , $site_name ) ) {
	$site_name = 'xoops_site' ;
}


//
// FUNCTIONS
//

function convert_array2info_recursive( $var_name , $var_value , $sum_array_name )
{
	switch( gettype( $var_value ) ) {
		case 'array' :
			foreach( $var_value as $key => $val ) {
				if( gettype( $key ) == 'integer' ) {
					$GLOBALS[ $sum_array_name ][ $var_name ] = '(array)' ;
					continue ;
				}
				convert_array2info_recursive( $var_name.'.'.$key , $val , $sum_array_name ) ;
			}
			return ;
		case 'string' :
			$GLOBALS[ $sum_array_name ][ $var_name ] = $var_value ;
			return ;
		case 'boolean' :
		case 'integer' :
		case 'float' :
		case 'double' :
			$GLOBALS[ $sum_array_name ][ $var_name ] = (string)$var_value ;
			return ;
		case 'null' :
			$GLOBALS[ $sum_array_name ][ $var_name ] = '(null)' ;
			return ;
		case 'object' :
			$GLOBALS[ $sum_array_name ][ $var_name ] = '(object)' ;
			return ;
		default :
			return ;
	}
}


function get_mxi_body( $mxi_name , $file_entries )
{
	global $site_name ;

	return '<macromedia-extension name="XOOPS-'.$site_name.' '.$mxi_name.'" version="1.0" type="Suite" requires-restart="true">
	<products>
		<product name="Dreamweaver" version="6" primary="true" />
	</products>
	<author name="GIJOE"/>
	<license-agreement><![CDATA[

XoopsDWSnipettets is published under the CC-GNU LGPL
http://creativecommons.org/licenses/LGPL/2.1/

(C) 2006 GIJOE http://www.peak.ne.jp/xoops/

	]]></license-agreement>
	<description><![CDATA[

Usable template variables in '.$mxi_name.'

	]]></description>
	<ui-access><![CDATA[

Available template-variables.
For more information:
http://www.peak.ne.jp/xoops/

	]]></ui-access>
	<files>
'.$file_entries.'
	</files>
	<configuration-changes>
	</configuration-changes>
</macromedia-extension>' ;

}



//
// TOTAL STAGE
//

$tplsvarsinfo_mod_tpl = array() ;
$tplsvarsinfo_total = array() ;

if( $handler = opendir( XOOPS_COMPILE_PATH . '/' ) ) {
	while( ( $file = readdir( $handler ) ) !== false ) {

		// skip files other than tplsvars_* files
		if( substr( $file , 0 , 9 ) !== 'tplsvars_' ) continue ;

		// 'tplsvars_'.(randomized 4byte).'_'.(tpl_file)
		$tpl_name = substr( $file , 14 ) ;
		if( ! preg_match( '/^[%0-9A-Za-z._-]+$/' , $tpl_name ) ) continue ;
		$file_path = XOOPS_COMPILE_PATH . '/' . $file ;
		$file_body = implode( '' , file( $file_path ) ) ;
		$tplsvars = @unserialize( $file_body ) ;
		if( ! is_array( $tplsvars ) ) $tplsvars = array() ;
		$GLOBALS['tplsvarsinfo'] = array() ;
		convert_array2info_recursive( '' , $tplsvars , 'tplsvarsinfo' ) ;
		if( strstr( $tpl_name , '%' ) ) {
			$mod_name = 'theme_etc' ;
		} else {
			list( $mod_name ) = explode( '_' , $tpl_name ) ;
		}
		$tplsvarsinfo_mod_tpl[$mod_name][$tpl_name] = $tplsvarsinfo ;
		$tplsvarsinfo_total = array_merge( $tplsvarsinfo_total , $tplsvarsinfo ) ;
	}
} else {
	die( 'XOOPS_COMPILE_PATH cannot be opened' ) ;
}

if( empty( $tplsvarsinfo_total ) ) {
	die( _TPLSADMIN_ERR_NOTPLSVARSINFO ) ;
}

//
// FOR DREAM WEAVER
//

$snippet_format = '<?xml version="1.0" encoding="utf-8"?>
<snippet name = "%1$s" description = "%2$s" preview="code" type="block">
<insertText location="beforeSelection">
<![CDATA[<{$%1$s}>]]>
</insertText>
<insertText location="afterSelection"><![CDATA[]]>
</insertText>
</snippet>' ;


if( ! empty( $_POST['as_dw_extension_zip'] ) ) {
	require_once XOOPS_ROOT_PATH.'/class/zipdownloader.php' ;
	$downloader = new XoopsZipDownloader();
	$do_download = true ;
} else if( ! empty( $_POST['as_dw_extension_tgz'] ) ) {
	require_once XOOPS_ROOT_PATH.'/class/tardownloader.php' ;
	$downloader = new XoopsTarDownloader();
	$do_download = true ;
}

if( ! empty( $do_download ) ) {
	// make files for each tplsvars
	foreach( $tplsvarsinfo_total as $key => $val ) {
		$name = substr( $key , 1 ) ;
		$description = htmlspecialchars( xoops_utf8_encode( xoops_substr( $val , 0 , 256 ) ) , ENT_QUOTES ) ;
		$snippet_body = sprintf( $snippet_format , $name , $description ) ;

		$file_name = strtr( $key , '.' , '_' ) . '.csn' ;
		$downloader->addFileData( $snippet_body , $dw_snippets_dirname.'/'.$file_name ) ;
	}

	// make a mxi file per module
	foreach( $tplsvarsinfo_mod_tpl as $mod_name => $tplsvarsinfo_tpl ) {
		$file_entries = '' ;
		foreach( $tplsvarsinfo_tpl as $tpl_name => $tplsvarsinfo ) {
			foreach( $tplsvarsinfo as $key => $val ) {
				$name = substr( $key , 1 ) ;
				$file_name = strtr( $key , '.' , '_' ) . '.csn' ;
				$file_entries .= "\t\t".'<file name="'.$dw_snippets_dirname.'/'.$file_name.'" destination="$Dreamweaver/Configuration/Snippets/XOOPS-'.$site_name.'/'.$tpl_name.'" />'."\n" ;
			}
		}
		$mxi_body = get_mxi_body( $mod_name , $file_entries ) ;
		$downloader->addFileData( $mxi_body , $mod_name.'.mxi' ) ;
	}

	echo $downloader->download( 'tplsvarsinfo' , true ) ;
}

?>
