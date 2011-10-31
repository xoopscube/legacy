<?php

require_once dirname(__FILE__).'/menu.php' ;
require_once dirname(__FILE__).'/archive.php' ;

function b_gnavi_ritem_show( $options )
{
	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$show_image = empty( $options[7] ) ? 0 : 1 ;
	$this_template = $show_image ?'db:'.$mydirname.'_block_ritem_p.html':'db:'.$mydirname.'_block_ritem.html';
	$query= "ORDER BY rand()" ;

	return b_gnavi_itemblock_show($options,$this_template,$query);
}

function b_gnavi_ritem_edit( $options )
{
	return b_gnavi_itemblock_edit( $options );
}

function b_gnavi_topnews_show( $options )
{
	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$show_image = empty( $options[7] ) ? 0 : 1 ;
	$this_template = $show_image ?'db:'.$mydirname.'_block_topnews_p.html':'db:'.$mydirname.'_block_topnews.html';
	$query= "ORDER BY unixtime DESC" ;

	return b_gnavi_itemblock_show($options,$this_template,$query);
}

function b_gnavi_topnews_edit( $options )
{
	return b_gnavi_itemblock_edit( $options );
}

function b_gnavi_tophits_show( $options )
{
	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$show_image = empty( $options[7] ) ? 0 : 1 ;
	$this_template = $show_image ?'db:'.$mydirname.'_block_tophits_p.html':'db:'.$mydirname.'_block_tophits.html';
	$query= "ORDER BY hits DESC" ;

	return b_gnavi_itemblock_show($options,$this_template,$query);
}

function b_gnavi_tophits_edit( $options )
{
	return b_gnavi_itemblock_edit( $options );
}


function b_gnavi_itemblock_show( $options ,$this_template,$query)
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	require dirname(dirname(__FILE__)).'/include/read_configs.php' ;

	$photos_num = empty( $options[1] ) ? 5 : intval( $options[1] ) ;
	$title_max_length = empty( $options[2] ) ? 20 : intval( $options[2] ) ;
	$cat_limitation = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$cat_limit_recursive = empty( $options[4] ) ? 0 : 1 ;
	$cols = empty( $options[6] ) ? 1 : intval( $options[6] ) ;
	$show_image = empty( $options[7] ) ? 0 : 1 ;
	$show_noimage = empty( $options[8] ) ? 0 : 1 ;
	$box_size = empty( $options[9] ) ? 0 : intval( $options[9] ) ;

	// Category limitation
	if( $cat_limitation ) {
		if( $cat_limit_recursive ) {
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
			$whr_cat = "l.cid='$cat_limitation'" ;
		}
	} else {
		$whr_cat = '1' ;
	}

	// WHERE clause for ext
	if(!$show_noimage){
		$whr_ext = "l.ext IN ('" . implode( "','" , $gnavi_normal_exts ) . "')" ;
	} else {
		$whr_ext = "1" ;
	}

	$block = array() ;
	$myts =& MyTextSanitizer::getInstance() ;

	$result = $xoopsDB->query("SELECT l.lid , l.cid , l.title , l.ext , l.res_x , l.res_y , l.submitter , l.status , l.date AS unixtime , l.hits , l.rating , l.votes , l.comments , c.title AS cat_title FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE l.status>0 AND $whr_cat AND $whr_ext $query" , $photos_num , 0 ) ;

	$count = 1 ;
	while( $photo = $xoopsDB->fetchArray( $result ) ) {

		$photo['title'] = xoops_substr( $myts->makeTboxData4Show( $photo['title'] ) , 0 , $title_max_length+3 );
		$photo['cat_title'] = $myts->makeTboxData4Show( $photo['cat_title'] ) ;
		$photo['suffix'] = $photo['hits'] > 1 ? 'hits' : 'hit' ;
		$photo['date'] = formatTimestamp( $photo['unixtime'] , 's' ) ;
		$photo['thumbs_url'] = $thumbs_url ;
		$photo['is_newphoto'] = ( $photo['unixtime'] > time() - 86400 * $gnavi_newdays && $photo['status'] == 1 );
		$photo['is_updatedphoto'] = ( $photo['unixtime'] > time() - 86400 * $gnavi_newdays && $photo['status'] == 2 );
		$photo['is_popularphoto'] = ( $photo['hits'] >= $gnavi_popular );

		if( in_array( strtolower( $photo['ext'] ) , $gnavi_normal_exts ) ) {
			// width&height attirbs for <img>
			if( $box_size <= 0 ) {
				$photo['img_attribs'] = "" ;
			} else {
				if( $photo['res_x'] > $box_size || $photo['res_y'] > $box_size && $gnavi_thumbsize > $box_size ) {
					if( $width > $height ) $photo['img_attribs'] = "width='$box_size'" ;
					else $photo['img_attribs'] = "height='$box_size'" ;
				} else {
					$photo['img_attribs'] = "" ;
				}
			}
		} else {
			if($photo['ext']){
				if(file_exists( "$mod_path/icons/".$photo['ext'].".gif" )){
					$photo['item'] = "$mod_url/icons/".$photo['ext'].".gif" ;
				}else{
					$photo['item'] = "$mod_url/icons/all.gif" ;
				}
			}else{
				$photo['item'] = "$mod_url/images/noimage.gif" ;
			}
		}


		$block['photo'][$count++] = $photo ;
	}
	$block['mod_url'] = $mod_url ;
	$block['cols'] = $cols ;

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl = new XoopsTpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		return $block ;
	}
}


function b_gnavi_itemblock_edit( $options )
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$photos_num = empty( $options[1] ) ? 5 : intval( $options[1] ) ;
	$title_max_length = empty( $options[2] ) ? 20 : intval( $options[2] ) ;
	$cat_limitation = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$cat_limit_recursive = empty( $options[4] ) ? 0 : 1 ;
	$cols = empty( $options[6] ) ? 1 : intval( $options[6] ) ;
	$show_image = empty( $options[7] ) ? 0 : 1 ;
	$show_noimage = empty( $options[8] ) ? 0 : 1 ;
	$box_size = empty( $options[9] ) ? 0 : intval( $options[9] ) ;

	include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;

	$cattree = new XoopsTree( $xoopsDB->prefix( "{$mydirname}_cat" ) , "cid" , "pid" ) ;

	ob_start() ;
	$cattree->makeMySelBox( "title" , "weight" , $cat_limitation , 1 , 'options[3]' ) ;
	$catselbox = ob_get_contents() ;
	ob_end_clean() ;

	return "
		"._GNAV_TEXT_DISP." &nbsp;
		<input type='hidden' name='options[0]' value='{$mydirname}' />
		<input type='text' size='4' name='options[1]' value='$photos_num' style='text-align:right;' />
		<br />
		"._GNAV_TEXT_STRLENGTH." &nbsp;
		<input type='text' size='6' name='options[2]' value='$title_max_length' style='text-align:right;' />
		<br />
		"._GNAV_TEXT_CATLIMITATION." &nbsp; $catselbox
		"._GNAV_TEXT_CATLIMITRECURSIVE."
		<input type='radio' name='options[4]' value='1' ".($cat_limit_recursive?"checked='checked'":"")."/>"._YES."
		<input type='radio' name='options[4]' value='0' ".($cat_limit_recursive?"":"checked='checked'")."/>"._NO."
		<br />
		<input type='hidden' name='options[5]' value='' />
		"._GNAV_TEXT_COLS."&nbsp;
		<input type='text' size='2' name='options[6]' value='$cols' style='text-align:right;' />
		<br />
		"._GNAV_TEXT_SHOWIMAGE."
		<input type='radio' name='options[7]' value='1' ".($show_image?"checked='checked'":"")."/>"._YES."
		<input type='radio' name='options[7]' value='0' ".($show_image?"":"checked='checked'")."/>"._NO."
		<br />
		"._GNAV_TEXT_SHOWNOIMAGE."
		<input type='radio' name='options[8]' value='1' ".($show_noimage?"checked='checked'":"")."/>"._YES."
		<input type='radio' name='options[8]' value='0' ".($show_noimage?"":"checked='checked'")."/>"._NO."
		<br />
		<input type='text' size='6' name='options[9]' value='$box_size' style='text-align:right;' />&nbsp;pixel "._GNAV_TEXT_BLOCK_WIDTH_NOTES."
		\n" ;
}

?>