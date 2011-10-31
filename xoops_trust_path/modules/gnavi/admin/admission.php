<?php
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

include "admin_header.php" ;
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php' ;
include_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
require_once dirname(dirname(__FILE__)).'/class/gnavi.textsanitizer.php' ;



// initialization of Xoops vars
$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;
$myts =& GNaviTextSanitizer::getInstance() ;


// GET vars
$pos = empty( $_GET[ 'pos' ] ) ? 0 : intval( $_GET[ 'pos' ] ) ;
$num = empty( $_GET[ 'num' ] ) ? 20 : intval( $_GET[ 'num' ] ) ;
$txt = empty( $_GET[ 'txt' ] ) ? '' : $myts->stripSlashesGPC( trim( $_GET[ 'txt' ] ) ) ;


if( ! empty( $_POST['action'] ) && $_POST['action'] == 'admit' && isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

	// Do admission
	$whr = "" ;
	foreach( $_POST[ 'ids' ] as $id ) {
		$id = intval( $id ) ;
		$whr .= "lid=$id OR " ;
	}
	$xoopsDB->query( "UPDATE $table_photos SET status=1 WHERE $whr 0" ) ;

	// Trigger Notification
	$notification_handler =& xoops_gethandler( 'notification' ) ;
	$rs = $xoopsDB->query( "SELECT l.lid,l.cid,l.submitter,l.title,c.title FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE $whr 0" ) ;
	while( list( $lid , $cid , $submitter , $title , $cat_title ) = $xoopsDB->fetchRow( $rs ) ) {

		$tags = array();
		$tags['PHOTO_TITLE'] = $title;
		$tags['PHOTO_URI']  = $mod_url."/index.php?lid=".$lid;

		// Global Notification
		gnavi_trigger_event('global', 0, 'new_item', $tags);

		// Category Notification
		$tags['CATEGORY_TITLE']  = $cat_title;
		$tags['PHOTO_URI']  = $mod_url."/index.php?lid=".$lid."&cid=".$cid ;

		gnavi_trigger_event('category', $cid, 'new_item', $tags);

	}

	redirect_header( 'index.php?page=admission' , 2 , _MD_A_GNAVI_ADMITTING ) ;
	exit ;

} else if( ! empty( $_POST['action'] ) && $_POST['action'] == 'delete' && isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

	// remove records

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( $_POST['ids'] as $lid ) {
		gnavi_delete_photos( "lid=".intval( $lid ) ) ;
	}
	redirect_header( "index.php?page=admission" , 2 , _MD_GNAV_SMT_DELETINGITEM ) ;
	exit ;
}


// extracting by free word
$whr = "l.status<=0 " ;
if( $txt != "" ) {
	$keywords = explode( " " , $txt ) ;
	foreach( $keywords as $keyword ) {
		$whr .= "AND (CONCAT( l.title , l.ext , c.title ) LIKE '%" . addslashes( $keyword ) . "%') " ;
	}
}

// query for listing
$rs = $xoopsDB->query( "SELECT count(l.lid) FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE $whr" ) ;
list( $numrows ) = $xoopsDB->fetchRow( $rs ) ;
$prs = $xoopsDB->query( "SELECT l.lid, l.cid, l.title, l.submitter, l.ext, t.description,t.arrowhtml,t.addinfo FROM $table_photos l LEFT JOIN $table_cat c ON l.cid=c.cid LEFT JOIN $table_text t ON l.lid=t.lid WHERE $whr ORDER BY l.lid DESC LIMIT $pos,$num" ) ;

// Page Navigation
$nav = new XoopsPageNav( $numrows , $num , $pos , 'pos' , "num=$num&txt=" . urlencode($txt) ) ;
$nav_html = $nav->renderNav(7) ;


// beggining of Output
xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;

// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( XOOPS_URL.'/user.php' , 1 , _NOPERM ) ;
echo "<h3 style='text-align:left;'>".sprintf(_MD_A_GNAVI_FMT_ADMISSION,$xoopsModule->name())."</h3>\n" ;

echo "
<p><font color='blue'>".(isset($_GET['mes'])?$_GET['mes']:"")."</font></p>
<table width='95%' border='0' cellpadding='4' cellspacing='0'><tr><td>
<form action='' method='GET' style='margin-bottom:0px;text-align:right'>
  <input type='hidden' name='num' value='$num'>
  <input type='text' name='txt' value='".htmlspecialchars($txt,ENT_QUOTES)."'>
  <input type='submit' value='"._MD_A_GNAVI_BUTTON_EXTRACT."' /> &nbsp; 
  <input type='hidden'name='page' value='admission'>
  $nav_html &nbsp; 
</form>
<form name='MainForm' action='' method='POST' style='margin-top:0px;'>
".$xoopsGTicket->getTicketHtml( __LINE__ )."
<input type='hidden' name='action' value='' />
<table width='95%' class='outer' cellpadding='4' cellspacing='1'>
  <tr valign='middle'>
    <th width='5'><input type='checkbox' name='dummy' onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'){elements[i].checked=this.checked;}}}\" /></th>
    <th></th>
    <th>"._MD_A_GNAVI_TH_SUBMITTER."</th>
    <th>"._MD_A_GNAVI_TH_TITLE."</th>
    <th>"._MD_A_GNAVI_TH_DESCRIPTION."</th>
    <th>"._MD_A_GNAVI_TH_CATEGORIES."</th>
  </tr>
" ;

// Listing
$oddeven = 'odd' ;
while( list( $lid , $cid , $title , $submitter , $ext , $description ) = $xoopsDB->fetchRow( $prs ) ) {
	$oddeven = ( $oddeven == 'odd' ? 'even' : 'odd' ) ;
	$title = $myts->makeTboxData4Show( $title ) ;
	$description = $myts->displayTarea( $description , 0 , 1 , 1 , 0 , 1 , 1 ) ;
	$cat = $cattree->getNicePathFromId( $cid , "title", "../index.php?" ) ;
	$editbutton = "<a href='".XOOPS_URL."/modules/$mydirname/index.php?page=submit&lid=$lid' target='_blank'><img src='".XOOPS_URL."/modules/$mydirname/images/editicon.gif' border='0' alt='"._MD_GNAV_SMT_EDITITEM."' title='"._MD_GNAV_SMT_EDITITEM."' /></a>  ";

	echo "
  <tr>
    <td class='$oddeven'><input type='checkbox' name='ids[]' value='$lid' /></td>
    <td class='$oddeven'>$editbutton</td>
    <td class='$oddeven'>".$xoopsUser->getUnameFromId($submitter)."</td>
    <td class='$oddeven'><a href='$photos_url/{$lid}.{$ext}' target='_blank'>$title</a></td>
    <td class='$oddeven' width='100%'>$description</td>
    <td class='$oddeven'>$cat</td>
  </tr>\n" ;
}

echo "
  <tr>
    <!-- <td colspan='4' align='left'>"._MD_A_GNAVI_LABEL_ADMIT."<input type='submit' name='admit' value='"._MD_A_GNAVI_BUTTON_ADMIT."' /></td> -->
    <td colspan='8' align='left'>"._MD_A_GNAVI_LABEL_ADMIT."<input type='button' value='"._MD_A_GNAVI_BUTTON_ADMIT."' onclick='document.MainForm.action.value=\"admit\"; submit();' /></td>
  </tr>
  <tr>
    <td colspan='8' align='left'>"._MD_A_GNAVI_LABEL_REMOVE."<input type='button' value='"._MD_A_GNAVI_BUTTON_REMOVE."' onclick='if(confirm(\""._MD_A_GNAVI_JS_REMOVECONFIRM."\")){document.MainForm.action.value=\"delete\"; submit();}' /></td>
  </tr>
</table>
</form>
</td></tr></table>
" ;

xoops_cp_footer();
?>
