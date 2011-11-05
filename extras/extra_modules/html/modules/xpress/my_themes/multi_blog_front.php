<?php
/*
Template Name: MultiBlog Front_Page
*/
?>
<?php get_header(); ?>

<div id="content" class="widecolumn">			
	<h3><?php _e('Site News', 'xpress'); ?></h3>
	<ul>
		<?php 
		query_posts('showposts=7');
		if (have_posts()) : ?><?php while (have_posts()) : the_post(); ?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title();?> </a></li>
		<?php endwhile; ?><?php endif; ?>
	</ul>
	<br />	
	<h3><?php _e('Blogs List', 'xpress'); ?></h3>
	<?php
	$blog_list = get_blog_list( 0, 'all' );
	echo "<ul>\n";
	foreach ($blog_list AS $blog) {
		$url =  'http://' .$blog['domain'] .$blog['path'];
		$blog_name = get_blog_option( $blog['blog_id'], 'blogname' );
		$post_count = $blog['postcount'];
		
		echo  "<li><a href=\" $url \"> $blog_name </a>  (" . __('post count','xpress') . ":$post_count) </li>";
	}
	echo "</ul>\n";
	?> 
	<br />
		
<?php
	echo '<h3>' . __('New Entries', 'xpress') . "</h3>\n";
	$data_array = xpress_grobal_recent_posts();
	echo "<ul>\n";
	foreach($data_array as $data){
		echo '<li>';
		printf(__('%1$s wrote %2$s in %3$s.','xpress'), $data->post_author,$data->title_link,$data->blog_link);
		echo "</li>\n";
	}
	echo "</ul>\n";
?>
	<br />

	<h3><?php _e('Updated Blogs', 'xpress'); ?></h3>
	<?php
	$blogs = get_last_updated();
	if( is_array( $blogs ) ) {
		?>
		<ul>
		<?php foreach( $blogs as $details ) {
			?><li><a href="http://<?php echo $details[ 'domain' ] . $details[ 'path' ] ?>"><?php echo get_blog_option( $details[ 'blog_id' ], 'blogname' ) ?></a></li><?php
		}
		?>
		</ul>
		<?php
	}
	?>
	<br />	
	<?php	
		$current_uid = get_current_user_id();
		if (!empty($current_uid)) {
			$user_info = get_userdata($current_uid);
			$display_name = $user_info->display_name;
			echo '<h3>';
			printf(__('Howdy %s', 'xpress'),$display_name);
			echo "</h3>\n";
			echo "<ul>\n";
		
			$user_blogs = get_blogs_of_user($current_uid);
			if( is_array($user_blogs) ) {
				echo '<li>' . __('Your Blogs list','xpress') ;
					echo "<ul>\n";
					foreach( $user_blogs as $blog ) {
						$url =  'http://' .$blog->domain .$blog->path;
						$blog_name = $blog->blogname;
						echo  "<li><a href=\" $url \"> $blog_name </a></li>";
					}
					echo "</ul>\n";			
				echo "</li>\n";
				wp_register();
			}
			echo '<li> <a href="wp-signup.php">' . __('Create a new blog','xpress') .  "</a></li>\n";
			echo "</ul>\n";			
		}
	?>

</div>

<?php get_footer(); ?>
