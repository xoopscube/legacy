<?php

require_once dirname(__FILE__).'/MyBlocksAdmin.class.php' ;

class MyBlocksAdminForX25 extends MyBlocksAdmin {

function MyBlocksAadminForX25()
{
}


function construct()
{
	parent::construct() ;

	@include_once XOOPS_ROOT_PATH.'/modules/system/language/'.$this->lang.'/admin/blocksadmin.php' ;
}

//HACK by domifara for php5.3+
//function &getInstance()
public static function &getInstance()
{
	static $instance;
	if (!isset($instance)) {
		$instance = new MyBlocksAdminForX25();
		$instance->construct() ;
	}
	return $instance;
}

// virtual
// visible and side
function renderCell4BlockPosition( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;
	$side = intval( $block_data['side'] ) ;
	$visible = intval( $block_data['visible'] ) ;

	$sseln = $ssel0 = $ssel1 = $ssel3 = $ssel4 = $ssel5 = $ssel7 = $ssel8 = $ssel9 = "";
	$scoln = $scol0 = $scol1 = $scol3 = $scol4 = $scol5 = $scol7 = $scol8 = $scol9 = "unselected";
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
			$ssel3 = " checked='checked'";
			$scol3 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_RIGHT :
			$ssel4 = " checked='checked'";
			$scol4 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_CENTER :
			$ssel5 = " checked='checked'";
			$scol5 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_BOTTOMLEFT :
			$ssel7 = " checked='checked'";
			$scol7 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_BOTTOMRIGHT :
			$ssel8 = " checked='checked'";
			$scol8 = "selected";
			break ;
		case XOOPS_CENTERBLOCK_BOTTOM :
			$ssel9 = " checked='checked'";
			$scol9 = "selected";
			break ;
		default :
			$value4extra_side = $side ;
			$stextbox = "selected" ;
			break ;
	}

	return "
	<table  cellspacing='0' style='table-layout: fixed; width: 200px; '>
		<tr>
			<td rowspan='2'>
				<div class='blockposition $scol0'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_SIDEBLOCK_LEFT."' class='blockposition' $ssel0 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_SIDEBLOCK_LEFT.";' />
				</div>
				<div style='float:"._GLOBAL_LEFT.";'>-</div>
			</td>
			<td>
				<div class='blockposition $scol3'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_LEFT."' class='blockposition' $ssel3 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_LEFT.";' />
				</div>
			</td>
			<td>
				<div class='blockposition $scol5'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_CENTER."' class='blockposition' $ssel5 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_CENTER.";' />
				</div>
			</td>
			<td>
				<div class='blockposition $scol4'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_RIGHT."' class='blockposition' $ssel4 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_RIGHT.";' />
				</div>
			</td>
			<td rowspan='2'>
				<div style='float:"._GLOBAL_LEFT.";'>-</div>
				<div class='blockposition $scol1'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_SIDEBLOCK_RIGHT."' class='blockposition' $ssel1 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_SIDEBLOCK_RIGHT.";' />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class='blockposition $scol7'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_BOTTOMLEFT."' class='blockposition' $ssel7 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_BOTTOMLEFT.";' />
				</div>
			</td>
			<td>
				<div class='blockposition $scol9'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_BOTTOM."' class='blockposition' $ssel9 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_BOTTOM.";' />
				</div>
			</td>
			<td>
				<div class='blockposition $scol8'>
					<input type='radio' name='sides[$bid]' value='".XOOPS_CENTERBLOCK_BOTTOMRIGHT."' class='blockposition' $ssel8 onclick='document.getElementById(\"extra_side_$bid\").value=".XOOPS_CENTERBLOCK_BOTTOMRIGHT.";' />
				</div>

			</td>
		</tr>
		<tr>
			<td colspan='5'>
				<div style='float:"._GLOBAL_LEFT.";width:50px;' class='$stextbox'>
					<input type='text' name='extra_sides[$bid]' value='".$value4extra_side."' style='width:20px;' id='extra_side_$bid' />
				</div>
				<div class='blockposition $scoln'>
					<input type='radio' name='sides[$bid]' value='-1' class='blockposition' $sseln onclick='document.getElementById(\"extra_side_$bid\").value=-1;' />
				</div>
				<div style='float:"._GLOBAL_LEFT.";'>"._NONE."</div>
			</td>
		</tr>
	</table>
	" ;
}

function form_edit( $bid , $mode = 'edit' )
{
	$bid = intval( $bid ) ;

//HACK by domifara
	$block = new XoopsBlock( $bid ) ;

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
//dhtml
	include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

	if ($block_data['ctype']=='H' || empty($block_data['ctype'])){
		$editor_configs=array();
		$editor_configs["name"] ="content_block";
		$editor_configs["value"] = $block_data['content'];
		$editor_configs["rows"] = 20;
		$editor_configs["cols"] = 100;
		$editor_configs["width"] = "100%";
		$editor_configs["height"] = "400px";
		$editor_configs["editor"] = xoops_getModuleOption('blocks_editor', 'system');
		$form= new XoopsFormEditor('', "textarea_content", $editor_configs);
		$rendered = $form->render();
		$tpl->assign( 'altsys_x25_dhtmltextarea' , $rendered ) ;
	}else{
		$form = new XoopsFormDhtmlTextArea('','textarea_content',$block_data['content'] , 80 , 20);
		$rendered = $form->render();
		$rendered = '<div id="textarea_content_bbcode_buttons_pre" style="display:block;">'.str_replace( array( '<textarea' , '</textarea><br />' ) , array( '</div><textarea' , '</textarea><div id="textarea_content_bbcode_buttons_post" style="display:block;">' ) , $rendered ) . '</div>' ;
		$tpl->assign( 'altsys_x25_dhtmltextarea' , $rendered ) ;
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
//HACK by domifara
	$tpl->display( 'db:altsys_main_myblocksadmin_edit_4x25.html' ) ;

	return ;
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
		'content' => $myts->stripSlashesGPC( @$_POST['textarea_content'] ) ,
		'ctype' => preg_replace( '/[^A-Z]/' , '' , @$_POST['ctypes'][$bid] ) ,
		'bcachetime' => intval( @$_POST['bcachetimes'][$bid] ) ,
		'bmodule' => is_array( @$_POST['bmodules'][$bid] ) ? $_POST['bmodules'][$bid] : array( 0 ) ,
		'bgroup' => is_array( @$_POST['bgroups'][$bid] ) ? $_POST['bgroups'][$bid] : array() ,
		'options' => is_array( @$_POST['options'][$bid] ) ? $_POST['options'][$bid] : array() ,
	) ;
}

function previewContent( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

	if( ! $block_data['is_custom'] ) return '' ;
	if( empty( $this->preview_request ) ) return '' ;

//HACK by domifara
//TODO : need no hook block at this
	$block = new XoopsBlock( $bid ) ;

	if( $block->getVar( 'mid' ) ) return '' ;

	$block->setVar( 'title' , $block_data['title'] ) ;
	$block->setVar( 'content' , $block_data['content'] ) ;

	restore_error_handler() ;
	$original_level = error_reporting( E_ALL ) ;

//	$ret = $block->getContent( 'S' , $block_data['ctype'] ) ;

	$c_type = $block_data['ctype'];
	if ($c_type == 'H') {
		$ret = str_replace('{X_SITEURL}', XOOPS_URL . '/', $block->getVar('content', 'N'));
	} else if ($c_type == 'P') {
		ob_start();
		echo eval($block->getVar('content', 'N'));
		$content = ob_get_contents();
		ob_end_clean();
		$ret = str_replace('{X_SITEURL}', XOOPS_URL . '/', $content);
	} else if ($c_type == 'S') {
		$myts =& MyTextSanitizer::getInstance();
		$content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $block->getVar('content', 'N'));
		$ret = $myts->displayTarea($content, 1, 1);
	} else {
		$myts =& MyTextSanitizer::getInstance();
		$content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $block->getVar('content', 'N'));
		$ret = $myts->displayTarea($content, 1, 0);
	}

	error_reporting( $original_level ) ;

	return $ret ;
}


}

?>
