<?php
/* Don't remove these lines. */
add_filter('comment_text', 'popuplinks');
while ( have_posts()) : the_post();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <title><?php printf(__('%1$s - Comments on %2$s', 'xpress'), get_option('blogname'), the_title('','',false)); ?></title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
		body { margin: 3px; }
	</style>

</head>
<body id="xpress_commentspopup">

<h1 id="xpress_header"><a href="" title="<?php echo get_option('blogname'); ?>"><?php echo get_option('blogname'); ?></a></h1>

<h2 id="xpress_comments"><?php _e('Comments', 'xpress'); ?></h2>

<p><a href="<?php echo get_post_comments_feed_link($post->ID); ?>"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post.', 'xpress'); ?></a></p>

<?php if ('open' == $post->ping_status) { ?>
<p><?php printf(__('The <abbr title="Universal Resource Locator">URL</abbr> to TrackBack this entry is: <em>%s</em>', 'xpress'), get_trackback_url()); ?></p>
<?php } ?>

<?php
// this line is WordPress' motor, do not delete it.
$commenter = wp_get_current_commenter();
extract($commenter);
$comments = get_approved_comments($id);
$post = get_post($id);
if (!empty($post->post_password) && $_COOKIE['wp-postpass_'. COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
	echo(get_the_password_form());
} else { ?>

<?php if ($comments) { ?>
<ol id="xpress_commentlist">
<?php foreach ($comments as $comment) { ?>
	<li id="comment-<?php comment_ID() ?>">
	<?php comment_text() ?>
	<p><cite><?php comment_type(__('Comment', 'xpress'), __('Trackback', 'xpress'), __('Pingback', 'xpress')); ?> <?php printf(__('by %1$s &#8212; %2$s @ <a href="#comment-%3$s">%4$s</a>', 'xpress'), get_comment_author_link(), get_comment_date(), get_comment_ID(), get_comment_time()); ?></cite></p>
	</li>

<?php } // end for each comment ?>
</ol>
<?php } else { // this is displayed if there are no comments so far ?>
	<p><?php _e('No comments yet.', 'xpress'); ?></p>
<?php } ?>

<?php if ('open' == $post->comment_status) { ?>
<h2><?php _e('Leave a comment', 'xpress'); ?></h2>
<p><?php printf(__('Line and paragraph breaks automatic, e-mail address never displayed, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <code>%s</code>', 'xpress'), allowed_tags()); ?></p>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<?php if ( $user_ID ) : ?>
	<p><?php printf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out &raquo;</a>', 'xpress'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity, get_option('siteurl') . '/wp-login.php?action=logout'); ?></p>
<?php else : ?>
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author"><?php _e('Name', 'xpress'); ?></label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo attribute_escape($_SERVER["REQUEST_URI"]); ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="xpress_email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email"><?php _e('E-mail', 'xpress'); ?></label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><?php _e('<abbr title="Universal Resource Locator">URL</abbr>', 'xpress'); ?></label>
	</p>
<?php endif; ?>

	<p>
	  <label for="comment"><?php _e('Your Comment', 'xpress'); ?></label>
	<br />
	  <textarea name="comment" id="xpress_comment" cols="70" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="<?php _e('Say It!' , 'xpress'); ?>" />
	</p>
	<?php do_action('comment_form', $post->ID); ?>
</form>
<?php } else { // comments are closed ?>
<p><?php _e('Sorry, the comment form is closed at this time.', 'xpress'); ?></p>
<?php }
} // end password check
?>

<div><strong><a href="javascript:window.close()"><?php _e('Close this window.', 'xpress'); ?></a></strong></div>

<?php // if you delete this the sky will fall on your head
endwhile;
?>

<!-- // this is just the end of the motor - don't touch that line either :) -->
<?php //} ?>
<p class="credit"><?php timer_stop(1); ?> <cite><?php printf(__('Powered by <a href="%s" title="Powered by WordPress, state-of-the-art semantic personal publishing platform"><strong>WordPress</strong></a>', 'xpress'), 'http://wordpress.org/'); ?></cite></p>
<?php // Seen at http://www.mijnkopthee.nl/log2/archive/2003/05/28/esc(18) ?>
<script type="text/javascript">
<!--
document.onkeypress = function esc(e) {
	if(typeof(e) == "undefined") { e=event; }
	if (e.keyCode == 27) { self.close(); }
}
// -->
</script>
</body>
</html>
