<?php
$mydirname = basename( dirname(dirname( __FILE__ ) ) );

eval( '

function  '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return xpress_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'xpress_global_search_base' ) ) {
	function xpress_global_search_base( $mydirname , $queryarray , $andor , $limit , $offset , $userid ){
		global $xoopsDB, $myts;
		
		require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');

		$myts =& MyTextSanitizer::getInstance();
		
		$xp_prefix = preg_replace('/wordpress/','wp',$mydirname);
		if ($userid) {
			$wp_uid = xoops_uid_to_wp_uid(intval($userid),$mydirname);
		}

		$prefix= XOOPS_DB_PREFIX . '_' . $xp_prefix  ;
		$posts_tables = get_table_list($prefix,'posts');
		$i = 0;
		$ret = array();
		foreach( $posts_tables as $views_table){
			$mid_prefix = get_multi_mid_prefix($prefix,'posts' , $views_table);
			$option_table = $prefix . $mid_prefix . 'options';
			$time_difference = get_blog_option($option_table ,'gmt_offset');
			$blog_url = get_blog_option($option_table , 'siteurl');
			$pattern = '/.*' . $mydirname . '/';
			$mid_url = preg_replace($pattern, '' , $blog_url);
			$mid_url = preg_replace('/\//' , '' , $mid_url);
			if (!empty($mid_url)) $mid_url = $mid_url . '/' ;
			
			$blog_name = get_blog_option($option_table , 'blogname');
			if (empty($mid_url)) $blog_name = ''; else $blog_name = $blog_name . ':: ';
			
			$now = date('Y-m-d H:i:s',(time() + ($time_difference * 3600)));
			$where = "(post_status = 'publish') AND (post_date <= '".$now."') AND (post_type <> 'revision') AND (post_type <> 'nav_menu_item') ";

			if ( is_array($queryarray) && $count = count($queryarray) ) {
				$str_query = array();
				for($j=0;$j<$count;$j++){
					$str_query[] = "(post_title LIKE '%".$queryarray[$j]."%' OR post_content LIKE '%".$queryarray[$j]."%')";
				}
				$where .= " AND ".implode(" $andor ", $str_query);
			}
			if ($userid) {
				if ($wp_uid){
					$where  .= " AND (post_author=".$wp_uid.")";
				} else {
					$where  .= " AND 0 ";
				}
			}

			$request = "SELECT * FROM " . $views_table ." WHERE ".$where;
			$request .= " ORDER BY post_date DESC";
			$result = $xoopsDB->query($request,$limit,$offset);
			while($myrow = $xoopsDB->fetchArray($result)){
				if ($myrow['post_type'] !=='revision' && $myrow['post_type'] !=='nav_menu_item')
				switch ($myrow['post_type']) {
				case 'page':
					$ret[$i]['link'] = $mid_url . '?page_id=' . $myrow['ID'];
					break;
				case 'post':
				case '':
					$ret[$i]['link'] = $mid_url . '?p=' . $myrow['ID'];
					break;
				default:
					$ret[$i]['link'] = $mid_url . '?'.$myrow['post_type'].'=' .$myrow['post_name'];
				}
				$ret[$i]['title'] = $blog_name . $myts->htmlSpecialChars($myrow['post_title']);
				$date_str = $myrow['post_date'];
				$yyyy = substr($date_str,0,4);
				$mm   = substr($date_str,5,2);
				$dd   = substr($date_str,8,2);
				$hh   = substr($date_str,11,2);
				$nn   = substr($date_str,14,2);
				$ss   = substr($date_str,17,2);
				$ret[$i]['time'] = mktime( $hh,$nn,$ss,$mm,$dd,$yyyy);
				$ret[$i]['uid'] = wp_uid_to_xoops_uid($myrow['post_author'],$mydirname);

				$context = '' ;
				$text =$myrow['post_content'];
				// get context for module "search"
				$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1 ;
				if( function_exists( 'search_make_context' ) && $showcontext ) {
					if( function_exists( 'easiestml' ) ) $text = easiestml( $text ) ;
					$full_context = strip_tags($text) ;
					$context = search_make_context( $full_context , $queryarray ) ;
				}
				$ret[$i]['context']=$context;
				$i++;
			}
		}
		return $ret;

	}
}

if( ! function_exists( 'get_blog_option' ) ) {
	function get_blog_option($option_table,$option_name){
		$xoopsDB =& Database::getInstance();

		$sql = "SELECT option_value FROM $option_table WHERE option_name = '" . $option_name . "'";
		
		$result =  $xoopsDB->query($sql, 0, 0);
		if ($xoopsDB->getRowsNum($result)  > 0){
			$row = $xoopsDB->fetchArray($result);
			return $row['option_value'];
		}
		return 0;
	}
}

?>