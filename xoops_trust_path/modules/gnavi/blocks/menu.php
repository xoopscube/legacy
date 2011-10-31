<?php

function b_gnavi_menu_show( $options )
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$cat_limit = empty( $options[1] ) ? 0 : 1 ;
	$this_template = 'db:'.$mydirname.'_block_menu.html';

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	require dirname(dirname(__FILE__)).'/include/read_configs.php' ;

	$myts =& MyTextSanitizer::getInstance() ;
	$result = $xoopsDB->query( "SELECT c1.cid ,c1.title,c1.imgurl ,c2.cid AS ccid, c2.title AS ctitle, c2.imgurl AS cimgurl FROM $table_cat c1 LEFT JOIN $table_cat c2 ON c1.cid=c2.pid WHERE c1.pid=0 ORDER BY c1.weight,c2.weight ") ;

	if(preg_match( '/'.'\/modules\/'.$mydirname.'/' ,htmlspecialchars(getenv('REQUEST_URI')))){
		$gcid = empty( $_GET['cid'] ) ? 0 : intval( $_GET['cid'] ) ;
		$glid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;
		$ocid = $gcid;
		if($gcid){
			if( ! $result2 = $xoopsDB->query( "SELECT pid FROM $table_cat WHERE cid=$gcid" ) ) {
				echo $xoopsDB->logger->dumpQueries() ;
				exit ;
			}
			list($gpid) = $xoopsDB->fetchRow( $result2 );
			$gcid = $gpid > 0 ? $gpid : $gcid ;
		}
	}else{
		$gcid =0 ;
		$glid =0 ;
		$ocid =0 ;
	}

	$blk = array() ;
	while( $content_row = $xoopsDB->fetchArray( $result ) ) {
		$cat_id = intval( $content_row['cid'] ) ;
		$blk[$cat_id]['cid'] = intval( $content_row['cid'] ) ;
		$blk[$cat_id]['title'] = $myts->makeTboxData4Show( $content_row['title'] ) ;
		$blk[$cat_id]['imgurl'] = $content_row['imgurl'] ;
		$blk[$cat_id]['active'] = intval( $content_row['cid'] )==$ocid && $glid==0 ? 1 : 0 ;
		if((!$cat_limit || $gcid==$cat_id) && intval( $content_row['ccid'] )>0){
			$blk[$cat_id]['contents'][] = array(
				'cid' => intval( $content_row['ccid'] ) ,
				'title' => $myts->makeTboxData4Show( $content_row['ctitle'] ) ,
				'imgurl' => $content_row['cimgurl'] ,
				'active' => intval( $content_row['ccid'] )==$ocid && $glid==0 ? 1 : 0 ,
			) ;
		}
	}
	$block = array() ;
	$block['categories'] = $blk ;

	if(preg_match('/page=map/',htmlspecialchars(getenv('REQUEST_URI')))){
		$block['mod_url'] = XOOPS_URL.'/modules/'.$mydirname."/index.php?page=map&" ;
	}elseif(preg_match('/page=category/',htmlspecialchars(getenv('REQUEST_URI')))){
		$block['mod_url'] = XOOPS_URL.'/modules/'.$mydirname."/index.php?page=category&" ;
	}else{
		$block['mod_url'] = XOOPS_URL.'/modules/'.$mydirname."/index.php?" ;
	}

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

function b_gnavi_menu_edit( $options )
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$cat_limit = empty( $options[1] ) ? 0 : 1 ;

	return "
		"._GNAV_TEXT_SHOWACTIVECAT."
		<input type='hidden' name='options[0]' value='{$mydirname}' />
		<input type='radio' name='options[1]' value='1' ".($cat_limit?"checked='checked'":"")."/>"._YES."
		<input type='radio' name='options[1]' value='0' ".($cat_limit?"":"checked='checked'")."/>"._NO."
		<br />
		\n" ;
}

?>