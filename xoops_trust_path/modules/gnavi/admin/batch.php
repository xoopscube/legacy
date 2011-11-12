<?php
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

include( "admin_header.php" ) ;
include_once( XOOPS_ROOT_PATH."/class/xoopstree.php" ) ;
include_once( "../../../class/xoopsformloader.php" ) ;
include_once( "../../../include/xoopscodes.php" ) ;

$myts =& MyTextSanitizer::getInstance() ;
$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;

// GPCS vars
$submitter = empty( $_POST['submitter'] ) ? $my_uid : intval( $_POST['submitter']  ) ;
if( isset( $_POST['cid'] ) ) $cid = intval( $_POST['cid'] ) ;
else if( isset( $_GET['cid'] ) ) $cid = intval( $_GET['cid'] ) ;
else $cid = 0 ;
$dir4edit = isset( $_POST['dir'] ) ? $myts->makeTboxData4Edit( $_POST['dir'] ) : '' ;
$title4edit = isset( $_POST['title'] ) ? $myts->makeTboxData4Edit( $_POST['title'] ) : '' ;
$desc4edit = isset( $_POST['desc'] ) ? $myts->makeTareaData4Edit( $_POST['desc'] ) : '' ;


// reject Not Admin
if( ! $isadmin ) {
	redirect_header( $mod_url.'/' , 2 , _MD_GNAV_MSG_MUSTREGFIRST ) ;
	exit ;
}

// check Categories exist
$result = $xoopsDB->query( "SELECT count(cid) as count FROM $table_cat" ) ;
list( $count ) = $xoopsDB->fetchRow( $result ) ;
if( $count < 1 ) {
	redirect_header("index.php?page=category",2,_MD_GNAV_MSG_MUSTADDCATFIRST);
	exit();
}


if( isset( $_POST['submit'] ) && $_POST['submit'] != "" ) {
	ob_start() ;

	// Check Directory
	$dir = $myts->stripSlashesGPC( $_POST['dir'] ) ;
	if( empty( $dir ) || ! is_dir( $dir ) ) {
		if( ord( $dir ) != 0x2f ) $dir = "/$dir" ;
		$prefix = XOOPS_ROOT_PATH ;
		while( strlen( $prefix ) > 0 ) {
			if( is_dir( "$prefix$dir" ) ) {
				$dir = "$prefix$dir" ;
				break ;
			}
			$prefix = substr( $prefix , 0 , strrpos( $prefix , '/' ) ) ;
		}
		if( ! is_dir( $dir ) ) {
			redirect_header( 'batch.php' , 3 , _MD_A_GNAV_MES_INVALIDDIRECTORY . "<br />$dir4edit" ) ;
			exit ;
		}
	}
	if( substr( $dir , -1 ) == '/' ) $dir = substr( $dir , 0 , -1 ) ;

	$title4save = $myts->makeTboxData4Save( $_POST['title'] ) ;
	$desc4save = $myts->makeTareaData4Save( $_POST['desc'] ) ;

	$date = strtotime( $_POST['post_date'] ) ;
	if( $date == -1 ) $date = time() ;

	$dir_h = opendir( $dir ) ;
	if( $dir_h === false ) {
		redirect_header( 'batch.php' , 3 , _MD_A_GNAV_MES_INVALIDDIRECTORY . "<br />$dir4edit" ) ;
		exit ;
	}
	// get all file_names from the directory.
	$file_names = array() ;
	while( $file_name = readdir( $dir_h ) ) {
		$file_names[] = $file_name ;
	}
	sort( $file_names ) ;

	$filecount = 1 ;
	foreach( $file_names as $file_name ) {

		// Skip '.' , '..' and hidden file
		if( substr( $file_name , 0 , 1 ) == '.' ) continue ;

		$ext = substr( strrchr( $file_name , '.' ) , 1 ) ;
		$node = substr( $file_name , 0 , - strlen( $ext ) - 1 ) ;
		$file_path = "$dir/$node.$ext" ;

		$title = empty( $_POST['title'] ) ? addslashes( $node ) : "$title4save $filecount" ;

		if( is_readable( $file_path ) && in_array( strtolower( $ext ) , $array_allowed_exts ) ) {
			$lid = $xoopsDB->genId( $table_photos."_lid_seq" ) ;
			if( in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
				list( $w , $h ) = getimagesize( $file_path ) ;
			} else {
				list( $w , $h ) = array( 0 , 0 ) ;
			}
			$sql = "INSERT INTO $table_photos SET lid='$lid', cid='$cid', title='$title', ext='$ext', res_x='$w', res_y='$h', submitter='$submitter', status=1, date='$date'" ;
			$xoopsDB->query( $sql ) or die( "DB error: INSERT photos table." ) ;
			if( $lid == 0 ) {
				$lid = $xoopsDB->getInsertId() ;
			}
			print " &nbsp; <a href='../index.php?lid=$lid' target='_blank'>$file_path</a>\n" ;
			copy( $file_path , "$photos_dir/$lid.$ext" ) ;
			gnavi_create_thumb( "$photos_dir/$lid.$ext" , $lid , $ext ) ;
			$xoopsDB->query( "INSERT INTO $table_text SET lid='$lid', description='$desc4save'" ) ;
			echo _MD_A_GNAVI_MB_FINISHED."<br />\n" ;

			$filecount ++ ;
		}
	}
	closedir( $dir_h ) ;

	if( $filecount <= 1 ) {
		echo "<p>$dir4edit : "._MD_A_GNAV_MES_BATCHNONE."</p>" ;
	} else {
		printf( "<p>"._MD_A_GNAV_MES_BATCHDONE."</p>" , $filecount - 1 ) ;
	}

	$result_str = ob_get_contents() ;
	ob_end_clean() ;
}


// Make form objects
$form = new XoopsThemeForm( _MD_A_GNAV_PHOTOBATCHUPLOAD , "batchupload" , "index.php?page=batch" ) ;

$title_text = new XoopsFormText( "" , "title" , 50 , 255 , $title4edit ) ;
$title_tray = new XoopsFormElementTray( _MD_A_GNAVI_TH_TITLE , '<br /><br />' ) ;
$title_tray->addElement( $title_text ) ;
$title_tray->addElement( new XoopsFormLabel( "" , _MD_A_GNAV_BATCHBLANK ) ) ;

$cat = new XoopsFormSelect( _MD_A_GNAVI_TH_CATEGORIES , "cid" , $cid ) ;
$tree = $cattree->getChildTreeArray( 0 , 'weight,title' ) ;
foreach( $tree as $leaf ) {
	$leaf['prefix'] = substr( $leaf['prefix'] , 0 , -1 ) ;
	$leaf['prefix'] = str_replace( "." , "--" , $leaf['prefix'] ) ;
	$cat->addOption( $leaf['cid'] , $leaf['prefix'] . $leaf['title'] ) ;
}

$submitter_select = new XoopsFormSelectUser( _MD_A_GNAVI_TH_SUBMITTER , 'submitter' , false , $submitter ) ;

$date_text = new XoopsFormText( _MD_A_GNAVI_TH_DATE , 'post_date' , 20 , 20 , formatTimestamp( time() , _MD_GNAV_DTFMT_YMDHI ) ) ;

$dir_tray = new XoopsFormElementTray( _MD_A_GNAV_TEXT_DIRECTORY , '<br /><br />' ) ;
$dir_text = new XoopsFormText( _MD_A_GNAV_PHOTOPATH , "dir", 50, 255 , $dir4edit ) ;
$dir_tray->addElement( $dir_text ) ;
$dir_tray->addElement( new XoopsFormLabel( _MD_A_GNAV_DESC_PHOTOPATH ) ) ;
$desc_tarea = new XoopsFormDhtmlTextarea( _MD_A_GNAVI_TH_DESCRIPTION , 'desc' , $desc4edit , 10 , 50 ) ;
$submit_button = new XoopsFormButton( '' , "submit" , _SUBMIT , 'submit' ) ;


// Render forms
xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;

echo "<h3 style='text-align:left;'>".sprintf(_MD_A_GNAVI_FMT_BATCHREGISTER,$xoopsModule->name())."</h3>\n" ;
gnavi_opentable();
$form->addElement( $title_tray ) ;
$form->addElement( $desc_tarea ) ;
$form->addElement( $cat ) ;
$form->addElement( $dir_tray ) ;
$form->addElement( $submitter_select ) ;
$form->addElement( $date_text ) ;
$form->addElement( $submit_button ) ;
$form->setRequired( $dir_text ) ;
$form->display() ;
gnavi_closetable();

if( isset( $result_str ) ) {
	echo "<br />\n" ;
	echo $result_str ;
}

xoops_cp_footer() ;

?>