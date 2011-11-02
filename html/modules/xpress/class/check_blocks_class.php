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

include_once dirname( __FILE__ ).'/../../../mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php';

//=========================================================
// class xoops_block_check
//=========================================================
class xoops_block_check
{
	var $_TOKEN_NAME = 'block';
	var $module_id        = 0;
	var $_msg_array  = array();
	var $_error_flag = false;
	var $_xoops_version = '2.0';
	var $_use_token     = false;
	var $_blocks_check_array = array();
	var $_all_ok_flag = true;
	var $_module_dir = '';
	var $module_name = '';
	var $module_dirname = '';
	var $remove_submit_form;
	var $update_link;
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
function get_message()
{
	return $this->_get_msg();
}

function get_op()
{
	$op  = '';
	$mid = 0;
	foreach ( $_POST as $k => $v )
	{
		if ( preg_match( "/^amid:/", $k ) )
		{
			$op  = 'remove_all_block';
			$mid = intval( str_replace("amid:", "", $k) );
			break;
		}
		if ( preg_match( "/^mid:/", $k ) )
		{
			$op  = 'remove_block';
			$mid = intval( str_replace("mid:", "", $k) );
			break;
		}
	}
	$this->module_id = $mid;
	return $op;
}

function check_blocks($module_dir)
{
	$this->_all_ok_flag = true;
	$this->_module_dir = $module_dir;
	$this->module_dirname = $module_dir;

	if ( $this->_use_token )
	{
		$text = $this->_create_token();
	}

	$objs =& $this->_get_module_objects();
	$obj = $objs[0];
	if (!empty($obj)){
		$this->module_id = $obj->getVar('mid', 'n');
		$url = $this->_build_url_module_update( $this->module_dirname );
		$this->update_link = $this->_build_url_module_update( $this->module_dirname );
		$this->remove_submit_form .= '<input type="submit" name="mid:'.$this->module_id.'" value="Remove Block: '.$this->module_name.'" />'."\n";

		return $this->_check_block_by_module( $obj );

	} else {
		$this->_err( 'Modules Not Found' );
		return false;
	}
}

function remove_all_block()
{
	$text = "<h1>Remove xoops block table</h1>\n";


/*
	if( $this->_use_token && !$this->_validate_token() ) 
	{
		$text .= '<h3 style="color:#ff0000">Token Error</h3>'."\n";
		$text .= '<a href="block_check.php">Check Bloks</a>';
		return $text;
	}
*/
	$module_obj =& $this->_get_module_object_bymodule_id( $this->module_id );
	if( !is_object($module_obj) ) 
	{
		$text .= '<h3 style="color:#ff0000">"No Module: mid='. $this->module_id ."</h3>\n";
		$text .= "<br />\n";
		$text .= '<a href="check_blocks.php">Check Bloks</a>';
		return $text;
	}

	$ret = $this->_remove_block_bymodule_id( $this->module_id );
	if ( $ret ) {
		$text .= '<h4 style="color:#0000ff">Success</h4>'."\n";
	} else {
		$text .= '<h4 style="color:#ff0000">Failed</h4>'."\n";
	}

	$url = $this->_build_url_module_update( $module_obj->getVar('dirname', 'n') );
	$text .= '<a href="'.$url.'">GO to Module Update: '.$module_obj->getVar('name', 's')."</a><br /><br />\n";
//	$text .= '<a href="check_blocks.php">Check xoops block table</a><br />'."\n";

	return $text;
}
function remove_block()
{
	$text = "<h1>Remove xoops block table</h1>\n";

	$error = false;
	$block_objs =& $this->_get_block_object_orber_num_bymodule_id( $this->module_id);
	$module_obj =& $this->_get_module_object_bymodule_id( $this->module_id );
	$infos    =& $module_obj->getInfo('blocks');

	foreach ( $infos as $num => $info )
	{
		$block_err = false;
		if ( !isset( $block_objs[ $num ] ) ){
			$block_err = true;
		} else {
			$block_obj = $block_objs[ $num ];
			if ( isset($info['file']) && ( $info['file'] != $block_obj->getVar('func_file', 'n') ) ) $block_err = true;
			if ( isset($info['show_func']) && ( $info['show_func'] != $block_obj->getVar('show_func', 'n') ) ) $block_err = true;
			if ( isset($info['edit_func']) && ( $info['edit_func'] != $block_obj->getVar('edit_func', 'n') ) ) $block_err = true;
			if ( isset($info['template']) && ( $info['template'] != $block_obj->getVar('template', 'n') ) ) $block_err = true;
			if ( isset($info['options']) ){
				$option_arr_1 = explode( '|', $info['options'] );
				$option_arr_2 = explode( '|', $block_obj->getVar('options', 'n') );
			
				$excludes_block = array();
			
				if (in_array($info['file'],$excludes_block)){
					if ( count($option_arr_1) > count($option_arr_2) ) $block_err = true;
				} else {
					if ( count($option_arr_1) != count($option_arr_2) ) $block_err = true;
				}
			}
		}
		if ($block_err){
			$ret = $this->_delete_block( $block_obj );
			if ( !$ret ) $error = true;
		}
	}

	if ( !$error ) {
		$text .= '<h4 style="color:#0000ff">Success</h4>'."\n";
	} else {
		$text .= '<h4 style="color:#ff0000">Failed</h4>'."\n";
	}

	$url = $this->_build_url_module_update( $module_obj->getVar('dirname', 'n') );
	$text .= '<a href="'.$url.'">GO to Module Update: '.$module_obj->getVar('name', 's')."</a><br /><br />\n";
//	$text .= '<a href="check_blocks.php">Check xoops block table</a><br />'."\n";

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

	$this->module_name = $mod_name ;
	$this->module_dirname = $dirname ;
	

	if ( !is_array($infos) || !count($infos) )
	{
		$this->_msg( 'No block' );
		return $this->_get_msg();
	}


	$block_objs =& $this->_get_block_object_orber_num_bymodule_id( $mid );

	foreach ( $infos as $num => $info )
	{
		if ( !isset( $block_objs[ $num ] ) )
		{
			$this->_err( htmlspecialchars( $info['name'] ).': not exist in block table' );
			$this->_all_ok_flag = false;

			continue;
		}

		$this->_check_block_by_obj( $info, $block_objs[ $num ] );
	}

	if ($this->_all_ok_flag){
	} else {		
	}

	return $this->_all_ok_flag;
}

function _check_block_by_obj( &$info, &$block_obj )
{
	$this->_error_flag = false;
	
	$bid = $block_obj->getVar('bid', 'n');
	$edit_url = $this->_build_url_block_edit( $bid );
	$name = '<a href="' . $edit_url . '">'. htmlspecialchars( $info['name'] ). '</a>';
//	$name = htmlspecialchars( $info['name'] );

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
		
		$excludes_block = array();
		
		if (in_array($info['file'],$excludes_block)){
			if ( count($option_arr_1) > count($option_arr_2) )
			{
				$this->_err( $name.': options count unmatch' );
			}

		} else {
			if ( count($option_arr_1) != count($option_arr_2) )
			{
				$this->_err( $name.': options count unmatch' );
			}
		}
	}

	if ( !$this->_error_flag )
	{
		$this->_msg( $name.': OK' );
	} else {
		$this->_all_ok_flag = false;
	}

}

function _remove_block_bymodule_id( $mid )
{
	$error = false;
	$objs =& $this->_get_block_object_bymodule_id( $mid );
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
	$this->_msg_array[] = '&emsp;' .$msg;
}

function _err( $msg )
{
	$this->_msg_array[] = '&emsp;' . $this->_highlight( $msg );
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
function _build_url_block_edit( $bid )
{
	$dir_name = $this->module_dirname;
	if (file_exists(XOOPS_ROOT_PATH . '/modules/altsys/admin/index.php')){
//		$url = XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname=xp_trunk&op=edit&bid='.$bid;
		$url = XOOPS_URL.'/modules/'.$dir_name.'/admin/index.php?mode=admin&lib=altsys&page=myblocksadmin&dirname='.$dir_name.'xp_trunk&op=edit&bid='.$bid;
	} else if ( $this->_xoops_version == '2.1' ) {
		$url = XOOPS_URL.'/modules/legacy/admin/index.php?action=BlockEdit&bid='.$bid;
	} else {
		$url = XOOPS_URL.'/modules/system/admin.php?fct=blocksadmin&op=mod&bid='.$bid;
	}
	return $url;
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
	$criteria->add( new Criteria('dirname', $this->_module_dir, '=') );

	$module_handler =& xoops_gethandler('module');
	$objs           =& $module_handler->getObjects( $criteria );
	return $objs;
}

function &_get_module_object_bymodule_id( $mid )
{
	$module_handler =& xoops_gethandler('module');
	$obj            =& $module_handler->get( $mid );
	return $obj;
}

//--------------------------------------------------------
// block handler
//--------------------------------------------------------
function &_get_block_object_orber_num_bymodule_id( $mid )
{
	$arr  = array();
	$objs =& $this->_get_block_object_bymodule_id( $mid );
	foreach ( $objs as $obj )
	{
		$arr[ $obj->getVar('func_num', 'n') ] = $obj;
	}
	return $arr;
}

function &_get_block_object_bymodule_id( $mid, $asobject=true )
{
	if ( defined('ICMS_VERSION_BUILD') && ICMS_VERSION_BUILD > 27  ) { /* ImpressCMS 1.2+ */
		$block_handler =& xoops_gethandler ('block');
		$objs =& $block_handler->getByModule( $mid, $asobject );
	} else { /* legacy support */
		$objs =& XoopsBlock::getByModule( $mid, $asobject ) ; /* from class/xoopsblock.php */
	}
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

?>