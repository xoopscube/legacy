<?php
// $Id: MyBlocksAdmin.class.php ,ver 0.0.7.1 2011/02/15 02:55:00 domifara Exp $

class MyBlocksAdmin {

var $db ;
var $lang ;
var $cachetime_options = array() ;
var $ctype_options = array() ;
var $type_options = array() ;
var $target_mid = 0 ;
var $target_dirname = '' ;
var $target_mname = '' ;
var $block_configs = array() ;
var $preview_request = array() ;

function MyBlocksAadmin()
{
}


function construct()
{
	$this->db =& Database::getInstance() ;
	$this->lang = @$GLOBALS['xoopsConfig']['language'] ;

	$this->cachetime_options = array(
		0 => _NOCACHE ,
		30 => sprintf( _SECONDS , 30) ,
		60 => _MINUTE ,
		300 => sprintf( _MINUTES , 5) ,
		1800 => sprintf( _MINUTES , 30) ,
		3600 => _HOUR ,
		18000 => sprintf( _HOURS , 5 ) ,
		86400 => _DAY ,
		259200 => sprintf( _DAYS , 3 ) ,
		604800 => _WEEK ,
		2592000 => _MONTH ,
	);

	$this->ctype_options = array(
		'H' => _MD_A_MYBLOCKSADMIN_CTYPE_HTML ,
		'T' => _MD_A_MYBLOCKSADMIN_CTYPE_NOSMILE ,
		'S' => _MD_A_MYBLOCKSADMIN_CTYPE_SMILE ,
		'P' => _MD_A_MYBLOCKSADMIN_CTYPE_PHP ,
	) ;

	$this->type_options = array(
		'C' => 'custom block' ,
		'E' => 'cloned custom block' ,
		'M' => 'module\'s block' ,
		'D' => 'cloned module\'s block' ,
		'S' => 'system block' ,
	) ;
}

//HACK by domifara for php5.3+
//function &getInstance()
public static function &getInstance()
{
	static $instance;
	if (!isset($instance)) {
		$instance = new MyBlocksAdmin() ;
		$instance->construct() ;
	}
	return $instance;
}


// virtual
function checkPermission()
{
	// only groups have 'module_admin' of 'altsys' can do that.
	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( 'altsys' ) ;
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
	if( ! is_object( @$GLOBALS['xoopsUser'] ) || ! $moduleperm_handler->checkRight( 'module_admin' , $module->getVar( 'mid' ) , $GLOBALS['xoopsUser']->getGroups() ) ) die( 'only admin of altsys can access this area' ) ;
}


function init( $xoopsModule )
{
	// altsys "module" MODE
	if( $xoopsModule->getVar('dirname') == 'altsys' ) {
		// set target_module if specified by $_GET['dirname']
		$module_handler =& xoops_gethandler('module');
		if( ! empty( $_GET['dirname'] ) ) {
			$dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $_GET['dirname'] ) ;
			$target_module =& $module_handler->getByDirname( $dirname ) ;
		}

		if( is_object( @$target_module ) ) {
			// module's blocks
			$this->target_mid = $target_module->getVar( 'mid' ) ;
			$this->target_mname = $target_module->getVar( 'name' ) . "&nbsp;" . sprintf( "(%2.2f)" , $target_module->getVar('version') / 100.0 ) ;
			$this->target_dirname = $target_module->getVar( 'dirname' ) ;
			$modinfo = $target_module->getInfo() ;
			// breadcrumbs
			$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
			$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin' , '_MI_ALTSYS_MENU_MYBLOCKSADMIN' ) ;
			$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname='.$this->target_dirname , $this->target_mname ) ;
		} else {
			// custom blocks
			$this->target_mid = 0 ;
			$this->target_mname = _MI_ALTSYS_MENU_CUSTOMBLOCKS ;
			$this->target_dirname = '__CustomBlocks__' ;
			// breadcrumbs
			$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
			$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin' , '_MI_ALTSYS_MENU_MYBLOCKSADMIN' ) ;
			$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname='.$this->target_dirname , '_MI_ALTSYS_MENU_CUSTOMBLOCKS' ) ;
		}
	} else {
		// myblocksadmin as a library
		$this->target_mid = $xoopsModule->getVar( 'mid' ) ;
		$this->target_mname = $xoopsModule->getVar( 'name' ) . "&nbsp;" . sprintf( "(%2.2f)" , $xoopsModule->getVar('version') / 100.0 ) ;
		$this->target_dirname = $xoopsModule->getVar( 'dirname' ) ;
		$mod_url = XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname') ;
		$modinfo = $xoopsModule->getInfo() ;
		$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
		$breadcrumbsObj->appendPath( $mod_url.'/'.@$modinfo['adminindex'] , $this->target_mname ) ;
		$breadcrumbsObj->appendPath( $mod_url.'/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin' , _MD_A_MYBLOCKSADMIN_BLOCKADMIN ) ;
	}

	// read xoops_version.php of the target
	$this->block_configs = $this->get_block_configs() ;
}


// virtual
function canEdit( $block )
{
	return true ;
}


// virtual
function canDelete( $block )
{
	// can delete if it is a cloned block
	if( $block->getVar("block_type") == 'D' || $block->getVar("block_type") == 'C' ) {
		return true ;
	} else {
		return false ;
	}
}


// virtual
// ret 0 : cannot
// ret 1 : forced by altsys or system
// ret 2 : can_clone
function canClone( $block )
{
	// can clone link if it is marked as cloneable block
	if( $block->getVar("block_type") == 'D' || $block->getVar("block_type") == 'C' ) {
		return 2 ;
	} else {
		// $modversion['blocks'][n]['can_clone']
		foreach( $this->block_configs as $bconf ) {
			if( $block->getVar("show_func") == @$bconf['show_func'] && $block->getVar("func_file") == @$bconf['file'] && ( empty( $bconf['template'] ) || $block->getVar("template") == @$bconf['template'] ) ) {
				if( ! empty( $bconf['can_clone'] ) ) return 2 ;
			}
		}
	}

	if( ! empty( $GLOBALS['altsysModuleConfig']['enable_force_clone'] ) ) {
		return 1 ;
	}

	return 0 ;
}


// virtual
// options
function renderCell4BlockOptions( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock( $bid ) ;
	}
	return $block->getOptions() ;
}


// virtual
// link blocks - modules
function renderCell4BlockModuleLink( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

	// get selected targets
	if( is_array( @$block_data['bmodule'] ) ) {
		// bmodule origined from request (preview etc.)
		$selected_mids = $block_data['bmodule'] ;
	} else {
		// origined from the table of `block_module_link`
		$result = $this->db->query( "SELECT module_id FROM ".$this->db->prefix('block_module_link')." WHERE block_id='$bid'" ) ;
		$selected_mids = array();
		while ( list( $selected_mid ) = $this->db->fetchRow( $result ) ) {
			$selected_mids[] = intval( $selected_mid ) ;
		}
		if( empty( $selected_mids ) ) $selected_mids = array( 0 ) ; // all pages
	}

	// get all targets
	$module_handler =& xoops_gethandler('module');
	$criteria = new CriteriaCompo(new Criteria('hasmain', 1));
	$criteria->add(new Criteria('isactive', 1));
	$module_list = $module_handler->getList($criteria);
	$module_list= array( -1 => _MD_A_MYBLOCKSADMIN_TOPPAGE , 0 => _MD_A_MYBLOCKSADMIN_ALLPAGES ) + $module_list ;

	// build options
	$module_options = '' ;
	foreach( $module_list as $mid => $mname ) {
		$mname = htmlspecialchars( $mname ) ;
		if( in_array( $mid , $selected_mids ) ) {
			$module_options .= "<option value='$mid' selected='selected'>$mname</option>\n" ;
		} else {
			$module_options .= "<option value='$mid'>$mname</option>\n" ;
		}
	}

	$ret = "
				<select name='bmodules[$bid][]' size='5' multiple='multiple'>
					$module_options
				</select>" ;

	return $ret ;
}


// virtual
// group_permission - 'block_read'
function renderCell4BlockReadGroupPerm( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

	// get selected targets
	if( is_array( @$block_data['bgroup'] ) ) {
		// bgroup origined from request (preview etc.)
		$selected_gids = $block_data['bgroup'] ;
	} else {
		// origined from the table of `group_perm`
		$result = $this->db->query( "SELECT gperm_groupid FROM ".$this->db->prefix('group_permission')." WHERE gperm_itemid='$bid' AND gperm_name='block_read'" ) ;
		$selected_gids = array();
		while ( list( $selected_gid ) = $this->db->fetchRow( $result ) ) {
			$selected_gids[] = intval( $selected_gid ) ;
		}
		if( $bid == 0 && empty( $selected_gids ) ) $selected_gids = $GLOBALS['xoopsUser']->getGroups() ;
	}

	// get all targets
	$group_handler =& xoops_gethandler('group');
	$groups = $group_handler->getObjects() ;

	// build options
	$group_options = '' ;
	foreach( $groups as $group ) {
		$gid = $group->getVar('groupid') ;
		$gname = $group->getVar('name','s') ;
		if( in_array( $gid , $selected_gids ) ) {
			$group_options .= "<option value='$gid' selected='selected'>$gname</option>\n" ;
		} else {
			$group_options .= "<option value='$gid'>$gname</option>\n" ;
		}
	}

	$ret = "
				<select name='bgroups[$bid][]' size='5' multiple='multiple'>
					$group_options
				</select>" ;

	return $ret ;
}


// virtual
// visible and side
function renderCell4BlockPosition( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;
	$side = intval( $block_data['side'] ) ;
	$visible = intval( $block_data['visible'] ) ;

	$sseln = $ssel0 = $ssel1 = $ssel2 = $ssel3 = $ssel4 = "";
	$scoln = $scol0 = $scol1 = $scol2 = $scol3 = $scol4 = "unselected";
	$stextbox = "unselected" ;
	$value4extra_side = '' ;

	if( $visible != 1 ) {
		$sseln = " checked='checked'";
		$scoln = "disabled";
	} else switch( $side ) {
		case XOOPS_SIDEBLOCK_LEFT :
			$ssel0 = " checked='checked'";
			$scol0 = "selected";
			break ;
		case XOOPS_SIDEBLOCK_RIGHT :
			$ssel1 = " checked='checked'";
			$scol1 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_LEFT :
			$ssel2 = " checked='checked'";
			$scol2 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_RIGHT :
			$ssel4 = " checked='checked'";
			$scol4 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_CENTER :
			$ssel3 = " checked='checked'";
			$scol3 = "selected";
			break ;
		default :
			$value4extra_side = $side ;
			$stextbox = "selected" ;
			break ;
	}

	return "
				<div class='blockposition $scol0'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_SIDEBLOCK_LEFT."' class='blockposition' $ssel0 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_SIDEBLOCK_LEFT.";' />
				</div>
				<div style='float:"._GLOBAL_LEFT.";'>-</div>
				<div class='blockposition $scol2'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_LEFT."' class='blockposition' $ssel2 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_LEFT.";' />
				</div>
				<div class='blockposition $scol3'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_CENTER."' class='blockposition' $ssel3 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_CENTER.";' />
				</div>
				<div class='blockposition $scol4'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_RIGHT."' class='blockposition' $ssel4 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_RIGHT.";' />
				</div>
				<div style='float:"._GLOBAL_LEFT.";'>-</div>
				<div class='blockposition $scol1'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_SIDEBLOCK_RIGHT."' class='blockposition' $ssel1 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_SIDEBLOCK_RIGHT.";' />
				</div>
				<br />
				<br />
				<div style='float:"._GLOBAL_LEFT.";width:50px;' class='$stextbox'>
					<input type='text' name='extra_sides[$bid]' value='".$value4extra_side."' style='width:20px;' id='extra_side_$bid' />
				</div>
				<div class='blockposition $scoln'>
					<input type='radio' name='sides[$bid]' value='-1' class='blockposition' $sseln onclick='document.getElementById(\"extra_side_$bid\").value=-1;' />
				</div>
				<div style='float:"._GLOBAL_LEFT.";'>"._NONE."</div>
	" ;
}


// public
function list_blocks()
{
	global $xoopsGTicket ;

	// main query
	$sql = "SELECT * FROM ".$this->db->prefix("newblocks")." WHERE mid='$this->target_mid' ORDER BY visible DESC,side,weight" ;
	$result = $this->db->query( $sql ) ;
	$block_arr = array() ;
//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');//add
	}
	while( $myrow = $this->db->fetchArray( $result ) ) {

//HACK by domifara
		if (defined( 'XOOPS_CUBE_LEGACY' )){
			$block_one =& $handler->create(false) ;
			$block_one->assignVars($myrow);
			$block_arr[] =& $block_one ;
		}else{
			$block_arr[] = new XoopsBlock( $myrow ) ;
		}

	}
	if( empty( $block_arr ) ) return ;

	// blocks rendering loop
	$blocks4assign = array() ;
	foreach( $block_arr as $i => $block ) {
		$block_data = array(
			'bid' => intval( $block->getVar( 'bid' ) ) ,
			'name' => $block->getVar( 'name' , 'n' ) ,
			'title' => $block->getVar( 'title' , 'n' ) ,
			'weight' => intval( $block->getVar( 'weight' ) ) ,
			'bcachetime' => intval( $block->getVar( 'bcachetime' ) ) ,
			'side' => intval( $block->getVar('side') ) ,
			'visible' => intval( $block->getVar('visible') ) ,
			'can_edit' => $this->canEdit( $block ) ,
			'can_delete' => $this->canDelete( $block ) ,
			'can_clone' => $this->canClone( $block ) ,
		) ;
		$blocks4assign[] = array(
			'name_raw' => $block_data['name'] ,
			'title_raw' => $block_data['title'] ,
			'cell_position' => $this->renderCell4BlockPosition( $block_data ) ,
			'cell_module_link' =>  $this->renderCell4BlockModuleLink( $block_data ) ,
			'cell_group_perm' =>  $this->renderCell4BlockReadGroupPerm( $block_data ) ,
		) + $block_data ;
	}

	// display
	require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
	$tpl = new D3Tpl() ;
	$tpl->assign( array(
		'target_mid' => $this->target_mid ,
		'target_dirname' => $this->target_dirname ,
		'target_mname' => $this->target_mname ,
		'language' => $this->lang ,
		'cachetime_options' => $this->cachetime_options ,
		'blocks' => $blocks4assign ,
		'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'myblocksadmin') ,
	) ) ;
	$tpl->display( 'db:altsys_main_myblocksadmin_list.html' ) ;
}


function get_block_configs()
{
	if( $this->target_mid <= 0 ) return array() ;
	include XOOPS_ROOT_PATH.'/modules/'.$this->target_dirname.'/xoops_version.php' ;

	if( empty( $modversion['blocks'] ) ) return array() ;
	else return $modversion['blocks'] ;
}


function list_groups()
{
	// query for getting blocks
	$sql = "SELECT * FROM ".$this->db->prefix("newblocks")." WHERE mid='$this->target_mid' ORDER BY visible DESC,side,weight" ;
	$result = $this->db->query( $sql ) ;
	$block_arr = array() ;
//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');//add
	}
	while( $myrow = $this->db->fetchArray( $result ) ) {
//HACK by domifara
		if (defined( 'XOOPS_CUBE_LEGACY' )){
			$block_one =& $handler->create(false) ;
			$block_one->assignVars($myrow);
			$block_arr[] =& $block_one ;
		}else{
			$block_arr[] = new XoopsBlock( $myrow ) ;
		}
	}

	$item_list = array() ;
	foreach( array_keys( $block_arr ) as $i ) {
		$item_list[ $block_arr[$i]->getVar("bid") ] = $block_arr[$i]->getVar("title") ;
	}

	$form = new MyXoopsGroupPermForm( _MD_A_MYBLOCKSADMIN_PERMFORM , 1 , 'block_read' , '' ) ;
	// skip system (TODO)
	if( $this->target_mid > 1 ) {
		$form->addAppendix( 'module_admin' , $this->target_mid , $this->target_mname . ' ' . _MD_A_MYBLOCKSADMIN_PERM_MADMIN ) ;
		$form->addAppendix( 'module_read' , $this->target_mid , $this->target_mname .' ' . _MD_A_MYBLOCKSADMIN_PERM_MREAD ) ;
	}
	foreach( $item_list as $item_id => $item_name) {
			$form->addItem( $item_id , $item_name ) ;
	}
	echo $form->render() ;
}


function update_block($bid, $bside, $bweight, $bvisible, $btitle, $bcontent, $bctype, $bcachetime, $options=array())
{
	global $xoopsConfig;

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock($bid);
	}

	if( $bside >= 0 ) $block->setVar('side', $bside);
	$block->setVar('weight', $bweight);
	$block->setVar('visible', $bvisible);
	$block->setVar('title', $btitle);
	if( isset( $bcontent ) ) $block->setVar('content', $bcontent);
	if( isset( $bctype ) ) $block->setVar('c_type', $bctype);
	$block->setVar('bcachetime', $bcachetime);
	if( is_array( $options ) && count( $options ) > 0 ) {
		$block->setVar( 'options' , implode( '|' , $options ) ) ;
	}
	if( $block->getVar('block_type') == 'C' ) {
		$name = $this->get_blockname_from_ctype( $block->getVar('c_type') ) ;
		$block->setVar('name', $name);
	}
	$msg = _MD_A_MYBLOCKSADMIN_DBUPDATED;
	if ($block->store() != false) {
		include_once XOOPS_ROOT_PATH.'/class/template.php';
		$xoopsTpl = new XoopsTpl();
		$xoopsTpl->xoops_setCaching(2);
		if ($block->getVar('template') != '') {
			if ($xoopsTpl->is_cached('db:'.$block->getVar('template'))) {
				if (!$xoopsTpl->clear_cache('db:'.$block->getVar('template'))) {
					$msg = 'Unable to clear cache for block ID'.$bid;
				}
			}
		} else {
			if ($xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
				if (!$xoopsTpl->clear_cache('db:system_dummy.html', 'blk_'.$bid)) {
					$msg = 'Unable to clear cache for block ID'.$bid;
				}
			}
		}
	} else {
		$msg = 'Failed update of block. ID:'.$bid;
	}
	return $msg ;
}


// virtual
function updateBlockModuleLink( $bid , $bmodules )
{
	$bid = intval( $bid ) ;
	$table = $this->db->prefix("block_module_link") ;

	$sql = "DELETE FROM `$table` WHERE `block_id`=$bid" ;
	$this->db->query( $sql ) ;
	foreach( $bmodules as $mid ) {
		$mid = intval( $mid ) ;
		$sql = "INSERT INTO `$table` (`block_id`,`module_id`) VALUES ($bid,$mid)" ;
		$this->db->query( $sql ) ;
	}
}


// virtual
function updateBlockReadGroupPerm( $bid , $req_gids )
{
	$bid = intval( $bid ) ;
	$table = $this->db->prefix("group_permission") ;
	$req_gids = array_map( 'intval' , $req_gids ) ;
	sort( $req_gids ) ;

	// compare group ids from request and the records.
	$sql = "SELECT `gperm_groupid` FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid" ;
	$result = $this->db->query( $sql ) ;
	$db_gids = array() ;
	while( list( $gid ) = $this->db->fetchRow( $result ) ) $db_gids[] = $gid ;
	$db_gids = array_map( 'intval' , $db_gids ) ;
	sort( $db_gids ) ;

	// if they are identical, just return (prevent increase of gperm_id)
	if( serialize( $req_gids ) == serialize( $db_gids ) ) return ;

	$sql = "DELETE FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid" ;
	$this->db->query( $sql ) ;
	foreach( $req_gids as $gid ) {
		$gid = intval( $gid ) ;
		$sql = "INSERT INTO `$table` (`gperm_groupid`,`gperm_itemid`,`gperm_modid`,`gperm_name`) VALUES ($gid,$bid,1,'block_read')" ;
		$this->db->query( $sql ) ;
	}
}


function do_order()
{
	$sides = is_array( @$_POST['sides'] ) ? $_POST['sides'] : array() ;
	foreach( array_keys( $sides ) as $bid ) {
		$request = $this->fetchRequest4Block( $bid ) ;

		// update the block
		$this->update_block( $request['bid'] , $request['side'] , $request['weight'] , $request['visible'] , $request['title'] , null , null , $request['bcachetime'] , array() ) ;

		// block_module_link update
		$this->updateBlockModuleLink( $bid , $request['bmodule'] ) ;

		// group_permission update
		$this->updateBlockReadGroupPerm( $bid , $request['bgroup'] ) ;
	}
	return _MD_A_MYBLOCKSADMIN_DBUPDATED ;
}


function fetchRequest4Block( $bid )
{
	$bid = intval( $bid ) ;
	$myts =& MyTextSanitizer::getInstance() ;

	if( @$_POST['extra_sides'][$bid] > 0 ) {
		$_POST['sides'][$bid] = intval( $_POST['extra_sides'][$bid] ) ;
	}

	if( @$_POST['sides'][$bid] < 0 ) {
		$visible = 0 ;
		$_POST['sides'][$bid] = -1 ;
	} else {
		$visible = 1 ;
	}

	return array(
		'bid' => $bid ,
		'side' => intval( @$_POST['sides'][$bid] ) ,
		'weight' => intval( @$_POST['weights'][$bid] ) ,
		'visible' => $visible ,
		'title' => $myts->stripSlashesGPC( @$_POST['titles'][$bid] ) ,
		'content' => $myts->stripSlashesGPC( @$_POST['contents'][$bid] ) ,
		'ctype' => preg_replace( '/[^A-Z]/' , '' , @$_POST['ctypes'][$bid] ) ,
		'bcachetime' => intval( @$_POST['bcachetimes'][$bid] ) ,
		'bmodule' => is_array( @$_POST['bmodules'][$bid] ) ? $_POST['bmodules'][$bid] : array( 0 ) ,
		'bgroup' => is_array( @$_POST['bgroups'][$bid] ) ? $_POST['bgroups'][$bid] : array() ,
		'options' => is_array( @$_POST['options'][$bid] ) ? $_POST['options'][$bid] : array() ,
	) ;
}


function do_delete( $bid )
{
	$bid = intval( $bid ) ;

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock( $bid ) ;
	}

	if( ! is_object( $block ) ) die( 'Invalid bid' ) ;
	if( ! $this->canDelete( $block ) ) die( 'Cannot delete this block' ) ;
    $this->do_deleteBlockReadGroupPerm( $bid ); //HACK add by domifara
	$block->delete() ;
	return _MD_A_MYBLOCKSADMIN_DBUPDATED ;
}

//HACK add by domifara
function do_deleteBlockReadGroupPerm( $bid )
{
    $bid = intval( $bid ) ;
    $table = $this->db->prefix("group_permission") ;
    $sql = "DELETE FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid" ;
    $this->db->query( $sql ) ;
}

function form_delete( $bid )
{
	$bid = intval( $bid ) ;

//HACK by domifara
//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock( $bid ) ;
	}

	if( ! is_object( $block ) ) die( 'Invalid bid' ) ;
	if( ! $this->canDelete( $block ) ) die( 'Cannot delete this block' ) ;

	// breadcrumbs
	$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
	$breadcrumbsObj->appendPath( '' , _DELETE ) ;

	xoops_confirm( array( 'op' => 'delete_ok' ) + $GLOBALS['xoopsGTicket']->getTicketArray( __LINE__ , 1800 , 'myblocksadmin' ) , "?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname=$this->target_dirname&amp;bid=$bid" , sprintf( _MD_A_MYBLOCKSADMIN_FMT_REMOVEBLOCK , $block->getVar('title') ) ) ;
}


function do_clone( $bid )
{
	$bid = intval( $bid ) ;

	$request = $this->fetchRequest4Block( $bid ) ;

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock( $bid ) ;
	}

	if( ! $block->getVar('bid') ) die( 'Invalid bid' ) ;
	if( ! $this->canClone( $block ) ) die( 'Invalid block_type' ) ;

	if( empty( $_POST['options'] ) ) $options = array() ;
	else if( is_array( $_POST['options'] ) ) $options = $_POST['options'] ;
	else $options = explode( '|' , $_POST['options'] ) ;

	// for backward compatibility
	// $cblock =& $block->clone(); or $cblock =& $block->xoopsClone();

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$cblock =& $handler->create(false) ;
	}else{
		$cblock = new XoopsBlock() ;
	}

	foreach( $block->vars as $k => $v ) {
		$cblock->assignVar( $k , $v['value'] ) ;
	}
	$cblock->setNew();
	$cblock->setVar('bid', 0);
	$cblock->setVar('block_type', $block->getVar('block_type') == 'C' ? 'C' : 'D' );
	$cblock->setVar('func_num', $this->find_func_num_vacancy( $block->getVar('mid') ) ) ;
	// store the block into DB as a new one
	$newbid = $cblock->store() ;
	if( ! $newbid ) {
		return $cblock->getHtmlErrors() ;
	}

	// update the block by the request
	$this->update_block( $newbid , $request['side'] , $request['weight'] , $request['visible'] , $request['title'] , $request['content'] , $request['ctype'] , $request['bcachetime'] , is_array( @$_POST['options'] ) ? $_POST['options'] : array() ) ;

	// block_module_link update
	$this->updateBlockModuleLink( $newbid , $request['bmodule'] ) ;

	// group_permission update
	$this->updateBlockReadGroupPerm( $newbid , $request['bgroup'] ) ;

	return _MD_A_MYBLOCKSADMIN_DBUPDATED ;
}


function find_func_num_vacancy( $mid )
{
	$func_num = 256 ;
	do {
		$func_num -- ;
		list( $count ) = $this->db->fetchRow( $this->db->query( "SELECT COUNT(*) FROM ".$this->db->prefix("newblocks")." WHERE mid=".intval($mid)." AND func_num=".$func_num ) ) ;
	} while( $count > 0 ) ;

	return $func_num > 128 ? $func_num : 255 ;
}


function do_edit( $bid )
{
	$bid = intval( $bid ) ;

	if( $bid <= 0 ) {
		// new custom block

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$new_block =& $handler->create(false) ;
	}else{
		$new_block = new XoopsBlock() ;
	}

		$new_block->setNew() ;
		$new_block->setVar( 'name' , $this->get_blockname_from_ctype( 'C' ) ) ;
		$new_block->setVar( 'block_type' , 'C' ) ;
		$new_block->setVar( 'func_num' , 0 ) ;
		$bid = $new_block->store() ;
		$request = $this->fetchRequest4Block( 0 ) ;
		// permission copy
		foreach( $GLOBALS['xoopsUser']->getGroups() as $gid ) {
			$sql = "INSERT INTO ".$this->db->prefix('group_permission')." (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ($gid, $bid, 1, 'block_read')";
			$this->db->query($sql);
		}
	} else {
		$request = $this->fetchRequest4Block( $bid ) ;
	}

	// update the block by the request
	$msg = $this->update_block( $bid , $request['side'] , $request['weight'] , $request['visible'] , $request['title'] , $request['content'] , $request['ctype'] , $request['bcachetime'] , is_array( @$_POST['options'] ) ? $_POST['options'] : array() ) ;

	// block_module_link update
	$this->updateBlockModuleLink( $bid , $request['bmodule'] ) ;

	// group_permission update
	$this->updateBlockReadGroupPerm( $bid , $request['bgroup'] ) ;

	return $msg ;
}


function form_edit( $bid , $mode = 'edit' )
{
	$bid = intval( $bid ) ;

//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$handler =& xoops_gethandler('block');
		$block =& $handler->create(false) ;
		$block->load($bid) ;
	}else{
		$block = new XoopsBlock( $bid ) ;
	}

	if( ! $block->getVar('bid') ) {
		// new defaults
		$bid = 0 ;
		$mode = 'new' ;
		$block->setVar( 'mid' , 0 ) ;
		$block->setVar( 'block_type' , 'C' ) ;
	}

	switch( $mode ) {
		case 'clone' :
			$form_title = _MD_A_MYBLOCKSADMIN_CLONEFORM ;
			$button_value = _MD_A_MYBLOCKSADMIN_BTN_CLONE ;
			$next_op = 'clone_ok' ;
			// breadcrumbs
			$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
			$breadcrumbsObj->appendPath( '' , _MD_A_MYBLOCKSADMIN_CLONEFORM ) ;
			break ;
		case 'new' :
			$form_title = _MD_A_MYBLOCKSADMIN_NEWFORM ;
			$button_value = _MD_A_MYBLOCKSADMIN_BTN_NEW ;
			$next_op = 'new_ok' ;
			// breadcrumbs
			$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
			$breadcrumbsObj->appendPath( '' , _MD_A_MYBLOCKSADMIN_NEWFORM ) ;
			break ;
		case 'edit' :
		default :
			$form_title = _MD_A_MYBLOCKSADMIN_EDITFORM ;
			$button_value = _MD_A_MYBLOCKSADMIN_BTN_EDIT ;
			$next_op = 'edit_ok' ;
			// breadcrumbs
			$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
			$breadcrumbsObj->appendPath( '' , _MD_A_MYBLOCKSADMIN_EDITFORM ) ;
			break ;
	}

	$is_custom = in_array( $block->getVar('block_type') , array( 'C' , 'E' ) ) ? true : false ;
	$block_template = $block->getVar('template','n') ;
	$block_template_tplset = '' ;

	if( ! $is_custom && $block_template ) {
		// find template of the block
		$tplfile_handler =& xoops_gethandler('tplfile');
		$found_templates = $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], 'block', null , null, $block_template ) ;
		$block_template_tplset = count( $found_templates ) > 0 ? $GLOBALS['xoopsConfig']['template_set'] : 'default' ;
	}
//HACK by domifara
/*
	if ( !($block->getVar('c_type')) ){
		$block->setVar('c_type','S');
	}
*/
	$block_data = $this->preview_request + array(
		'bid' => $bid ,
		'name' => $block->getVar('name','n') ,
		'title' => $block->getVar('title','n') ,
		'weight' => intval( $block->getVar('weight') ) ,
		'bcachetime' => intval( $block->getVar('bcachetime') ) ,
		'side' => intval( $block->getVar('side') ) ,
		'visible' => intval( $block->getVar('visible') ) ,
		'template' => $block_template ,
		'template_tplset' => $block_template_tplset ,
		'options' => $block->getVar('options') ,
		'content' => $block->getVar('content', 'n') ,
		'is_custom' => $is_custom ,
		'type' => $block->getVar('block_type') ,
		'ctype' => $block->getVar('c_type') ,
	) ;

	$block4assign = array(
		'name_raw' => $block_data['name'] ,
		'title_raw' => $block_data['title'] ,
		'content_raw' => $block_data['content'] ,
		'cell_position' => $this->renderCell4BlockPosition( $block_data ) ,
		'cell_module_link' => $this->renderCell4BlockModuleLink( $block_data ) ,
		'cell_group_perm' =>  $this->renderCell4BlockReadGroupPerm( $block_data ) ,
		'cell_options' => $this->renderCell4BlockOptions( $block_data ) ,
		'content_preview' => $this->previewContent( $block_data ) ,
	) + $block_data ;

	// display
	require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
	$tpl = new D3Tpl() ;

	//HACK by domifara
	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$tpl->assign( 'xoops_cube_legacy' , true ) ;
		include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
	}else{
		$tpl->assign( 'xoops_cube_legacy' , false ) ;
	}

	$tpl->assign( array(
		'target_dirname' => $this->target_dirname ,
		'target_mname' => $this->target_mname ,
		'language' => $this->lang ,
		'cachetime_options' => $this->cachetime_options ,
		'ctype_options' => $this->ctype_options ,
		'block' => $block4assign ,
		'op' => $next_op ,
		'form_title' => $form_title ,
		'submit_button' => $button_value ,
		'common_fck_installed' => file_exists( XOOPS_ROOT_PATH.'/common/fckeditor/fckeditor.js' ) ,
		'gticket_hidden' => $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__ , 1800 , 'myblocksadmin') ,
	) ) ;

	if (defined( 'XOOPS_CUBE_LEGACY' )){
		$tpl->display( 'db:altsys_main_myblocksadmin_edit_4legacy.html' ) ;
	}else{
		$tpl->display( 'db:altsys_main_myblocksadmin_edit.html' ) ;
	}
	return ;
}


function previewContent( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

	if( ! $block_data['is_custom'] ) return '' ;
	if( empty( $this->preview_request ) ) return '' ;

//HACK by domifara
//TODO : need no hook block at this
	$block = new XoopsBlock( $bid ) ;
/*
	$handler =& xoops_gethandler('block');
	$block =& $handler->create(false) ;
	$block->load($bid) ;
*/

	if( $block->getVar( 'mid' ) ) return '' ;

	$block->setVar( 'title' , $block_data['title'] ) ;
	$block->setVar( 'content' , $block_data['content'] ) ;

	restore_error_handler() ;
	$original_level = error_reporting( E_ALL ) ;
	$ret = $block->getContent( 'S' , $block_data['ctype'] ) ;
	error_reporting( $original_level ) ;

	return $ret ;
}


function get_blockname_from_ctype( $bctype )
{
	$ctypes = array(
		'H' => _MD_A_MYBLOCKSADMIN_CTYPE_HTML ,
		'S' => _MD_A_MYBLOCKSADMIN_CTYPE_SMILE ,
		'N' => _MD_A_MYBLOCKSADMIN_CTYPE_NOSMILE ,
		'P' => _MD_A_MYBLOCKSADMIN_CTYPE_PHP ,
	) ;

	return isset( $ctypes[$bctype] ) ? $ctypes[$bctype] : _MD_A_MYBLOCKSADMIN_CTYPE_SMILE ;
}


function processPost()
{
	// Ticket Check
	if( ! $GLOBALS['xoopsGTicket']->check( true , 'myblocksadmin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$GLOBALS['xoopsGTicket']->getErrors());
	}

	$msg = '' ;
	$bid = intval( @$_GET['bid'] ) ;
	if( ! empty( $_POST['preview'] ) ) {
		// preview
		$this->preview_request = $this->fetchRequest4Block( $bid ) ;
		$_GET['op'] = str_replace( '_ok' , '' , @$_POST['op'] ) ;
		return ; // continue ;
	} else if( @$_POST['op'] == 'order' ) {
		// order ok
		$msg = $this->do_order() ;
	} else if( @$_POST['op'] == 'delete_ok' ) {
		// delete ok
		$msg = $this->do_delete( $bid ) ;
	} else if( @$_POST['op'] == 'clone_ok' ) {
		// clone ok
		$msg = $this->do_clone( $bid ) ;
	} else if( @$_POST['op'] == 'edit_ok' || @$_POST['op'] == 'new_ok' ) {
		// edit ok
		$msg = $this->do_edit( $bid ) ;
	} else if( ! empty( $_POST['submit'] ) ) {
		// update module_admin,module_read,block_read
		include dirname(dirname(__FILE__)).'/include/mygroupperm.php' ;
		$msg = _MD_A_MYBLOCKSADMIN_PERMUPDATED ;
	}

	redirect_header( '?mode=admin&lib=altsys&page=myblocksadmin&dirname='.$this->target_dirname , 1 , $msg ) ;
	exit ;
}


function processGet()
{
	$bid = intval( @$_GET['bid'] ) ;
	switch( @$_GET['op'] ) {
		case 'clone' :
			$this->form_edit( $bid , 'clone' ) ;
			break ;
		case 'new' :
		case 'edit' :
			$this->form_edit( $bid , 'edit' ) ;
			break ;
		case 'delete' :
			$this->form_delete( $bid ) ;
			break ;
		case 'list' :
		default :
			// the first form (blocks)
			$this->list_blocks() ;
			// the second form (groups)
			$this->list_groups() ;
			break ;
	}
}


}

?>