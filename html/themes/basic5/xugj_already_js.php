<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
/* ------------------------------------------------- */
/*  assign true or false.                            */
/*  XCL22_header ,jquery is already                  */
/* ------------------------------------------------- */

$handler =& xoops_gethandler('config');
$configArr =& $handler->getConfigsByDirname('legacyRender');
if( isset($configArr['feed_url'])){
	$this->assign( 'xugj_feed_url' , $configArr['feed_url'] ) ;
}else{
	$this->assign( 'xugj_feed_url' ,'#' ) ;
}

$this->assign( 'xcl22_jquery_is_already' , xcl22_jquery_is_already($configArr) ) ;
function xcl22_jquery_is_already($configArr)
{
	$ret = false;
	//jquery_is_alreadys, load main jQuerylibrary ?
	//$coreType = is_numeric(configArr['jquery_core']) ? 'google' : 'local';
	if( isset($configArr['jquery_core'])){
		if( $configArr['jquery_core'] !=""){
				$ret = true;
		}
	}
	return $ret;
}

/* ------------------------------------------------- */
/*  assign true or false.                            */
/*  xoops_module_header ,jquery is already           */
/* ------------------------------------------------- */
if(isset($this->_tpl_vars["xoops_module_header"])){
	$this->assign( 'xugj_jquery_is_already' , xugj_jquery_is_already($this->_tpl_vars["xoops_module_header"]) ) ;
}else{
	$this->assign( 'xugj_jquery_is_already' , false ) ;
}
function xugj_jquery_is_already($document)
{
	$ret = false;
	if (preg_match('/(www\.google\.com\/jsapi|jquery([0-9\.-]+?)\.min\.js)/isx',$document) || preg_match('/[^(ckeditor\/adapters\/)](jquery\.js)/isx',$document)) {
		$ret = true;
	}
	return $ret;
}

/* ------------------------------------------------- */
/*  assign arry for  etc.                            */
/*  xoops_module_header js file name arry            */
/* ------------------------------------------------- */
if(isset($this->_tpl_vars["xoops_module_header"])){
	$this->assign( 'xugj_already_js' , xugj_jquery_is_already($this->_tpl_vars["xoops_module_header"]) ) ;
}else{
	$this->assign( 'xugj_already_js' , array() ) ;
}
$this->assign( 'xugj_already_js' , xugj_strip_xoops_module_header_links($this->_tpl_vars["xoops_module_header"]) ) ;
function xugj_strip_xoops_module_header_links($document)
{
	// catenate the non-empty matches from the conditional subpattern
	$match =array();
	if (empty($document)){
		return array();
	}
	preg_match_all("'<\s*script\s.*?src\s*=\s*			# find <script src=
					([\"\'])?					# find single or double quote
					(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
												# quote, otherwise match up to next space
					'isx",$document,$links);

	while(list($key,$val) = each($links[2]))
	{
		if(!empty($val)){
			$match[] = $val;
		}
	}
	while(list($key,$val) = each($links[3]))
	{
		if(!empty($val)){
			$match[] = $val;
		}
	}
	$match_js =array();
	if (empty($match)){
		return array();
	}
	while(list($key,$val) = each($match))
	{
		$jsbase_name=pathinfo($val,PATHINFO_BASENAME );
		if(!empty($jsbase_name)){
			$jsbase_name=strtolower($jsbase_name);
			$jsbase_name = preg_replace('/.*=/isx','',$jsbase_name);
			if( substr($jsbase_name,-3) == '.js'){
				$match_js[] = $jsbase_name;
			}
		}
	}
	// return the js name
	return $match_js;

}

?>