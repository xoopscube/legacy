<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     d3forum_comment_postscount
 * Version:  1.0
 * Date:     Oct 16, 2006
 * Author:   GIJOE
 * Purpose:  
 * Input:    
 * 
 * Examples: {d3forum_comment_postscount dirname=d3forum forum_id=1}
 * -------------------------------------------------------------
 */

function smarty_function_d3forum_comment_postscount($params, &$smarty)
{
	$dirname = isset( $params['dirname'] ) ? $params['dirname'] : @$GLOBALS['xoopsModuleConfig']['d3forum_comment_dirname'] ;
	$forum_id = isset( $params['forum_id'] ) ? intval( $params['forum_id'] ) : intval( @$GLOBALS['xoopsModuleConfig']['d3forum_comment_forum_id'] ) ;

	if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $dirname ) || $forum_id <= 0 || ! file_exists( XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ) ) {
		echo "<p>d3forum_comment does not set properly.</p>" ;
	} else {
		require_once( XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ) ;
		d3forum_display_comment_topicscount( $dirname , $forum_id , $params , 'post' , $smarty ) ;
	}
}

/* vim: set expandtab: */

?>