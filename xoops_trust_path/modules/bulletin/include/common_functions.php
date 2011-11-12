<?php


function bulletin_get_submenu( $mydirname )
{
	static $submenus_cache ;

	if( ! empty( $submenus_cache[$mydirname] ) ) return $submenus_cache[$mydirname] ;

	$db =& Database::getInstance() ;
	$myts =& MyTextSanitizer::getInstance();

	$categories = array( 0 => array( 'pid' => -1 , 'name' => '' , 'url' => '' , 'sub' => array() ) ) ;

	// categories query
	$sql = "SELECT topic_id,topic_pid,topic_title FROM ".$db->prefix($mydirname."_topics")." ORDER BY topic_title" ;
	$crs = $db->query( $sql ) ;
	if( $crs ) while( $cat_row = $db->fetchArray( $crs ) ) {
		$topic_id = intval( $cat_row['topic_id'] ) ;
		$categories[ $topic_id ] = array(
			'name' => $myts->makeTboxData4Show( $cat_row['topic_title'] ) ,
			'url' => 'index.php?storytopic='.$topic_id ,
			'pid' => $cat_row['topic_pid'] ,
		) ;
	}

	// restruct categories
	$submenus_cache[$mydirname] = array_merge( $categories[0]['sub'] , bulletin_restruct_categories( $categories , 0 ) ) ;
	return $submenus_cache[$mydirname] ;
}


function bulletin_restruct_categories( $categories , $parent )
{
	$ret = array() ;
	foreach( $categories as $cat_id => $category ) {
		if( $category['pid'] == $parent ) {
			if( empty( $category['sub'] ) ) $category['sub'] = array() ;
			$ret[] = array(
				'name' => $category['name'] ,
				'url' => $category['url'] ,
				'sub' => array_merge( $category['sub'] , bulletin_restruct_categories( $categories , $cat_id ) ) ,
			) ;
		}
	}

	return $ret ;
}


function bulletin_utf8_encode( $text )
{
	if (XOOPS_USE_MULTIBYTES == 1) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($text, 'UTF-8', _CHARSET ) ;
		}
		return $text;
	}
	return utf8_encode($text);
}




?>