<?php

$field_defs = array(
	'pubtime' => 'time' ,
	'headline' => 'string' ,
	'link' => 'string' ,
	'description' => 'text' ,
	'content_encoded' => 'text' ,
) ;

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

// get field
$field = @$_GET['field'] ;
if( isset( $field_defs[ $field ] ) ) {

	// get value (from UTF-8 to IE)
	$value_utf8 = $myts->stripSlashesGPC( @$_POST['value'] ) ;
	$value4disp = d3pipes_common_convert_encoding_utf8toie( $mydirname , $value_utf8 ) ;
	$value = d3pipes_admin_disp2raw( $value4disp , $field_defs[ $field ] ) ;

	// get clipping
	$clipping_id = intval( @$_GET['clipping_id'] ) ;
	$clipping = d3pipes_common_get_clipping( $mydirname , $clipping_id ) ;

	if( $clipping ) {
		list( $data_serialized ) = $db->fetchRow( $db->query( "SELECT data FROM ".$db->prefix($mydirname."_clippings")." WHERE clipping_id=$clipping_id" ) ) ;
		$data = d3pipes_common_unserialize( $data_serialized ) ;
		$data[$field] = $value ;
		$db->query( "UPDATE ".$db->prefix($mydirname."_clippings")." SET `data`='".addslashes(serialize($data))."' WHERE clipping_id=$clipping_id" ) ;
		// This query can raise an error, but it's OK
		$db->query( "UPDATE ".$db->prefix($mydirname."_clippings")." SET `$field`='".addslashes($value)."' WHERE clipping_id=$clipping_id" ) ;
	}
}

while( ob_get_level() ) {
	ob_end_clean() ;
}
//	ini_set( 'default_encoding' , 'UTF-8' ) ;
//	echo htmlspecialchars( d3pipes_common_convert_encoding_ietoutf8( $mydirname , $value4disp ) , ENT_QUOTES ) ;
echo htmlspecialchars( $value4disp , ENT_QUOTES ) ;
if( ! 'ALTSYS_DONT_USE_ADMIN_IN_THEME' ) define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME' , 1 ) ;
unset( $xoopsUser ) ; // for preventing older admin_in_theme
exit ;

?>