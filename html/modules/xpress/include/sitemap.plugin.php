<?php
// $Id: xpress.php
// FILE		::	xpress.php
// AUTHOR	::	toemon
//
// WordPress 2.0+

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_sitemap_' . $mydirname . '(){	
	return _sitemap_xpress("'.$mydirname.'");
}
') ;

if(!function_exists('_sitemap_xpress')){
	function _sitemap_xpress($mydirname){
		global $sitemap_configs , $xoopsDB;

		if (!file_exists(XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/wp-includes/version.php')){
			return '';
		}
		include (XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/wp-includes/version.php');
		if ($wp_db_version < 6124) {  // UNDER WP2.3
			    $block = sitemap_get_categoires_map($xoopsDB->prefix("wp_categories"), "cat_ID", "category_parent", "cat_name", "index.php?cat=", "cat_name");
			return $block;
		}
		
		$disp_sub =@$sitemap_configs["show_subcategoris"];
		
		$prefix = preg_replace('/wordpress/','wp',$mydirname);
		$prefix = $xoopsDB->prefix($prefix);
		require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
		$options_tables = get_table_list($prefix,'options');
		
		$index = 0;
		$blogs =array();
		foreach( $options_tables as $options_table){
			$blog_url = get_blog_option($options_table , 'siteurl');
			$blog_sub_url = preg_replace('/.*\/' . $mydirname . '/' , '' , $blog_url);
			$blog_sub_url = preg_replace('/\//' , '' , $blog_sub_url);
			if (!empty($blog_sub_url)) {
				$blog_sub_url = $blog_sub_url . '/';
			}
			$blog_name = get_blog_option($options_table , 'blogname');
			$db_prefix = get_multi_prefix($options_table , 'options');

			$data = array(
				'blog_name' => $blog_name ,
				'blog_sub_url' => $blog_sub_url ,
				'term_taxonomy' => $db_prefix. 'term_taxonomy' ,
				'terms' => $db_prefix . 'terms'
			);
			$blogs[$index] = $data;
			$index++;
		}
		return xpress_get_categoires_map($blogs,$disp_sub);
	}
}

if(!function_exists('xpress_get_categoires_map')){
	function xpress_get_categoires_map($blogs ,$disp_sub){
		global $sitemap_configs;
		
		$xoopsDB =& Database::getInstance();
		
		$sitemap = array();
		$myts =& MyTextSanitizer::getInstance();
		
		$blogs_count = count($blogs);
		$i = 0;
		$blog = array();
		for ($b_no = 0 ; $b_no < $blogs_count ; $b_no++){
			$blog = $blogs[$b_no];
			$terms = $blog['terms'];
			$term_taxonomy = $blog['term_taxonomy'];
			$blog_sub_url = $blog['blog_sub_url'];
			$cat_url = $blog['blog_sub_url'] . '?cat=';
			$blog_name = $blog['blog_name'];
			
			$sql  = "SELECT term_id , name FROM $terms";
			$result = $xoopsDB->query($sql);
			$cat_name = array();
			while (list($id, $name) = $xoopsDB->fetchRow($result)){
				$cat_name["'ID" . $id . "'"] = $name;
			}
			if ($blogs_count > 1){
					$sitemap['parent'][$i]['id'] = 0;
					$sitemap['parent'][$i]['title'] = $blog_name ;
					$sitemap['parent'][$i]['image'] = 1 ;
					$sitemap['parent'][$i]['url'] = $blog_sub_url;
					$blog_index = $i;
					$i++;
			}

			$mytree = new XoopsTree($term_taxonomy, 'term_id' , 'parent');
			$sql  = "SELECT term_id  FROM $term_taxonomy WHERE parent = 0 AND taxonomy = 'category'";
			$result = $xoopsDB->query($sql);
			while (list($catid) = $xoopsDB->fetchRow($result)){
				if ($blogs_count <= 1){
					$dipth = 1;
					$sitemap['parent'][$i]['id'] = $catid;
					$sitemap['parent'][$i]['title'] = $cat_name["'ID" . $catid . "'"] ; ;
					$sitemap['parent'][$i]['image'] = $dipth ;
					$sitemap['parent'][$i]['url'] = $cat_url.$catid;

					if($disp_sub){ 
						$j = 0;
						$child_ary = $mytree->getChildTreeArray($catid, '');
						foreach ($child_ary as $child)
						{
							$count = strlen($child['prefix']) + $dipth;
							$sitemap['parent'][$i]['child'][$j]['id'] = $child['term_id'];
							$sitemap['parent'][$i]['child'][$j]['title'] = $cat_name["'ID" .$child['term_id'] . "'"];
							$sitemap['parent'][$i]['child'][$j]['image'] = (($count > 3) ? 4 : $count);
							$sitemap['parent'][$i]['child'][$j]['url'] = $cat_url.$child['term_id'];
							$j++;
						}
					}
					$i++;
				} else {
					$dipth = 2;
					$sitemap['parent'][$blog_index]['child'][$i]['id'] = $catid;
					$sitemap['parent'][$blog_index]['child'][$i]['title'] = $cat_name["'ID" . $catid . "'"];
					$sitemap['parent'][$blog_index]['child'][$i]['image'] = $dipth;
					$sitemap['parent'][$blog_index]['child'][$i]['url'] = $cat_url.$catid;
					$i++;
					$parent_id = $blog_index;
					if($disp_sub){ 
						$child_ary = $mytree->getChildTreeArray($catid, '');
						foreach ($child_ary as $child)
						{
							$count = strlen($child['prefix']) + $dipth; 
							$sitemap['parent'][$blog_index]['child'][$i]['id'] = $child['term_id'];
							$sitemap['parent'][$blog_index]['child'][$i]['title'] = $cat_name["'ID" .$child['term_id'] . "'"];
							$sitemap['parent'][$blog_index]['child'][$i]['image'] = (($count > 3) ? 4 : $count);
							$sitemap['parent'][$blog_index]['child'][$i]['url'] = $cat_url.$child['term_id'];
							$i++;
						}
					}
				}
			$i++;
			}
		}
		return $sitemap;
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