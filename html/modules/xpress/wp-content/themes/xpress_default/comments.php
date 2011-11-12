<?php // Do not delete these lines
	if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	
	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'xpress'); ?></p> 
	<?php
		return;
	}

?>

<!-- You can start editing here. -->
<?php if ( xpress_is_wp_version('>=','2.7')) : ?>
	<?php if ( have_comments() ) : ?>
		<?php if ( ! empty($comments_by_type['comment']) ) : ?>
			<h3 id="xpress_comments"><?php comments_number(__('No Responses', 'xpress'), __('One Response', 'xpress'), __('% Responses', 'xpress'));?> <?php printf(__('to &#8220;%s&#8221;', 'xpress'), the_title('', '', false)); ?></h3>
			<div id="xpress_commentlist">
				<?php wp_list_comments('type=comment&style=div'); ?>
			</div>
		<?php endif; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
	<?php else : // this is displayed if there are no comments so far ?>

		<?php if ('open' == $post->comment_status) : ?>
			<!-- If comments are open, but there are no comments. -->
		<?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<p class="nocomments"><?php _e('Comments are closed.' , 'xpress'); ?></p>
		<?php endif; ?>
	<?php endif; ?>
<?php else : // is version 2.7 under?>
	<?php if ( $comments ) : ?>
		<h3 id="xpress_comments"><?php comments_number(__('No Responses', 'xpress'), __('One Response', 'xpress'), __('% Responses', 'xpress'));?> <?php printf(__('to &#8220;%s&#8221;', 'xpress'), the_title('', '', false)); ?></h3>
		<div id="xpress_commentlist">
			<?php foreach ($comments as $comment) : ?>
				<li <?php echo $oddcomment; ?>id="comment-<?php comment_ID() ?>">
					<?php if (function_exists('get_avatar')) echo get_avatar( $comment, 32 ); ?>	
					<?php printf(__('<cite>%s</cite> Says:', 'xpress'), get_comment_author_link()); ?>
					<?php if ($comment->comment_approved == '0') : ?>
					<em><?php _e('Your comment is awaiting moderation.', 'xpress'); ?></em>
					<?php endif; ?>
					<br />
					<small class="commentmetadata"><a href="#comment-<?php comment_ID() ?>" title=""><?php printf(__('%1$s at %2$s', 'xpress'), get_comment_date(), get_comment_time()); ?></a> <?php edit_comment_link(__('Comment Edit', 'xpress'),'&nbsp;&nbsp;',''); ?></small>
					<?php comment_text() ?>
				</li>
				<?php
					/* Changes every other comment to a different class */
					$oddcomment = ( empty( $oddcomment ) ) ? 'class="alt" ' : '';
				?>
			<?php endforeach; /* end for each comment */ ?>
			</ol>
		</div>
	<?php else : // this is displayed if there are no comments so far ?>
		<?php if ('open' == $post->comment_status) : ?>
			<!-- If comments are open, but there are no comments. -->
		<?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<p class="nocomments"><?php _e('Comments are closed.' , 'xpress'); ?></p>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if ('open' == $post->comment_status) : ?>
	<div id="respond">
		<h3><?php comment_form_title( __('Leave a Reply', 'xpress'), __('Leave a Reply for %s' , 'xpress') ); ?></h3>
			
		<?php if ( function_exists('cancel_comment_reply_link') ): ?>
			<div id="cancel-comment-reply"> 
				<small><?php cancel_comment_reply_link() ?></small>
			</div> 
		<?php endif; ?>
		
		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'xpress'), get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode(get_permalink())); ?></p>
		<?php else : ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<div id="xpress-comment-user">
					<?php if ( $user_ID ) : ?>
						<?php if ( function_exists('wp_logout_url') ): ?>
							<p><?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'xpress'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'xpress'); ?>"><?php _e('Log out &raquo;', 'xpress'); ?></a></p>
						<?php else : ?>
							<p><?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'xpress'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', 'xpress'); ?>"><?php _e('Log out &raquo;', 'xpress'); ?></a></p>
						<?php endif; ?>
					<?php else : ?>
						<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req && 0) echo "aria-required='true'"; ?> />
						<label for="author"><small><?php _e('Name', 'xpress'); ?> <?php if ($req) _e("(required)", "xpress"); ?></small></label></p>

						<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req && 0) echo "aria-required='true'"; ?> />
						<label for="email"><small><?php _e('Mail (will not be published)', 'xpress'); ?> <?php if ($req) _e("(required)", "xpress"); ?></small></label></p>

						<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
						<label for="url"><small><?php _e('Website', 'xpress'); ?></small></label></p>
					<?php endif; ?>
				</div>

				<div id="xpress-comment-form">
					<!--<p><small><?php printf(__('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', 'xpress'), allowed_tags()); ?></small></p>-->

					<textarea name="comment" id="comment" cols="100" rows="5" tabindex="4" style="width:100%"></textarea>
					<div class ="xpress-comment-submit">
						<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment', 'xpress'); ?>" />
					</div>
					<?php if (function_exists('comment_id_fields')) : ?>
						<?php comment_id_fields(); //@since 2.7.0 ?>  
					<?php else : ?>
						<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					<?php endif; ?>
					<?php
						ob_start();
							do_action('comment_form', $post->ID);
							$output = ob_get_contents();
						ob_end_clean();
						echo str_replace(' id="_wp', ' id="wp', $output);
					?>
				</div>
			</form>
		<?php endif; // If registration required and not logged in ?>
	</div>

<?php endif; // if you delete this the sky will fall on your head ?>
