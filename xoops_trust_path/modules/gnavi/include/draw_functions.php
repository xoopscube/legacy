<?php

// for older files
function gnavi_header()
{
	global $mod_url , $mydirname ;

	$tpl = new XoopsTpl() ;
	$tpl->assign( array( 'mod_url' => $mod_url ) ) ;
	$tpl->display( "db:{$mydirname}_header.html" ) ;
}


// for older files
function gnavi_footer()
{
	global $mod_copyright , $mydirname ;

	$tpl = new XoopsTpl() ;
	$tpl->assign( array( 'mod_copyright' => $mod_copyright ) ) ;
	$tpl->display( "db:{$mydirname}_footer.html" ) ;
}


// returns appropriate name from uid
function gnavi_get_name_from_uid( $uid )
{
	global $gnavi_nameoruname ;

	if( $uid > 0 ) {
		$member_handler =& xoops_gethandler( 'member' ) ;
		$poster =& $member_handler->getUser( $uid ) ;

		if( is_object( $poster ) ) {
			if( $gnavi_nameoruname == 'uname' || trim( $poster->name() ) == '' ) {
				$name = htmlspecialchars( $poster->uname() , ENT_QUOTES ) ;
			} else {
				$name = htmlspecialchars( $poster->name() , ENT_QUOTES ) ;
			}
		} else {
			$name = _GNAV_CAPTION_GUESTNAME ;
		}

	} else {
		$name = _GNAV_CAPTION_GUESTNAME ;
	}

	return $name ;
}

// returns appropriate name from uid
function gnavi_check_name_from_uid( $uid , $poster_name )
{
	global $gnavi_nameoruname ;

	if( $uid > 0 ) {
		$member_handler =& xoops_gethandler( 'member' ) ;
		$poster =& $member_handler->getUser( $uid ) ;

		if( $poster_name == htmlspecialchars( $poster->uname() , ENT_QUOTES )||
			$poster_name == htmlspecialchars( $poster->name() , ENT_QUOTES )){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}



// Get photo's array to assign into template (heavy version)
function gnavi_photo_assign($photo)
{

	global $gnavi_middlepixel,$gnavi_liquidimg,$gnavi_normal_exts;
	// Middle size calculation
	$photo['width_height'] = '' ;
	$photo['width_height1'] = '' ;
	$photo['width_height2'] = '' ;

	$photo['flawidth'] =  $photo['res_x'] ;
	$photo['flaheight'] =  $photo['res_y'] ;
	$photo['flawidth1'] =  $photo['res_x1'] ;
	$photo['flaheight1'] =  $photo['res_y1'] ;
	$photo['flawidth2'] =  $photo['res_x2'] ;
	$photo['flaheight2'] =  $photo['res_y2'] ;

	list( $max_w , $max_h ) = explode( 'x' , $gnavi_middlepixel ) ;

	//summary contents width  ( 4(margin)+ 1(border)+2(padding) ) * 2 =14 
	$cwidth=14;
	$maxw=$max_w;
	$cpic=0;
	if($gnavi_liquidimg){
		//allimage
		if($photo['ext'])$cpic+=1;
		if($photo['ext1'])$cpic+=1;
		if($photo['ext2'])$cpic+=1;
		if($cpic>1){
			$max_w=intval(($max_w+$cwidth-$cwidth*$cpic)/$cpic);
		}
	}

	//set caption width min
	$min_captionw= intval(($maxw+$cwidth-$cwidth*3)/3) ;


	$photo['img']  = 0;
	$photo['img1'] = 0;
	$photo['img2'] = 0;

	if( ! empty( $max_w ) && ! empty( $photo['res_x'] ) ) {
		if( empty( $max_h ) ) $max_h = $max_w ;
		if( $max_h / $max_w > $photo['res_y'] / $photo['res_x'] ) {
			if( $photo['res_x'] > $max_w ) {
				$photo['width_height'] = "width='$max_w'" ;
				$photo['flawidth'] = $max_w ;
				$photo['flaheight'] = round($photo['res_y'] / $photo['res_x'] * $max_w) ;
				$photo['img'] = 1;
			}else{
				$photo['img'] = 2;
			}
		} else {
			if( $photo['res_y'] > $max_h ){
				$photo['width_height'] = "height='$max_h'" ;
				$photo['flaheight'] = $max_h ;
				$photo['flawidth'] = round($photo['res_x'] / $photo['res_y'] * $max_h) ;
				$photo['img'] = 1;
			}else{
				$photo['img'] = 2;
			}
		}
	}

	if( ! empty( $max_w ) && ! empty( $photo['res_x1'] ) ) {
		if( empty( $max_h ) ) $max_h = $max_w ;
		if( $max_h / $max_w > $photo['res_y1'] / $photo['res_x1'] ) {
			if( $photo['res_x1'] > $max_w ){
				$photo['width_height1'] = "width='$max_w'" ;
				$photo['flawidth1'] = $max_w ;
				$photo['flaheight1'] = round($photo['res_y1'] / $photo['res_x1'] * $max_w) ;
				$photo['img1'] = 1;
			}else{
				$photo['img1'] = 2;
			}
		} else {
			if( $photo['res_y1'] > $max_h ){
				$photo['width_height1'] = "height='$max_h'" ;
				$photo['flaheight1'] = $max_h ;
				$photo['flawidth1'] = round($photo['res_x1'] / $photo['res_y1'] * $max_h) ;
				$photo['img1'] = 1;
			}else{
				$photo['img1'] = 2;
			}
		}
	}

	if( ! empty( $max_w ) && ! empty( $photo['res_x2'] ) ) {
		if( empty( $max_h ) ) $max_h = $max_w ;
		if( $max_h / $max_w > $photo['res_y2'] / $photo['res_x2'] ) {
			if( $photo['res_x2'] > $max_w ) {
				$photo['width_height2'] = "width='$max_w'" ;
				$photo['flawidth2'] = $max_w ;
				$photo['flaheight2'] = round($photo['res_y2'] / $photo['res_x2'] * $max_w) ;
				$photo['img2'] = 1;
			}else{
				$photo['img2'] = 2;
			}
		} else {
			if( $photo['res_y2'] > $max_h ){
				$photo['width_height2'] = "height='$max_h'" ;
				$photo['flaheight2'] = $max_h ;
				$photo['flawidth2'] = round($photo['res_x2'] / $photo['res_y2'] * $max_h) ;
				$photo['img2'] = 1;
			}else{
				$photo['img2'] = 2;
			}
		}
	}

	$photo['captionstyle']= "style='width:".( $min_captionw > $photo['flawidth'] ? $min_captionw : $photo['flawidth'] )."px;'" ;
	$photo['captionstyle1']= "style='width:".( $min_captionw > $photo['flawidth1'] ? $min_captionw : $photo['flawidth1'] )."px;'" ;
	$photo['captionstyle2']= "style='width:".( $min_captionw > $photo['flawidth2'] ? $min_captionw : $photo['flawidth2'] )."px;'" ;


	return $photo;

}

function gnavi_get_array_for_photo_assign( $fetched_result_array , $summary = false )
{
	global $my_uid , $isadmin , $global_perms ;
	global $photos_url , $thumbs_url , $thumbs_dir , $mod_url , $mod_path ;
	global $gnavi_makethumb , $gnavi_thumbsize , $gnavi_popular , $gnavi_newdays , $gnavi_normal_exts ,$gnavi_gmap_exts;

	include_once dirname(dirname(__FILE__)).'/class/gnavi.textsanitizer.php' ;

	$myts =& GNaviTextSanitizer::getInstance() ;

	extract( $fetched_result_array ) ;

	list($imgsrc_photo ,$ahref_photo ,$imgsrc_thumb ,$ahref_thumb,$is_normal_image ) = gnavi_get_img_urls("$lid.$ext");
	list($imgsrc_photo1,$ahref_photo1,$imgsrc_thumb1,$ahref_thumb1,$is_normal_image1) = gnavi_get_img_urls($lid."_1.".$ext1);
	list($imgsrc_photo2,$ahref_photo2,$imgsrc_thumb2,$ahref_thumb2,$is_normal_image2) = gnavi_get_img_urls($lid."_2.".$ext2);

	$arrow_html = $arrowhtml ? 1 : 0 ;
	$arrow_br =  $arrowhtml ? 0 : 1 ;

	$addinfo_array = gnavi_addinfo_array($addinfo,$myts);

	// Voting stats
	if( $rating > 0 ) {
		if( $votes == 1 ) {
			$votestring = _MD_GNAV_RAT_ONEVOTE ;
		} else {
			$votestring = sprintf( _MD_GNAV_RAT_NUMVOTES , $votes ) ;
		}
		$info_votes = number_format( $rating , 2 )." ($votestring)";
	} else {
		$info_votes = '0.00 ('.sprintf( _MD_GNAV_RAT_NUMVOTES , 0 ) . ')' ;
	}

	// Submitter's name

	if ($submitter>0){
		$submitter_name = gnavi_get_name_from_uid( $submitter );
	}else{
		$submitter_name = $poster_name;
	}

	// Category's title
	$cat_title = empty( $cat_title ) ? '' : $cat_title ;

	// Summarize description
	if( $summary ) $description = $myts->extractSummary( $description ) ;
	
	//kml lists
	$mykmls='';
	if(in_array($ext,$gnavi_gmap_exts)){
		if($mykmls)$mykmls.=',';
		$mykmls.="'".$photos_url."/".$lid.".".$ext."'";
	}
	if(in_array($ext1,$gnavi_gmap_exts)){
		if($mykmls)$mykmls.=',';
		$mykmls.="'".$photos_url."/".$lid."_1.".$ext1."'";
	}
	if(in_array($ext2,$gnavi_gmap_exts)){
		if($mykmls)$mykmls.=',';
		$mykmls.="'".$photos_url."/".$lid."_2.".$ext2."'";
	}



	return array(
		'lid' => $lid ,
		'mycat' => gnavi_get_mycat($cid,$cid1,$cid2,$cid3,$cid4) ,
		'cid' => $cid ,
		'cid1' => $cid1 ,
		'cid2' => $cid2 ,
		'cid3' => $cid3 ,
		'cid4' => $cid4 ,
		'icd' => $icd ,
		'ext' => $ext ,
		'ext1' => $ext1 ,
		'ext2' => $ext2 ,
		'mykmls' => $mykmls ,
		'res_x' => $res_x ,
		'res_y' => $res_y ,
		'window_x' => $res_x + 16 ,
		'window_y' => $res_y + 16 ,
		'res_x1' => $res_x1 ,
		'res_y1' => $res_y1 ,
		'window_x1' => $res_x1 + 16 ,
		'window_y1' => $res_y1 + 16 ,
		'res_x2' => $res_x2 ,
		'res_y2' => $res_y2 ,
		'window_x2' => $res_x2 + 16 ,
		'window_y2' => $res_y2 + 16 ,
		'title' => $myts->makeTboxData4Show( $title ) ,
		'caption' => $myts->makeTboxData4Show( $caption ) ,
		'caption1' => $myts->makeTboxData4Show( $caption1 ) ,
		'caption2' => $myts->makeTboxData4Show( $caption2 ) ,
		'datetime' => formatTimestamp( $date , 'm' ) ,
		'description' => $myts->displayTarea( $description , $arrow_html , 1 , 1 , 1 , $arrow_br , 1 ) ,
		'sdescription' => xoops_substr(strip_tags($myts->displayTarea( $description , $arrow_html , 1 , 1 , 1 , 1 , 1 )),0,512) ,
		'addinfo'=> $addinfo_array ,
		'ahref_thumb' => $ahref_thumb ,
		'ahref_thumb1' => $ahref_thumb1 ,
		'ahref_thumb2' => $ahref_thumb2 ,
		'imgsrc_thumb' => $imgsrc_thumb ,
		'imgsrc_thumb1' => $imgsrc_thumb1 ,
		'imgsrc_thumb2' => $imgsrc_thumb2 ,
		'imgsrc_photo' => $imgsrc_photo ,
		'imgsrc_photo1' => $imgsrc_photo1 ,
		'imgsrc_photo2' => $imgsrc_photo2 ,
		'ahref_photo' => $ahref_photo ,
		'ahref_photo1' => $ahref_photo1 ,
		'ahref_photo2' => $ahref_photo2 ,
		'can_edit' => ( ( $global_perms & GNAV_GPERM_EDITABLE ) && ( $my_uid == $submitter || $isadmin ) ) ,
		'submitter' => $submitter ,
		'submitter_name' => $myts->makeTboxData4Show($submitter_name) ,
		'poster_name' => $myts->makeTboxData4Show($poster_name) ,
		'hits' => $hits ,
		'status' => $status ,
		'rating' => $rating ,
		'rank' => floor( $rating - 0.001 ) ,
		'votes' => $votes ,
		'info_votes' => $info_votes ,
		'comments' => $comments ,
		'lat' => $lat ,
		'lng' => $lng ,
		'zoom' => $zoom ,
		'mtype' => $myts->makeTboxData4Show($mtype) ,
		'url' => $myts->makeTboxData4Show($url) ,
		'rss' => $myts->makeTboxData4Show($rss) ,
		'tel' => $myts->makeTboxData4Show($tel) ,
		'fax' => $myts->makeTboxData4Show($fax) ,
		'zip' => $myts->makeTboxData4Show($zip) ,
		'address' => $myts->makeTboxData4Show($address) ,
		'is_normal_image'=>$is_normal_image,
		'is_newphoto' => ( $date > time() - 86400 * $gnavi_newdays && $status == 1 ) , 
		'is_updatedphoto' => ( $date > time() - 86400 * $gnavi_newdays && $status == 2 ) , 
		'is_popularphoto' => ( $hits >= $gnavi_popular ) 
	) ;
}




// get list of sub categories in header space
function gnavi_get_sub_categories( $parent_id , $cattree ,$where="")
{
	global $xoopsDB , $table_cat ;

	$myts =& MyTextSanitizer::getInstance() ;

	$ret = array() ;

	$crs = $xoopsDB->query( "SELECT cid, title, imgurl,description FROM $table_cat WHERE pid=$parent_id ORDER BY weight,title") or die( "Error: Get Category." ) ;

	while( list( $cid , $title , $imgurl,$description ) = $xoopsDB->fetchRow( $crs ) ) {

		// Show first child of this category
		$subcat = array() ;
		$arr = $cattree->getFirstChild( $cid , "weight" ) ;
		foreach( $arr as $child ) {
			$subcat[] = array(
				'cid' => $child['cid'] ,
				'description' => $child['description'] ,
				'title' => $myts->makeTboxData4Show( $child['title'] ) ,
				'photo_small_sum' => gnavi_get_photo_small_sum_from_cat( $child['cid'] , "status>0 ".$where ) ,
				'number_of_subcat' => sizeof( $cattree->getFirstChildId( $child['cid'] ) )
			) ;
		}

		// Category's banner default
		if( $imgurl == "http://" ) $imgurl = '' ;

		// Total sum of photos
		$cids = $cattree->getAllChildId( $cid ) ;
		array_push( $cids , $cid ) ;
		$photo_total_sum = gnavi_get_photo_total_sum_from_cats( $cids , "status>0 ".$where ) ;

		$ret[] = array(
			'cid' => $cid ,
			'description' => $description,
			'imgurl' => $myts->makeTboxData4Edit( $imgurl ) ,
			'photo_small_sum' => gnavi_get_photo_small_sum_from_cat( $cid , "status>0 ".$where ) ,
			'photo_total_sum' => $photo_total_sum ,
			'title' => $myts->makeTboxData4Show( $title ) ,
			'subcategories' => $subcat
		) ;
	}

	return $ret ;
}


// for older files
function gnavi_get_mycat($cid,$cid1,$cid2,$cid3,$cid4){
	global $xoopsDB;
	global $table_cat;
	$ret='';
	$myts =& MyTextSanitizer::getInstance() ;
	
	$where='';
	if($cid){
		if($where!='')$where.=' OR ';
		$where .= "cid = ".$cid;
	}
	if($cid1){
		if($where!='')$where.=' OR ';
		$where .= "cid = ".$cid1;
	}
	if($cid2){
		if($where!='')$where.=' OR ';
		$where .= "cid = ".$cid2;
	}
	if($cid3){
		if($where!='')$where.=' OR ';
		$where .= "cid = ".$cid3;
	}
	if($cid4){
		if($where!='')$where.=' OR ';
		$where .= "cid = ".$cid4;
	}
	if($where=='')return '';

	$sql="SELECT cid,title FROM $table_cat WHERE $where ORDER BY pid,weight";
	$crs = $xoopsDB->query($sql) ;
	while(list($cid,$title) = $xoopsDB->fetchRow( $crs )){
		$ret.="&nbsp;<a href='index.php?cid=$cid' >".$myts->makeTboxData4Show( $title )."</a>&nbsp;/";
	} 
	if($ret!='')$ret =substr($ret, 0, -1);
	return $ret ;
}


// get attributes of <img> for preview image
function gnavi_get_img_attribs_for_preview($photo, $preview_name,$preview_name1,$preview_name2)
{
	global $photos_url , $mod_url , $mod_path , $gnavi_normal_exts , $gnavi_thumbsize,$photos_dir ;

	$photo['res_x']=0;
	$photo['res_y']=0;
	$photo['res_x1']=0;
	$photo['res_y1']=0;
	$photo['res_x2']=0;
	$photo['res_y2']=0;
	$photo['window_x']=0;
	$photo['window_y']=0;
	$photo['window_x1']=0;
	$photo['window_y1']=0;
	$photo['window_x2']=0;
	$photo['window_y2']=0;


	$photo['ext'] = substr( strrchr( $preview_name , '.' ) , 1 ) ;
	if( in_array( strtolower( $photo['ext'] ) , $gnavi_normal_exts ) ) {
		$dim = GetImageSize( "$photos_dir/$preview_name" ) ;
		if( $dim ) {$photo['res_x']=$dim[0];$photo['res_y']=$dim[1];}
		$photo['window_x']=$photo['res_x']+16;
		$photo['window_y']=$photo['res_y']+16;
	}
	$photo['ext1'] = substr( strrchr( $preview_name1 , '.' ) , 1 ) ;
	if( in_array( strtolower( $photo['ext1'] ) , $gnavi_normal_exts ) ) {
		$dim = GetImageSize( "$photos_dir/$preview_name1" ) ;
		if( $dim ) {$photo['res_x1']=$dim[0];$photo['res_y1']=$dim[1];}
		$photo['window_x1']=$photo['res_x1']+16;
		$photo['window_y1']=$photo['res_y1']+16;
	}
	$photo['ext2'] = substr( strrchr( $preview_name2 , '.' ) , 1 ) ;
	if( in_array( strtolower( $photo['ext2'] ) , $gnavi_normal_exts ) ) {
		$dim = GetImageSize( "$photos_dir/$preview_name2" ) ;
		if( $dim ) {$photo['res_x2']=$dim[0];$photo['res_y2']=$dim[1];}
		$photo['window_x2']=$photo['res_x2']+16;
		$photo['window_y2']=$photo['res_y2']+16;
	}

	list($photo['imgsrc_photo'],$photo['ahref_photo']) = gnavi_get_img_urls($preview_name);
	list($photo['imgsrc_photo1'],$photo['ahref_photo1']) = gnavi_get_img_urls($preview_name1);
	list($photo['imgsrc_photo2'],$photo['ahref_photo2']) = gnavi_get_img_urls($preview_name2);

	return $photo;

}

function gnavi_get_img_urls($file_name){

	global $gnavi_normal_exts,$mod_url,$photos_url,$mod_path,$thumbs_url,$gnavi_makethumb;
	$ext = substr( strrchr( $file_name , '.' ) , 1 ) ;
	if ($ext){
		if( in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
			$is_normal_image=1;
			$imgsrc_photo = "$photos_url/$file_name" ;
			if($gnavi_makethumb){
				$imgsrc_thumb = "$thumbs_url/$file_name" ;
			}else{
				$imgsrc_thumb = $imgsrc_photo ;
			}
		} else {
			$is_normal_image=0;
			if(file_exists( "$mod_path/icons/$ext.gif" )){
				$imgsrc_photo = "$mod_url/icons/$ext.gif" ;
				$imgsrc_thumb = "$mod_url/icons/$ext.gif" ;
			}else{
				$imgsrc_photo = "$mod_url/icons/all.gif" ;
				$imgsrc_thumb = "$mod_url/icons/all.gif" ;
			}
		}
		$ahref_photo = "$photos_url/$file_name" ;
		$ahref_thumb = "$thumbs_url/$file_name" ;
	}else{
		$is_normal_image=0;
		$ahref_thumb ="";
		$imgsrc_thumb = "";
		$imgsrc_photo = "" ;
		$ahref_photo ="" ;
	}
	return array($imgsrc_photo,$ahref_photo,$imgsrc_thumb,$ahref_thumb,$is_normal_image);
}



?>