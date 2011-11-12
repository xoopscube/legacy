<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if (function_exists('language_attributes')) { language_attributes(); }?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name');
if ( is_single() ) {
	_e('&raquo; Blog Archive', 'xpress');
}
wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'xpress'), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php printf(__('%s Atom Feed', 'xpress'), get_bloginfo('name')); ?>" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
// comment-replay.js load  @since 2.7.0
if (function_exists('wp_enqueue_script')) { // not function Ver2.0
	if ( is_singular() ){  //not function Ver2.0
		wp_enqueue_script( 'comment-reply' );
	}
}
?>
<?php wp_head(); ?>
</head>
<body <?php if(function_exists('body_class')) body_class(); ?>>
<div id="xpress_page">
	<div id="xpress-header-bar">
		<!-- <div id="xpress_header" role="banner"> -->
		<div id="xpress_header">
			<div id="xpress-header-bar-top">
				<div class="xpress-header-title">
					<?php  if (xpress_selected_author_id('echo=0') && function_exists('get_avatar')) echo get_avatar(xpress_selected_author_id('echo=0'),$size = '32'); ?>
					<a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a>
				</div>
				<div class="xpress-conditional-title">
					&nbsp; <?php xpress_conditional_title();?> 
				</div>
				<div class="xpress-description">
					<?php bloginfo('description'); ?>
				</div>
			</div><!-- #xpress-header-bar-top -->
	
			<!-- <div id="menu_div" role="navigation"> -->
			<div id="menu_div">
				<div id="access">
					<?php if (function_exists('wp_nav_menu')) wp_nav_menu( array( 'sort_column' => 'menu_order', 'container_class' => 'menu-aheader' ) ); ?> 
				</div><!-- #access -->
				<div id="xpress-menu">
					<div class="menu-header">
						<div class="menu">
							<ul>
								<?php
									if(xpress_is_multiblog()){
										$blog_details = get_blog_details(1);
										$site_url = $blog_details->siteurl;
										echo '<li><a href="' . $site_url . '/">' . __('Main Page','xpress') . '</a></li>';
									}
								?>
								<li><a href="<?php echo get_option('home'); ?>/"><?php _e('Blogs Home','xpress')?></a></li>
								<?php if(xpress_is_contributor()) { echo '<li>'. xpress_post_new_link('link_title='. __('Post New','xpress'). '&echo=0').'</li>'; }?>
							</ul>
						</div><!-- #menu -->
					</div><!-- #menu-header -->
				</div><!-- #xpress-menu -->
			</div><!-- #menu_div -->
		</div><!-- #xpress_header -->
	</div><!-- #xpress-header-bar -->