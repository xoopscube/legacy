<?php

function d3pipes_main_fetch_entries_main_aggr( $mydirname , &$errors , $max_entries )
{
	global $xoopsModuleConfig ;

	$db =& Database::getInstance() ;

	// get pipe_ids for latest headlines (main_aggr)
	$result = $db->query( "SELECT pipe_id FROM ".$db->prefix($mydirname."_pipes")." WHERE main_aggr ORDER BY weight" ) ;
	$union_options = array() ;
	while( list( $pipe_id ) = $db->fetchRow( $result ) ) {
		$union_options[] = $pipe_id.':'.$xoopsModuleConfig['index_each'] ;
	}

	// Union object
	$union_obj =& d3pipes_common_get_joint_object_default( $mydirname , 'union' , implode( ',' , $union_options ) . '||' . (empty($xoopsModuleConfig['index_keeppipe'])?0:1) ) ;
	$union_obj->setModConfigs( $xoopsModuleConfig ) ;
	$entries = $union_obj->execute( array() , $max_entries ) ;
	$errors = $union_obj->getErrors() ;
	return $entries ;
}


// get <link> to CSS for main
function d3pipes_main_get_link2maincss( $mydirname )
{
	global $xoopsModuleConfig ;

	$css_uri4disp = htmlspecialchars( str_replace( '{mod_url}' , XOOPS_URL.'/modules/'.$mydirname , @$xoopsModuleConfig['css_uri'] ) , ENT_QUOTES ) ;

	return '<link rel="stylesheet" type="text/css" media="all" href="'.$css_uri4disp.'" />'."\n" ;
}


// get <link> for RSS alternate
function d3pipes_main_get_link2rss( $mydirname , $pipe_id , $pipe4assign = null )
{
	global $xoopsModule ;

	return empty( $pipe4assign['main_rss'] ) && $pipe_id > 0 ? '' : '<link rel="alternate" type="application/rss+xml" title="'.(empty($pipe4assign['name'])?$xoopsModule->getVar('name'):$pipe4assign['name']).' RSS" href="'.XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=xml&amp;style=rss20&amp;pipe_id='.$pipe_id.'" />'."\n" ;
}


// get <link> to common/lib/*.js
function d3pipes_main_get_script2commonlib( $mydirname )
{
	return '<script type="text/javascript" src="'.XOOPS_URL.'/common/lib/prototype.js"></script>'."\n".'<script type="text/javascript" src="'.XOOPS_URL.'/common/lib/scriptaculous.js"></script>'."\n" ;
}


function d3pipes_main_get_clipping_count_moduledb( $mydirname , $pipe_id )
{
	require_once dirname(dirname(__FILE__)).'/joints/clip/D3pipesClipModuledb.class.php' ;

	$clip_obj = new D3pipesClipModuledb( $mydirname , 0 , '' ) ;
	return $clip_obj->getClippingCount( $pipe_id ) ;
}


function d3pipes_main_get_clippings_moduledb( $mydirname , $pipe_id , $num , $pos )
{
	require_once dirname(dirname(__FILE__)).'/joints/clip/D3pipesClipModuledb.class.php' ;

	$clip_obj = new D3pipesClipModuledb( $mydirname , 0 , '' ) ;
	return $clip_obj->getClippings( $pipe_id , $num , $pos ) ;
}




?>