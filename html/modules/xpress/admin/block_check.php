<?php 
// $Id: xoops_version.php,v 1.8 2005/06/03 01:35:02 phppp Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: phppp (D.J.)                                                      //
// URL: http://xoopsforge.com, http://xoops.org.cn                           //
// ------------------------------------------------------------------------- //
//include_once 'cp_functions.php';
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
require_once '../../../include/cp_header.php' ;
//require_once '../include/gtickets.php' ;
//define( '_MYMENU_CONSTANT_IN_MODINFO' , '_MI_TELLAFRIEND_MODNAME' ) ;

// branch for altsys
if( defined( 'XOOPS_TRUST_PATH' ) && ! empty( $_GET['lib'] ) ) {
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$mydirpath = dirname( dirname( __FILE__ ) ) ;

	// common libs (eg. altsys)
	$lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;
	
	if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
	} else {
		die( 'wrong request' ) ;
	}
	exit ;
}

//include_once('./menu.php');
//include_once('./../../../include/cp_header.php');
xoops_cp_header();
include( './mymenu.php' ) ;

include_once(dirname(__FILE__) . '/../class/check_blocks_class.php');

//BLOCK CHECK
echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . 'Check ' . $mydirname . ' block table' . "</legend>";
echo "<div style='padding: 8px;'>";

$xoops_block_check =& xoops_block_check::getInstance();

if ( !$xoops_block_check->is_admin() )
{
	include XOOPS_ROOT_PATH.'/footer.php';
	exit();
}

switch ( $xoops_block_check->get_op() ) 
{
case "remove_all_block":
	echo $xoops_block_check->remove_all_block();
	break;
case "remove_block":
	echo $xoops_block_check->remove_block();
	break;
default:
	if ($xoops_block_check->check_blocks($mydirname)){
			echo $xoops_block_check->get_message();
			echo "<br /><br />";
			echo _AM_XP2_BLOCK_OK ;
	} else {

			echo $xoops_block_check->get_message();
			echo "<br /><br />\n";
			echo _AM_XP2_BLOCK_NG."<br />\n";
			echo _AM_XP2_BLOCK_REPAIR_HOWTO."<br />\n"; 
			echo '<form method="POST">'."\n";
			echo _AM_XP2_BLOCK_REPAIR_STEP1 .' : <br />'."\n"; 
			echo '&emsp;&emsp;&emsp;&emsp;&nbsp;';
			echo '<input type="submit" name="mid:'.$xoops_block_check->module_id.'" value="' . _AM_XP2_NG_BLOCK_REMOVE . ': '.$xoops_block_check->module_name.'" />'."<br />\n";
			echo '&emsp;&emsp;&emsp;&nbsp;' . _AM_XP2_BLOCK_OR . '<br />'."\n";
			echo '&emsp;&emsp;&emsp;&emsp;&nbsp;';
			echo '<input type="submit" name="amid:'.$xoops_block_check->module_id.'" value="' . _AM_XP2_BLOCK_REMOVE . ': '.$xoops_block_check->module_name.'" />'."<br />\n";
			echo '<br />'."\n";
			echo '&emsp;&emsp;&emsp;&nbsp;' . _AM_XP2_BLOCK_REMOVE_NOTE;
			echo "</form>\n";
			echo "<br />\n";
			echo _AM_XP2_BLOCK_REPAIR_STEP2 . ' : ' . _AM_XP2_BLOCK_UPDATE . "<br />\n";
			echo '&emsp;&emsp;&emsp;&emsp;&nbsp;';
			echo '<a href="'.$xoops_block_check->update_link.'">' .$xoops_block_check->module_name . ' ' . _AM_XP2_TO_MODELE_UPDATE .'</a>';
			echo "<br />\n";
			echo "<br />\n";
			
			echo _AM_XP2_BLOCK_REPAIR_STEP3 . ' : ' . _AM_XP2_BLOCK_ADMIN_SETTING . "<br />\n";
			echo '&emsp;&emsp;&emsp;&emsp;&nbsp;';
			echo '<a href="admin_blocks.php">' .$xoops_block_check->module_name . ' ' . _AM_XP2_BLOCK_TO_SETTING .'</a>';
			echo "<br />\n";
	}
	break;

}

//if ( $xoops_block_check->get_xoops_version() == '2.1' ) {
//	$xoopsTpl->assign( 'xoops_contents', $cont );
//} else {
//}
echo "</fieldset>";
xoops_cp_footer();
	
?>