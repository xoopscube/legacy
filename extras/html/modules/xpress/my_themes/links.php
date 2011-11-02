<?php
/*
Template Name: Links
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

<h2><?php _e('Links:', 'xpress'); ?></h2>
<ul>
<?php wp_list_bookmarks(); ?>
</ul>

</div>

<?php get_footer(); ?>
