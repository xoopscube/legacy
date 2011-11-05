<?php get_header(); ?>

		
<div id="xpress_wrap">				
	<?php
		if(xpress_is_theme_sidebar_disp()) {
			echo '<div id="xpress_content" class="narrowcolumn">';	
		} else {
			echo '<div id="xpress_content" class="narrowcolumn_nonside">';
		}	
	?>				
		<?php if (have_posts()) : ?>
			<div class="xpress-navi-bar">
				<?php if(function_exists('wp_pagenavi')) : ?>			
					<div class="xpress_pagenavi"><?php wp_pagenavi(); ?></div>
				<?php else : ?>
					<div class="alignleft"><?php xpress_left_arrow_posts_link('echo=1'); ?></div>
					<div class="alignright"><?php xpress_right_arrow_posts_link('echo=1'); ?></div>
				<?php endif; ?>
			</div>
				
			<?php while (have_posts()) : the_post(); ?>

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
						<?php xpress_the_content(); ?>
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

			<?php endwhile; ?>
				
			<div class="xpress-navi-bar">
				<?php if(function_exists('wp_pagenavi')) : ?>			
					<div class="xpress_pagenavi"><?php wp_pagenavi(); ?></div>
				<?php else : ?>
					<div class="alignleft"><?php xpress_left_arrow_posts_link('echo=1'); ?></div>
					<div class="alignright"><?php xpress_right_arrow_posts_link('echo=1'); ?></div>
				<?php endif; ?>
			</div>
				
		<?php else : ?>

			<h2 class="center"><?php _e('Not Found', 'xpress'); ?></h2>
			<p class="center"><?php _e('Sorry, but you are looking for something that isn&#8217;t here.', 'xpress'); ?></p>
			<?php include (get_template_directory() . "/searchform.php"); ?>

		<?php endif; ?>
	</div>
</div>
<?php if(xpress_is_theme_sidebar_disp()) get_sidebar(); ?>

<?php get_footer(); ?>
