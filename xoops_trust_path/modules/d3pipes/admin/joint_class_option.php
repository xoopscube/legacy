<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

$index = intval( $_GET['index'] ) ;
$option_value_utf8 = $myts->stripSlashesGPC( @$_GET['option_value'] ) ;
$option_value = d3pipes_common_convert_encoding_utf8toie( $mydirname , $option_value_utf8 ) ;

$all_joints = d3pipes_admin_fetch_joints( $mydirname ) ;
$disabled_option_input = '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($option_value,ENT_QUOTES).'" size="20" disabled="disabled" />' ;

@list( $joint_type , $joint_class ) = explode( '::' , $myts->stripSlashesGPC( @$_GET['type_class'] ) ) ;
if( empty( $joint_type ) || ! isset( $all_joints[ $joint_type ] ) ) die( $disabled_option_input ) ;
$valid_classes = d3pipes_admin_fetch_classes( $mydirname , $joint_type ) ;
if( ! isset( $valid_classes[ $joint_class ] ) ) die( $disabled_option_input ) ;

$obj =& d3pipes_common_get_joint_object( $mydirname , $joint_type , $joint_class ) ;

while( ob_get_level() ) {
	ob_end_clean() ;
}
@ini_set( 'default_charset' , 'UTF-8' ) ;
@header( 'Content-Type: text/html; charset=UTF-8' ) ;
echo d3pipes_common_convert_encoding_ietoutf8( $mydirname , $obj->renderOptions( $index , $option_value ) ) ;
if( ! defined( 'ALTSYS_DONT_USE_ADMIN_IN_THEME' ) ) define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME' , 1 ) ;
unset( $xoopsUser ) ; // for preventing older admin_in_theme
exit ;


?>