<?php
// ------------------------------------------------------------------------- //
//                            myblocksadmin.php                              //
//                - XOOPS block admin for each modules -                     //
//                          GIJOE <http://www.peak.ne.jp/>                   //
// ------------------------------------------------------------------------- //

include_once( '../../../include/cp_header.php' ) ;
include_once( 'mygrouppermform.php' ) ;
include_once( XOOPS_ROOT_PATH.'/class/xoopsblock.php' ) ;


$xoops_system_url = XOOPS_URL . '/modules/system' ;
$xoops_system_path = XOOPS_ROOT_PATH . '/modules/system' ;

// language files
$language = $xoopsConfig['language'] ;
if( ! file_exists( "$xoops_system_path/language/$language/admin/blocksadmin.php") ) $language = 'english' ;

include_once( "$xoops_system_path/language/$language/admin.php" ) ;
include_once( "$xoops_system_path/language/$language/admin/blocksadmin.php" ) ;
$group_defs = file( "$xoops_system_path/language/$language/admin/groups.php" ) ;
foreach( $group_defs as $def ) {
	if( strstr( $def , '_AM_ACCESSRIGHTS' ) || strstr( $def , '_AM_ACTIVERIGHTS' ) ) eval( $def ) ;
}


// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( XOOPS_URL.'/user.php' , 1 , _NOPERM ) ;

// get blocks owned by the module
$block_arr =& XoopsBlock::getByModule( $xoopsModule->mid() ) ;

// add by Tom
sort ($block_arr);
reset ($block_arr);

function list_blocks()
{
	global $xoopsUser , $xoopsConfig , $xoopsDB ;
	global $block_arr , $xoops_system_url ;

	$side_descs = array( 0 => _AM_SBLEFT, 1 => _AM_SBRIGHT, 3 => _AM_CBLEFT, 4 => _AM_CBRIGHT, 5 => _AM_CBCENTER ) ;

	// displaying TH
	echo "
	<table width='100%' class='outer' cellpadding='4' cellspacing='1'>
	<tr valign='middle'><th width='20%'>"._AM_BLKDESC."</th><th>"._AM_TITLE."</th><th align='center' nowrap='nowrap'>"._AM_SIDE."</th><th align='center'>"._AM_WEIGHT."</th><th align='center'>"._AM_VISIBLE."</th><th align='right'>"._AM_ACTION."</th></tr>
	";

	// blocks displaying loop
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$blockAdmin = XOOPS_URL."/modules/legacy/admin/index.php?action=BlockEdit&amp;bid=";
		$blockInstallAdmin = XOOPS_URL."/modules/legacy/admin/index.php?action=BlockInstallEdit&amp;bid=";
	}else{
		$blockAdmin = $xoops_system_url."/admin.php?fct=blocksadmin&amp;op=edit&amp;bid=";
	}
	$class = 'even' ;
	foreach( array_keys( $block_arr ) as $i ) {
		$visible = ( $block_arr[$i]->getVar("visible") == 1 ) ? _YES : _NO ;
		$weight = $block_arr[$i]->getVar("weight") ;
		$side_desc = $side_descs[ $block_arr[$i]->getVar("side") ] ;
		$title = $block_arr[$i]->getVar("title") ;
		if( $title == '' ) $title = "&nbsp;" ;
		$name = $block_arr[$i]->getVar("name") ;
		$bid = $block_arr[$i]->getVar("bid") ;

		echo "<tr valign='top'>
		<td class='$class'>$name</td>
		<td class='$class'>$title</td>
		<td class='$class' align='center'>$side_desc</td>
		<td class='$class' align='center'>$weight</td>
		<td class='$class' align='center' nowrap>$visible</td>
		<td class='$class' align='right'>";
		if ($visible === _YES) {
			echo "<a href='$blockAdmin$bid' target='_blank'>"._EDIT."</a>";
		} else {
			echo "<a href='$blockInstallAdmin$bid' target='_blank'>"._INSTALL."</a>";
		}
		echo "</td>
		</tr>\n" ;
		
		$class = ( $class == 'even' ) ? 'odd' : 'even' ;
	}
	echo "<tr><td class='foot' align='center' colspan='7'>
	</td></tr></table>\n" ;
}


function list_groups()
{
	global $xoopsUser , $xoopsConfig , $xoopsDB ;
	global $xoopsModule , $block_arr , $xoops_system_url ;

	foreach( array_keys( $block_arr ) as $i ) {
		$item_list[ $block_arr[$i]->getVar("bid") ] = $block_arr[$i]->getVar("title") ;
	}

	$form = new MyXoopsGroupPermForm( '' , 1 , 'block_read' , _MD_AM_ADGS ) ;
	$form->addAppendix('module_admin',$xoopsModule->mid(),$xoopsModule->name().' '._AM_ACTIVERIGHTS);
	$form->addAppendix('module_read',$xoopsModule->mid(),$xoopsModule->name().' '._AM_ACCESSRIGHTS);
	foreach( $item_list as $item_id => $item_name) {
		$form->addItem( $item_id , $item_name ) ;
	}
	echo $form->render() ;
}



if( ! empty( $_POST['submit'] ) ) {
	include( "mygroupperm.php" ) ;
	redirect_header( XOOPS_URL."/modules/".$xoopsModule->dirname()."/admin/myblocksadmin.php" , 1 , _MD_AM_DBUPDATED );
}

xoops_cp_header() ;

// for multimenu admin menu
//echo "<h3 style='text-align:left;'>".$xoopsModule->name()."</h3>\n" ;
require 'admin_function.php';
$class = new multimenu($menu_num);

$class->mm_admin_menu(0, _AM_BADMIN );

//echo "<h4 style='text-align:left;'>"._AM_BADMIN."</h4>\n" ;
list_blocks() ;
if( !defined( 'XOOPS_CUBE_LEGACY' ) ) {
	list_groups() ;
}
xoops_cp_footer() ;

?>