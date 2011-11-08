<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/include/common_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/blocks/list.php' ;

function smarty_function_pico_ef_list( $params , &$smarty )
{
	$mydirname = @$params['dir'] . @$params['dirname'] ;
	$cat_ids = @$params['id'] . @$params['cat_id'] ;
	$sortby = empty( $params['sortby'] ) ? '' : $params['sortby'] ;
	$order = empty( $params['order'] ) ? '' : $params['order'] ;
	$limit_params = @$params['limit'] ;
	$template = @$params['template'] ;
	$var_name = @$params['item'] . @$params['assign'] ;

	//errors
	if( empty( $mydirname ) ) $mydirname = $smarty->get_template_vars( 'mydirname' ) ;
	if( empty( $mydirname ) ) {
		echo 'error '.__FUNCTION__.' [specify dirname]';
		return ;
	}
	$error_vals = array( 'created_time_formatted', 'modified_time_formatted', 'expiring_time_formatted' ) ;
	if( in_array( $sortby , $error_vals ) ) {
		echo 'error '.__FUNCTION__.' [please use unixtime format]';
		return ;
	}

	//fetch and unserialize
	require_once XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/blocks.php' ;
	$contents = b_pico_list_show( array( $mydirname , $cat_ids , '' , $limit_params , $template , 'disable_renderer' => true ) ) ;

	$default_vals = array( 'link', 'poster_uname', 'modifier_uname', 'votes_avg', 'subject', 'body', 'tags_array', 'cat_title', 'can_vote', 'id', 'created_time_formatted', 'modified_time_formatted', 'expiring_time_formatted', 'subject_raw', 'body_raw', 'isadminormod', 'public', 'can_read', 'can_readfull', 'can_edit', 'can_delete', 'content_id', 'permission_id', 'vpath', 'cat_id', 'weight', 'created_time', 'modified_time', 'expiring_time', 'last_cached_time', 'poster_uid', 'poster_ip', 'modifier_uid', 'modifier_ip', 'subject_waiting', 'locked', 'visible', 'approval', 'use_cache', 'allow_comment', 'show_in_navi', 'show_in_menu', 'viewed', 'votes_sum', 'votes_count', 'comments_count', 'htmlheader', 'htmlheader_waiting', 'body_waiting', 'body_cached', 'filters', 'tags', 'extra_fields', 'redundants', 'for_search' ) ;

	foreach ( $contents['contents'] as $k => $v ){
		$unserialized_ef = pico_common_unserialize($v['extra_fields']);
		$contents['contents'][$k]['extra_fields_items'] = $unserialized_ef ;
		if( in_array( $sortby , $default_vals ) ){
			$contents['contents'][$k]['extra_fields_sortby'] = $v[$sortby] ;
		}elseif( strpos( $sortby,'dateof_' ) === 0 ){
			$udate = pico_common_get_server_timestamp( strtotime( $unserialized_ef[$sortby] ) ) ;
			$contents['contents'][$k]['extra_fields_sortby'] = $udate ;
		}else{
			$contents['contents'][$k]['extra_fields_sortby'] = $unserialized_ef[$sortby] ;
		}
		$contents['contents'][$k]['extra_fields_count'] = count( $unserialized_ef ) ;
	}
//	$contents['contents']['count'] = count( $contents['contents'] ) ;

	//sort
	foreach ( $contents['contents'] as $key => $row ) {
		$extra_fields_sortby[$key] = $row['extra_fields_sortby'] ;
	}
	if( $order == 'SORT_DESC' || $order == 'DESC'  ){
		array_multisort( $extra_fields_sortby, SORT_DESC, $contents['contents'] );
	}else{
		array_multisort( $extra_fields_sortby, SORT_ASC, $contents['contents'] );
	}

	// assign or display
	if( $var_name ) {
		$smarty->assign( $var_name , $contents['contents'] ) ;
		$smarty->assign( $var_name.'_count' , count( $contents['contents'] ) ) ;
	} else {
		echo '<pre>' ;
		var_dump( @$contents['contents'] ) ;
		echo '</pre>' ;
	}
}
?>
