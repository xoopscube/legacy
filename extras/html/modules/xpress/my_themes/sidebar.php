	<div id="xpress_sidebar">
		<ul>
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<li>
				<?php include (get_template_directory() . '/searchform.php'); ?>
			</li>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2><?php _e('Author', 'xpress'); ?></h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->

			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?> <li>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p><?php printf(__('You are currently browsing the archives for the %s category.', 'xpress'), single_cat_title('', false)); ?></p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the day %3$s.', 'xpress'), get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('l, F jS, Y', 'xpress'))); ?></p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for %3$s.', 'xpress'), get_bloginfo('url'), get_bloginfo('name'), get_the_time(__('F, Y', 'xpress'))); ?></p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives for the year %3$s.', 'xpress'), get_bloginfo('url'), get_bloginfo('name'), get_the_time('Y')); ?></p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p><?php printf(__('You have searched the <a href="%1$s/">%2$s</a> blog archives for <strong>&#8216;%3$s&#8217;</strong>. If you are unable to find anything in these search results, you can try one of these links.', 'xpress'), get_bloginfo('url'), get_bloginfo('name'), get_search_query()); ?></p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p><?php printf(__('You are currently browsing the <a href="%1$s/">%2$s</a> blog archives.', 'xpress'), get_bloginfo('url'), get_bloginfo('name')); ?></p>

			<?php } ?>

			</li> <?php }?>

			<?php wp_list_pages('title_li=<h2>' . __('Pages', 'xpress') . '</h2>' ); ?>

			<li><h2><?php _e('Archives', 'xpress'); ?></h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>
            
            <?php if(function_exists('wp_list_categories')) : ?>
			<?php wp_list_categories('show_count=1&title_li=<h2>' . __('Categories', 'xpress') . '</h2>'); ?>
			<?php else : ?>
			<li><h2><?php _e('Categories', 'xpress'); ?></h2>
			<ul><?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?></ul></li>
			<?php endif; ?>


			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
            <?php if(function_exists('wp_list_bookmarks')) : ?>
				<?php wp_list_bookmarks(); ?>
			<?php endif; ?>
				<li><h2><?php _e('Meta', 'xpress'); ?></h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional', 'xpress'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>', 'xpress'); ?></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="<?php _e('XHTML Friends Network', 'xpress'); ?>"><?php _e('XFN', 'xpress'); ?></abbr></a></li>
					<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.', 'xpress'); ?>">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php } ?>
			<?php endif; ?>
		</ul>
	</div>