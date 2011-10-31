<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                       outPut kml or xml files                             //
// ------------------------------------------------------------------------- //

include dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

// kml must no warning , notice ...
error_reporting(0);

$myts =& MyTextSanitizer::getInstance() ; // MyTextSanitizer object

$num = empty( $_GET['num'] ) ? 0 : intval( $_GET['num'] ) ;
$cid = empty( $_GET['cid'] ) ? 0 : intval( $_GET['cid'] ) ;
$uid = empty( $_GET['uid'] ) ? 0 : intval( $_GET['uid'] ) ;
$lid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;

$mtype = empty( $_GET['mime'] ) ? "" : $myts->stripSlashesGPC( $_GET['mime']) ;
$mtype = $mtype==G_KML || $mtype==G_XML ? $mtype : G_KML;

if (function_exists('mb_http_output')) {
	mb_http_output('pass');
}

header("Cache-Control: public"); 
if($mtype==G_XML){
	header('Content-Type:text/xml; charset=utf-8');
	//header('Content-Disposition: attachment; filename="kml.xml"');
}else{
	header('Content-Type: application/vnd.google-earth.kml+xml');
	header('Content-Disposition: attachment; filename="kml.kml"');
}


$xml = '<?xml version="1.0" encoding="UTF-8"?>';
if($mtype==G_KML)$xml .= '<kml xmlns="http://earth.google.com/kml/2.1">';
$xml .= '<Document>
    <name></name>
    <description></description>';

$prs = $xoopsDB->query( "SELECT ext,shadow_ext,icd,x,y,shadow_x,shadow_y,Anchor_x,Anchor_y,infoWindowAnchor_x,infoWindowAnchor_y FROM $table_icon") ;
while( $rs = $xoopsDB->fetchArray( $prs ) ) {
	extract($rs);
	$iconstyle="style$icd";
	$iconurl="$icon_url/$icd.$ext";
	if($shadow_ext){
			$shadow="$icon_url/".$icd."_s.$ext";
	}else{$shadow='x';}

if($mtype==G_XML){
		//original xml
		$xml .='
	      <IconStyle>
	          <icd>'.$icd.'</icd>
	          <href>'.$iconurl.'</href>
	          <shadow>'.$shadow.'</shadow>
	          <param>'.$x.','.$y.','.$shadow_x.','.$shadow_y.','.$Anchor_x.','.$Anchor_y.','.$infoWindowAnchor_x.','.$infoWindowAnchor_y.'</param>
	      </IconStyle>';
}else{
		//true kml
		$xml .='
	    <Style id="'.$iconstyle.'">
	      <IconStyle>
	        <Icon>
	          <href>'.$iconurl.'</href>
	        </Icon>
	      </IconStyle>
	    </Style>';
}
}

$where='';
$op_link='';
if($cid){
	$op_link="&amp;cid=$cid";
	$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;
	$cids = $cattree->getAllChildId( $cid ) ;
	array_push( $cids , $cid ) ;
	$whr = "";
	foreach( $cids as $cid ) {
		$whr .= "$cid," ;
}
$whr =substr($whr, 0, -1);
$where .=" AND ( l.cid IN($whr) OR l.cid1 IN($whr) OR l.cid2 IN($whr) OR l.cid3 IN($whr) OR l.cid4 IN($whr) ) ";
}
if($uid){
	if( $uid < 0 )$uid=$my_uid ;
	$where .= " AND l.submitter=$uid " ;
}
if($lid){
	$where .= " AND l.lid=$lid " ;
}
	$where .= " AND (l.lat<>0 OR l.lng<>0) " ;

$prs = $xoopsDB->query( "SELECT l.lid,l.lat,l.lng,l.zoom, l.cid,l.cid1,l.cid2,l.cid3,l.cid4, l.title, l.poster_name,l.icd, l.ext, l.res_x, l.res_y, l.ext1, l.res_x1, l.res_y1,l.ext2, l.res_x2, l.res_y2, l.status, l.date, l.hits, l.rating, l.votes, l.comments,l.caption,l.caption1,l.caption2, l.submitter, t.description,t.arrowhtml,t.addinfo,c.title AS cat_title FROM $table_photos l USE INDEX (date) INNER JOIN $table_text t ON l.lid=t.lid LEFT JOIN $table_cat c ON l.cid=c.cid WHERE l.status>0 $where ORDER BY date DESC" , $num ) ;

while( $rs = $xoopsDB->fetchArray( $prs ) ) {
	global $cattree;
	$title = strip_tags($myts->displayTarea($rs['title'], 1 , 1 , 1 , 1 , 1 , 1 ));
	$title = xoops_substr($title,0,35);
	$lid=intval($rs['lid']);
	$desc = '';
	if( !empty($rs['description']) ){
		$desc = xoops_substr(strip_tags($myts->displayTarea( $rs['description'] , 1 , 1 , 1 , 1 , 1 , 1 )),0,128);
	}

	$title = htmlspecialchars ($title);
	$desc = htmlspecialchars ($desc);

	$ext=$rs['ext'];
	if($ext){
		if( in_array( strtolower( $ext ) , $gnavi_normal_exts ) ) {
			$imgsrc_photo = "$thumbs_url/$lid.$ext" ;
			if($gnavi_thumbrule=='w' || ($gnavi_thumbrule=='b' && $rs['res_x'] > $rs['res_y'] )){
				if($rs['res_x']>$gnavi_thumbsize){
					if($rs['res_x']>0)$y=intval($gnavi_thumbsize/$rs['res_x']*$rs['res_y']);
				}else{
					$y=$rs['res_y'];
				}
			}else{
				if($rs['res_y']>$gnavi_thumbsize){
					$y=$gnavi_thumbsize;
				}else{
					$y=$rs['res_y'];
				}
			}
		} else {
			if(file_exists( "$mod_path/icons/$ext.gif" )){
				$imgsrc_photo = "$mod_url/icons/$ext.gif" ;
				$imgs = "icons/$ext.gif" ;
			}else{
				$imgsrc_photo = "$mod_url/icons/all.gif" ;
				$imgs = "icons/all.gif" ;
			}
			$dim = GetImageSize($imgs) ;
			if( $dim ) {$x=$dim[0];$y=$dim[1];}
		}
		$h = $y>0 ? "style='height:".$y."px;margin:5px;'" : "" ;
		$desc = "<div align='center' $h><a href='$mod_url/index.php?lid=$lid$op_link'><img src='$imgsrc_photo' /></a></div>".$desc;
	}

	$desc.="<div style='clear:both;' align='right'><a href='$mod_url/index.php?lid=$lid$op_link'>"._MD_GNAV_NAV_READMORE."</a></div>";

	$lng = floatval($rs['lng']);
	$lat = floatval($rs['lat']);
	$zoom = floatval($rs['zoom']);
	$icon = $rs['icd'] > 0 ? $rs['icd'] : gnavi_get_icon($cattree,$rs['cid'],$rs['cid1'],$rs['cid2'],$rs['cid3'],$rs['cid4'],$cid);
	if($icon>0){
		$iconstyle='
	      <styleUrl>#style'.$icon.'</styleUrl>';
	}else{
		$iconstyle='';
	}

if($mtype==G_XML){
		$desc = $myts->makeTboxData4Show($desc);

		$xml .='
	    <Placemark>
	      <name>'.$title.'</name>
	      <description>
'.$desc.'
	      </description>
	      <lid>'.$lid.'</lid><icd>'.$icon.'</icd>
	      <coordinates>'.$lng.','.$lat.','.$zoom.'</coordinates>
	    </Placemark>';
}else{
		$xml .='
	    <Placemark>
	      <name>'.$title.'</name>
	      <description>
	        <![CDATA[
'.$desc.'
	        ]]>
	      </description>'.$iconstyle.'	      
	      <Point>
	        <coordinates>'.$lng.','.$lat.',0</coordinates>
	      </Point>
	    </Placemark>';
}

}

$xml .= '
</Document>';
if($mtype==G_KML)$xml .= '</kml>';



print xoops_utf8_encode( $xml );
exit(0) ;

?>