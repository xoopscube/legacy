<?php

// RSS
if( @$_GET['page'] == 'rss' ) {
	$d3forum_output_rss = true ;
	$GLOBALS['xoopsUser'] = false ;
}

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;

// branches (TODO viewallforum)
if( ! empty( $_GET['post_id'] ) ) {
	include dirname(dirname(__FILE__)).'/include/viewpost.php' ;
	$d3forum_output_rss = false ;
} else if( ! empty( $_GET['topic_id'] ) ) {
	include dirname(dirname(__FILE__)).'/include/listposts.php' ;
	$d3forum_output_rss = false ;
} else if( ! empty( $_GET['forum_id'] ) ) {
	include dirname(dirname(__FILE__)).'/include/listtopics.php' ;
} else if( ! empty( $_GET['cat_id'] ) ) {
	include dirname(dirname(__FILE__)).'/include/listforums.php' ;
} else if( isset( $_GET['cat_ids'] ) ) {
	include dirname(dirname(__FILE__)).'/include/listtopics_over_categories.php' ;
} else {
	include dirname(dirname(__FILE__)).'/include/listcategories.php' ;
	$d3forum_output_rss = false ;
}


// form elements or javascripts for anti-SPAM
if( d3forum_common_is_necessary_antispam( $xoopsUser , $xoopsModuleConfig ) ) {
	$antispam_obj =& d3forum_common_get_antispam_object( $xoopsModuleConfig ) ;
	$antispam4assign = $antispam_obj->getHtml4Assign() ;
} else {
	$antispam4assign = array() ;
}


$xoopsTpl->assign(
	array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'mod_config' => $xoopsModuleConfig ,
		'xoops_config' => $xoopsConfig ,
		'uid' => $uid ,
		'postorder' => $postorder ,
		'icon_meanings' => $d3forum_icon_meanings ,
		'antispam' => $antispam4assign ,
		'forum_jumpbox_options' => d3forum_make_jumpbox_options( $mydirname , $whr_read4cat , $whr_read4forum , @$forum_row['forum_id'] ) ,
		'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

if( ! empty( $d3forum_output_rss ) ) {
	// RSS 2.0
	if( function_exists( 'mb_http_output' ) ) mb_http_output( 'pass' ) ;
	if( _CHARSET != 'UTF-8' ) {
		$data = $xoopsTpl->get_template_vars() ;
		d3forum_common_utf8_encode_recursive( $data ) ;
		$xoopsTpl->assign( $data ) ;
		if( empty( $_GET['forum_id'] ) ) {
			$rss = array( 'title' => $data['pagetitle'] , 'query' => 'cat_ids='.$data['cat_ids'] , 'desc' => '' , 'category_title' => '' ) ;
		} else {
			$rss = array( 'title' => $data['forum']['title'] , 'query' => 'forum_id='.$data['forum']['id'] , 'desc' => $data['forum']['desc'] , 'category_title' => $data['category']['title'] ) ;
		}
		$xoopsTpl->assign( 'rss' , $rss ) ;
	}
	header( 'Content-Type:text/xml; charset=utf-8' ) ;
	$xoopsTpl->display( 'db:'.$mydirname.'_independent_rss20_listtopics.html' ) ;
	exit ;
} else {
	// display
	include XOOPS_ROOT_PATH.'/footer.php';
}

?>