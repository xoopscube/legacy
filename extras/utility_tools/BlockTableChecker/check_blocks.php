<?php

//================================================================
// check block table
// 2007-10-10 K.OHWADA
//================================================================

//---------------------------------------------------------
// this program works in XOOPS 2.0.16aJP, XOOPS Cube 2.1.2, XOOPS 2.0.17
//
// NOTE
// xoops 2.0.16 JP  : class/xoopsblock.php and kernel/block.php are different
// xoops cube 2.1.2 : class/xoopsblock.php and kernel/block.php are the same
// xoops 2.0.17     : no token class
//---------------------------------------------------------

include 'mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php';

//=========================================================
// class xoops_block_check
//=========================================================
class xoops_block_check
{
	var $_TOKEN_NAME = 'block';
	var $_mid        = 0;
	var $_is_special = false;
	var $_msg_array  = array();
	var $_error_flag = false;
	var $_xoops_version = '2.0';
	var $_use_token     = false;

//---------------------------------------------------------
// constructor
//---------------------------------------------------------
function xoops_block_check()
{
	$this->_detect_xoops_version();
}

function &getInstance()
{
	static $instance;
	if (!isset($instance)) 
	{
		$instance = new xoops_block_check();
	}
	return $instance;
}

//--------------------------------------------------------
// public
//--------------------------------------------------------
function get_op()
{
	$op  = '';
	$mid = 0;
	foreach ( $_POST as $k => $v )
	{
		if ( preg_match( "/^mid:/", $k ) )
		{
			$op  = 'remove_block';
			$mid = intval( str_replace("mid:", "", $k) );
			break;
		}
	}
	$this->_mid = $mid;
	return $op;
}

function check_blocks()
{
	$text  = "<h1>Check xoops block table</h1>\n";
	$text .= 'Please use carefully, because <b>Remove Block</b> deletes records in block table';

	$text .= '<form method="POST">'."\n";

	if ( $this->_use_token )
	{
		$text .= $this->_create_token();
	}

	$objs =& $this->_get_module_objects();
	foreach ( $objs as $obj ) 
	{
		$text .= $this->_check_block_by_module( $obj );
	}

	$text .= "</form>\n";
	return $text;
}

function remove_block()
{
	$text = "<h1>Remove xoops block table</h1>\n";

	if( $this->_use_token && !$this->_validate_token() ) 
	{
		$text .= '<h3 style="color:#ff0000">Token Error</h3>'."\n";
		$text .= '<a href="check_blocks.php">Check Bloks</a>';
		return $text;
	}

	$module_obj =& $this->_get_module_object_by_mid( $this->_mid );
	if( !is_object($module_obj) ) 
	{
		$text .= '<h3 style="color:#ff0000">"No Module: mid='. $this->_mid ."</h3>\n";
		$text .= "<br />\n";
		$text .= '<a href="check_blocks.php">Check Bloks</a>';
		return $text;
	}

	$ret = $this->_remove_block_by_mid( $this->_mid );
	if ( $ret ) {
		$text .= '<h4 style="color:#0000ff">Success</h4>'."\n";
	} else {
		$text .= '<h4 style="color:#ff0000">Failed</h4>'."\n";
	}

	$url = $this->_build_url_module_update( $module_obj->getVar('dirname', 'n') );
	$text .= '<a href="'.$url.'">GO to Module Update: '.$module_obj->getVar('name', 's')."</a><br /><br />\n";
	$text .= '<a href="check_blocks.php">Check xoops block table</a><br />'."\n";

	return $text;
}

//--------------------------------------------------------
// private
//--------------------------------------------------------
function _check_block_by_module( &$module_obj )
{
	$this->_msg_array = array();

	$mid      =  $module_obj->getVar('mid', 'n');
	$mod_name =  $module_obj->getVar('name', 's');
	$dirname  =  $module_obj->getVar('dirname', 'n');
	$infos    =& $module_obj->getInfo('blocks');

	$this->_msg( '<h3>'.$mod_name.'</h3>' );

	if ( !is_array($infos) || !count($infos) )
	{
		$this->_msg( 'No block' );
		return $this->_get_msg();
	}

	$this->_is_special = $this->_is_special_module( $dirname );

	$block_objs =& $this->_get_block_object_orber_num_by_mid( $mid );

	foreach ( $infos as $num => $info )
	{
		if ( !isset( $block_objs[ $num ] ) )
		{
			$this->_err( htmlspecialchars( $info['name'] ).': not exist in block table' );
			continue;
		}

		$this->_check_block_by_obj( $info, $block_objs[ $num ] );
	}

	$url = $this->_build_url_module_update( $dirname );
	$this->_msg( '' );
	$this->_msg( '<a href="'.$url.'">GO to Module Update: '.$mod_name.'</a>' );
	$this->_msg( '' );
	$this->_msg( '<input type="submit" name="mid:'.$mid.'" value="Remove Block: '.$mod_name.'" />' );

	return $this->_get_msg();
}

function _check_block_by_obj( &$info, &$block_obj )
{
	$this->_error_flag = false;

	$name = htmlspecialchars( $info['name'] );

	if ( isset($info['file']) && ( $info['file'] != $block_obj->getVar('func_file', 'n') ) )
	{
		$this->_err( $name.': file unmatch' );
	}

	if ( isset($info['show_func']) && ( $info['show_func'] != $block_obj->getVar('show_func', 'n') ) )
	{
		$this->_err( $name.': show_func unmatch' );
	}

	if ( isset($info['edit_func']) && ( $info['edit_func'] != $block_obj->getVar('edit_func', 'n') ) )
	{
		$this->_err( $name.': edit_func unmatch' );
	}

	if ( isset($info['template']) && ( $info['template'] != $block_obj->getVar('template', 'n') ) )
	{
		$this->_err( $name.': template unmatch' );
	}

	if ( isset($info['options']) )
	{
		$option_arr_1 = explode( '|', $info['options'] );
		$option_arr_2 = explode( '|', $block_obj->getVar('options', 'n') );

		if ( count($option_arr_1) != count($option_arr_2) )
		{
			$this->_err( $name.': options count unmatch' );
		}
		if ( $this->_is_special && ( $option_arr_1[0] != $option_arr_2[0] ) )
		{
			$this->_err( $name.': options dirname unmatch' );
		}
	}

	if ( !$this->_error_flag )
	{
		$this->_msg( $name.': OK' );
	}
}

function _remove_block_by_mid( $mid )
{
	$error = false;
	$objs =& $this->_get_block_object_by_mid( $mid );
	foreach ( $objs as $obj )
	{
		$ret = $this->_delete_block( $obj );
		if ( !$ret )
		{	$error = true;	}
	}
	if ( $error )
	{	return false;	}
	return true;
}

function _msg( $msg )
{
	$this->_msg_array[] = $msg;
}

function _err( $msg )
{
	$this->_msg_array[] = $this->_highlight( $msg );
	$this->_error_flag  = true;
}

function _get_msg()
{
	$msg = implode( "<br />\n", $this->_msg_array );
	return $msg;
}

function _highlight( $msg )
{
	$text = null;
	if ( $msg )
	{
		$text = '<span style="color: #ff0000;">'.$msg.'</span>';
	}
	return $text;
}

function _build_url_module_update( $dirname )
{
	if ( $this->_xoops_version == '2.1' ) {
		$url = XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='.$dirname;
	} else {
		$url = XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&op=update&module='.$dirname;
	}
	return $url;
}

//--------------------------------------------------------
// special module
//--------------------------------------------------------
function _is_special_module( $dirname )
{
	$dir = XOOPS_ROOT_PATH.'/modules/'.$dirname;

	if ( file_exists( $dir.'/include/weblinks_version.php' ) ) 
	{	return true;	}

	if ( file_exists( $dir.'/include/rssc_version.php' ) )
	{	return true;	}

	if ( file_exists( $dir.'/include/whatsnew_version.php' ) )
	{	return true;	}

	if ( file_exists( $dir.'/include/happy_search_version.php' ) ) 
	{	return true;	}

	return false;
}

//--------------------------------------------------------
// user handler
//--------------------------------------------------------
function is_admin()
{
	global $xoopsUser;
	if ( is_object($xoopsUser) && $xoopsUser->isAdmin() )
	{	return true;	}
	return false;
}

//--------------------------------------------------------
// module handler
//--------------------------------------------------------
function &_get_module_objects()
{
	$criteria = new CriteriaCompo();
	$criteria->add( new Criteria('isactive', '1', '=') );
	$module_handler =& xoops_gethandler('module');
	$objs           =& $module_handler->getObjects( $criteria );
	return $objs;
}

function &_get_module_object_by_mid( $mid )
{
	$module_handler =& xoops_gethandler('module');
	$obj            =& $module_handler->get( $mid );
	return $obj;
}

//--------------------------------------------------------
// block handler
//--------------------------------------------------------
function &_get_block_object_orber_num_by_mid( $mid )
{
	$arr  = array();
	$objs =& $this->_get_block_object_by_mid( $mid );
	foreach ( $objs as $obj )
	{
		$arr[ $obj->getVar('func_num', 'n') ] = $obj;
	}
	return $arr;
}

function &_get_block_object_by_mid( $mid, $asobject=true )
{
	$objs =& xoopsBlock::getByModule( $mid, $asobject );
	return $objs;
}

function _delete_block( &$obj )
{
// NOT use xoops_gethandler in xoops 2.0.16jp
	return $obj->delete();
}

//--------------------------------------------------------
// token handler
//--------------------------------------------------------
function _create_token()
{
	$token_handler =& xoops_gethandler('SingleToken');
	$obj =& $token_handler->quickCreate( $this->_TOKEN_NAME );
	return $obj->getHtml();
}

function _validate_token()
{
	$token_handler =& xoops_gethandler('SingleToken');
	return $token_handler->quickValidate( $this->_TOKEN_NAME );
}

//--------------------------------------------------------
// xoops version
//--------------------------------------------------------
function get_xoops_version()
{
	return $this->_xoops_version;
}

function _detect_xoops_version()
{
// very easy way
	if ( preg_match("/XOOPS[\s+]Cube.*[\s+]2\.1/i", XOOPS_VERSION) )
	{
		$this->_xoops_version = '2.1';
	}

// xoops 2.0.17 has no token class
	if ( file_exists( XOOPS_ROOT_PATH.'/class/token.php' ) ) 
	{
		$this->_use_token = true;
	}
}

// --- class end ---
}

//=========================================================
// main
//=========================================================

include XOOPS_ROOT_PATH.'/header.php';

$xoops_block_check =& xoops_block_check::getInstance();

if ( !$xoops_block_check->is_admin() )
{
	include XOOPS_ROOT_PATH.'/footer.php';
	exit();
}

switch ( $xoops_block_check->get_op() ) 
{
case "remove_block":
	$cont = $xoops_block_check->remove_block();
	break;

default:
	$cont = $xoops_block_check->check_blocks();
	break;

}

if ( $xoops_block_check->get_xoops_version() == '2.1' ) {
	$xoopsTpl->assign( 'xoops_contents', $cont );
} else {
	echo $cont;
}

include XOOPS_ROOT_PATH.'/footer.php';
exit();

?>