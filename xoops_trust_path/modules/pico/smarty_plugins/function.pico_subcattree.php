<?php

function smarty_function_pico_subcattree( $params , &$smarty )
{
	$mydirname = @$params['dir'] . @$params['dirname'] ;
	$cat_id = @$params['id'] + @$params['cat_id'] ;
	$var_name = @$params['item'] . @$params['assign'] ;

	if( empty( $var_name ) ) {
		echo 'error '.__FUNCTION__.' [specify item]';
		return ;
	}

	if( empty( $mydirname ) ) $mydirname = $smarty->get_template_vars( 'mydirname' ) ;
	if( empty( $mydirname ) ) {
		echo 'error '.__FUNCTION__.' [specify dirname]';
		return ;
	}

	$db =& Database::getInstance() ;
	$sql = "SELECT c.cat_redundants FROM ".$db->prefix($mydirname."_categories")." c WHERE c.cat_id=$cat_id" ;
	list( $redundants_serialized ) = $db->fetchRow( $db->query( $sql ) ) ;
	$redundants = pico_common_unserialize( $redundants_serialized ) ;
	if( empty( $redundants ) ) $redundants = array() ;

	$smarty->assign( $var_name , $redundants ) ;
}

?>