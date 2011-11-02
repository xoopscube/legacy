<?php get_header(); ?>

       <div id="xpress_wrap">
	   
	<?php if(xpress_is_theme_sidebar_disp()) : ?>
	   
		<div id="xpress_content" class="narrowcolumn">
		
	<?php else : ?>
	
		<div id="xpress_content" class="narrowcolumn_nonside">
		
	<?php endif; ?>

    <div id="xpress_header">
    	<div id="xpress_headerimg">
    		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
    		<div class="description"><?php bloginfo('description'); ?></div>
    	</div>
    </div>

		<h2 class="center"><?php _e('Error 404 - Not Found', 'xpress'); ?></h2>

	</div>
       </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>