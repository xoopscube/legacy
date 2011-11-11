<?php

require_once dirname(__FILE__).'/MyBlocksAdmin.class.php' ;

class MyBlocksAdminForICMS extends MyBlocksAdmin {

var $block_positions = array() ;

function MyBlocksAadminForICMS()
{
}


function construct()
{
	parent::construct() ;

	@include_once XOOPS_ROOT_PATH.'/modules/system/language/'.$this->lang.'/admin/blocksadmin.php' ;
	$result = $this->db->query( "SELECT id,pname,title FROM ".$this->db->prefix("block_positions") ) ;
	while( list( $id , $pname , $title ) = $this->db->fetchRow( $result ) ) {
		$this->block_positions[ $id ] = defined( $title ) ? constant( $title ) : $title ;
	}
	$this->block_positions[ -1 ] = _NONE ;
}

//HACK by domifara for php5.3+
//function &getInstance()
public static function &getInstance()
{
	static $instance;
	if (!isset($instance)) {
		$instance = new MyBlocksAdminForICMS();
		$instance->construct() ;
	}
	return $instance;
}


// virtual
// link blocks - modules - pages
function renderCell4BlockModuleLink( $block_data )
{
	$bid = intval( $block_data['bid'] ) ;

	// get selected targets
	if( is_array( @$block_data['bmodule'] ) ) {
		// bmodule origined from request (preview etc.)
		$selected_pages = $block_data['bmodule'] ;
	} else {
		// origined from the table of `block_module_link`
		$result = $this->db->query( "SELECT module_id,page_id FROM ".$this->db->prefix('block_module_link')." WHERE block_id='$bid'" ) ;
		$selected_pages = array();
		while ( list( $mid , $pid ) = $this->db->fetchRow( $result ) ) {
			$selected_pages[] = intval( $mid ) . '-' . intval( $pid ) ;
		}
	}

	$page_handler =& xoops_gethandler('page');
	$ret = "
				<select name='bmodules[$bid][]' size='5' multiple='multiple'>
					".$page_handler->getPageSelOptions( $selected_pages )."
				</select>" ;

	return $ret ;
}


// virtual
// visible and side
function renderCell4BlockPosition( $block_data )
{
	return "
	<table>
		<tr>
			<td rowspan='2'>".$this->renderRadio4BlockPosition(1,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(3,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(4,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(5,$block_data)."</td>
			<td rowspan='2'>".$this->renderRadio4BlockPosition(2,$block_data)."</td>
		</tr>
		<tr>
			<td>".$this->renderRadio4BlockPosition(6,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(7,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(8,$block_data)."</td>
		</tr>
		<tr>
			<td colspan='5'>".$this->renderRadio4BlockPositions($block_data,array(1,2,3,4,5,6,7,8))."</td>
		</tr>
	</table>" ;
}


// private
function renderRadio4BlockPosition( $target_side , $block_data )
{
	$bid = intval( $block_data['bid'] ) ;
	$visible = intval( $block_data['visible'] ) ;
	$current_side = $visible ? intval( $block_data['side'] ) : -1 ;

	$label4disp = htmlspecialchars( $this->block_positions[ $target_side ] , ENT_QUOTES ) ;

	if( $current_side == $target_side ) {
		$checked = "checked='checked'" ;
		$divstyle = $target_side == -1 ? "disabled" : "selected" ;
	} else {
		$checked = "" ;
		$divstyle = "unselected" ;
	}

	return "<div class='blockposition $divstyle' title='$label4disp'><input type='radio' name='sides[$bid]' value='$target_side' class='blockposition' $checked /></div>" ;
}


// private
function renderRadio4BlockPositions( $block_data , $skip_sides = array() )
{
	$bid = intval( $block_data['bid'] ) ;
	$visible = intval( $block_data['visible'] ) ;
	$current_side = $visible ? intval( $block_data['side'] ) : -1 ;

	$ret = '' ;
	foreach( $this->block_positions as $target_side => $label ) {
		if( in_array( $target_side , $skip_sides ) ) continue ;

		$label4disp = htmlspecialchars( $label , ENT_QUOTES ) ;

		if( $current_side == $target_side ) {
			$checked = "checked='checked'" ;
			$divstyle = $target_side == -1 ? "disabled" : "selected" ;
		} else {
			$checked = "" ;
			$divstyle = "unselected" ;
		}

		$ret .= "<div style='clear:both;'><div class='blockposition $divstyle' title='$label4disp'><input type='radio' name='sides[$bid]' id='sides_{$bid}_{$target_side}' value='$target_side' class='blockposition' $checked /></div><label for='sides_{$bid}_{$target_side}'>$label4disp</label></label></div>" ;
	}

	return $ret ;
}


// virtual
function updateBlockModuleLink( $bid , $bmodules )
{
	$bid = intval( $bid ) ;
	$table = $this->db->prefix("block_module_link") ;

	$sql = "DELETE FROM `$table` WHERE `block_id`=$bid" ;
	$this->db->query( $sql ) ;
	foreach( $bmodules as $mid ) {
		$regs = explode( '-' , $mid ) ;
		$module_id = intval( @$regs[0] ) ;
		$page_id = intval( @$regs[1] ) ;
		$sql = "INSERT INTO `$table` (`block_id`,`module_id`,`page_id`) VALUES ($bid,$module_id,$page_id)" ;
		$this->db->query( $sql ) ;
	}
}


}

?>
