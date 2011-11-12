<?php
// Block Version: 1.0
function recent_posts_content_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_recent_posts_content_block.html' : trim( $options[1] );
	$disp_count =  ($options[2])?intval($options[2]):10;
	$excerpt = empty( $options[3] ) ? false : true ;
	$excerpt_size =  ($options[4])?intval($options[4]):100;
	$date_format = empty( $options[5] ) ? '' : $options[5] ;
	$time_format = empty( $options[6] ) ? '' : $options[6] ;
	$tag_select = $options[7] ;
	$cat_select = empty( $options[8] ) ? '0' : $options[8] ;
	$day_select = ($options[9])?intval($options[9]):0;
	$day_size = ($options[10])?intval($options[10]):0;

	$selected = explode(',' , $cat_select);
	
	$mydirpath = get_xpress_dir_path();
	
	if (empty($date_format)) $date_format = get_settings('date_format');
	if (empty($time_format)) $time_format = get_settings('time_format');
	if(empty($tag_select)) $tag_where = ''; else $tag_where = "tag='$tag_select'&";
	if (array_search(0,$selected)===0) $cat_select = 0;

	$selected_author_id = xpress_selected_author_id('echo=0');	
	if (!empty($selected_author_id)){
		$author_where ="author=$selected_author_id&";
	} else {
		$author_where = '';
	}
	
	$date_today = mktime (0, 0, 0, date("m"), date("d"), date("Y"));
	$between_days = $day_size * 60 * 60 * 24;
	
	global $wpdb,$wp_query;
	$block = array();
	$item_no = 0;	
	if (!is_null($wpdb)){
		$wp_query->in_the_loop = true;		//for use the_tags() in multi lopp 
		if ($cat_select) {
			$r = new WP_Query($author_where . $tag_where ."cat=$cat_select&showposts=$disp_count&what_to_show=posts&nopaging=0&post_status=publish");
		} else {
			$r = new WP_Query($author_where . $tag_where ."showposts=$disp_count&what_to_show=posts&nopaging=0&post_status=publish");
		}
		while($r->have_posts()){			
			$r->the_post();
			if ($day_select == 1){
				$post_date = mktime (0, 0, 0, get_the_time("m"), get_the_time("d"), get_the_time("Y"));
				$base_date = $date_today - $between_days;
				if ($post_date < $base_date) continue;
			}
			if ($day_select == 2){
				$post_date = mktime (0, 0, 0, get_the_time("m"), get_the_time("d"), get_the_time("Y"));
				if (empty($latest_date)) $latest_date = $post_date;
				$base_date = $latest_date - $between_days;
				if ($post_date < $base_date) continue;
			}
			
			ob_start();
				the_ID();
				$post_id = ob_get_contents();
			ob_end_clean();
			
			$title = xpress_the_title('echo=0');
			
			ob_start();
				the_permalink();
				$permalink = ob_get_contents();
			ob_end_clean();					
			
			ob_start();
				the_author_posts_link();
				$author = ob_get_contents();
			ob_end_clean();
			
			ob_start();
				the_category(' &bull; ');
				$category = ob_get_contents();
			ob_end_clean();	
			
			if (function_exists('the_tags')){
				ob_start();
					the_tags(__('Tags:', 'xpress') . ' ',' &bull; ','');
					$tags = ob_get_contents();
				ob_end_clean();	
			} else {
				$tags = '';
			}

			$param = array(
				'configration_select' => 0, 
				'do_excerpt' => $excerpt,
				'excerpt_length_word' => $excerpt_size, 
				'excerpt_length_character' => $excerpt_size, 
				'echo' => 0
			);

			$post_content = xpress_the_content($param);
			
			ob_start();
				the_modified_date($date_format);
				$post_modified_date = ob_get_contents();
			ob_end_clean();
				
			ob_start();
				the_modified_date($time_format);
				$post_modified_time = ob_get_contents();
			ob_end_clean();
			
			ob_start();
				the_time($date_format);
				$post_date = ob_get_contents();
			ob_end_clean();
			
			ob_start();
				the_time($time_format);
				$post_time = ob_get_contents();
			ob_end_clean();
			
			
			ob_start();
				comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)'));
				$comments_popup_link = ob_get_contents();
			ob_end_clean();

// all_in_one			
			ob_start();
?>
				<div class="xpress-post" id="post-<?php the_ID(); ?>">
					<div class ="xpress-post-header">
						<?php if (function_exists('hotDates')) { hotDates(); }?>
						<div class ="xpress-post-title">
							<?php if(function_exists('the_title_attribute')) : ?>			
								<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'xpress'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
							<?php else : ?>
								<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'xpress'), the_title('','',false)); ?>"><?php the_title(); ?></a></h2>
							<?php endif; ?>

						</div>
					</div>
					<div class="xpress-post-entry">
					<?php	echo $post_content; ?>							
					</div>
					<div class="xpress-link-pages"><?php wp_link_pages() ?></div>
					<div class ="xpress-post-footer">
<?php
						the_time('Y/m/d l');
						echo ' - ';
						the_author_posts_link();
						echo ' (' . xpress_post_views_count('post_id=' . $post->ID . '&format=' . __('Views :%d', 'xpress'). '&echo=0') . ')'; 
						
						echo ' | ';
						// echo the_tags(__('Tags:', 'xpress') . ' ', ', ', ' | ');
						printf(__('Posted in %s', 'xpress'), get_the_category_list(', '));
						echo ' | ';
						edit_post_link(__('Edit', 'xpress'), '', ' | ');
						comments_popup_link(__('No Comments &#187;', 'xpress'), __('1 Comment &#187;', 'xpress'), __('% Comments &#187;', 'xpress'), '', __('Comments Closed', 'xpress') );
?>
					</div>
				</div>
<?php
			$all_in_one = ob_get_contents();
			ob_end_clean();
						
			
			$post_title = '<a href="' . $permalink . '">' . $title . '</a>';
			$post_date_time = $post_date . ' ' . $post_time ;
			$post_modified_date_time = $post_modified_date . ' ' . $post_modified_time ;
			$trackback_url = trackback_url(false);
			$post_viwes = xpress_post_views_count('post_id=' . $post_id . '&format=' . __('Views :%d', 'xpress'). '&echo=0');

//			if (empty($tags)) $tags = __('Not Tag');

			$row_data = array(
				'post_id'		=> $post_id ,
				'post_title'	=> $post_title ,
				'post_content' 		=> $post_content ,
				'post_date' => $post_date ,
				'post_time' => $post_time ,
				'post_date_time' => $post_date_time ,
				'post_modified_date' => $post_modified_date ,
				'post_modified_time' => $post_modified_time ,
				'post_modified_date_time' => $post_modified_date_time ,
				'post_author' 	=> $author ,
				'post_category' 	=> $category ,	
				'post_tags' 		=> $tags,
				'post_views' 		=> $post_viwes,
				'comment_link' 	=> $comments_popup_link ,
				'trackback_url' => $trackback_url ,
				'all_in_one' => $all_in_one
			);
			
			$block['contents']['item'.$item_no] = $row_data;
			$item_no++;
		}
		$block['data_count'] = $item_no;  //xml unserialise error
	}
	return $block ;
}

?>