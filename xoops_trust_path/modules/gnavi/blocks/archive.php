<?php

function b_gnavi_archive_show( $options )
{
	global $xoopsDB ;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	$this_template = 'db:'.$mydirname.'_block_archive.html';

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	require dirname(dirname(__FILE__)).'/include/read_configs.php' ;

	$block = array() ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;
	$myts =& MyTextSanitizer::getInstance() ;
	$result = $xoopsDB->query( "SELECT date FROM $table_photos WHERE status>0 ORDER BY date DESC" ) ;

	$count = 1 ;
	while( $photo = $xoopsDB->fetchArray( $result ) ) {

		$photo['link'] = date ("Ym",$photo["date"]) ;
		//$photo['text'] = date ("Y? m??",$photo["date"]) ;
		$_year = date("Y",$photo["date"]);
		$_month = date("m",$photo["date"]);
		$photo['text'] = sprintf( constant( $constpref.'_ARCH_POSTMONTH'), $_year, $_month ) ;

		if($count>1){
			if($block['archive'][$count-1]['link']!=$photo['link']){
				$block['archive'][$count++] = $photo ;
			}
		}else{
			$block['archive'][$count++] = $photo ;
		}
	}

	$block['mod_url'] = $mod_url ;

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

?>