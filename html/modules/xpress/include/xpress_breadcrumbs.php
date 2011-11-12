<?php
function get_breadcrumbs(){
//	$xoops_breadcrumbs[0] = array( 'name' => get_bloginfo('description') , 'url' => get_settings('home'));
	$xoops_breadcrumbs[0] = array( 'name' => get_bloginfo('name') , 'url' => get_settings('home'));
	$pagetitle='';
	if (is_category()) {
			$this_cat = get_category($cat);
			$p_count = 0;
			while ($this_cat->parent) {			
	  			$this_cat = get_category($this_cat->parent);
				$cat_parrent[$p_count] = array( 'name' => $this_cat->cat_name , 'url' => get_category_link($this_cat->cat_ID));
				$p_count++;
			}
			for ($i = 1 ; $i <= $p_count ;$i++){
					$xoops_breadcrumbs[$i] = $cat_parrent[$p_count - $i];
			}
			$xoops_breadcrumbs[$p_count+1] = array( 'name' => single_cat_title('', false));
	} elseif (is_day()) {
			$xoops_breadcrumbs[1] = array( 'name' => get_the_time(__('F j, Y')));
	} elseif (is_month()) {
			$xoops_breadcrumbs[1] = array( 'name' => get_the_time(__('F, Y')));
	} elseif (is_year()) {
			$xoops_breadcrumbs[1] = array( 'name' => get_the_time('Y'));
	} elseif (is_author()) {
			$xoops_breadcrumbs[1] = array( 'name' => get_author_name( get_query_var('author') ));
	} elseif (is_single()) {
			$xoops_breadcrumbs[1] = array( 'name' => single_post_title('', false));
	} elseif (is_page()) {
			$now_page = get_page($page_id);
			$this_page = $now_page;
			$p_count = 0;
			while ($this_page->post_parent) {			
	  			$this_page = get_page($this_page->post_parent);

				$page_parrent[$p_count] = array( 'name' => $this_page->post_title , 'url' => get_permalink($this_page->ID));
				$p_count++;
			}
			for ($i = 1 ; $i <= $p_count ;$i++){
					$xoops_breadcrumbs[$i] = $page_parrent[$p_count - $i];
			}
			$xoops_breadcrumbs[$p_count+1] = array( 'name' => $now_page->post_title);
	} elseif (is_search()){
			$xoops_breadcrumbs[1] = array( 'name' => $pagetitle);
	} elseif(function_exists( 'is_tag' )){
		if(is_tag() ) {
			$xoops_breadcrumbs[1] = array( 'name' => single_tag_title('', false));
		}
	}
	return $xoops_breadcrumbs;
}
?>