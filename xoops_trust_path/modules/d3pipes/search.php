<?php

require_once dirname(__FILE__).'/include/common_functions.php' ;

eval( '

function '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return d3pipes_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'd3pipes_global_search_base' ) ) {

function d3pipes_global_search_base( $mydirname , $keywords , $andor , $limit , $offset , $userid )
{
	// not implemented for uid specifications
	if( ! empty( $userid ) ) {
		return array() ;
	}

	$db =& Database::getInstance() ;

	// XOOPS Search module
	$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1 ;
	$select4con = $showcontext ? "`data` AS text" : "'' AS text" ;

	if( is_array( $keywords ) && count( $keywords ) > 0 ) {
		switch( strtolower( $andor ) ) {
			case "and" :
				$whr = "" ;
				foreach( $keywords as $keyword ) {
					$whr .= "`data` LIKE '%$keyword%' AND " ;
				}
				$whr .= "1" ;
				break ;
			case "or" :
				$whr = "" ;
				foreach( $keywords as $keyword ) {
					$whr .= "`data` LIKE '%$keyword%' OR " ;
				}
				$whr .= "0" ;
				break ;
			default :
				$whr = "`data` LIKE '%{$keywords[0]}%'" ;
				break ;
		}
	} else {
		$whr = 1 ;
	}

	$sql = "SELECT `clipping_id`,`headline`,`pubtime`,$select4con FROM ".$db->prefix($mydirname."_clippings")." WHERE ($whr) AND can_search ORDER BY `pubtime` DESC" ;
	$result = $db->query( $sql , $limit , $offset ) ;
	$ret = array() ;
	$context = '' ;
	while( list( $clipping_id , $title , $mtime , $serialized_data ) = $db->fetchRow( $result ) ) {

		// get context for module "search"
		if( function_exists( 'search_make_context' ) && $showcontext ) {
			$data = d3pipes_common_unserialize( $serialized_data ) ;
			$text = @$data['description'] ;
			$full_context = strip_tags( $text ) ;
			if( function_exists( 'easiestml' ) ) $full_context = easiestml( $full_context ) ;
			$context = search_make_context( $full_context , $keywords ) ;
		}

		$ret[] = array(
			"image" => "" ,
			"link" => "index.php?page=clipping&amp;clipping_id=".intval($clipping_id) ,
			"title" => $title ,
			"time" => $mtime ,
			"uid" => "0" ,
			"context" => $context
		) ;
	}

	return $ret ;
}

}


?>