<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
<label class="hidden" for="s"><?php _e('Search for:', 'xpress'); ?></label>
<?php if(function_exists('the_search_query')) : ?>			
	<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<?php else : ?>
	<div><input type="text" value="<?php echo attribute_escape($s); ?>" name="s" id="s" />
<?php endif; ?>
<input type="submit" id="searchsubmit" value="<?php _e('Search', 'xpress'); ?>" />
</div>
</form>
