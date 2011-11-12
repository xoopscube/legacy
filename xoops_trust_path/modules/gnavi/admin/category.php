<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //



include "admin_header.php" ;
require_once XOOPS_ROOT_PATH."/include/xoopscodes.php" ;
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
include_once XOOPS_ROOT_PATH."/class/xoopscomments.php" ;
require_once dirname(dirname(__FILE__)).'/include/common_javalang.inc.php' ;


// GPCS vars
$action = isset( $_POST[ 'action' ] ) ? $_POST[ 'action' ] : '' ;
//¢­¢­¢­¢­¢­¢­

// Initializations
$myts =& MyTextSanitizer::getInstance();
$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;

$disp = isset( $_GET[ 'disp' ] ) ? $_GET[ 'disp' ] : '' ;
$cid = isset( $_GET[ 'cid' ] ) ? intval( $_GET[ 'cid' ] ) : 0 ;


$pid = isset( $_POST[ 'pid' ] ) ? intval( $_POST[ 'pid' ] ) : 0 ;
$description =isset( $_POST[ 'description' ] ) ? $myts->stripSlashesGPC( @$_POST["description"] ):'' ;
$imgurl =isset( $_POST[ 'imgurl' ] ) ? $myts->stripSlashesGPC( @$_POST["imgurl"] ) :'';
$kmlurl =isset( $_POST[ 'kmlurl' ] ) ? $myts->stripSlashesGPC( @$_POST["kmlurl"] ) :'';

$title = isset( $_POST[ 'title' ] ) ? $myts->stripSlashesGPC( @$_POST["title"] ):'' ;
$weight = isset( $_POST[ 'weight' ] ) ? intval($myts->stripSlashesGPC( $_POST['weight'] )) : 0 ;
$icd = isset( $_POST[ 'icd' ] ) ? intval($myts->stripSlashesGPC( $_POST['icd'] )) : 0 ;

if(!$gnavi_usegooglemap || !empty( $_POST['set_latlng'] )){
	$lat = 0 ;
	$lng = 0 ;
	$zoom = 0 ;
	$mtype = "" ;
}else{
	$lat = isset( $_POST[ 'lat' ] ) ?floatval($myts->stripSlashesGPC( $_POST['lat'] )) : 0;
	$lng = isset( $_POST[ 'lng' ] ) ?floatval($myts->stripSlashesGPC( $_POST['lng'] )): 0 ;
	$zoom = isset( $_POST[ 'z' ] ) ?intval($myts->stripSlashesGPC( $_POST['z'] )): 0 ;
	$mtype = !in_array($myts->stripSlashesGPC( @$_POST["mt"] ),$gnavi_maptypes) ? "" : $myts->stripSlashesGPC( @$_POST["mt"] ) ;


	if($lat==$gnavi_defaultlat && $lng==$gnavi_defaultlng){
		$lat = 0 ;
		$lng = 0 ;
		$zoom = 0 ;
		$mtype = "" ;
	}
 }




//
// DB part
//
if( $action == "insert" ) {

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// newly insert
	$sql = "INSERT INTO $table_cat (pid,title,imgurl,kmlurl,description,weight,lat,lng,zoom,mtype,icd) VALUES ($pid,'".addSlashes($title)."','".addSlashes($imgurl)."','".addSlashes($kmlurl)."','".addSlashes($description)."',$weight,$lat,$lng,$zoom,'".addSlashes($mtype)."',$icd)";

	$xoopsDB->query( $sql ) or die( "DB Error: insert category" ) ;

	// Check if cid == pid
	$cid = $xoopsDB->getInsertId() ;
	if( $cid == intval( $_POST['pid'] ) ) {
		$xoopsDB->query( "UPDATE $table_cat SET pid='0' WHERE cid='$cid'" ) ;
	}

	redirect_header( "index.php?page=category" , 1 , _MD_A_GNAVI_CAT_INSERTED ) ;
	exit ;

} else if( $action == "update" && ! empty( $_POST['cid'] ) ) {

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$cid = intval( $_POST['cid'] ) ;
	$pid = intval( $_POST['pid'] ) ;

	// Check if new pid was a child of cid
	if( $pid != 0 ) {
		$children = $cattree->getAllChildId( $cid ) ;
		$children[] = $cid ;
		foreach( $children as $child ) {
			if( $child == $pid ) redirect_header( "index.php?page=category&disp=edit&cid=$cid" , 1 , _MD_A_GNAVI_CAT_LOOP ) ; 
		}
	}

	// update
	$sql = "UPDATE $table_cat SET pid=$pid,title='".addSlashes($title)."',imgurl='".addSlashes($imgurl)."',kmlurl='".addSlashes($kmlurl)."',description='".addSlashes($description)."',weight=$weight,lat=$lat,lng=$lng,zoom=$zoom,mtype='".addSlashes($mtype)."',icd=$icd";
	$sql .= " WHERE cid='$cid'" ;
	$xoopsDB->query( $sql ) or die( "DB Error: update category" ) ;
	redirect_header( "index.php?page=category" , 1 , _MD_A_GNAVI_CAT_UPDATED ) ;
	exit ;

} else if( ! empty( $_POST['delcat'] ) ) {

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// Delete
	$cid = intval( $_POST['delcat'] ) ;

	//get all categories under the specified category
	$children = $cattree->getAllChildId( $cid ) ;
	$whr = "cid IN (" ;
	foreach( $children as $child ) {
		$whr .= "$child," ;
		xoops_notification_deletebyitem( $gnavi_mid , 'category' , $child ) ;
	}
	$whr .= "$cid)" ;
	xoops_notification_deletebyitem( $gnavi_mid , 'category' , $cid ) ;

	gnavi_delete_photos( $whr ) ;

	$xoopsDB->query( "DELETE FROM $table_cat WHERE $whr" ) or die( "DB error: DELETE cat table" ) ;
	redirect_header( 'index.php?page=category' , 2 , _MD_A_GNAVI_CATDELETED ) ;
	exit ;

} else if( ! empty( $_POST['batch_update'] ) ) {

	// Batch update

}




//
// Form Part
//
xoops_cp_header() ;
include dirname(__FILE__).'/mymenu.php' ;

// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( "$mod_url/" , 1 , _NOPERM ) ;
echo "<h3 style='text-align:left;'>".sprintf( _MD_A_GNAVI_FMT_CATEGORIES , $xoopsModule->name() )."</h3>\n" ;

if( $disp == "edit" && $cid > 0 ) {

	// Editing
	$sql = "SELECT cid,pid,title,imgurl,kmlurl,description,weight,lat,lng,zoom,mtype,icd FROM $table_cat WHERE cid='$cid'" ;
	$crs = $xoopsDB->query( $sql ) ;
	$cat_array = $xoopsDB->fetchArray( $crs ) ;
	display_edit_form( $cat_array , _MD_A_GNAVI_CAT_MENU_EDIT , 'update' ) ;

} else if( $disp == "new" ) {

	// New
	$cat_array = array( 'cid' => 0 , 'pid' => $cid , 'title' => '' , 'imgurl' => 'http://','kmlurl' => 'http://', 'description' => '','weight' => 0,'lat'=> 0,'lng'=> 0,'zoom'=> 0 ,'mtype'=>'' ,'icd'=> 0 ) ;
	display_edit_form( $cat_array , _MD_A_GNAVI_CAT_MENU_NEW , 'insert' ) ;

} else {

	// Listing
	$cat_tree_array = $cattree->getChildTreeArray( 0 , 'weight,title' ) ;

	// Get ghost categories
	$live_cids = $cattree->getAllChildId(0);
	$whr_cid = "cid NOT IN (" ;
	foreach( $live_cids as $cid ) {
		$whr_cid .= "$cid," ;
	}
	$whr_cid .= "0)" ;

	$rs = $xoopsDB->query( "SELECT * FROM $table_cat WHERE $whr_cid" ) ;
	if( $xoopsDB->fetchArray( $rs ) != false ) {
		$xoopsDB->queryF( "UPDATE $table_cat SET pid='0' WHERE $whr_cid" ) ;
		redirect_header( 'index.php?page=category' , 0 , 'A Ghost Category found.' ) ;
		exit ;
	}

	// Waiting Admission
	$ars = $xoopsDB->query( "SELECT COUNT(*) FROM $table_photos WHERE status=0" ) ;
	list( $waiting ) = $xoopsDB->fetchRow( $ars ) ;
	$link_admission = $waiting > 0 ? sprintf( _MD_A_GNAVI_CAT_FMT_NEEDADMISSION , $waiting ) : '' ;

	// Top links
	echo "<p><a href='?page=category&disp=new&cid=0'>"._MD_A_GNAVI_CAT_LINK_MAKETOPCAT."</a> &nbsp;  &nbsp; <a href='index.php?page=admission' style='color:red;'>$link_admission</a></p>\n" ;

	// TH
	echo "
	<form name='MainForm' action='' method='post' style='margin:10px;'>
	".$xoopsGTicket->getTicketHtml( __LINE__ )."
	<input type='hidden' name='delcat' value='' />
	<table width='75%' class='outer' cellpadding='4' cellspacing='1'>
	  <tr valign='middle'>
	    <th nowrap='nowrap'>"._MD_A_GNAVI_CAT_TH_IMAGE."</th>
	    <th>"._MD_A_GNAVI_CAT_TH_TITLE."</th>
	    <th>"._MD_A_GNAVI_CAT_TH_WEIGHT."</th>

	    <th>"._MD_A_GNAVI_CAT_TH_PHOTOS."</th>
	    <th>"._MD_A_GNAVI_CAT_TH_OPERATION."</th>
	  </tr>
	" ;

	// TD
	$oddeven = 'odd' ;
	foreach( $cat_tree_array as $cat_node ) {
		$oddeven = $oddeven == 'odd' ? 'even' : 'odd' ;
		extract( $cat_node ) ;

		$prefix = str_replace( '.' , '&nbsp;--' , substr( $prefix , 1 ) ) ;
		$cid = intval( $cid ) ;
		$del_confirm = 'confirm("' . sprintf( _MD_A_GNAVI_CAT_FMT_CATDELCONFIRM , $title ) . '")' ;
		$prs = $xoopsDB->query( "SELECT COUNT(*) FROM $table_photos WHERE cid='$cid'" ) ;
		list( $photos_num ) = $xoopsDB->fetchRow( $prs ) ;
		if( $icd){
		$prs = $xoopsDB->query( "SELECT ext FROM $table_icon WHERE icd='$icd'" ) ;
		list( $ext ) = $xoopsDB->fetchRow( $prs ) ;
			 $imgsrc4show = "<img src='$icon_url/$icd.$ext' />" ;
		}else{
			 $imgsrc4show='';
		}

		echo "
	  <tr>
	    <td class='$oddeven' align='center'>$imgsrc4show</td>
	    <td class='$oddeven' width='200'><a href='index.php?page=photomanager&cid=$cid'>$prefix&nbsp;".$myts->htmlSpecialChars($title)."</a></td>
	    <td class='$oddeven' nowrap='nowrap' align='center'>".$myts->htmlSpecialChars($weight)."</td>
	    <td class='$oddeven' nowrap='nowrap' align='right'>
	      <a href='index.php?page=photomanager&cid=$cid'>$photos_num</a>&nbsp;"._MD_A_GNAVI_C_DATA."&nbsp;&nbsp;
	      [<a href='../index.php?page=submit&cid=$cid'>"._MD_A_GNAVI_CAT_LINK_ADDPHOTOS."</a>]</td>
	    <td class='$oddeven' align='center' nowrap='nowrap'>
	      &nbsp;
	      [<a href='?page=category&disp=edit&amp;cid=$cid'>"._MD_A_GNAVI_CAT_LINK_EDIT."</a>]
	      &nbsp;
	      [<a href='?page=category&disp=new&amp;cid=$cid'>"._MD_A_GNAVI_CAT_LINK_MAKESUBCAT."</a>]
	      &nbsp;
	      <input type='button' value='"._DELETE."' onclick='if($del_confirm){document.MainForm.delcat.value=\"$cid\"; submit();}' />
	    </td>
	  </tr>\n" ;
	}

	// Table footer
	echo "
	  <!-- <tr>
	    <td colspan='4' align='right' class='foot'><input type='submit' name='batch_update' value='"._MD_A_GNAVI_CAT_BTN_BATCH."' /></td>
	  </tr> -->
	</table>
	</form>
	" ;
}

xoops_cp_footer();

function display_edit_form( $cat_array , $form_title , $action)
{
	global $cattree ,$table_icon;
	global $gnavi_defaultlat;
	global $gnavi_defaultlng;
	global $gnavi_defaultzoom;
	global $gnavi_defaultmtype;
	global $gnavi_googlemapapi_key;
	global $gnavi_usegooglemap,$xoopsDB;
	global $gnavi_lang_java;
	global $gnavi_body_editor;
	global $gnavi_googlemap_url;

	$myts =& MyTextSanitizer::getInstance();

	extract( $cat_array ) ;

	// Beggining of XoopsForm
	$form = new XoopsThemeForm( $form_title , 'MainForm' , '' ) ;

	// Hidden
	$form->addElement( new XoopsFormHidden( 'action' , $action ) ) ;
	$form->addElement( new XoopsFormHidden( 'cid' , $cid ) ) ;

	// Title
	$form->addElement( new XoopsFormText( _MD_A_GNAVI_CAT_TH_TITLE , 'title' , 30 , 50 , $myts->htmlSpecialChars( $title ) ) , true ) ;

	// Image URL
	$form->addElement( new XoopsFormText( _MD_A_GNAVI_CAT_TH_IMGURL , 'imgurl' , 50 , 150 , $myts->htmlSpecialChars( $imgurl ) ) ) ;

	// Kml URL
	$form->addElement( new XoopsFormText( _MD_A_GNAVI_CAT_TH_KMLURL , 'kmlurl' , 50 , 150 , $myts->htmlSpecialChars( $kmlurl ) ) ) ;

	// Parent Category
	ob_start() ;
	$cattree->makeMySelBox( "title" , "weight" , $pid , 1 , 'pid' ) ;
	$cat_selbox = ob_get_contents() ;
	ob_end_clean() ;
	$form->addElement( new XoopsFormLabel( _MD_A_GNAVI_CAT_TH_PARENT , $cat_selbox ) ) ;

//----------------------------editor-------------------------------------------
	if( $gnavi_body_editor == 'common_fckeditor') {

		// FCKeditor in common/fckeditor/
		$jscript = '
			<script type="text/javascript" src="'.XOOPS_URL.'/common/fckeditor/fckeditor.js"></script>
			<script type="text/javascript"><!--
				function fckeditor_exec() {
					var oFCKeditor = new FCKeditor( "description" , "100%" , "500" , "Default" );
					
					oFCKeditor.BasePath = "'.XOOPS_URL.'/common/fckeditor/";
					
					oFCKeditor.ReplaceTextarea();
				}
			// --></script>
		' ;
		$wysiwyg_body = '<textarea id="description" name="description">'.htmlspecialchars( $description ,ENT_QUOTES).'</textarea><script>fckeditor_exec();</script>' ;
		$desc_tarea =  new XoopsFormLabel( _MD_A_GNAVI_CAT_TH_DESC , $jscript.$wysiwyg_body ) ;
	} else if( $gnavi_body_editor == 'common_spaw' && file_exists( XOOPS_ROOT_PATH.'/common/spaw/spaw_control.class.php' ) ) {

		// older spaw in common/spaw/
		include XOOPS_ROOT_PATH.'/common/spaw/spaw_control.class.php' ;
		ob_start() ;
		$sw = new SPAW_Wysiwyg( "description" ,  $description  ) ;
		$sw->show() ;
		$wysiwyg_body = ob_get_contents() ;
		ob_end_clean() ;
		$desc_tarea =  new XoopsFormLabel( _MD_A_GNAVI_CAT_TH_DESC , $wysiwyg_body ) ;
	}else if ($gnavi_body_editor == 'pure_html'){
		$desc_tarea = new XoopsFormTextArea(_MD_A_GNAVI_CAT_TH_DESC, "description" , $myts->makeTareaData4Edit( $description ) , 20 , 60 ) ;
	} else {
		$desc_tarea = new XoopsFormDhtmlTextArea(_MD_A_GNAVI_CAT_TH_DESC, "description" , $myts->makeTareaData4Edit( $description ) , 20 , 60 ) ;
	}

	$form->addElement($desc_tarea) ;

//---------------------------------------------------------------------------------

	$form->addElement( new XoopsFormText(_MD_A_GNAVI_CAT_TH_WEIGHT, 'weight' , 5 , 3 , $myts->htmlSpecialChars( $weight ) ) ) ;


//Google Maps
if($gnavi_usegooglemap){
$set_latlng_state="";
if($lat==0 && $lng==0){
	$lat=$gnavi_defaultlat;
	$lng=$gnavi_defaultlng;
	$zoom=$gnavi_defaultzoom;
	$mtype=$gnavi_defaultmtype;
	if( $action == "update") {
		$set_latlng_state="checked";
	}
}
$gmap = new XoopsFormLabel(_MD_GNAV_MAP, "
<div><input type='checkbox' name='set_latlng' id='set_latlng' value='1' onclick='ChangeMapArea(this)' $set_latlng_state/>&nbsp;"._MD_GNAV_MAP_UNINPUT."</div>
<div id='maparea'>
<div id='map' style='width:100%;height:400px;'></div>
<div id='gn_latlng'>"._MD_GNAV_MAP_LAT.":&nbsp;<span id='slat'>".$myts->makeTboxData4Edit($lat)."</span>&nbsp;&nbsp;&nbsp;"._MD_GNAV_MAP_LNG.":&nbsp;<span id='slng'>".$myts->makeTboxData4Edit($lng)."</span>&nbsp;&nbsp;&nbsp;"._MD_GNAV_MAP_ZOOM.":&nbsp;<span id='sz'>".$myts->makeTboxData4Edit($zoom)."</span></div>
<input type='hidden' name='lat' id='lat' size='20' value='".$myts->makeTboxData4Edit($lat)."' />
<input type='hidden' name='lng' id='lng' size='20' value='".$myts->makeTboxData4Edit($lng)."' />
<input type='hidden' name='z' id='z' size='20' value='".$myts->makeTboxData4Edit($zoom)."' />
<input type='hidden' name='mt' id='mt' size='30' value='".$myts->makeTboxData4Edit($mtype)."' />
</div>
<script src='".$gnavi_googlemap_url."/maps?file=api&amp;v=2&amp;key=$gnavi_googlemapapi_key' type='text/javascript' charset='utf-8'></script>
<script src='../js/map.js' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
//<![CDATA[
	$gnavi_lang_java
	window.onload = InputGMap;
//]]>
</script>" ) ;

$form->addElement( $gmap ) ;

	$icon_select = new XoopsFormSelect(_MD_GNAV_MAP_ICON, 'icd', $icd, 1, false);
	$sql = "SELECT icd, title FROM $table_icon ";
	$result = $xoopsDB->query($sql);
	$icons_array = array();
	$icons_array[0] = '---';
	while ($myrow = $xoopsDB->fetchArray($result)) {
		$icons_array[$myrow['icd']] = $myrow['title'];
	}
	$icon_select->addOptionArray($icons_array);
	$form -> addElement( $icon_select );

}

	// Buttons
	$button_tray = new XoopsFormElementTray( '' , '&nbsp;' ) ;
	$button_tray->addElement( new XoopsFormButton( '' , 'submit' , _SUBMIT, 'submit' ) ) ;
	$button_tray->addElement( new XoopsFormButton( '' , 'reset' , _CANCEL, 'reset' ) ) ;
	$form->addElement( $button_tray ) ;

	// Ticket
	$GLOBALS['xoopsGTicket']->addTicketXoopsFormElement( $form , __LINE__ ) ;

	// End of XoopsForm
	$form->display();
}


?>
