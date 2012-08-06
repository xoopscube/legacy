<?php 

define( 'FCK_IS_BROWSER_CONNECTOR' , 1 ) ;

// for XOOPS
require '../../../../../../mainfile.php' ;

require_once dirname(__FILE__).'/functions.php' ;
if( file_exists( dirname(__FILE__).'/config_and_auth.inc.php' ) ) {
	include dirname(__FILE__).'/config_and_auth.inc.php' ;
} else {
	include dirname(__FILE__).'/config_and_auth.inc.dist.php' ;
}

// Get the main request informaiton.
$sCommand = preg_replace( '?[^0-9a-zA-Z_/-]?' , '' , @$_GET['Command'] ) ;
$sCurrentFolder = preg_replace( '?[^0-9a-zA-Z_/-]?' , '' , @$_GET['CurrentFolder'] ) ;
$sType = @$_GET['Type'] ;
if( ! in_array( $sType , array('File','Image','Flash','Media') ) ) $sType = 'Image' ;

// Check the current folder syntax (must begin and start with a slash).
if( substr( $sCurrentFolder , -1 ) !== '/' ) $sCurrentFolder .= '/' ;
if( substr( $sCurrentFolder , 0 , 1 ) !== '/' ) $sCurrentFolder = '/' . $sCurrentFolder ;

// Execute the required command.
switch ( $sCommand )
{
	case 'FileUpload' :
		FileUpload( $sCurrentFolder ) ;
		break ;
	case 'DeleteFile' :
		CreateXmlHeader( 'DeleteFile' , $sCurrentFolder ) ;
		DeleteFile( $sCurrentFolder , $sType ) ;
		CreateXmlFooter() ;
		break ;
	case 'GetFoldersAndFiles' :
		CreateXmlHeader( 'GetFoldersAndFiles' , $sCurrentFolder ) ;
		GetFoldersAndFiles( $sCurrentFolder , $sType ) ;
		CreateXmlFooter() ;
		break ;
	case 'CreateFolder' :
		CreateXmlHeader( 'CreateFolder' , $sCurrentFolder ) ;
		CreateFolder( $sCurrentFolder , $sType ) ;
		CreateXmlFooter() ;
		break ;
	default :
	case 'GetFolders' :
		CreateXmlHeader( 'GetFolders' , $sCurrentFolder ) ;
		GetFolders( $sCurrentFolder , $sType ) ;
		CreateXmlFooter() ;
		break ;
}

?>