<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //




include "admin_header.php" ;
require_once XOOPS_ROOT_PATH . "/include/xoopscodes.php" ;
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
include_once XOOPS_ROOT_PATH."/class/xoopscomments.php" ;
require_once dirname(dirname(__FILE__)).'/class/myuploader.php' ;

// check file_uploads = on
if( ! ini_get( "file_uploads" ) ) $file_uploads_off = true ;

// get flag of safe_mode
$safe_mode_flag = ini_get( "safe_mode" ) ;

// check or make photos_dir
if( ! is_dir( $photos_dir ) ) {
	if( $safe_mode_flag ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/",10,"At first create & chmod 777 '$photos_dir' by ftp or shell.");
		exit ;
	}

	$rs = mkdir( $photos_dir , 0777 ) ;
	if( ! $rs ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/",10,"$photos_dir is not a directory");
		exit ;
	} else @chmod( $photos_dir , 0777 ) ;
}
// check or make icon_dir
if( ! is_dir( $icon_dir ) ) {
	if( $safe_mode_flag ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/",10,"At first create & chmod 777 '$icon_dir' by ftp or shell.");
		exit ;
	}

	$rs = mkdir( $icon_dir , 0777 ) ;
	if( ! $rs ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/",10,"'$icon_dir' is not a directory");
		exit ;
	} else @chmod( $icon_dir , 0777 ) ;
}

// GPCS vars
$action = isset( $_POST[ 'action' ] ) ? $_POST[ 'action' ] : '' ;
//¢­¢­¢­¢­¢­¢­

// Initializations
$myts =& MyTextSanitizer::getInstance();

$disp = isset( $_GET[ 'disp' ] ) ? $_GET[ 'disp' ] : '' ;
$icd = isset( $_GET[ 'icd' ] ) ? intval( $_GET[ 'icd' ] ) : 0 ;

$Anchor_x = isset( $_POST[ 'Anchor_x' ] ) ? intval( $_POST[ 'Anchor_x' ] ) : 0 ;
$Anchor_y = isset( $_POST[ 'Anchor_y' ] ) ? intval( $_POST[ 'Anchor_y' ] ) : 0 ;
$infoWindowAnchor_x = isset( $_POST[ 'infoWindowAnchor_x' ] ) ? intval( $_POST[ 'infoWindowAnchor_x' ] ) : 0 ;
$infoWindowAnchor_y = isset( $_POST[ 'infoWindowAnchor_y' ] ) ? intval( $_POST[ 'infoWindowAnchor_y' ] ) : 0 ;



//
// DB Part
//
if( $action == "insert" || ( $action == "update" &&  $icd > 0 )  ) {

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	//file uploads-------------------------------------------------------------------------------------------------
	$errmsg='';

$preview_name = empty( $_POST['preview_name'] ) ? '' : $_POST['preview_name'] ;
$preview_name1 = empty( $_POST['preview_name1'] ) ? '' : $_POST['preview_name1'] ;
$del_photo = empty( $_POST['del_photo'] ) ? 0 : intval( $_POST['del_photo'] ) ;
$del_photo1 = empty( $_POST['del_photo1'] ) ? 0 : intval( $_POST['del_photo1'] ) ;
$title = isset( $_POST[ 'title' ] ) ? $myts->stripSlashesGPC( @$_POST["title"] ):'' ;

	list($tmp_name ,$ext ,$errmsg) = gnavi_submit_uploader(@$_POST["xoops_upload_file"][0] ,$del_photo ,$preview_name , 1, $errmsg);
	list($tmp_name1,$shadow_ext,$errmsg) = gnavi_submit_uploader(@$_POST["xoops_upload_file"][1] ,$del_photo1,$preview_name1, 2, $errmsg);

	if($ext=='' && empty( $gnavi_allownoimage ) ) {
		redirect_header( 'index.php?page=submit' , 2 , _MD_GNAV_MSG_NOIMAGESPECIFIED ) ;
		exit ;
	}
	if($errmsg) {
		if($tmp_name)@unlink($tmp_name) ;
		if($tmp_name1)@unlink($tmp_name1) ;
		redirect_header( 'index.php?page=icon' , 2 , $errmsg ) ;
		exit ;
	}


	if(( $ext && !in_array( strtolower( $ext ) , $gnavi_normal_exts)) ||($shadow_ext && !in_array( strtolower( $shadow_ext ) , $gnavi_normal_exts ))) {
		if($tmp_name)@unlink($tmp_name) ;
		if($tmp_name1)@unlink($tmp_name1) ;
		redirect_header( 'index.php?page=icon' , 2 , "allowed file type is '".implode( "','" , $gnavi_normal_exts )."'" ) ;
		exit ;
	}

	if( $action == "insert" ) {
		$icd= $icd==0 ? $xoopsDB->genId( $table_icon."_icd_seq" ):$icd;
		// newly insert
		$sql = "INSERT INTO $table_icon (icd) VALUES ($icd)";
		$xoopsDB->query( $sql ) or die( "DB Error: insert icon" ) ;
		if( $icd == 0 ) {
			$icd = $xoopsDB->getInsertId();
		}
		$p_ext='';
		$p_shadow_ext='';
		$x=0;
		$y=0;
		$shadow_x=0;
		$shadow_y=0;
	}else{
		// Editing
		$prs = $xoopsDB->query( "SELECT ext,shadow_ext,x,y,shadow_x,shadow_y FROM $table_icon WHERE icd=$icd" ) ;
		list($p_ext , $p_shadow_ext , $x , $y , $shadow_x , $shadow_y ) = $xoopsDB->fetchRow( $prs ) ;
	}



	//delete old files
	if( $p_ext){
		if($del_photo==1 || $ext){
				@unlink( "$icon_dir/$icd.$p_ext");
				$p_ext='';
		}
	}
	if( $p_shadow_ext){
		if($del_photo1==1 || $shadow_ext){
				@unlink( $icon_dir."/".$icd."_s.".$p_shadow_ext);
				$p_shadow_ext='';
		}
	}


	if($ext){
		gnavi_modify_photo( "$photos_dir/$tmp_name" , "$icon_dir/$icd.$ext" ) ;
		$dim = GetImageSize( "$icon_dir/$icd.$ext" ) ;
		if( $dim ) {$x=$dim[0];$y=$dim[1];}
		$Anchor_x = $x/2;
		$Anchor_y = $y;
		$infoWindowAnchor_x = $x/2;
		$infoWindowAnchor_y = 3;

	}else{
		$ext=$p_ext;
	}
	if($shadow_ext){
		gnavi_modify_photo( "$photos_dir/$tmp_name1" , $icon_dir."/".$icd."_s.".$shadow_ext ) ;
	}else{
		$shadow_ext=$p_shadow_ext;
	}

	$x=0;
	$y=0;
	$shadow_x=0;
	$shadow_y=0;

	if($ext){
		$dim = GetImageSize( "$icon_dir/$icd.$ext" ) ;
		if( $dim ) {$x=$dim[0];$y=$dim[1];}
	}
	if($shadow_ext){
		$dim = GetImageSize( $icon_dir."/".$icd."_s.".$shadow_ext ) ;
		if( $dim ) {$shadow_x=$dim[0];$shadow_y=$dim[1];}
	}


	// update
	$sql = "UPDATE $table_icon SET title='$title', ext='$ext',shadow_ext='$shadow_ext',x=$x,y=$y,shadow_x=$shadow_x,shadow_y=$shadow_y,Anchor_x=$Anchor_x,Anchor_y=$Anchor_y,infoWindowAnchor_x=$infoWindowAnchor_x,infoWindowAnchor_y=$infoWindowAnchor_y";
	$sql .= " WHERE icd=$icd" ;
	$xoopsDB->query( $sql ) or die( "DB Error: update category" ) ;



	if( $action == "insert" ) {
		redirect_header( "index.php?page=icon" , 1 , _MD_A_GNAVI_ICO_INSERTED ) ;
	} else{
		redirect_header( "index.php?page=icon" , 1 , _MD_A_GNAVI_ICO_UPDATED ) ;
	}
	exit ;

}else if( ! empty( $_POST['delcat'] ) ) {

	// Ticket Check
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// Delete
	$icd = intval( $_POST['delcat'] ) ;
	$whr = "icd=$icd" ;
	$prs = $xoopsDB->query( "SELECT ext,shadow_ext,x,y,shadow_x,shadow_y FROM $table_icon WHERE icd=$icd" ) ;
	list( $p_ext , $p_shadow_ext) = $xoopsDB->fetchRow( $prs ) ;
	//delete old files
	if( $p_ext){
		@unlink( "$icon_dir/$icd.$p_ext");
	}
	if( $p_shadow_ext){
		@unlink( $icon_dir."/".$icd."_s.".$p_shadow_ext);
	}

	$xoopsDB->query( "UPDATE $table_cat SET icd=0 WHERE $whr" ) or die( "DB error: DELETE icon table (table_cat)" ) ;
	$xoopsDB->query( "UPDATE $table_photos SET icd=0 WHERE $whr" ) or die( "DB error: DELETE icon table(table_photos)" ) ;
	$xoopsDB->query( "DELETE FROM $table_icon WHERE $whr" ) or die( "DB error: DELETE icon table" ) ;
	redirect_header( 'index.php?page=icon' , 2 , _MD_A_GNAVI_ICODELETED ) ;
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
echo "<h3 style='text-align:left;'>".sprintf( _MD_A_GNAVI_FMT_ICONS , $xoopsModule->name() )."</h3>\n" ;

if( $disp == "edit" && $icd > 0 ) {

	// Editing
	$sql = "SELECT * FROM $table_icon WHERE icd=$icd" ;
	$crs = $xoopsDB->query( $sql ) ;
	$icons = $xoopsDB->fetchArray( $crs ) ;
	display_edit_form( $icons , _MD_A_GNAVI_CAT_MENU_EDIT , 'update' ) ;

} else if( $disp == "new" ) {

	// New
	$icons = array( 'icd' => 0 , 'title' => '' ,'ext' => '' , 'shadow_ext' => '' , 'Anchor_x' => 0,'Anchor_y' => 0,'infoWindowAnchor_x' => 0,'infoWindowAnchor_y' => 0 ) ;
	display_edit_form( $icons , _MD_A_GNAVI_CAT_MENU_NEW , 'insert' ) ;

} else {
	// Top links
	echo "<p><a href='?page=icon&disp=new&icd=0'>"._MD_A_GNAVI_CAT_LINK_MAKEICO."</a></p>\n" ;

	// TH
	echo "
	<form name='MainForm' action='' method='post' style='margin:10px;'>
	".$xoopsGTicket->getTicketHtml( __LINE__ )."
	<input type='hidden' name='delcat' value='' />
	<table width='75%' class='outer' cellpadding='4' cellspacing='1'>
	  <tr valign='middle'>
	    <th>"._MD_A_GNAVI_ICO_LIST_NO."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_TITLE."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_ICON."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_SHADOW."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_ANCHOR."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_WINANC."</th>
	    <th>"._MD_A_GNAVI_ICO_LIST_EDIT."</th>
	  </tr>
	" ;

	// TD
	$oddeven = 'odd' ;


	$sql = "SELECT * FROM $table_icon ORDER BY icd" ;
	$crs = $xoopsDB->query( $sql ) ;

	while( $icon = $xoopsDB->fetchArray( $crs ) ) {
		$oddeven = $oddeven == 'odd' ? 'even' : 'odd' ;
		extract( $icon ) ;
		$del_confirm = 'confirm("' . sprintf( _MD_A_GNAVI_ICO_FMT_CATDELCONFIRM , $title ) . '")' ;
		echo "
	  <tr>
	    <td class='$oddeven' style='vertical-align:middle;' align='center'>$icd</td>
	    <td class='$oddeven' style='vertical-align:middle;' align='center'>$title</td>
	    <td class='$oddeven' ><img src='$icon_url/$icd.$ext' align='middle' />&nbsp;&nbsp;($x,$y)</td>
	    <td class='$oddeven' ><img src='$icon_url/".$icd."_s.$shadow_ext' align='middle' />&nbsp;&nbsp;($shadow_x,$shadow_y)</td>
	    <td class='$oddeven' style='vertical-align:middle;' align='center'>$Anchor_x,$Anchor_y</td>
	    <td class='$oddeven' style='vertical-align:middle;' align='center'>$infoWindowAnchor_x,$infoWindowAnchor_y</td>
	    <td class='$oddeven' style='vertical-align:middle;' align='center'>
	      &nbsp;
	      [<a href='?page=icon&disp=edit&amp;icd=$icd'>"._MD_A_GNAVI_CAT_LINK_EDIT."</a>]
	      &nbsp;
	      <input type='button' value='"._DELETE."' onclick='if($del_confirm){document.MainForm.delcat.value=\"$icd\"; submit();}' />
	    </td>
	  </tr>\n" ;
	}

	// Table footer
	echo "
	  <!-- <tr>
	    <td colspan='6' align='right' class='foot'><input type='submit' name='batch_update' value='"._MD_A_GNAVI_CAT_BTN_BATCH."' /></td>
	  </tr> -->
	</table>
	</form>
	" ;
}
xoops_cp_footer();

function display_edit_form( $icons , $form_title , $action)
{
	global $cattree ,$icon_url;
	global $gnavi_defaultlat;
	global $gnavi_defaultlng;
	global $gnavi_defaultzoom;
	global $gnavi_googlemapapi_key;
	global $gnavi_usegooglemap,$gnavi_fsize;

	$myts =& MyTextSanitizer::getInstance();

	extract( $icons ) ;

	// Beggining of XoopsForm
	$form = new XoopsThemeForm( $form_title , 'MainForm' , '' ) ;
	$form->setExtra( "enctype='multipart/form-data'" ) ;

	// Hidden
	$form->addElement( new XoopsFormHidden( 'action' , $action ) ) ;
	$form->addElement( new XoopsFormHidden( 'icd' , $icd ) ) ;

	// Title
	$form->addElement( new XoopsFormText( _MD_A_GNAVI_ICO_TH_TITLE , 'title' , 30 , 50 , $myts->htmlSpecialChars( $title ) ) , true ) ;

	if($ext){
		$photoview = new XoopsFormLabel(_MD_A_GNAVI_ICO_IMG, "<img src='".$icon_url."/".$icd.".".$ext."' />" ) ;
		$file_form = new XoopsFormFile(_MD_A_GNAVI_ICO_EDIIMG, "photofile" , $gnavi_fsize ) ;
	}else{
		$file_form = new XoopsFormFile(_MD_A_GNAVI_ICO_IMG, "photofile" , $gnavi_fsize ) ;
		$form->setRequired( $file_form ) ;
	}
	$file_form->setExtra( "size='70'" ) ;
	if($shadow_ext){
		$photoview1 = new XoopsFormLabel(_MD_A_GNAVI_ICO_SHADOW, "<img src='".$icon_url."/".$icd."_s.".$shadow_ext."'  />" ) ;
		$file_form1 = new XoopsFormFile(_MD_A_GNAVI_ICO_EDISHADOW, "photofile1" , $gnavi_fsize ) ;
	}else{
		$file_form1 = new XoopsFormFile(_MD_A_GNAVI_ICO_SHADOW, "photofile1" , $gnavi_fsize ) ;
	}
	$file_form1->setExtra( "size='70'" ) ;
	$del_box1 = new XoopsFormCheckBox( "&nbsp;" , "del_photo1" , array( 0 ) ) ;
	$del_box1->addOption( '1' ,_MD_A_GNAVI_ICO_DELSHADOW) ;

	if($ext){
		$form->addElement( $photoview ) ;
	}
	$form->addElement( $file_form ) ;
	if($shadow_ext){
		$form->addElement( $photoview1 ) ;
		$form->addElement( $del_box1 ) ;
	}
	$form->addElement( $file_form1 ) ;

	// Anchor
	$form->addElement( new XoopsFormText( 'Anchor_x' , 'Anchor_x' , 4 , 4 , $myts->htmlSpecialChars( $Anchor_x ) ) , true ) ;
	$form->addElement( new XoopsFormText( 'Anchor_y' , 'Anchor_y' , 4 , 4 , $myts->htmlSpecialChars( $Anchor_y ) ) , true ) ;
	//infoWindowAnchor
	$form->addElement( new XoopsFormText( 'infoWindowAnchor_x' , 'infoWindowAnchor_x' , 4 , 4 , $myts->htmlSpecialChars( $infoWindowAnchor_x ) ) , true ) ;
	$form->addElement( new XoopsFormText( 'infoWindowAnchor_y' , 'infoWindowAnchor_y' , 4 , 4 , $myts->htmlSpecialChars( $infoWindowAnchor_y ) ) , true ) ;




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
