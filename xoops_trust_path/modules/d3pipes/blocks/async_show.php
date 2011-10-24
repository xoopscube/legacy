<?php

function b_d3pipes_async_show( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3pipes' : $options[0] ;
	$unique_id = empty( $options[1] ) ? uniqid( rand() ) : htmlspecialchars( $options[1] , ENT_QUOTES ) ;
	$pipe_ids = empty( $options[2] ) ? array(0) : explode( ',' , preg_replace( '/[^0-9,:]/' , '' ,  $options[2] ) ) ;
	$max_entries = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$this_template = empty( $options[4] ) ? 'db:'.$mydirname.'_block_async.html' : trim( $options[4] ) ;
	$union_class = @$options[5] == 'separated' ? 'separated' : 'mergesort' ;
	$link2clipping = empty( $options[6] ) ? false : true ;
	$keep_pipeinfo = empty( $options[7] ) ? false : true ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	// insert javascript if necessary
	d3pipes_insert_javascript4async() ;

	$block = array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$configs['images_dir'] ,
		'mod_config' => $configs ,
		'unique_id' => $unique_id ,
		'pipe_ids' => $pipe_ids ,
		'max_entries' => $max_entries ,
		'union_class' => $union_class ,
		'link2clipping' => $link2clipping ,
		'keep_pipeinfo' => $keep_pipeinfo ,
		'lang_async_noscript' => constant($constpref."_ASYNC_NOSCRIPT") ,
	) ;

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
		$tpl = new D3Tpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		return $block ;
	}
}


function d3pipes_insert_javascript4async()
{
	// javascript placed between <head></head>
	$head_script = '
		<script type="text/javascript">
		<!--
		function d3pipes_add_script( url )
		{
			script = document.createElement("script");
			script.setAttribute("type", "text/javascript");
			script.setAttribute("src", url + "&time=" + (new Date().getTime()) );
			script.setAttribute("charset", "'._CHARSET.'");
			document.getElementsByTagName("head").item(0).appendChild(script);
		}

		function d3pipes_insert_html( id , html )
		{
		  document.getElementById( id ).innerHTML = html ;
		}
		//-->
		</script>
	' ;

	if( is_object( $GLOBALS['xoopsTpl'] ) ) {
		$xoops_module_header = $GLOBALS['xoopsTpl']->get_template_vars( "xoops_module_header" ) ;
		if( ! strstr( $xoops_module_header , 'd3pipes_add_script' ) ) {
			$GLOBALS['xoopsTpl']->assign( 'xoops_module_header' , $head_script . $xoops_module_header ) ;
		}
	}
}

?>