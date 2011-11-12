<?php
// ------------------------------------------------------------------------- //
//                      myAlbum-P - XOOPS photo album                        //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

include( "admin_header.php" ) ;
include_once( "../../../class/xoopsformloader.php" ) ;

// get and check $_POST['size']
$start = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0 ;
$size = isset( $_POST['size'] ) ? intval( $_POST['size'] ) : 10 ;
if( $size <= 0 || $size > 10000 ) $size = 10 ;

$forceredo = isset( $_POST['forceredo'] ) ? intval( $_POST['forceredo'] ) : false ;
$removerec = isset( $_POST['removerec'] ) ? intval( $_POST['removerec'] ) : false ;
$resize = isset( $_POST['resize'] ) ? intval( $_POST['resize'] ) : false ;

// get flag of safe_mode
$safe_mode_flag = ini_get( "safe_mode" ) ;

// even if makethumb is off, it is treated as makethumb on
if( ! $gnavi_makethumb ) {
	$gnavi_makethumb = 1 ;
	$thumbs_dir = XOOPS_ROOT_PATH . $gnavi_thumbspath ;
	$thumbs_url = XOOPS_URL . $gnavi_thumbspath ;
}

// check if the directories of thumbs and photos are same.
if( $thumbs_dir == $photos_dir ) die( "The directory for thumbnails is same as for photos." ) ;

// check or make thumbs_dir
if( $gnavi_makethumb && ! is_dir( $thumbs_dir ) ) {
	if( $safe_mode_flag ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/admin/",10,"At first create & chmod 777 '$thumbs_dir' by ftp or shell.");
		exit ;
	}

	$rs = mkdir( $thumbs_dir , 0777 ) ;
	if( ! $rs ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/",10,"$thumbs_dir is not a directory");
		exit ;
	} else @chmod( $thumbs_dir , 0777 ) ;
}

if( ! empty( $_POST['submit'] ) ) {
	ob_start() ;

	$result = $xoopsDB->query( "SELECT lid , ext , res_x , res_y ,ext1 , res_x1 , res_y1 ,ext2 , res_x2 , res_y2 FROM $table_photos ORDER BY lid LIMIT $start , $size") or die( "DB Error" ) ;
	$record_counter = 0 ;
	while( list( $lid , $ext , $w , $h , $ext1 , $w1 , $h1 , $ext2 , $w2 , $h2 ) = $xoopsDB->fetchRow( $result ) ) {
		$record_counter ++ ;
		echo "---<strong>&nbsp;".( $record_counter + $start - 1 ) . "&nbsp;</strong>---<br />\n" ;

		// Check if the main image exists
		if( ! is_readable( "$photos_dir/$lid.$ext" ) && ! is_readable( $photos_dir."/".$lid."_1.".$ext1 ) && ! is_readable( $photos_dir."/".$lid."_2.".$ext2 ) ) {
			echo _MD_A_GNAVI_MB_PHOTONOTEXISTS." &nbsp; " ;
			if( $removerec ) {
				gnavi_delete_photos( "lid='$lid'" ) ;
				echo _MD_A_GNAVI_MB_RECREMOVED."<br />\n" ;
			} else {
				echo _MD_A_GNAVI_MB_SKIPPED."<br />\n" ;
			}
			continue ;
		}

		gnavi_exeResize($lid , 0 , $ext  , $w  , $h  , 1 ) ;
		gnavi_exeResize($lid , 1 , $ext1 , $w1 , $h1 , 1 ) ;
		gnavi_exeResize($lid , 2 , $ext2 , $w2 , $h2 , 1 ) ;


	}
	$result_str = ob_get_contents() ;
	ob_end_clean() ;

	$start += $size ;
}

function gnavi_exeResize($lid, $fileNo , $ext , $w , $h ,$makethumbs){
		
	global $photos_dir,$gnavi_normal_exts,$thumbs_dir,$gnavi_makethumb,$table_photos,$xoopsDB;
	global $gnavi_width,$gnavi_height;
	global $resize,$forceredo ;
		$file = $fileNo ? $lid."_". $fileNo : $lid ;

		$res_x = "res_x". ($fileNo ? $fileNo : '' ) ;
		$res_y = "res_y". ($fileNo ? $fileNo : '' ) ;

		if(!$ext) {
			printf( _MD_A_GNAVI_FMT_CHECKING , "$file" ) ;
			echo _MD_A_GNAVI_MB_SKIPPED."<br />\n" ;
			return ;
		}
		printf( _MD_A_GNAVI_FMT_CHECKING , "$file.$ext" ) ;


		if( ! is_readable( "$photos_dir/$file.$ext" ) ) {
			echo _MD_A_GNAVI_MB_PHOTONOTEXISTS." &nbsp; " ;
			echo _MD_A_GNAVI_MB_SKIPPED."<br />\n" ;
			return ;
		}

		// Check if the file is normal image
		if( ! in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
			echo _MD_A_GNAVI_MB_SKIPPED."<br />\n" ;
			return ;
		}

		// Size of main photo
		list( $true_w , $true_h ) = getimagesize( "$photos_dir/$file.$ext" ) ;
		echo "{$true_w}x{$true_h} .. " ;

		// Check and resize the main photo if necessary
		if( $resize && ( $true_w > $gnavi_width || $true_h > $gnavi_height ) ) {
			$tmp_path = "$photos_dir/gnavi_tmp_photo" ;
			@unlink( $tmp_path ) ;
			rename( "$photos_dir/$file.$ext" , $tmp_path ) ;
			gnavi_modify_photo( $tmp_path , "$photos_dir/$file.$ext" ) ;
			@unlink( $tmp_path ) ;
			echo _MD_A_GNAVI_MB_PHOTORESIZED."&nbsp;" ;
			list( $true_w , $true_h ) = getimagesize( "$photos_dir/$file.$ext" ) ;
		} else {
			echo _MD_A_GNAVI_MB_SKIPPED." &nbsp; " ;
		}

		// Check and repair record of the photo if necessary
		if( $true_w != $w || $true_h != $h ) {
			$xoopsDB->query( "UPDATE $table_photos SET $res_x=$true_w, $res_y=$true_h WHERE lid=$lid" ) or die( "DB error: UPDATE photo table." ) ;
			echo  "->&nbsp;{$true_w}x{$true_h}&nbsp;"._MD_A_GNAVI_MB_SIZEREPAIRED." &nbsp; " ;
		}

		if($makethumbs){

			// Create Thumbs
			if( is_readable( "$thumbs_dir/$file.$ext" ) ) {
				list( $thumb_w , $thumb_h ) = getimagesize( "$thumbs_dir/$file.$ext" ) ;
				echo "{$thumb_w}x{$thumb_h} ... " ;
				if( $forceredo ) {
					$retcode = gnavi_create_thumb( "$photos_dir/$file.$ext" , $file , $ext ) ;
				} else {
					$retcode = 3 ;
				}
			} else {
				if( $gnavi_makethumb ) {
					$retcode = gnavi_create_thumb( "$photos_dir/$file.$ext" , $file , $ext ) ;
				} else {
					$retcode = 3 ;
				}
			}

			switch( $retcode ) {
				case 0 : 
					echo _MD_A_GNAVI_MB_FAILEDREADING ;
					break ;
				case 1 : 
					echo _MD_A_GNAVI_MB_CREATEDTHUMBS ;
					break ;
				case 2 : 
					echo _MD_A_GNAVI_MB_BIGTHUMBS ;
					break ;
				case 3 : 
					echo _MD_A_GNAVI_MB_SKIPPED ;
					break ;
			}

		}

		echo "<br />\n" ;
		return ;
}



// Make form objects
$form = new XoopsThemeForm( _MD_A_GNAVI_FORM_RECORDMAINTENANCE , "batchupload" , "index.php?page=redothumbs" ) ;
$form->setExtra( "enctype='multipart/form-data'" ) ;

$start_text = new XoopsFormText( _MD_A_GNAVI_TEXT_RECORDFORSTARTING , "start" , 20 , 20 , $start ) ;
$size_text = new XoopsFormText( _MD_A_GNAVI_TEXT_NUMBERATATIME."<br /><br /><span style='font-weight:normal'>"._MD_A_GNAVI_LABEL_DESCNUMBERATATIME."</span>", "size" , 20 , 20 , $size ) ;
$forceredo_radio = new XoopsFormRadioYN( _MD_A_GNAVI_RADIO_FORCEREDO , 'forceredo' , $forceredo ) ;
$removerec_radio = new XoopsFormRadioYN( _MD_A_GNAVI_RADIO_REMOVEREC , 'removerec' , $removerec ) ;
$resize_radio = new XoopsFormRadioYN( _MD_A_GNAVI_RADIO_RESIZE." ({$gnavi_width}x{$gnavi_height})" , 'resize' , $resize ) ;

if( isset( $record_counter ) && $record_counter < $size ) {
	$submit_button = new XoopsFormLabel( "" , _MD_A_GNAVI_MB_FINISHED." &nbsp; <a href='index.php?page=redothumbs'>"._MD_A_GNAVI_LINK_RESTART."</a>" ) ;
} else {
	$submit_button = new XoopsFormButton( "" , "submit" , _MD_A_GNAVI_SUBMIT_NEXT , "submit" ) ;
}


// Render forms
xoops_cp_header() ;
include dirname(__FILE__).'/mymenu.php' ;

// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( "$mod_url/" , 1 , _NOPERM ) ;
echo "<h3 style='text-align:left;'>".sprintf(_MD_A_GNAVI_FMT_RECORDMAINTENANCE,$xoopsModule->name())."</h3>\n" ;

gnavi_opentable() ;
$form->addElement( $start_text ) ;
$form->addElement( $size_text ) ;
$form->addElement( $forceredo_radio ) ;
$form->addElement( $removerec_radio ) ;
$form->addElement( $resize_radio ) ;
$form->addElement( $submit_button ) ;
$form->display() ;
gnavi_closetable() ;

if( isset( $result_str ) ) {
	echo "<br />\n" ;
	echo $result_str ;
}

xoops_cp_footer() ;

?>