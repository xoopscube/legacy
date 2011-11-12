<?php

$gnavi_googlemap_url="http://maps.google.com";
//$gnavi_googlemap_url="http://ditu.google.com"; Chinese GoogleMap

$gnavi_maptypes=array('G_NORMAL_MAP',
					'G_SATELLITE_MAP',
					'G_HYBRID_MAP',
					'G_PHYSICAL_MAP',
					'G_HYBRID_PHYSICAL_MAP',
					'G_MOON_ELEVATION_MAP',
					'G_MOON_VISIBLE_MAP',
					'G_MARS_ELEVATION_MAP',
					'G_MARS_VISIBLE_MAP',
					'G_MARS_INFRARED_MAP',
					'G_SKY_VISIBLE_MAP');

//javascripts language
$gnavi_lang_java  = "here:escape('"._MD_GNAV_JAVA_HERE."')";
$gnavi_lang_java .= ",gmapdisable:escape('"._MD_GNAV_JAVA_GMAPDISABLE."')";
$gnavi_lang_java .= ",setpoint:escape('"._MD_GNAV_JAVA_SETPOINT."')";
$gnavi_lang_java .= ",nodata:escape('"._MD_GNAV_JAVA_NODATA."')";
$gnavi_lang_java .= ",notfound:escape('"._MD_GNAV_JAVA_NOTFOUND."')";
$gnavi_lang_java .= ",additem:escape('"._MD_GNAV_JAVA_ADDITEM."')";
$gnavi_lang_java .= ",addlabel:escape('"._MD_GNAV_JAVA_LABEL."')";
$gnavi_lang_java  = "gn_lg = {".$gnavi_lang_java."};" ;

//map option
$str = '';
$urls = preg_split("/(\r\n|\n|\r)/", $gnavi_include_map);
foreach($urls as $url){
	$str .= (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url ) ? ($str=='' ? '' : ',')."'".trim($url)."'" :'');
}
if($str!=''){
	$gnavi_lang_java .="\n\t"."gn_kmls = new Array(".$str.");";
}
?>