<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     d3forum_comment
 * Version:  1.0
 * Date:     Oct 16, 2006
 * Author:   GIJOE
 * Purpose:  
 * Input:    
 * 
 * Examples: {d3forum_comment forum_dirname=d3forum forum_id=1}
 * -------------------------------------------------------------
 */
function smarty_function_d3forum_comment($params, &$smarty)
{
	// transitional from 'dirname' -> 'forum_dirname'
	$params['forum_dirname'] = @$params['forum_dirname'] . @$params['dirname'] ;

	$forum_dirname = ! empty( $params['forum_dirname'] ) ? $params['forum_dirname'] : @$GLOBALS['xoopsModuleConfig']['d3forum_comment_dirname'] ;
	$forum_id = ! empty( $params['forum_id'] ) ? intval( $params['forum_id'] ) : intval( @$GLOBALS['xoopsModuleConfig']['d3forum_comment_forum_id'] ) ;

	if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $forum_dirname ) || $forum_id <= 0 || ! file_exists( XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ) ) {
		echo "<p>d3forum_comment does not set properly.</p>" ;
	} else {
		require_once( XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ) ;
		d3forum_display_comment( $forum_dirname , $forum_id , $params ) ;
	}
}

/* vim: set expandtab: */

?>
