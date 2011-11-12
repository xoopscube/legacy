<?php
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

include( "admin_header.php" ) ;
$myts =& MyTextSanitizer::getInstance() ;

// GPCS vars
$max_col = 4 ;
$cid = empty( $_GET[ 'cid' ] ) ? 0 : intval( $_GET[ 'cid' ] ) ;
$pos = empty( $_GET[ 'pos' ] ) ? 0 : intval( $_GET[ 'pos' ] ) ;
$num = empty( $_GET[ 'num' ] ) ? 20 : intval( $_GET[ 'num' ] ) ;
$txt = empty( $_GET[ 'txt' ] ) ? '' : $myts->stripSlashesGPC( trim( $_GET[ 'txt' ] ) ) ;


// Database actions
if( ! empty( $_POST['action'] ) && $_POST['action'] == 'delete' && isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

	// remove records

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( $_POST['ids'] as $lid ) {
		gnavi_delete_photos( "lid=".intval( $lid ) ) ;
	}
	redirect_header( "index.php?page=photomanager&num=$num&cid=$cid" , 2 , _MD_GNAV_SMT_DELETINGITEM ) ;
	exit ;

} else if( isset( $_POST['update'] ) && isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

	// batch update

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// set clause for text table
	if( ! empty( $_POST['new_desc_text'] ) ) {
		$set_for_text = "description='".$myts->makeTareaData4Save( $_POST['new_desc_text'] )."'" ;
	}

	// set clause for photos table
	$set = '' ;

	// new_title
	if( ! empty( $_POST['new_title'] ) ) {
		$set .= "title='".$myts->makeTboxData4Save( $_POST['new_title'] )."'," ;
	}

	// new_cid
	if( ! empty( $_POST['new_cid'] ) ) {
		$set .= "cid='".intval( $_POST['new_cid'] )."'," ;
	}

	// new_submitter
	if( ! empty( $_POST['new_submitter'] ) ) {
		$set .= "submitter='".intval( $_POST['new_submitter'] )."'," ;
	}

	// new_post_date
	if( ! empty( $_POST['new_post_date'] ) ) {
		$new_date = strtotime( $_POST['new_post_date'] ) ;
		if( $new_date != -1 ) $set .= "date='$new_date'," ;
	}

	if( $set ) $set = substr( $set , 0 , -1 ) ;

	// $whr clause
	$whr = "lid IN (" ;
	foreach( $_POST[ 'ids' ] as $lid ) {
		$whr .= intval( $lid ) . ',' ;
	}
	$whr = substr( $whr , 0 , -1 ) . ')' ;

	if( $set ) $xoopsDB->query( "UPDATE $table_photos SET $set WHERE $whr" ) ;
	if( ! empty( $set_for_text ) ) $xoopsDB->query( "UPDATE $table_text SET $set_for_text WHERE $whr" ) ;

	redirect_header( "index.php?page=photomanager&num=$num&cid=$cid" , 2 , _MD_GNAV_MSG_DBUPDATED ) ;
	exit ;

}


// make 'WHERE'
$whr = "1 " ;

// Limitation by category's id
if( $cid != 0 ) {
	$whr .= "AND (l.cid=$cid OR l.cid1=$cid OR l.cid2=$cid OR l.cid3=$cid OR l.cid4=$cid )" ;
}

// Search by free word 
if( $txt != "" ) {
	$keywords = explode( " " , $txt ) ;
	foreach( $keywords as $keyword ) {
		$whr .= "AND (CONCAT( l.title , l.ext , c.title ) LIKE '%" . addslashes( $keyword ) . "%') " ;
	}
}

// Query
$rs = $xoopsDB->query( "SELECT count(l.lid) FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE $whr" ) ;
list( $numrows ) = $xoopsDB->fetchRow( $rs ) ;
$prs = $xoopsDB->query( "SELECT l.lid, l.title, l.submitter, l.ext, l.res_x, l.res_y, l.status FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE $whr ORDER BY l.lid DESC LIMIT $pos,$num" ) ;

// Page Navigation
include XOOPS_ROOT_PATH.'/class/pagenav.php';
$nav = new XoopsPageNav( $numrows , $num , $pos , 'pos' , "num=$num&cid=$cid&txt=" . urlencode($txt) ) ;
$nav_html = $nav->renderNav(7) ;

// Information of page navigating
$last = $pos + $num ;
if( $last > $numrows ) $last = $numrows ;
$photonavinfo = sprintf( _MD_GNAV_NAV_ITEMPOS , $pos + 1 , $last , $numrows ) ;

// Options for the number of photos in a display
$numbers = explode( '|' , '10|20|50|100' ) ;
$num_options = '' ;
foreach( $numbers as $number ) {
	$number = intval( $number ) ;
	if( $number < 1 ) continue ;
	$selected = $number == $num ? "selected='selected'" : "" ;
	$num_options .= "<option value='$number' $selected>".sprintf(_MD_A_GNAVI_FMT_PHOTONUM,$number)."</option>\n" ;
}

gnavi_get_cat_options() ;

// Options for Selecting a category
$cat_options = gnavi_get_cat_options( 'title' , $cid , '--' , '----' ) ;
$cat_options_for_update = gnavi_get_cat_options( 'title' , 0 , '--' , _MD_A_GNAVI_OPT_NOCHANGE ) ;

// Options for Selecting a user
$user_options = "<option value='0'>"._MD_A_GNAVI_OPT_NOCHANGE."</option>\n" ;
$urs = $xoopsDB->query( "SELECT uid,uname FROM ".$xoopsDB->prefix("users")." ORDER BY uname" ) ;
while( list( $uid , $uname ) = $xoopsDB->fetchRow( $urs ) ) {
	$user_options .= "<option value='$uid'>".htmlspecialchars($uname,ENT_QUOTES)."</option>\n" ;
}


// Start of outputting
xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;

// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( "$mod_url/" , 1 , _NOPERM ) ;
echo "<h3 style='text-align:left;'>".sprintf(_MD_A_GNAVI_FMT_PHOTOMANAGER,$xoopsModule->name())."</h3>\n" ;

gnavi_opentable() ;

echo "
<p><font color='blue'>".(isset($_GET['mes'])?$_GET['mes']:"")."</font></p>
<form name='moveForm' action='' method='GET' style='margin-bottom:0px;'>
  <table border='0' cellpadding='0' cellspacing='0' style='width:100%;'>
    <tr>
      <td align='left'>
        <select name='num' onchange='submit();'>
          $num_options
        </select>
        <select name='cid' onchange='submit();'>
          $cat_options
        </select>
        <input type='text' name='txt' value='".htmlspecialchars($txt,ENT_QUOTES)."'>
        <input type='submit' value='"._MD_A_GNAVI_BUTTON_EXTRACT."'> &nbsp; 
        <input type='hidden'name='page' value='photomanager'>
      </td>
      <td align='right'>
        $nav_html &nbsp; 
      </td>
    </tr>
  </table>
</form>
<p align='center' style='margin:0px;'>
  $photonavinfo
  <a href='../index.php?page=submitcid=$cid'><img src='../images/pictadd.gif' alt='"._MD_A_GNAVI_CAT_LINK_ADDPHOTOS."' title='"._MD_A_GNAVI_CAT_LINK_ADDPHOTOS."' /></a>
</p>
<form name='MainForm' action='?page=photomanager&num=$num&cid=$cid' method='POST' style='margin-top:0px;'>
".$xoopsGTicket->getTicketHtml( __LINE__ )."
<table width='100%' border='0' cellspacing='0' cellpadding='4'>
<tr>
<td align='center' colspan='2'>
	<table border='0' cellspacing='5' cellpadding='0' width='100%'>
" ;

// list part
$col = 0 ;
while( list( $lid , $title , $submitter , $ext , $w , $h , $status ) = $xoopsDB->fetchRow( $prs ) ) {
	$title = $myts->makeTboxData4Show( $title ) ;

	if( in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
		$imgsrc_thumb = "$thumbs_url/$lid.$ext" ;
		$ahref_photo = "$photos_url/$lid.$ext" ;
		$widthheight = $w > $h ? "width='$gnavi_thumbsize'" : "height='$gnavi_thumbsize'" ;
	} else {
		if($ext){
			if(file_exists( "$mod_path/icons/".$ext.".gif" )){
				$imgsrc_thumb = "$mod_url/icons/".$ext.".gif" ;
				$ahref_photo = "$mod_url/icons/".$ext.".gif" ;
				$widthheight = '';
			}else{
				$imgsrc_thumb = "$mod_url/icons/all.gif" ;
				$ahref_photo = "$mod_url/icons/all.gif" ;
				$widthheight = '';
			}
		}else{
			$imgsrc_thumb = "$mod_url/images/noimage.gif" ;
			$ahref_photo = "$mod_url/images/noimage.gif" ;
			$widthheight = '';
		}
	}

	$bgcolor = $status ? "#FFFFFF" : "#FFEEEE" ;

	$editbutton = "<a href='".XOOPS_URL."/modules/$mydirname/index.php?page=submit&lid=$lid' target='_blank'><img src='".XOOPS_URL."/modules/$mydirname/images/editicon.gif' border='0' alt='"._MD_GNAV_SMT_EDITITEM."' title='"._MD_GNAV_SMT_EDITITEM."' /></a>  ";
	$deadlinkbutton = is_readable( "$photos_dir/{$lid}.{$ext}" ) ? "" : "<img src='".XOOPS_URL."/modules/$mydirname/images/deadlink.gif' border='0' alt='"._MD_A_GNAVI_DEADLINKMAINPHOTO."' title='"._MD_A_GNAVI_DEADLINKMAINPHOTO."' />" ;

	if( $col == 0 ) echo "\t<tr>\n" ;

	echo "
		<td align='center' style='background-color:$bgcolor; margin: 0 px; padding: 3 px; border-width:0px 2px 2px 0px; border-style: solid; border-color:black;'>
			<table border='0' cellpadding='0' cellmargin='0'>
				<tr>
					<td></td>
					<td><img src='../images/pixel_trans.gif' width='$gnavi_thumbsize' height='1' alt='' /></td>
					<td></td>
				</tr>
				<tr>
					<td><img src='../images/pixel_trans.gif' width='1' height='$gnavi_thumbsize' alt='' /></td>
					<td align='center'><a href='$ahref_photo' target='_blank'><img src='$imgsrc_thumb' $widthheight border='0' alt='$title' title='$title' /></a></td>
					<td><img src='../images/pixel_trans.gif' width='1' height='$gnavi_thumbsize' alt='' /></td>
				</tr>
				<tr>
					<td></td>
					<td align='center'>$editbutton $deadlinkbutton <span style='font-size:10pt;'>$title <input type='checkbox' name='ids[]' value='$lid' style='border:none;'></span></td>
					<td></td>
				</tr>
			</table>

		</td>
	\n" ;

	if( ++ $col >= $max_col ) { echo "\t</tr>\n" ; $col = 0 ; }

}

echo "
	</table>
</td>
</tr>
<tr>
	<td align='left'>
		<input type='button' value='"._MD_A_GNAVI_BTN_SELECTNONE."' onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'){elements[i].checked=false;}}}\" />
		&nbsp;
		<input type='button' value='"._MD_A_GNAVI_BTN_SELECTALL."' onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'){elements[i].checked=true;}}}\" />
	</td>
	<td align='right'>
		<input type='hidden' name='action' value='' />
		"._MD_A_GNAVI_LABEL_REMOVE."<input type='button' value='"._MD_A_GNAVI_BUTTON_REMOVE."' onclick='if(confirm(\""._MD_A_GNAVI_JS_REMOVECONFIRM."\")){document.MainForm.action.value=\"delete\"; submit();}' />
	</td>
</tr>
</table>
<br />
<table class='outer' style='width:100%;'>
	<tr>
		<th colspan='2'>"._MD_A_GNAVI_TH_BATCHUPDATE."</th>
	</tr>
	<tr>
		<td class='head'>"._MD_A_GNAVI_TH_TITLE."</td>
		<td class='even'><input type='text' name='new_title' size='50' /></td>
	</tr>
	<tr valign='top'>
		<td class='head'>"._MD_A_GNAVI_TH_DESCRIPTION."</td>
		<td class='even'><textarea name='new_desc_text' cols='50' rows='5'></textarea></td>
	</tr>
	<tr>
		<td class='head'>"._MD_A_GNAVI_TH_CATEGORIES."</td>
		<td class='even'>
			<select name='new_cid'>
				$cat_options_for_update
			</select>
		</td>
	</tr>
	<tr>
		<td class='head'>"._MD_A_GNAVI_TH_SUBMITTER."</td>
		<td class='even'>
			<select name='new_submitter'>
				$user_options
			</select>
		</td>
	</tr>
	<tr valign='top'>
		<td class='head'>"._MD_A_GNAVI_TH_DATE."</td>
		<td class='even'><input type='text' name='new_post_date' size='20' value='".formatTimestamp(time(),_MD_GNAV_DTFMT_YMDHI)."'></textarea></td>
	</tr>
	<tr>
		<td class='head'></td>
		<td class='even'><input type='submit' name='update' value='"._MD_A_GNAVI_BUTTON_UPDATE."' onclick='return confirm(\""._MD_A_GNAVI_JS_UPDATECONFIRM."\")' tabindex='1' /></td>
	</tr>
</table>
</form>
" ;


gnavi_closetable() ;
xoops_cp_footer();
?>
