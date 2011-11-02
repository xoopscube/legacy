<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '

function '.$mydirname.'_new($limit=0, $offset=0){
	return _xpress_new("'.$mydirname.'" ,$limit, $offset ) ;
}

function '.$mydirname.'_num(){
	return _xpress_num("'.$mydirname.'") ;
}

function '.$mydirname.'_data($limit=0, $offset=0){
	return _xpress_data("'.$mydirname.'" ,$limit, $offset ) ;
}
' ) ;

if (!function_exists('_xpress_new')) {
//================================================================
// What's New Module
// get aritciles from module
// xpress 0.20 <http://www.toemon.com>
// 2007-07-17 toemon 
//================================================================

// --- function start ---
function _xpress_new($mydirname, $limit=0, $offset=0) 
{
	global $xoopsDB;

	$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
	include(XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/wp-includes/version.php');

	$modules_table = $xoopsDB->prefix('modules');
	$modSQL ="SELECT mid FROM " . $modules_table . " WHERE dirname LIKE '" . $mydirname . "'";
	$modRes = $xoopsDB->query($modSQL, 0, 0);
	$modRow = $xoopsDB->fetchArray($modRes);
	$module_id = $modRow['mid'];

	$table_config = $xoopsDB->prefix('config');
	$confSQL ="SELECT conf_value FROM " . $table_config . " WHERE (conf_modid = " . $module_id . ") AND (conf_name LIKE 'whatsnew_use_mod_date')";
	$confRes = $xoopsDB->query($confSQL, 0, 0);
	$confRow = $xoopsDB->fetchArray($confRes);
	$use_modified_date = $confRow['conf_value'];

	$url_mod = XOOPS_URL."/modules/".$mydirname;
	
	require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
	$prefix = $xoopsDB->prefix($wp_prefix);
	$options_tables = get_table_list($prefix,'options');
	$table_count = count($options_tables);
	$sql1 = '';
	foreach( $options_tables as $options_table){
		$blog_url = get_blog_option($options_table , 'siteurl');
		$blog_url = preg_replace('/\/$/', '' ,$blog_url);
		$blogname = get_blog_option($options_table , 'blogname');
		
		$table_prefix = get_multi_prefix($options_table,'options');
		
		$table_posts      = $table_prefix . "posts";
		
		$sub_sql  = "SELECT ID, post_author, post_title, post_content, post_type, comment_count, post_date, UNIX_TIMESTAMP(post_date) AS unix_post_date, UNIX_TIMESTAMP(post_modified) AS unix_post_modified, post_status, '$blog_url' AS blog_url, '$table_prefix' AS table_prefix, '$blogname' AS blogname ";
		$sub_sql .= " FROM ".$table_posts;
		$sub_sql .= " WHERE (post_status='publish') AND (UNIX_TIMESTAMP(post_date) <= UNIX_TIMESTAMP()) ";

		if ($table_count > 1){
			$sub_sql = '(' . $sub_sql . ')';
			if (!empty($sql1)) $sql1 = $sql1 . ' UNION ';
			$sql1 = $sql1 . $sub_sql;
		} else {
			$sql1 =  $sub_sql;
		}
	}
	$sql1 .= " ORDER BY post_date DESC LIMIT $offset,$limit";

	$res1 = $xoopsDB->queryF($sql1);

	$i = 0;
	$ret = array();

	while($row1 = $xoopsDB->fetchArray($res1))
	{
		$id = $row1['ID'];
		$blog_url = $row1['blog_url'];
		$blogname = $row1['blogname'];
		$table_views   =$modules_table = $xoopsDB->prefix($wp_prefix) . "_views";
		$table_term_relationships = $row1['table_prefix'] . "term_relationships";
		$table_term_taxonomy = $row1['table_prefix'] . "term_taxonomy";
		$table_terms = $row1['table_prefix'] . "terms";
		$table_categories = $row1['table_prefix'] . "categories";
		$table_post2cat   = $row1['table_prefix'] . "post2cat";

		if ($table_count <= 1){
			if ($wp_db_version < 6124){
				$sql2 = "SELECT c.cat_ID, c.cat_name FROM ".$table_categories." c, ".$table_post2cat." p2c WHERE c.cat_ID = p2c.category_id AND p2c.post_id=".$id;
			} else {
				$sql2  = "SELECT $table_term_relationships.object_id, $table_terms.term_id AS cat_ID, $table_terms.name AS cat_name ";
				$sql2 .= "FROM $table_term_relationships INNER JOIN ($table_term_taxonomy INNER JOIN $table_terms ON $table_term_taxonomy.term_id = $table_terms.term_id) ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id ";
				$sql2 .= "WHERE ($table_term_relationships.object_id =" . $id.") AND ($table_term_taxonomy.taxonomy='category')";		
			}
			$row2 = $xoopsDB->fetchArray( $xoopsDB->query($sql2) );
			$ret[$i]['cat_link'] = $blog_url."/index.php?cat=".$row2['cat_ID'];
			$ret[$i]['cat_name'] = $row2['cat_name'];
		} else {
			$ret[$i]['cat_link'] = $blog_url;
			$ret[$i]['cat_name'] = $blogname;
		}

		if ($row1['post_type'] == 'page'){
				$ret[$i]['link']     = $blog_url."/?page_id=".$id;
		} else {
			$ret[$i]['link']     = $blog_url."/index.php?p=".$id;
		}

		$ret[$i]['title']    = $row1['post_title'];

		$ret[$i]['uid'] = wp_uid_to_xoops_uid($row1['post_author'],$mydirname);
		$ret[$i]['replies'] = $row1['comment_count'];


		if(empty($use_modified_date)) {
			$time = $row1['unix_post_date'];
		} else {

			if ($row1['unix_post_modified'] > $row1['unix_post_date']){
				$time = $row1['unix_post_modified'];
			} else {
				$time = $row1['unix_post_date'];
			}
		}

	   	$ret[$i]['time']     = $time;
		$ret[$i]['modified'] = $time;
		$ret[$i]['issued']   = $row1['unix_post_date'];
		$content=$row1['post_content'];
		$content = strip_tags($content);

		$ret[$i]['description'] = $content;

		$sql3 = "SELECT post_views FROM  " .  $table_views . " WHERE post_id = " . $id;
		$row3 = $xoopsDB->fetchArray( $xoopsDB->query($sql3) );
	   	$ret[$i]['hits']     = $row3['post_views'];



		$i++;
	}

	return $ret;
}

function _xpress_num($mydirname) 
{
	// get $mydirnumber
	if( ! preg_match( '/^(\D+)(\d*)$/' , $mydirname , $regs ) ) echo ( "invalid dirname: " . htmlspecialchars( $mydirname ) ) ;

	global $xoopsDB;
	$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
	$prefix = $xoopsDB->prefix($wp_prefix);
	$options_tables = get_table_list($prefix,'options');
	$table_count = count($options_tables);
	$sql = '';
	foreach( $options_tables as $options_table){
		$blog_url = get_blog_option($options_table , 'siteurl');
		$blog_url = preg_replace('/\/$/', '' ,$blog_url);
		$blogname = get_blog_option($options_table , 'blogname');
		
		$table_prefix = get_multi_prefix($options_table,'options');
		$table_posts      = $table_prefix . "posts";

		$sub_sql  = "SELECT ID, post_author, post_title, post_content, post_type, comment_count, post_date, UNIX_TIMESTAMP(post_date) AS unix_post_date, UNIX_TIMESTAMP(post_modified) AS unix_post_modified, post_status, '$blog_url' AS blog_url, '$table_prefix' AS table_prefix, '$blogname' AS blogname ";
		$sub_sql .= " FROM ".$table_posts;
		$sub_sql .= " WHERE (post_status='publish') AND (UNIX_TIMESTAMP(post_date) <= UNIX_TIMESTAMP()) ";

		if ($table_count > 1){
			$sub_sql = '(' . $sub_sql . ')';
			if (!empty($sql)) $sql = $sql . ' UNION ';
			$sql = $sql . $sub_sql;
		} else {
			$sql =  $sub_sql;
		}
	}

	$array = $xoopsDB->fetchRow( $xoopsDB->queryF($sql) );
	$num = $array[0];
	if (empty($num)) $num = 0;

	return $num;
}

function _xpress_data($mydirname,$limit=0, $offset=0) 
{
	// get $mydirnumber
	if( ! preg_match( '/^(\D+)(\d*)$/' , $mydirname , $regs ) ) echo ( "invalid dirname: " . htmlspecialchars( $mydirname ) ) ;

	global $xoopsDB;
	$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	
	require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
	$prefix = $xoopsDB->prefix($wp_prefix);
	$options_tables = get_table_list($prefix,'options');
	$table_count = count($options_tables);
	$sql = '';
	foreach( $options_tables as $options_table){
		$blog_url = get_blog_option($options_table , 'siteurl');
		$blog_url = preg_replace('/\/$/', '' ,$blog_url);
		$blogname = get_blog_option($options_table , 'blogname');
		
		$table_prefix = get_multi_prefix($options_table,'options');
		$table_posts      = $table_prefix . "posts";

		$sub_sql  = "SELECT ID, post_author, post_title, post_content, post_type, comment_count, post_date, UNIX_TIMESTAMP(post_date) AS unix_post_date, UNIX_TIMESTAMP(post_modified) AS unix_post_modified, post_status, '$blog_url' AS blog_url, '$table_prefix' AS table_prefix, '$blogname' AS blogname ";
		$sub_sql .= " FROM ".$table_posts;
		$sub_sql .= " WHERE (post_status='publish') AND (UNIX_TIMESTAMP(post_date) <= UNIX_TIMESTAMP()) ";

		if ($table_count > 1){
			$sub_sql = '(' . $sub_sql . ')';
			if (!empty($sql)) $sql = $sql . ' UNION ';
			$sql = $sql . $sub_sql;
		} else {
			$sql =  $sub_sql;
		}
	}

	$result = $xoopsDB->queryF($sql,$limit,$offset);

	$i = 0;
	$ret = array();

	while($row1 = $xoopsDB->fetchArray($result))
	{
		$id = $row1['ID'];
		$blog_url = $row1['blog_url'];
		$blogname = $row1['blogname'];
		$ret[$i]['id'] = $id;
		$ret[$i]['link'] = $blog_url . "/index.php?p=".$id;
		$ret[$i]['title'] = $row1['post_title'];
		$ret[$i]['time']  = $row1['unix_post_date'];
		$i++;
	}

	return $ret;

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


// --- function end ---

}

?>
