<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

// constants
define( 'PIPEID_GD' , 0 ) ;
define( 'PIPEID_IMAGICK' , 1 ) ;
define( 'PIPEID_NETPBM' , 2 ) ;


function gnavi_get_thumbnail_wh( $width , $height )
{
	global $gnavi_thumbsize , $gnavi_thumbrule ;

	switch( $gnavi_thumbrule ) {
		case 'w' :
			$new_w = $gnavi_thumbsize ;
			$scale = $width / $new_w ;
			$new_h = intval( round( $height / $scale ) ) ;
			break ;
		case 'h' :
			$new_h = $gnavi_thumbsize ;
			$scale = $height / $new_h ;
			$new_w = intval( round( $width / $scale ) ) ;
			break ;
		case 'b' :
			if( $width > $height ) {
				$new_w = $gnavi_thumbsize ;
				$scale = $width / $new_w ;
				$new_h = intval( round( $height / $scale ) ) ;
			} else {
				$new_h = $gnavi_thumbsize ;
				$scale = $height / $new_h ;
				$new_w = intval( round( $width / $scale ) ) ;
			}
			break ;
		default :
			$new_w = $gnavi_thumbsize ;
			$new_h = $gnavi_thumbsize ;
			break ;
	}

	return array( $new_w , $new_h ) ;
}


// create_thumb Wrapper
// return value
//   0 : read fault
//   1 : complete created
//   2 : copied
//   3 : skipped
//   4 : icon gif (not normal exts)
function gnavi_create_thumb( $src_path , $node , $ext )
{
	global $gnavi_imagingpipe , $gnavi_makethumb , $gnavi_normal_exts ;

	if( ! in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
		return gnavi_copy_thumb_from_icons( $src_path , $node , $ext ) ;
	}

	if( ! $gnavi_makethumb ) return 3 ;

	if( $gnavi_imagingpipe == PIPEID_IMAGICK ) {
		return gnavi_create_thumb_by_imagick( $src_path , $node , $ext ) ;
	} else if( $gnavi_imagingpipe == PIPEID_NETPBM ) {
		return gnavi_create_thumb_by_netpbm( $src_path , $node , $ext ) ;
	} else {
		return gnavi_create_thumb_by_gd( $src_path , $node, $ext ) ;
	}
}


// Copy Thumbnail from directory of icons
function gnavi_copy_thumb_from_icons( $src_path , $node , $ext )
{
	global $mod_path , $thumbs_dir ;

	@unlink( "$thumbs_dir/$node.gif" ) ;
	if( file_exists( "$mod_path/icons/$ext.gif" ) ) {
		$copy_success = copy( "$mod_path/icons/$ext.gif" , "$thumbs_dir/$node.gif" ) ;
	}
	if( empty( $copy_success ) ) {
		@copy( "$mod_path/icons/default.gif" , "$thumbs_dir/$node.gif" ) ;
	}

	return 4 ;
}


// Creating Thumbnail by GD
function gnavi_create_thumb_by_gd( $src_path , $node , $ext )
{
	global $gnavi_forcegd2 , $thumbs_dir ;

	$bundled_2 = false ;
	if( ! $gnavi_forcegd2 && function_exists( 'gd_info' ) ) {
		$gd_info = gd_info() ;
		if( substr( $gd_info['GD Version'] , 0 , 10 ) == 'bundled (2') $bundled_2 = true ;
	}

	if( ! is_readable( $src_path ) ) return 0 ;
	@unlink( "$thumbs_dir/$node.$ext" ) ;
	list( $width , $height , $type ) = getimagesize( $src_path ) ;
	switch( $type ) {
		case 1 :
			// GIF (skip)
//HACK by domifara
//			@copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
//			return 2 ;
			$src_img = imagecreatefromgif( $src_path ) ;
			if (empty($src_img)){
				@copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
				return 2 ;
			}
			break ;
		case 2 :
			// JPEG
			$src_img = imagecreatefromjpeg( $src_path ) ;
			break ;
		case 3 :
			// PNG
			$src_img = imagecreatefrompng( $src_path ) ;
			break ;
		default :
			@copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
			return 2 ;
	}

	list( $new_w , $new_h ) = gnavi_get_thumbnail_wh( $width , $height ) ;

	if( $width <= $new_w && $height <= $new_h ) {
		// only copy when small enough
		copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
		return 2 ;
	}

	if( $bundled_2 ) {
		$dst_img = imagecreate( $new_w , $new_h ) ;
		imagecopyresampled( $dst_img , $src_img , 0 , 0 , 0 , 0 , $new_w , $new_h , $width , $height ) ;
	} else {
		$dst_img = @imagecreatetruecolor( $new_w , $new_h ) ;
		if( ! $dst_img ) {
			$dst_img = imagecreate( $new_w , $new_h ) ;
			imagecopyresized( $dst_img , $src_img , 0 , 0 , 0 , 0 , $new_w , $new_h , $width , $height ) ;
		} else {
			imagecopyresampled( $dst_img , $src_img , 0 , 0 , 0 , 0 , $new_w , $new_h , $width , $height ) ;
		}
	}

	switch( $type ) {
//HACK by  domifara
		case 1 :
			// GIF
			imagegif( $dst_img, "$thumbs_dir/$node.$ext" ) ;
			imagedestroy( $dst_img ) ;
			break ;
		case 2 :
			// JPEG
			imagejpeg( $dst_img, "$thumbs_dir/$node.$ext" ) ;
			imagedestroy( $dst_img ) ;
			break ;
		case 3 :
			// PNG
			imagepng( $dst_img, "$thumbs_dir/$node.$ext" ) ;
			imagedestroy( $dst_img ) ;
			break ;
	}

	imagedestroy( $src_img ) ;
	return 1 ;
}


// Creating Thumbnail by ImageMagick
function gnavi_create_thumb_by_imagick( $src_path , $node , $ext )
{
	global $gnavi_imagickpath , $thumbs_dir ;

	// Check the path to binaries of imaging packages
	if( trim( $gnavi_imagickpath ) != '' && substr( $gnavi_imagickpath , -1 ) != DIRECTORY_SEPARATOR ) {
		$gnavi_imagickpath .= DIRECTORY_SEPARATOR ;
	}

	if( ! is_readable( $src_path ) ) return 0 ;
	@unlink( "$thumbs_dir/$node.$ext" ) ;
	list( $width , $height , $type ) = getimagesize( $src_path ) ;

	list( $new_w , $new_h ) = gnavi_get_thumbnail_wh( $width , $height ) ;

	if( $width <= $new_w && $height <= $new_h ) {
		// only copy when small enough
		copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
		return 2 ;
	}

	// Make Thumb and check success
	exec( "{$gnavi_imagickpath}convert -geometry {$new_w}x{$new_h} $src_path $thumbs_dir/$node.$ext" ) ;
	if( ! is_readable( "$thumbs_dir/$node.$ext" ) ) {
		// can't exec convert, big thumbs!
		copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
		return 2 ;
	}

	return 1 ;
}


// Creating Thumbnail by NetPBM
function gnavi_create_thumb_by_netpbm( $src_path , $node , $ext )
{
	global $gnavi_netpbmpath , $thumbs_dir ;

	// Check the path to binaries of imaging packages
	if( trim( $gnavi_netpbmpath ) != '' && substr( $gnavi_netpbmpath , -1 ) != DIRECTORY_SEPARATOR ) {
		$gnavi_netpbmpath .= DIRECTORY_SEPARATOR ;
	}

	if( ! is_readable( $src_path ) ) return 0 ;
	@unlink( "$thumbs_dir/$node.$ext" ) ;
	list( $width , $height , $type ) = getimagesize( $src_path ) ;
	switch( $type ) {
		case 1 :
			// GIF
			$pipe0 = "{$gnavi_netpbmpath}giftopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}ppmquant 256 | {$gnavi_netpbmpath}ppmtogif" ;
			break ;
		case 2 :
			// JPEG
			$pipe0 = "{$gnavi_netpbmpath}jpegtopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}pnmtojpeg" ;
			break ;
		case 3 :
			// PNG
			$pipe0 = "{$gnavi_netpbmpath}pngtopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}pnmtopng" ;
			break ;
		default :
			@copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
			return 2 ;
	}

	list( $new_w , $new_h ) = gnavi_get_thumbnail_wh( $width , $height ) ;

	if( $width <= $new_w && $height <= $new_h ) {
		// only copy when small enough
		copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
		return 2 ;
	}

	$pipe1 = "{$gnavi_netpbmpath}pnmscale -xysize $new_w $new_h" ;

	// Make Thumb and check success
	exec( "$pipe0 < $src_path | $pipe1 | $pipe2 > $thumbs_dir/$node.$ext" ) ;
	if( ! is_readable( "$thumbs_dir/$node.$ext" ) ) {
		// can't exec convert, big thumbs!
		copy( $src_path , "$thumbs_dir/$node.$ext" ) ;
		return 2 ;
	}

	return 1 ;
}


// modifyPhoto Wrapper
function gnavi_modify_photo( $src_path , $dst_path )
{
	global $gnavi_imagingpipe , $gnavi_forcegd2 , $gnavi_normal_exts ;

	$ext = substr( strrchr( $dst_path , '.' ) , 1 ) ;

	if( ! in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
		rename( $src_path , $dst_path ) ;
	}

	if( $gnavi_imagingpipe == PIPEID_IMAGICK ) {
		gnavi_modify_photo_by_imagick( $src_path , $dst_path ) ;
	} else if( $gnavi_imagingpipe == PIPEID_NETPBM ) {
		gnavi_modify_photo_by_netpbm( $src_path , $dst_path ) ;
	} else {
		if( $gnavi_forcegd2 ) gnavi_modify_photo_by_gd( $src_path , $dst_path ) ;
		else rename( $src_path , $dst_path ) ;
	}
}


// Modifying Original Photo by GD
function gnavi_modify_photo_by_gd( $src_path , $dst_path )
{
	global $gnavi_width , $gnavi_height ;

	if( ! is_readable( $src_path ) ) return 0 ;

	list( $width , $height , $type ) = getimagesize( $src_path ) ;

	switch( $type ) {
		case 1 :
			// GIF
			@rename( $src_path, $dst_path ) ;
			return 2 ;
		case 2 :
			// JPEG
			$src_img = imagecreatefromjpeg( $src_path ) ;
			break ;
		case 3 :
			// PNG
			$src_img = imagecreatefrompng( $src_path ) ;
			break ;
		default :
			@rename( $src_path, $dst_path ) ;
			return 2 ;
	}

	if( $width > $gnavi_width || $height > $gnavi_height ) {
		if( $width / $gnavi_width > $height / $gnavi_height ) {
			$new_w = $gnavi_width ;
			$scale = $width / $new_w ;
			$new_h = intval( round( $height / $scale ) ) ;
		} else {
			$new_h = $gnavi_height ;
			$scale = $height / $new_h ;
			$new_w = intval( round( $width / $scale ) ) ;
		}
		$dst_img = imagecreatetruecolor( $new_w , $new_h ) ;
		imagecopyresampled( $dst_img , $src_img , 0 , 0 , 0 , 0 , $new_w , $new_h , $width , $height ) ;
	}

	if( isset( $_POST['rotate'] ) && function_exists( 'imagerotate' ) ) switch( $_POST['rotate'] ) {
		case 'rot270' :
			if( ! isset( $dst_img ) || ! is_resource( $dst_img ) ) $dst_img = $src_img ;
			// patch for 4.3.1 bug
			$dst_img = imagerotate( $dst_img , 270 , 0 ) ;
			$dst_img = imagerotate( $dst_img , 180 , 0 ) ;
			break ;
		case 'rot180' :
			if( ! isset( $dst_img ) || ! is_resource( $dst_img ) ) $dst_img = $src_img ;
			$dst_img = imagerotate( $dst_img , 180 , 0 ) ;
			break ;
		case 'rot90' :
			if( ! isset( $dst_img ) || ! is_resource( $dst_img ) ) $dst_img = $src_img ;
			$dst_img = imagerotate( $dst_img , 270 , 0 ) ;
			break ;
		default :
		case 'rot0' :
			break ;
	}

	if( isset( $dst_img ) && is_resource( $dst_img ) ) switch( $type ) {
		case 2 :
			// JPEG
			imagejpeg( $dst_img , $dst_path ) ;
			imagedestroy( $dst_img ) ;
			break ;
		case 3 :
			// PNG
			imagepng( $dst_img , $dst_path ) ;
			imagedestroy( $dst_img ) ;
			break ;
	}

	imagedestroy( $src_img ) ;
	if( ! is_readable( $dst_path ) ) {
		// didn't exec convert, rename it.
		@rename( $src_path , $dst_path ) ;
		return 2 ;
	} else {
		@unlink( $src_path ) ;
		return 1 ;
	}
}



// Modifying Original Photo by ImageMagick
function gnavi_modify_photo_by_imagick( $src_path , $dst_path )
{
	global $gnavi_width , $gnavi_height , $gnavi_imagickpath ;

	// Check the path to binaries of imaging packages
	if( trim( $gnavi_imagickpath ) != '' && substr( $gnavi_imagickpath , -1 ) != DIRECTORY_SEPARATOR ) {
		$gnavi_imagickpath .= DIRECTORY_SEPARATOR ;
	}

	if( ! is_readable( $src_path ) ) return 0 ;

	// Make options for imagick
	$option = "" ;
	$image_stats = getimagesize( $src_path ) ;
	if( $image_stats[0] > $gnavi_width || $image_stats[1] > $gnavi_height ) {
		$option .= " -geometry {$gnavi_width}x{$gnavi_height}" ;
	}
	if( isset( $_POST['rotate'] ) ) switch( $_POST['rotate'] ) {
		case 'rot270' :
			$option .= " -rotate 270" ;
			break ;
		case 'rot180' :
			$option .= " -rotate 180" ;
			break ;
		case 'rot90' :
			$option .= " -rotate 90" ;
			break ;
		default :
		case 'rot0' :
			break ;
	}

	// Do Modify and check success
	if( $option != "" ) exec( "{$gnavi_imagickpath}convert $option $src_path $dst_path" ) ;

	if( ! is_readable( $dst_path ) ) {
		// didn't exec convert, rename it.
		@rename( $src_path , $dst_path ) ;
		$ret = 2 ;
	} else {
		@unlink( $src_path ) ;
		$ret = 1 ;
	}

	// water mark
	$wmfile = dirname( dirname( __FILE__ ) ) . '/images/watermark.gif' ;
	if( file_exists( $wmfile ) ) {
		exec( "{$gnavi_imagickpath}composite -compose plus $wmfile $dst_path $dst_path" ) ;
	}

	return $ret ;
}


// Modifying Original Photo by NetPBM
function gnavi_modify_photo_by_netpbm( $src_path , $dst_path )
{
	global $gnavi_width , $gnavi_height , $gnavi_netpbmpath ;

	// Check the path to binaries of imaging packages
	if( trim( $gnavi_netpbmpath ) != '' && substr( $gnavi_netpbmpath , -1 ) != DIRECTORY_SEPARATOR ) {
		$gnavi_netpbmpath .= DIRECTORY_SEPARATOR ;
	}

	if( ! is_readable( $src_path ) ) return 0 ;

	list( $width , $height , $type ) = getimagesize( $src_path ) ;

	$pipe1 = '' ;
	switch( $type ) {
		case 1 :
			// GIF
			$pipe0 = "{$gnavi_netpbmpath}giftopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}ppmquant 256 | {$gnavi_netpbmpath}ppmtogif" ;
			break ;
		case 2 :
			// JPEG
			$pipe0 = "{$gnavi_netpbmpath}jpegtopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}pnmtojpeg" ;
			break ;
		case 3 :
			// PNG
			$pipe0 = "{$gnavi_netpbmpath}pngtopnm" ;
			$pipe2 = "{$gnavi_netpbmpath}pnmtopng" ;
			break ;
		default :
			@rename( $src_path, $dst_path ) ;
			return 2 ;
	}

	if( $width > $gnavi_width || $height > $gnavi_height ) {
		if( $width / $gnavi_width > $height / $gnavi_height ) {
			$new_w = $gnavi_width ;
			$scale = $width / $new_w ;
			$new_h = intval( round( $height / $scale ) ) ;
		} else {
			$new_h = $gnavi_height ;
			$scale = $height / $new_h ;
			$new_w = intval( round( $width / $scale ) ) ;
		}
		$pipe1 .= "{$gnavi_netpbmpath}pnmscale -xysize $new_w $new_h |" ;
	}

	if( isset( $_POST['rotate'] ) ) switch( $_POST['rotate'] ) {
		case 'rot270' :
			$pipe1 .= "{$gnavi_netpbmpath}pnmflip -r90 |" ;
			break ;
		case 'rot180' :
			$pipe1 .= "{$gnavi_netpbmpath}pnmflip -r180 |" ;
			break ;
		case 'rot90' :
			$pipe1 .= "{$gnavi_netpbmpath}pnmflip -r270 |" ;
			break ;
		default :
		case 'rot0' :
			break ;
	}

	// Do Modify and check success
	if( $pipe1 ) {
		$pipe1 = substr( $pipe1 , 0 , -1 ) ;
		exec( "$pipe0 < $src_path | $pipe1 | $pipe2 > $dst_path" ) ;
	}

	if( ! is_readable( $dst_path ) ) {
		// didn't exec convert, rename it.
		@rename( $src_path , $dst_path ) ;
		return 2 ;
	} else {
		@unlink( $src_path ) ;
		return 1 ;
	}
}



// Clear templorary files
function gnavi_clear_tmp_files( $dir_path , $prefix = 'tmp_' )
{
	// return if directory can't be opened
	if( ! ( $dir = @opendir( $dir_path ) ) ) {
		return 0 ;
	}

	$ret = 0 ;
	$prefix_len = strlen( $prefix ) ;
	while( ( $file = readdir( $dir ) ) !== false ) {
		if( strncmp( $file , $prefix , $prefix_len ) === 0 ) {
			if( @unlink( "$dir_path/$file" ) ) $ret ++ ;
		}
	}
	closedir( $dir ) ;

	return $ret ;
}


//updates rating data in itemtable for a given item
function gnavi_updaterating( $lid )
{
	global $xoopsDB , $table_votedata , $table_photos ;

	$query = "SELECT rating FROM $table_votedata WHERE lid=$lid" ;
	$voteresult = $xoopsDB->query( $query ) ;
	$votesDB = $xoopsDB->getRowsNum( $voteresult ) ;
	$totalrating = 0 ;
	while( list( $rating ) = $xoopsDB->fetchRow( $voteresult ) ) {
		$totalrating += $rating ;
	}
	$finalrating = number_format( $totalrating / $votesDB , 4 ) ;
	$query = "UPDATE $table_photos SET rating=$finalrating, votes=$votesDB WHERE lid = $lid" ;

	$xoopsDB->query( $query ) or die( "Error: DB update rating." ) ;
}


// Returns the number of photos included in a Category
function gnavi_get_photo_small_sum_from_cat( $cid , $whr_append = "" )
{
	global $xoopsDB , $table_photos ;

	if( $whr_append ) $whr_append = "AND ($whr_append)" ;

	$sql = "SELECT COUNT(lid) FROM $table_photos WHERE ( cid=$cid or cid1=$cid or cid2=$cid or cid3=$cid or cid4=$cid )$whr_append" ;
	$rs = $xoopsDB->query( $sql ) ;
	list( $numrows ) = $xoopsDB->fetchRow( $rs ) ;

	return $numrows ;
}


// Returns the number of whole photos included in a Category
function gnavi_get_photo_total_sum_from_cats( $cids , $whr_append = "" )
{
	global $xoopsDB , $table_photos ;

	if( $whr_append ) $whr_append = "AND ($whr_append)" ;

	//$whr = "cid IN (" ;
	//foreach( $cids as $cid ) {
	//	$whr .= "$cid," ;
	//}
	//$whr = "$whr 0)" ;

	$whr = "";
	foreach( $cids as $cid ) {
		$whr .= "$cid," ;
	}
	$whr =substr($whr, 0, -1);

	$whr = "cid IN($whr) or cid1 IN($whr) or cid2 IN($whr) or cid3 IN($whr) or cid4 IN($whr)";

	$sql = "SELECT COUNT(lid) FROM $table_photos WHERE ( $whr ) $whr_append" ;
	$rs = $xoopsDB->query( $sql ) ;
	list( $numrows ) = $xoopsDB->fetchRow( $rs ) ;

	return $numrows ;
}


// Update a photo
function gnavi_update_item($mode,$lid,
							$title,$cid,$cid1,$cid2,$cid3,$cid4,
                            $url,$tel,$fax,$zip,$address,$rss,$lat,$lng,$zoom,$mtype,$icd,
                            $submitter,$poster_name,$valid){
	global $xoopsDB;
	global $table_photos , $isadmin ;

	$myts =& GNaviTextSanitizer::getInstance() ;

	if( $mode == G_INSERT){

		$lid = $xoopsDB->genId( $table_photos."_lid_seq" ) ;
		$date = time() ;

		$sql  = "INSERT INTO $table_photos (lid,title,cid,cid1,cid2,cid3,cid4,";
		$sql .= "url,tel,fax,zip,address,rss,lat,lng,zoom,mtype,icd,";
		$sql .= "submitter,poster_name,status,date) VALUES (";
		$sql .= "$lid,'".addSlashes($title)."',$cid,$cid1,$cid2,$cid3,$cid4,";
		$sql .= "'".addSlashes($url)."','".addSlashes($tel)."','".addSlashes($fax)."','".addSlashes($zip)."','".addSlashes($address)."','".addSlashes($rss)."',$lat,$lng,$zoom,'".addSlashes($mtype)."',$icd,";
		$sql .= "$submitter,'".addSlashes($poster_name)."',$valid,$date)";

		$xoopsDB->query( $sql ) or die( "DB error: INSERT photo table" ) ;
		if( $lid == 0 ) {
			$lid = $xoopsDB->getInsertId();
		}

	}else{

		if( isset( $valid ) ) {
			$set_status = ",status='$valid'" ;
		} else {
			$set_status = '' ;
		}

		$set_date = empty( $_POST['store_timestamp'] ) ? "" : ",date=UNIX_TIMESTAMP()" ;

		// not admin can only touch photos status>0
		$whr_status = $isadmin ? '' : 'AND status>0' ;

		$sql  = "UPDATE $table_photos SET ";
        $sql .= "title='".addSlashes($title)."',cid='$cid',cid1='$cid1',cid2='$cid2',cid3='$cid3',cid4='$cid4',";
        $sql .= "url='".addSlashes($url)."',tel='".addSlashes($tel)."',fax='".addSlashes($fax)."',zip='".addSlashes($zip)."',address='".addSlashes($address)."',rss='".addSlashes($rss)."',lat=$lat,lng=$lng,zoom=$zoom,mtype='".addSlashes($mtype)."',icd=$icd,";
		$sql .= "submitter='$submitter',poster_name='".addSlashes($poster_name)."' $set_status $set_date WHERE lid='$lid' $whr_status" ;

		$xoopsDB->query( $sql ) or die( "DB error: UPDATE photo table" ) ;

	}

	return $lid;

}


function gnavi_update_desc($mode,$lid,$cid,$title,$submitter,$valid,
							$ext,$ext1,$ext2,$resx,$resy,$resx1,$resy1,$resx2,$resy2,
							$caption,$caption1,$caption2,
                            $desc,$arrowhtml,$addinfo){

	global $xoopsDB,$gnavi_addposts;
	global $table_photos , $table_text , $table_cat , $mod_url , $isadmin ;
	$myts =& GNaviTextSanitizer::getInstance() ;

	$caption  = $ext =='' ? '' : $caption  ;
	$caption1 = $ext1=='' ? '' : $caption1 ;
	$caption2 = $ext2=='' ? '' : $caption2 ;

	$sql="UPDATE $table_photos SET caption='".addSlashes($caption)."',caption1='".addSlashes($caption1)."',caption2='".addSlashes($caption2)."',ext='{$ext}',ext1='{$ext1}',ext2='{$ext2}',res_x={$resx},res_y={$resy},res_x1={$resx1},res_y1={$resy1},res_x2={$resx2},res_y2={$resy2} WHERE lid='$lid'" ;
	$xoopsDB->query( $sql ) or die( "DB error: UPDATE text table" ) ;

	if( $mode == G_INSERT){

		$sql = "INSERT INTO $table_text (lid, description,arrowhtml,addinfo) VALUES ($lid,'".addSlashes($desc)."',$arrowhtml,'".addSlashes($addinfo)."')" ;

		$xoopsDB->query( $sql ) or die( "DB error: INSERT desc table" ) ;

		// Update User's Posts (Should be modified when need admission.)
		$user_handler =& xoops_gethandler('user') ;
		$submitter_obj =& $user_handler->get( $submitter ) ;
		if( is_object( $submitter_obj ) ) {
			for( $i = 0 ; $i < $gnavi_addposts ; $i ++ ) {
				$submitter_obj->incrementPost() ;
			}
		}

	}else{

		// not admin can only touch photos status>0
		$whr_status = $isadmin ? '' : 'AND status>0' ;

		$sql="UPDATE $table_text SET description='".addSlashes($desc)."',arrowhtml=$arrowhtml,addinfo='".addSlashes($addinfo)."' WHERE lid='$lid'";
		$xoopsDB->query( $sql ) or die( "DB error: UPDATE text table" ) ;

	}

	// Trigger Notification if Change Or submit
	if( $valid == 1 ) {

		$notification_handler =& xoops_gethandler('notification');

		$tags = array();
		$tags['PHOTO_TITLE'] = $title;
		$tags['PHOTO_URI']  = $mod_url."/index.php?lid=".$lid;

		// Global Notification
		gnavi_trigger_event('global', 0, 'new_item', $tags);

		// Category Notification
		$rs = $xoopsDB->query( "SELECT title FROM $table_cat WHERE cid=$cid" ) ;
		list( $cat_title ) = $xoopsDB->fetchRow( $rs ) ;
		$tags['CATEGORY_TITLE']  = $cat_title;
		$tags['PHOTO_URI']  = $mod_url."/index.php?lid=".$lid."&cid=".$cid ;

		gnavi_trigger_event('category', $cid, 'new_item', $tags);

	}

}

function gnavi_trigger_event( $category , $item_id , $event , $tags, $extra_tags=array() , $user_list=array() , $omit_user_id=null )
{
	global $xoopsModule , $xoopsConfig , $mydirname , $mydirpath , $mytrustdirname , $mytrustdirpath ;

	$notification_handler =& xoops_gethandler('notification') ;

	$mid = $xoopsModule->getVar('mid') ;

	// language file
	$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;
	if( file_exists( "$mydirpath/language/$language/mail_template/" ) ) {
		// user customized language file
		$mail_template_dir = "$mydirpath/language/$language/mail_template/" ;
	} else if( file_exists( "$mytrustdirpath/language/$language/mail_template/" ) ) {
		// default language file
		$mail_template_dir = "$mytrustdirpath/language/$language/mail_template/";
	} else {
		// fallback english
		$mail_template_dir = "$mytrustdirpath/language/english/mail_template/";
	}

	// Check if event is enabled
	$config_handler =& xoops_gethandler('config');
	$mod_config =& $config_handler->getConfigsByCat(0,$mid);
	if (empty($mod_config['notification_enabled'])) {
		return false;
	}
	$category_info =& notificationCategoryInfo ($category, $mid);
	$event_info =& notificationEventInfo ($category, $event, $mid);
	if (!in_array(notificationGenerateConfig($category_info,$event_info,'option_name'),$mod_config['notification_events']) && empty($event_info['invisible'])) {
		return false;
	}

	if (!isset($omit_user_id)) {
		global $xoopsUser;
		if (!empty($xoopsUser)) {
			$omit_user_id = $xoopsUser->getVar('uid');
		} else {
			$omit_user_id = 0;
		}
	}
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('not_modid', intval($mid)));
	$criteria->add(new Criteria('not_category', $category));
	$criteria->add(new Criteria('not_itemid', intval($item_id)));
	$criteria->add(new Criteria('not_event', $event));
	$mode_criteria = new CriteriaCompo();
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDALWAYS), 'OR');
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE), 'OR');
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT), 'OR');
	$criteria->add($mode_criteria);
	if (!empty($user_list)) {
		$user_criteria = new CriteriaCompo();
		foreach ($user_list as $user) {
			$user_criteria->add (new Criteria('not_uid', $user), 'OR');
		}
		$criteria->add($user_criteria);
	}
	$notifications =& $notification_handler->getObjects($criteria);
	if (empty($notifications)) {
		return;
	}

	// Add some tag substitutions here
	//$tags = array();
	// {X_ITEM_NAME} {X_ITEM_URL} {X_ITEM_TYPE} from lookup_func are disabled
	$tags['X_MODULE'] = $xoopsModule->getVar('name','n');
	$tags['X_MODULE_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/';
	$tags['X_NOTIFY_CATEGORY'] = $category;
	$tags['X_NOTIFY_EVENT'] = $event;

	$template = $event_info['mail_template'] . '.tpl';
	$subject = $event_info['mail_subject'];

	foreach ($notifications as $notification) {
		if (empty($omit_user_id) || $notification->getVar('not_uid') != $omit_user_id) {
			// user-specific tags
			//$tags['X_UNSUBSCRIBE_URL'] = 'TODO';
			// TODO: don't show unsubscribe link if it is 'one-time' ??
			$tags['X_UNSUBSCRIBE_URL'] = XOOPS_URL . '/notifications.php';
			$tags = array_merge ($tags, $extra_tags);

			$notification->notifyUser($mail_template_dir, $template, $subject, $tags);
		}
	}
}


// Delete photos hit by the $whr clause
function gnavi_delete_photos( $whr )
{
	global $xoopsDB ;
	global $photos_dir , $thumbs_dir , $gnavi_mid ;
	global $table_photos , $table_text , $table_votedata ;

	$prs = $xoopsDB->query("SELECT lid, ext, ext1, ext2 FROM $table_photos WHERE $whr" ) ;
	while( list( $lid , $ext , $ext1 , $ext2 ) = $xoopsDB->fetchRow( $prs ) ) {

		xoops_comment_delete( $gnavi_mid , $lid ) ;
		xoops_notification_deletebyitem( $gnavi_mid , 'photo' , $lid ) ;

		$xoopsDB->query( "DELETE FROM $table_votedata WHERE lid=$lid" ) or die( "DB error: DELETE votedata table." ) ;
		$xoopsDB->query( "DELETE FROM $table_text WHERE lid=$lid" ) or die( "DB error: DELETE text table." ) ;
		$xoopsDB->query( "DELETE FROM $table_photos WHERE lid=$lid" ) or die( "DB error: DELETE photo table." ) ;

		if($ext){
			@unlink( "$photos_dir/$lid.$ext" ) ;
			@unlink( "$thumbs_dir/$lid.$ext" ) ;
		}
		if($ext1){
			@unlink( $photos_dir."/".$lid."_1.".$ext1 ) ;
			@unlink( $photos_dir."/".$lid."_1.".$ext1 ) ;
		}
		if($ext2){
			@unlink( $photos_dir."/".$lid."_2.".$ext2 ) ;
			@unlink( $photos_dir."/".$lid."_2.".$ext2 ) ;
		}



	}
}


// Substitution of opentable()
function gnavi_opentable()
{
	echo "<div style='border: 2px solid #2F5376;padding:8px;width:95%;' class='bg4'>\n" ;
}


// Substitution of closetable()
function gnavi_closetable()
{
	echo "</div>\n" ;
}


// returns extracted string for options from table with xoops tree
function gnavi_get_cat_options( $order = 'title' , $preset = 0 , $prefix = '--' , $none = null , $table_name_cat = null , $table_name_photos = null )
{
	global $xoopsDB ;

	$myts =& MyTextSanitizer::getInstance() ;

	if( empty( $table_name_cat ) ) $table_name_cat = $GLOBALS['table_cat'] ;
	if( empty( $table_name_photos ) ) $table_name_photos = $GLOBALS['table_photos'] ;

	$cats[0] = array( 'cid' => 0 , 'pid' => -1 , 'next_key' => -1 , 'depth' => 0 , 'title' => '' , 'num' => 0 ) ;

	$rs = $xoopsDB->query( "SELECT c.title,c.cid,c.pid,COUNT(p.lid) AS num FROM $table_name_cat c LEFT JOIN $table_name_photos p ON c.cid=p.cid GROUP BY c.cid ORDER BY pid ASC,$order DESC" ) ;

	$key = 1 ;
	while( list( $title , $cid , $pid , $num ) = $xoopsDB->fetchRow( $rs ) ) {
		$cats[ $key ] = array( 'cid' => intval( $cid ) , 'pid' => intval( $pid ) , 'next_key' => $key + 1 , 'depth' => 0 , 'title' => $myts->makeTboxData4Show( $title ) , 'num' => intval( $num ) ) ;
		$key ++ ;
	}
	$sizeofcats = $key ;

	$loop_check_for_key = 1024 ;
	for( $key = 1 ; $key < $sizeofcats ; $key ++ ) {
		$cat =& $cats[ $key ] ;
		$target =& $cats[ 0 ] ;
		if( -- $loop_check_for_key < 0 ) $loop_check = -1 ;
		else $loop_check = 4096 ;

		while( 1 ) {
			if( $cat['pid'] == $target['cid'] ) {
				$cat['depth'] = $target['depth'] + 1 ;
				$cat['next_key'] = $target['next_key'] ;
				$target['next_key'] = $key ;
				break ;
			} else if( -- $loop_check < 0 ) {
				$cat['depth'] = 1 ;
				$cat['next_key'] = $target['next_key'] ;
				$target['next_key'] = $key ;
				break ;
			} else if( $target['next_key'] < 0 ) {
				$cat_backup = $cat ;
				array_splice( $cats , $key , 1 ) ;
				array_push( $cats , $cat_backup ) ;
				-- $key ;
				break ;
			}
			$target =& $cats[ $target['next_key'] ] ;
		}
	}

	if( isset( $none ) ) $ret = "<option value=''>$none</option>\n" ;
	else $ret = '' ;
	$cat =& $cats[ 0 ]  ;
	for( $weight = 1 ; $weight < $sizeofcats ; $weight ++ ) {
		$cat =& $cats[ $cat['next_key'] ] ;
		$pref = str_repeat( $prefix , $cat['depth'] - 1 ) ;
		$selected = $preset == $cat['cid'] ? "selected='selected'" : '' ;
		$ret .= "<option value='{$cat['cid']}' $selected>$pref {$cat['title']} ({$cat['num']})</option>\n" ;
	}

	return $ret ;
}


function gnavi_submit_uploader_pre($field , $preview_name,$del_photo,$guard_name){

	global $gnavi_canresize,$photos_dir , $array_allowed_mimetypes , $gnavi_fsize , $gnavi_width , $gnavi_height , $array_allowed_exts;

	if( is_readable( $_FILES[$field]['tmp_name'] ) ) {
		// new preview
		if( $preview_name != ''){
			if($guard_name != $preview_name){
				@unlink( "$photos_dir/$preview_name" ) ;
			}
		}
		if( $gnavi_canresize ) $uploader = new MyXoopsMediaUploader( $photos_dir , $array_allowed_mimetypes , $gnavi_fsize , null , null , $array_allowed_exts ) ;
		else $uploader = new MyXoopsMediaUploader( $photos_dir , $array_allowed_mimetypes , $gnavi_fsize , $gnavi_width , $gnavi_height , $array_allowed_exts ) ;
		$uploader->setPrefix( 'tmp_' ) ;
		if( $uploader->fetchMedia( $field ) && $uploader->upload() ) {
			$tmp_name = $uploader->getSavedFileName() ;
			$preview_name = str_replace( 'tmp_' , 'tmp_prev_' , $tmp_name ) ;
			gnavi_modify_photo( "$photos_dir/$tmp_name" , "$photos_dir/$preview_name" ) ;
		} else {
			@unlink( $uploader->getSavedDestination() ) ;
			$preview_name='';
		}
	} else if( $preview_name != '' && is_readable( "$photos_dir/$preview_name" ) ) {
		if($del_photo==1){
			if($guard_name != $preview_name){
				@unlink( "$photos_dir/$preview_name" ) ;
			}
			$preview_name='';
		}
	}else{
		$preview_name='';
	}
	return $preview_name;
}

function gnavi_submit_uploader($field ,$del_photo,$preview_name, $num, $errmsg){

	global $gnavi_canresize,$photos_dir , $array_allowed_mimetypes , $gnavi_fsize , $gnavi_width , $gnavi_height , $array_allowed_exts;

	$tmp_name='';
	$ext='';

	// Check if upload file name specified
	if( empty( $field ) || $field == "" ) {
		die( "UPLOAD error: file name not specified" ) ;
	}

	if( $_FILES[$field]['name'] == '' ) {
		// No photo uploaded
		if( $preview_name != '' && is_readable( "$photos_dir/$preview_name" ) ) {
			if($del_photo==1){
				@unlink( "$photos_dir/$preview_name" ) ;
			}else{
				$tmp_name = $preview_name ;
				$ext = substr( strrchr( $tmp_name , '.' ) , 1 ) ;
			}
		}
	} else if( $_FILES[$field]['tmp_name'] == "" ) {
		// Fail to upload (wrong file name etc.)
		$errmsg .= "<br />File $num "._MD_GNAV_MSG_FILEERROR ;
	} else {
		if($preview_name!='' && is_readable( "$photos_dir/$preview_name" ))@unlink("$photos_dir/$preview_name") ;
		if( $gnavi_canresize ) $uploader = new MyXoopsMediaUploader( $photos_dir , $array_allowed_mimetypes , $gnavi_fsize , null , null , $array_allowed_exts ) ;
		else $uploader = new MyXoopsMediaUploader( $photos_dir , $array_allowed_mimetypes , $gnavi_fsize , $gnavi_width , $gnavi_height , $array_allowed_exts ) ;
		$uploader->setPrefix( 'tmp_' ) ;
		if( $uploader->fetchMedia( $field ) && $uploader->upload() ) {
			// Succeed to upload
			$tmp_name = $uploader->getSavedFileName() ;
			$ext = substr( strrchr( $tmp_name , '.' ) , 1 ) ;
		} else {
			// Fail to upload (sizeover etc.)
			$errmsg .= "<br />".$uploader->getErrors() ;
			@unlink( $uploader->getSavedDestination() ) ;
		}
		if( ! is_readable( "$photos_dir/$tmp_name" ) ) {
			$errmsg .= "<br />File $num "._MD_GNAV_MSG_FILEREADERROR ;
		}
	}

	return array($tmp_name,$ext,$errmsg);
}

function gnavi_get_icon($cattree,$cid,$cid1,$cid2,$cid3,$cid4,$mcid=0){
	if($mcid>0){
		//$cid
		if(in_array($mcid,$cattree->getAllParentId($cid))){
			$icon=gnavi_geticon_byParentId($cid);
			if($icon>0)return $icon;
		}
		//$cid1
		if(in_array($mcid,$cattree->getAllParentId($cid1))){
			$icon=gnavi_geticon_byParentId($cid1);
			if($icon>0)return $icon;
		}
		//$cid2
		if(in_array($mcid,$cattree->getAllParentId($cid2))){
			$icon=gnavi_geticon_byParentId($cid2);
			if($icon>0)return $icon;
		}
		//$cid3
		if(in_array($mcid,$cattree->getAllParentId($cid3))){
			$icon=gnavi_geticon_byParentId($cid3);
			if($icon>0)return $icon;
		}
		//$cid4
		if(in_array($mcid,$cattree->getAllParentId($cid4))){
			$icon=gnavi_geticon_byParentId($cid4);
			if($icon>0)return $icon;
		}
	}

	//$cid
	$icon=gnavi_geticon_byParentId($cid);
	if($icon>0)return $icon;
	//$cid1
	$icon=gnavi_geticon_byParentId($cid1);
	if($icon>0)return $icon;
	//$cid2
	$icon=gnavi_geticon_byParentId($cid2);
	if($icon>0)return $icon;
	//$cid3
	$icon=gnavi_geticon_byParentId($cid3);
	if($icon>0)return $icon;
	//$cid4
	$icon=gnavi_geticon_byParentId($cid4);
	if($icon>0)return $icon;


	return 0;

}

	//returns an array of ALL parent ids for a given id($sel_id)
function gnavi_geticon_byParentId($cid){
	global $table_cat,$xoopsDB;
	$sql = "SELECT pid,icd FROM $table_cat WHERE cid=$cid";
	$result=$xoopsDB->query($sql);
	list($pid,$icd) = $xoopsDB->fetchRow($result);
	if ( intval($icd) > 0 ) {
		return intval($icd);
	}
	if ( intval($pid) == 0 ) {
		return 0;
	}
	return gnavi_geticon_byParentId(intval($pid));
}

function gnavi_get_anony_perms(){
	global $xoopsDB,$mydirname ;
	$anony_perms=0;
	$whr_groupid = "GPERM_groupid=".XOOPS_GROUP_ANONYMOUS ;
	$rs = $xoopsDB->query( "SELECT GPERM_itemid FROM ".$xoopsDB->prefix("group_permission")." LEFT JOIN ".$xoopsDB->prefix("modules")." m ON GPERM_modid=m.mid WHERE m.dirname='$mydirname' AND GPERM_name='gnavi_global' AND ($whr_groupid)" ) ;
	while( list( $itemid ) = $xoopsDB->fetchRow( $rs ) ) {
		$anony_perms |= $itemid ;
	}
	return $anony_perms;
}
function gnavi_get_gicon($icd,$op=0){
	//op 1:img src   0:for java
	global $xoopsDB,$table_icon,$icon_url;
	if($icd==0)return '';
	$result = $xoopsDB->query( "SELECT ext,shadow_ext,x,y,shadow_x,shadow_y,Anchor_x,Anchor_y FROM $table_icon WHERE icd=$icd");
	list($ext,$shadow_ext,$x,$y,$shadow_x,$shadow_y,$Anchor_x,$Anchor_y) = $xoopsDB->fetchRow( $result );
	if(!$ext)return '';
	if($op==1){
		$ret='';
		$ret.="$icon_url/$icd.$ext";
		return "<img src='$ret' />";
	}else{
		$ret='';
		$ret.="$icon_url/$icd.$ext,$x,$y,";
		$ret.= !$shadow_ext ? ",,," : "$icon_url/$icd"."_s.".$shadow_ext.",$shadow_x,$shadow_y,";
		$ret.="$Anchor_x,$Anchor_y";
		return "gn_ic='$ret';";
	}
}

function gnavi_addinfo_reg($str){
	$str=trim($str);
	if( XOOPS_USE_MULTIBYTES ) {
		$str = mbereg_replace(_MD_GNAV_MB_GT,">",$str);
		$str = mbereg_replace(_MD_GNAV_MB_LT,"<",$str);
	}

	$strarray=explode("<",$str);

	$ret="";
	foreach($strarray as $item){
		$itemarray=explode(">",$item);
		if(count($itemarray)>1){
//			$ret.="< ".trim($itemarray[0])." > ".trim($itemarray[1]).PHP_EOL ;
			$ret.="< ".trim($itemarray[0])." > ".trim($itemarray[1])."\r\n" ;
		}
	}
	return $ret;
}

function gnavi_addinfo_array($str,$myts){
	$str=gnavi_addinfo_reg($str);
	$strarray=explode("<",$str);

	$foo = array();
	$i=0;
	foreach($strarray as $item){
		$itemarray=explode(">",$item);
		if(count($itemarray)>1){
			$foo[$i]=array('title'=> $myts->makeTboxData4Show(trim($itemarray[0])) ,
			               'desc' => $myts->displayTarea(trim($itemarray[1]),0,1,1,1,1,1)) ;
			$i++;
		}
	}

	return $foo;
}

function gnavi_check_folders(){

	global $photos_dir,$mydirname,$gnavi_makethumb,$thumbs_dir,$qrimg_dir,$gnavi_mobile_useqr;

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

	// check or make thumbs_dir
	if( $gnavi_makethumb && ! is_dir( $thumbs_dir ) ) {
		if( $safe_mode_flag ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",10,"At first create & chmod 777 '$thumbs_dir' by ftp or shell.");
			exit ;
		}

		$rs = mkdir( $thumbs_dir , 0777 ) ;
		if( ! $rs ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",10,"$thumbs_dir is not a directory");
			exit ;
		} else @chmod( $thumbs_dir , 0777 ) ;
	}

	// check or make qr_dir
	if( $gnavi_mobile_useqr && ! is_dir( $qrimg_dir ) ) {
		if( $safe_mode_flag ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",10,"At first create & chmod 777 '$qrimg_dir' by ftp or shell.");
			exit ;
		}

		$rs = mkdir( $qrimg_dir , 0777 ) ;
		if( ! $rs ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",10,"$qrimg_dir is not a directory");
			exit ;
		} else @chmod( $qrimg_dir , 0777 ) ;
	}

	// check or set permissions of photos_dir
	if( ! is_writable( $photos_dir ) || ! is_readable( $photos_dir ) ) {
		$rs = chmod( $photos_dir , 0777 ) ;
		if( ! $rs ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",5,"chmod 0777 into $photos_dir failed");
			exit ;
		}
	}

	// check or set permissions of thumbs_dir
	if( $gnavi_makethumb && ! is_writable( $thumbs_dir ) ) {
		$rs = chmod( $thumbs_dir , 0777 ) ;
		if( ! $rs ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",5,"chmod 0777 into $thumbs_dir failed");
			exit ;
		}
	}

	// check or set permissions of qrimg_dir
	if( $gnavi_mobile_useqr && ! is_writable( $qrimg_dir ) ) {
		$rs = chmod( $qrimg_dir , 0777 ) ;
		if( ! $rs ) {
			redirect_header(XOOPS_URL."/modules/$mydirname/",5,"chmod 0777 into $qrimg_dir failed");
			exit ;
		}
	}

}
function gnavi_add_breadcrumbs($sel_id, $url , $arrcrumbs ){

	$arr = gnavi_make_breadcrumbs($sel_id, $url);
	foreach($arr as $a){
		$arrcrumbs[]=$a;
	}
	return $arrcrumbs;
}
function gnavi_make_breadcrumbs($sel_id, $url , $arrcrumbs = array() )
{
	global $table_cat , $mydirname ;
	global $xoopsDB ;

	$sql = "SELECT pid , title FROM $table_cat WHERE cid = $sel_id";
	$result = $xoopsDB->query($sql);
	if ( $xoopsDB->getRowsNum($result) == 0 ) return $arrcrumbs;
	list($pid,$title) = $xoopsDB->fetchRow($result);
	$myts =& MyTextSanitizer::getInstance();
	$title = $myts->makeTboxData4Show($title);

	$urls = $url.(preg_match('/\?/',$url) ? '&' :'?').'cid='.$sel_id;

	$arr = array( 'url' => $urls , 'name' => $title );
	if ( $pid == 0 ) {
		return array($arr) ;
	}
	$arrcrumbs = gnavi_make_breadcrumbs( $pid , $url , $arrcrumbs );

	array_push($arrcrumbs,$arr);

	return $arrcrumbs;
}

function gnavi_mobile_templete_disp($templete){
	global $xoopsTpl,$gnavi_mobile_encording;

	$out_text='';

    if (XOOPS_USE_MULTIBYTES == 1 && $gnavi_mobile_encording && $gnavi_mobile_encording!=_CHARSET) {
        if (function_exists('mb_convert_encoding')) {

			$xoopsTpl->assign('charset',$gnavi_mobile_encording);
			$buffer = $xoopsTpl->fetch($templete);
            $out_text = mb_convert_encoding($buffer, $gnavi_mobile_encording , _CHARSET);
			$charset=$gnavi_mobile_encording;
        }
    }



	if($out_text){
		echo $out_text;
	}else{
		$xoopsTpl->assign('charset',_CHARSET);
		$xoopsTpl->display($templete);
	}


}

// added by XCL2.2 distribution pack
function gnavi_get_submenu( $mydirname ){
	global $xoopsDB , $xoopsUser , $gnavi_catonsubmenu ,$gnavi_usevote, $table_cat ,
				$gnavi_usegooglemap,$gnavi_indexpage ;
	$constpref = '_MI_' . strtoupper( $mydirname ) ;
	$subcount = 1 ;

	static $submenus_cache ;

	include dirname( __FILE__ ) . '/get_perms.php' ;
	if( isset( $gnavi_usegooglemap ) && $gnavi_usegooglemap ) {
		if( isset( $gnavi_indexpage ) && $gnavi_indexpage == 'map' ) {
			$modversion['sub'][$subcount]['name'] = constant($constpref.'_TEXT_SMNAME6');
			$sub[$subcount++]['url'] = "index.php?page=category";
		}else{
			$sub[$subcount]['name'] = constant($constpref.'_TEXT_SMNAME5');
			$sub[$subcount++]['url'] = "index.php?page=map";
		}
	}
	if( isset( $gnavi_catonsubmenu ) && $gnavi_catonsubmenu ) {
		$crs = $xoopsDB->query( "SELECT cid, title FROM $table_cat WHERE pid=0 ORDER BY weight,title") ;
		if( $crs !== false ) {
		    while( list( $cid , $title ) = $xoopsDB->fetchRow( $crs ) ) {
			$sub[$subcount]['name'] = "$title" ;
			$sub[$subcount++]['url'] = "index.php?cid=$cid" ;
		    }
		}
	}
	if( $global_perms & 1 ) {	// GNAV_GPERM_INSERTABLE
		$sub[$subcount]['name'] = constant($constpref.'_TEXT_SMNAME1');
		$sub[$subcount++]['url'] = "index.php?page=submit";
		if($xoopsUser){
			$sub[$subcount]['name'] = constant($constpref.'_TEXT_SMNAME4');
			$sub[$subcount++]['url'] = "index.php?uid=-1";
		}
	}
	$sub[$subcount]['name'] = constant($constpref.'_TEXT_SMNAME2');
	$sub[$subcount++]['url'] = "index.php?page=topten&amp;hit=1";
	if( $global_perms & 256 ) {	// GNAV_GPERM_RATEVIEW
		if( isset( $gnavi_usevote ) && $gnavi_usevote ) {
			$sub[$subcount]['name'] = constant($constpref.'_TEXT_SMNAME3');
			$sub[$subcount++]['url'] = "index.php?page=topten&amp;rate=1";
		}
	}

	$submenus_cache[$mydirname] = $sub ;
	return $submenus_cache[$mydirname] ;

}

?>