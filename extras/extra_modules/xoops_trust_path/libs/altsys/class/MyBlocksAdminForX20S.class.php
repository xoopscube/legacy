<?php

require_once dirname(__FILE__).'/MyBlocksAdmin.class.php' ;

class MyBlocksAdminForX20S extends MyBlocksAdmin {

var $block_positions = array() ;

function MyBlocksAadminForX20S()
{
}


function construct()
{
	parent::construct() ;

	@include_once XOOPS_ROOT_PATH.'/modules/system/language/'.$this->lang.'/admin/blocksadmin.php' ;
	$this->block_positions = array(
		-1 => _NONE ,
		0 => _AM_SBLEFT ,
		1 => _AM_SBRIGHT ,
		3 => _AM_CBLEFT ,
		4 => _AM_CBRIGHT ,
		5 => _AM_CBCENTER ,
		7 => _AM_CBBOTTOMLEFT ,
		8 => _AM_CBBOTTOMRIGHT ,
		9 => _AM_CBBOTTOM ,
	) ;
}

//HACK by domifara for php5.3+
//function &getInstance()
public static function &getInstance()
{
	static $instance;
	if (!isset($instance)) {
		$instance = new MyBlocksAdminForX20S();
		$instance->construct() ;
	}
	return $instance;
}


// virtual
// visible and side
function renderCell4BlockPosition( $block_data )
{
	return "
	<table style='width:80px;'>
		<tr>
			<td rowspan='2'>".$this->renderRadio4BlockPosition(0,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(3,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(5,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(4,$block_data)."</td>
			<td rowspan='2'>".$this->renderRadio4BlockPosition(1,$block_data)."</td>
		</tr>
		<tr>
			<td>".$this->renderRadio4BlockPosition(7,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(9,$block_data)."</td>
			<td>".$this->renderRadio4BlockPosition(8,$block_data)."</td>
		</tr>
		<tr>
			<td colspan='5'>".$this->renderRadio4BlockPosition(-1,$block_data)._NONE."</td>
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





}

?>
