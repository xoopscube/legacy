<?php

/* tplsadmin compiled cache hookings */

// save assigned variables for the template
function tplsadmin_save_tplsvars( $file , $smarty )
{
	$tplsvars_file = 'tplsvars_' ;
	$tplsvars_file .= substr( md5( substr( XOOPS_DB_PASS , 0 , 4 ) ) , 0 , 4 ) . '_' ;
	if( strncmp( $file , 'db:' , 3 ) === 0 ) {
		$tplsvars_file .= substr( $file , 3 ) ;
	} else if( strncmp( $file , 'file:' , 5 ) === 0 ) {
		$tplsvars_file .= strtr( substr( $file , 5 ) , '/' , '%' ) ;
	} else {
		$tplsvars_file .= strtr( $file , '/' , '%' ) ;
	}

	if( $fw = @fopen( XOOPS_COMPILE_PATH.'/'.$tplsvars_file , 'x' ) ) {
		fwrite( $fw , serialize( $smarty->_tpl_vars ) ) ;
		fclose( $fw ) ;
		return true ;
	}
	return false ;
}


?>