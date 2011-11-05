<?php get_header(); ?>

   <div id="xpress_wrap">
	   
	<?php if(xpress_is_theme_sidebar_disp()) : ?>
	   
		<div id="xpress_content" class="narrowcolumn">
		
	<?php else : ?>
	
		<div id="xpress_content" class="narrowcolumn_nonside">
		
	<?php endif; ?>

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<?php if (function_exists('hotDates')) { hotDates(); }?>
		<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">' . __('Read the rest of this page &raquo;', 'xpress') . '</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'xpress') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link(__('Edit this entry.', 'xpress'), '<p>', '</p>'); ?>

	</div>
       </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>