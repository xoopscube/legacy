<?php

class PicoUriMapper {

var $mydirname ;
var $config ;
var $request = array() ; // public
var $path_info = null ; // public

function PicoUriMapper( $mydirname , $config )
{
	$this->mydirname = $mydirname ;
	$this->config = $config ;
	$this->request = array() ;
}


function initGet()
{
	// avoid to use parse_str()
	if( $this->config['use_wraps_mode'] && $this->config['use_rewrite'] ) {
		$uri_array = parse_url( $_SERVER['REQUEST_URI'] ) ;
		$queries = explode( '&' , @$uri_array['query'] ) ;
		foreach( $queries as $query ) {
			if( preg_match( '/([a-zA-Z0-9_]{1,30})\=([a-zA-Z0-9_.-]{1,60})/' , $query , $regs ) ) {
				$_GET[ $regs[1] ] = $regs[2] ;
				$_REQUEST[ $regs[1] ] = $regs[2] ;
			}
		}
	}
}


function parseRequest()
{
	if( intval( @$_REQUEST['content_id'] ) > 0 ) {
		// 1st check $_REQUEST['content_id']
		$content_id = intval( @$_REQUEST['content_id'] ) ;
		$cat_id = pico_common_get_cat_id_from_content_id( $this->mydirname , $content_id ) ;
	} else {
		// 2nd check path_info
		$path_info = $this->getPathInfo() ;
		if( $path_info ) {
			//if( @$_GET['page'] == 'makecontent' ) {
			if( defined( 'PICO_URI_MAPPER_ALLOW_CAT_ID_OVERWRITING' ) ) {
				$content_id = 0 ;
				$cat_id = 0 ;
			} else {
				list( $content_id , $cat_id ) = $this->parsePathInfo( $path_info ) ;
				if( is_null( $content_id ) ) {
					list( $content_id , $cat_id ) = $this->processWrapPath( $path_info ) ;
				}
			}
		} else {
			// 3rd check $_REQUEST['cat_id']
			$content_id = 0 ;
			$cat_id = intval( @$_REQUEST['cat_id'] ) ;
		}
	}

	// set the parameter of $this->request  (controller/view/content_id etc.)
	$this->judgeController( $cat_id , $content_id ) ;

	// cat_id modification (makecontent/contentmanager etc.)
	if( defined( 'PICO_URI_MAPPER_ALLOW_CAT_ID_OVERWRITING' ) && isset( $_REQUEST['cat_id'] ) ) {
		$cat_id = intval( @$_REQUEST['cat_id'] ) ;
	}

	// content_id modification (contentmanager)
	if( defined( 'PICO_URI_MAPPER_ALLOW_CONTENT_ID_OVERWRITING' ) && isset( $_REQUEST['content_id'] ) ) {
		$content_id = intval( @$_REQUEST['content_id'] ) ;
	}

	// for notification
	if( $content_id ) $_GET['content_id'] = $content_id ;
	if( $cat_id ) $_GET['cat_id'] = $cat_id ;

	// set request
	$this->request['content_id'] = $content_id ;
	$this->request['cat_id'] = $cat_id ;

	return $this->request ;
}


// override it if you want to add another URI mapping rules
function judgeController( &$cat_id , &$content_id )
{
	// default controller
	if( empty( $this->request['controller'] ) ) {
		$this->request['controller'] = 'content' ;
	}

	// controller/view
	if( @$_GET['page'] == 'singlecontent' || substr( $_SERVER['REQUEST_URI'] , -19 ) == '?page=singlecontent' ) {
		$this->request['view'] = 'singlecontent' ;
	} else if( @$_GET['page'] == 'print' || substr( $_SERVER['REQUEST_URI'] , -11 ) == '?page=print' ) {
		$this->request['view'] = 'print' ;
	} else if( @$_GET['page'] == 'rss' || substr( $_SERVER['REQUEST_URI'] , -9 ) == '?page=rss' ) {
		$this->request['controller'] = 'latestcontents' ;
		$this->request['view'] = 'rss' ;
	} else if( ! empty( $_GET['tag'] ) ) {
		$this->request['tag'] = $_GET['tag'] ;
		$this->request['controller'] = 'querycontents' ;
		$this->request['view'] = 'list' ;
	} else if( $content_id > 0 || ! empty( $this->request['path_info'] ) ) {
		$this->request['view'] = 'detail' ;
	} else if( $cat_id > 0 ) {
		$this->request['controller'] = 'category' ;
		$this->request['view'] = 'list' ;
	} else if( @$_GET['cat_id'] !== '0' && ( $this->config['show_menuinmoduletop'] || @$_GET['page'] == 'menu' ) ) {
		$this->request['controller'] = 'menu' ;
		$this->request['view'] = 'menu' ;
	} else {
		$this->request['controller'] = 'category' ;
		$this->request['view'] = 'list' ;
	}
}


function modifyRequest( $request , $currentCategoryObj )
{
	$this->config = $currentCategoryObj->getOverriddenModConfig() ;
	$cat_data = $currentCategoryObj->getData() ;

	if( empty( $this->config['show_listasindex'] ) && $request['controller'] == 'category' && $request['view'] == 'list' && empty( $request['content_id'] ) ) {
		$top_content_id = pico_main_get_top_content_id_from_cat_id( $currentCategoryObj->mydirname , $cat_data['id'] ) ;
		if( $top_content_id > 0 ) {
			// redirect to the top of the content
			$redirect_uri = XOOPS_URL.'/modules/'.$this->mydirname.'/'.pico_common_make_content_link4html( $this->config , $top_content_id , $this->mydirname ) ;
			if( headers_sent() ) {
				redirect_header( $redirect_uri , 0 , '&nbsp;' ) ;
			} else {
				header( 'Location: '.$redirect_uri ) ;
			}
		} else {
			$request['controller'] = 'category' ;
			$request['view'] = 'list' ;
			$request['content_id'] = 0 ;
		}
	}

	return $request ;
}


function getPathInfo()
{
	if( ! empty( $_GET['path_info'] ) ) {
		// path_info=($path_info) by mod_rewrite
		$path_info = '/' . str_replace( '..' , '' , preg_replace( _MD_PICO_WRAPS_DISALLOWEDCHARS4PREGEX , '' , $_GET['path_info'] ) ) ;
		$_SERVER['SCRIPT_NAME'] = '' ; // for EMLH
		unset( $_SERVER['QUERY_STRING'] ) ; // for EMLH
	} else if( ! empty( $_SERVER['PATH_INFO'] ) ) {
		// try PATH_INFO first
		$path_info = str_replace( '..' , '' , preg_replace( _MD_PICO_WRAPS_DISALLOWEDCHARS4PREGEX , '' , @$_SERVER['PATH_INFO'] ) ) ;
	} else if( stristr( $_SERVER['REQUEST_URI'] , $this->mydirname.'/index.php/' ) ) {
		// try REQUEST_URI second
		list( , $path_info_query ) = explode( $this->mydirname.'/index.php' , $_SERVER['REQUEST_URI'] , 2 ) ;
		list( $path_info_tmp ) = explode( '?' , $path_info_query , 2 ) ;
		$path_info = str_replace( '..' , '' , preg_replace( _MD_PICO_WRAPS_DISALLOWEDCHARS4PREGEX , '' , $path_info_tmp ) ) ;
	} else if( strlen( $_SERVER['PHP_SELF'] ) > strlen( $_SERVER['SCRIPT_NAME'] ) ) {
		// try PHP_SELF & SCRIPT_NAME third
		$path_info = str_replace( '..' , '' , preg_replace( _MD_PICO_WRAPS_DISALLOWEDCHARS4PREGEX , '' , substr( $_SERVER['PHP_SELF'] , strlen( $_SERVER['SCRIPT_NAME'] ) ) ) ) ;
	} else {
		$path_info = false ;
	}

	return $path_info ;
}


function parsePathInfo( $path_info )
{
	// check vpath in DB (1st)
	$ext = strtolower( substr( strrchr( $path_info , '.' ) , 1 ) ) ;
	if( in_array( $ext , explode( '|' , _MD_PICO_EXTS4HTMLWRAPPING ) ) ) {
		$db =& Database::getInstance() ;
		$result = $db->query( "SELECT content_id,cat_id FROM ".$db->prefix($this->mydirname."_contents")." WHERE vpath='".mysql_real_escape_string($path_info)."'" ) ;
		list( $content_id , $cat_id ) = $db->fetchRow( $result ) ;
		if( $content_id > 0 ) {
			return array( intval( $content_id ) , intval( $cat_id ) ) ;
		}
	}

	// check cat_vpath in DB (2nd)
	if( substr( $path_info , -1 ) == '/' ) {
		$db =& Database::getInstance() ;
		$result = $db->query( "SELECT cat_id FROM ".$db->prefix($this->mydirname."_categories")." WHERE cat_vpath='".mysql_real_escape_string($path_info)."' OR cat_vpath='".mysql_real_escape_string(substr($path_info,0,-1))."'" ) ;
		list( $cat_id ) = $db->fetchRow( $result ) ;
		if( $cat_id > 0 ) {
			return array( 0 , intval( $cat_id ) ) ;
		}
	}

	// check path_info obeys the ruled for autonaming for contents (3rd)
	if( preg_match( _MD_PICO_AUTONAME4PREGEX , $path_info , $regs ) ) {
		$content_id = intval( @$regs[1] ) ;
		return array( $content_id , pico_common_get_cat_id_from_content_id( $this->mydirname , $content_id ) ) ;
	}

	// check path_info obeys the ruled for autonaming for category (4th)
	if( preg_match( _MD_PICO_AUTOCATNAME4PREGEX , $path_info , $regs ) ) {
		return array( 0 , intval( @$regs[1] ) ) ;
	}
	
	return array( null , null ) ;
}


function processWrapPath( $path_info )
{
	// check wraps mode enabled
	if( empty( $this->config['use_wraps_mode'] ) ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCONTENT ) ;
		exit ;
	}

	// check wrap file 
	$wrap_full_path = XOOPS_TRUST_PATH._MD_PICO_WRAPBASE.'/'.$this->mydirname.$path_info ;
	if( ! file_exists( $wrap_full_path ) ) {
		// TODO (don't die here)
		header( 'HTTP/1.0 404 Not Found' ) ;
		die( "The requested file ".htmlspecialchars($path_info)." is not found" ) ;
	}

	$path_info_is_dir = is_dir( $wrap_full_path ) ;
	$ext = strtolower( substr( strrchr( $path_info , '.' ) , 1 ) ) ;

	if( $path_info_is_dir || in_array( $ext , explode( '|' , _MD_PICO_EXTS4HTMLWRAPPING ) ) ) {
		// HTML wrapping
		// get category from path_info (finding longest equality)
		$db =& Database::getInstance() ;
		$dir_tmp = strtolower( $path_info ) ;
		$vpaths4sql = '' ;
		do {
			$vpaths4sql .= ",'".mysql_real_escape_string($dir_tmp)."'" ;
			$dir_tmp = substr( $path_info , 0 , strrpos( $dir_tmp , '/' ) ) ;
		} while( $dir_tmp ) ;
		$vpaths4sql = $vpaths4sql ? substr( $vpaths4sql , 1 ) : "''" ;
		$result = $db->query( "SELECT cat_id FROM ".$db->prefix($this->mydirname."_categories")." WHERE cat_vpath IN ($vpaths4sql) ORDER BY LENGTH(cat_vpath) DESC" ) ;
		list( $cat_id ) = $db->fetchRow( $result ) ;
		if( $path_info_is_dir ) {
			// just return $cat_id
			return array( 0 , intval( $cat_id ) ) ;
		} else {
			// just HTML wrapping (without content_id)
			$this->request['path_info'] = $path_info ;
			$this->request['controller'] = 'htmlwrapped' ;
			return array( 0 , intval( $cat_id ) ) ;
		}
	} else {
		// just transfer (image files etc.)
		$this->transferWrappedFile( $wrap_full_path , $ext ) ;
	}
}


function transferWrappedFile( $wrap_full_path , $ext )
{
	// remove output bufferings
	while( ob_get_level() ) {
		ob_end_clean() ;
	}

	// can headers be sent?
	if( headers_sent() ) {
		restore_error_handler() ;
		die( "Can't send headers. check language files etc." ) ;
	}

	// headers for browser cache
	$cache_limit = intval( @$this->config['browser_cache'] ) ;
	if( $cache_limit > 0 ) {
		session_cache_limiter('public');
		header("Expires: ".date('r',intval(time()/$cache_limit)*$cache_limit+$cache_limit));
		header("Cache-Control: public, max-age=$cache_limit");
		header("Last-Modified: ".date('r',intval(time()/$cache_limit)*$cache_limit));
	}

	require dirname(dirname(__FILE__)).'/include/mimes.php' ;
	if( ! empty( $mimes[ $ext ] ) ) {
		header( 'Content-Type: '.$mimes[ $ext ] ) ;
	} else {
		header( 'Content-Type: application/octet-stream' ) ;
	}
	@set_time_limit( 0 ) ;
	$fp = fopen( $wrap_full_path , "rb" ) ;
	while( ! feof( $fp ) ) {
		echo fread( $fp , 65536 ) ;
	}
	exit ;
}


function redirect4WrapsPreview()
{
	$sess_index = $this->mydirname . '_preview' ;

	if( ! empty( $_SESSION[ $sess_index ] ) ) {
		// restore POST from SESSION after redirection
		$_POST = $_SESSION[ $sess_index ] ;
		unset( $_SESSION[ $sess_index ] ) ;

	} else if( ! empty( $_POST['contentman_preview'] ) ) {
		// set targeted_cat_id into uri for redirection
		$cat_id = intval( @$_POST['cat_id'] ) ;
		$content_id = intval( @$_GET['content_id'] ) ;

		// save POST into SESSION, then redirect
		$_POST['content_id'] = $content_id ;
		$_SESSION[ $sess_index ] = $_POST ;

		// duplicated? almost same as pico_common_make_content_link4html()
		$link = $this->config['use_rewrite'] ? '' : '/index.php' ;
		$link .= empty( $_POST['vpath'] ) ? sprintf( _MD_PICO_AUTONAME4SPRINTF , intval( $_GET['content_id'] ) ) : preg_replace( '#[^0-9a-zA-Z_/.+-]#' , '' , $_POST['vpath'] ) ;
		$page = $content_id > 0 ? 'contentmanager' : 'makecontent' ;

		header( 'Location: '.XOOPS_URL.'/modules/'.$this->mydirname.$link.'?page='.$page.'&cat_id='.$cat_id.'&content_id='.$content_id.'&ret='.urlencode(@$_GET['ret']) ) ;
		exit ;
	}
}


}



?>