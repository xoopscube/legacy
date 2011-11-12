<?php

// for d3pipes block emulation
function b_gnavi_d3pipes_joints( $options )
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	require dirname(dirname(__FILE__)).'/include/read_configs.php' ;

	$cat_limitation = empty( $options[1] ) ? 0 : 1 ;
	$photos_num = empty( $options[2] ) ? 10 : intval( $options[2] ) ;
	$show_desc = empty( $options[3] ) ? 0 : 1 ;

	$query= "ORDER BY unixtime DESC" ;

	$title_max_length = 255 ;

	// Category limitation
	if( $cat_limitation ) {
		include_once( XOOPS_ROOT_PATH."/class/xoopstree.php" ) ;
		$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;
		$children = $cattree->getAllChildId( $cat_limitation ) ;

		$whr_cat = "l.cid IN (" ;
		foreach( $children as $child ) {
			$whr_cat .= "$child," ;
		}
		$whr_cat .= "$cat_limitation)" ;

		for ($i = 1; $i <= 4; $i++) {
			$whr_cat .= " OR l.cid$i IN (" ;
			foreach( $children as $child ) {
				$whr_cat .= "$child," ;
			}
			$whr_cat .= "$cat_limitation)" ;
		}
		$whr_cat = "(".$whr_cat.")";

	} else {
		$whr_cat = '1' ;
	}


	$block = array() ;
	$myts =& MyTextSanitizer::getInstance() ;

	if($show_desc){
		$result = $xoopsDB->query("SELECT l.lid , l.cid , l.title , l.ext , l.res_x , l.res_y , l.submitter , l.status , l.date AS unixtime , l.hits , l.rating , l.votes , l.comments , c.title AS cat_title ,t.description ,t.arrowhtml FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid LEFT JOIN $table_text t ON l.lid=t.lid WHERE l.status>0 AND $whr_cat $query" , $photos_num , 0 ) ;
	}else{
		$result = $xoopsDB->query("SELECT l.lid , l.cid , l.title , l.ext , l.res_x , l.res_y , l.submitter , l.status , l.date AS unixtime , l.hits , l.rating , l.votes , l.comments , c.title AS cat_title FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE l.status>0 AND $whr_cat $query" , $photos_num , 0 ) ;
	}

	$count = 1 ;
	while( $photo = $xoopsDB->fetchArray( $result ) ) {
		$photo['title'] = xoops_substr(  $myts->makeTboxData4Show( $photo['title'] ) , 0 , $title_max_length+3 );
		$photo['cat_title'] = $myts->makeTboxData4Show( $photo['cat_title'] ) ;
		$photo['date'] = formatTimestamp( $photo['unixtime'] , 's' ) ;
		if($show_desc){
			$photo['body'] = xoops_substr(strip_tags($myts->displayTarea( $photo['description'] , $photo['arrowhtml'] , 1 , 1 , 1 , 1 , 1 )),0,512)  ;
		}else{
			$photo['body'] = '';
		}
		$photo['date'] = formatTimestamp( $photo['unixtime'] , 's' ) ;

		$block['photo'][$count++] = $photo ;
	}

	$block['mod_url'] = $mod_url ;

	return $block ;
}

?>