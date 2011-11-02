<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="xpress_content" class="widecolumn">

    <div id="xpress_header">
    	<div id="xpress_headerimg">
    		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
    		<div class="description"><?php bloginfo('description'); ?></div>
    	</div>
    </div>

<?php include (get_template_directory() . '/searchform.php'); ?>

	<h2><?php _e('Archives by Month:', 'xpress'); ?></h2>
	<ul>
		<?php wp_get_archives('type=monthly'); ?>
	</ul>

	<h2><?php _e('Archives by Subject:', 'xpress'); ?></h2>
	<ul>
		 <?php wp_list_categories(); ?>
	</ul>

</div>

<?php get_footer(); ?>
