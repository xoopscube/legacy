<?php
/** Tell WordPress to run xpress_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'xpress_setup' );
if ( ! function_exists( 'xpress_setup' ) ):
function xpress_setup() {
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'xpress', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu()
	if ( function_exists('add_theme_support') )add_theme_support( 'nav-menus' );
	// This theme uses wp_nav_menu() in one location.
	if ( function_exists('register_nav_menus') ){
			register_nav_menus( array(
			'primary' => __('Primary Navigation','xpress'),
		) );
	}
	
}	
endif;
	
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));


function xpress_head() {
	$head = "<style type='text/css'>\n<!--";
	$output = '';
	if ( xpress_header_image() ) {
		$url =  xpress_header_image_url() ;
		$output .= "#xpress_header { background: url('$url') repeat-x bottom left; }\n";
	}
	if ( false !== ( $color = xpress_header_color() ) ) {
		$output .= "#xpress-header-bar-top a, #xpress-header-bar-top a:visited, #xpress-header-bar-top .xpress-description ,#xpress-header-bar-top .xpress-conditional-title,#access a, #xpress-menu a{ color: $color; }\n";
	}
	if ( false !== ( $display = xpress_header_display() ) ) {
		$output .= "#headerimg { display: $display }\n";
	}
	$foot = "--></style>\n";
	if ( '' != $output )
		echo $head . $output . $foot;
}

add_action('wp_head', 'xpress_head');

function xpress_header_image() {
	return apply_filters('xpress_header_image', get_option('xpress_header_image'));
}

function xpress_upper_color() {
	if (strpos($url = xpress_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['upper'];
	} else
		return 'ffffff';
}

function xpress_lower_color() {
	if (strpos($url = xpress_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['lower'];
	} else
		return 'ffffff';
}

function xpress_header_image_url() {
	if ( $image = xpress_header_image() )
		$url = get_template_directory_uri() . '/images/' . $image;
	else
		$url = get_template_directory_uri() . '/images/xpressheader.jpg';

	return $url;
}

function xpress_header_color() {
	return apply_filters('xpress_header_color', get_option('xpress_header_color'));
}

function xpress_header_color_string() {
	$color = xpress_header_color();
	if ( false === $color )
		return 'black';

	return $color;
}

function xpress_header_display() {
	return apply_filters('xpress_header_display', get_option('xpress_header_display'));
}

function xpress_header_display_string() {
	$display = xpress_header_display();
	return $display ? $display : 'inline';
}

add_action('admin_menu', 'xpress_add_theme_page');

function xpress_add_theme_page() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == basename(__FILE__) ) {
		if ( isset( $_REQUEST['action'] ) && 'save' == $_REQUEST['action'] ) {
			check_admin_referer('xpress-header');
			if ( isset($_REQUEST['njform']) ) {
				if ( isset($_REQUEST['defaults']) ) {
					delete_option('xpress_header_image');
					delete_option('xpress_header_color');
					delete_option('xpress_header_display');
				} else {
					if ( '' == $_REQUEST['njfontcolor'] )
						delete_option('xpress_header_color');
					else {
						$fontcolor = preg_replace('/^.*(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['njfontcolor']);
						update_option('xpress_header_color', $fontcolor);
					}
					if ( preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njuppercolor'], $uc) && preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njlowercolor'], $lc) ) {
						$uc = ( strlen($uc[0]) == 3 ) ? $uc[0]{0}.$uc[0]{0}.$uc[0]{1}.$uc[0]{1}.$uc[0]{2}.$uc[0]{2} : $uc[0];
						$lc = ( strlen($lc[0]) == 3 ) ? $lc[0]{0}.$lc[0]{0}.$lc[0]{1}.$lc[0]{1}.$lc[0]{2}.$lc[0]{2} : $lc[0];
						update_option('xpress_header_image', "header-img.php?upper=$uc&lower=$lc");
					}

					if ( isset($_REQUEST['toggledisplay']) ) {
						if ( false === get_option('xpress_header_display') )
							update_option('xpress_header_display', 'none');
						else
							delete_option('xpress_header_display');
					}
				}
			} else {

				if ( isset($_REQUEST['headerimage']) ) {
					check_admin_referer('xpress-header');
					if ( '' == $_REQUEST['headerimage'] )
						delete_option('xpress_header_image');
					else {
						$headerimage = preg_replace('/^.*?(header-img.php\?upper=[0-9a-fA-F]{6}&lower=[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['headerimage']);
						update_option('xpress_header_image', $headerimage);
					}
				}

				if ( isset($_REQUEST['fontcolor']) ) {
					check_admin_referer('xpress-header');
					if ( '' == $_REQUEST['fontcolor'] )
						delete_option('xpress_header_color');
					else {
						$fontcolor = preg_replace('/^.*?(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['fontcolor']);
						update_option('xpress_header_color', $fontcolor);
					}
				}

				if ( isset($_REQUEST['fontdisplay']) ) {
					check_admin_referer('xpress-header');
					if ( '' == $_REQUEST['fontdisplay'] || 'inline' == $_REQUEST['fontdisplay'] )
						delete_option('xpress_header_display');
					else
						update_option('xpress_header_display', 'none');
				}
			}
			

			//print_r($_REQUEST);
			wp_redirect("themes.php?page=functions.php&saved=true");
			die;
		}
		add_action('admin_head', 'xpress_theme_page_head');
		
		if ( isset( $_REQUEST['action'] ) && 'update_footer' == $_REQUEST['action'] ) {
			check_admin_referer('xpress-footer');
			if ( isset($_REQUEST['xpress_footer_sidebars_count']) ) {
				check_admin_referer('xpress-footer');
				if ( '' == $_REQUEST['xpress_footer_sidebars_count'] || '0' == $_REQUEST['xpress_footer_sidebars_count'] ){
					delete_option('xpress_footer_sidebars_count');
				} else {
					update_option('xpress_footer_sidebars_count',  $_REQUEST['xpress_footer_sidebars_count']);
				}
			}

			//print_r($_REQUEST);
			wp_redirect("themes.php?page=functions.php&footer_saved=true");
			die;
		}
	}
	add_theme_page(__('Header & Footer', 'xpress'), __('Header & Footer', 'xpress'), 'edit_themes', basename(__FILE__), 'xpress_theme_header_page');
}


function xpress_theme_page_head() {
?>
<script type="text/javascript" src="../wp-includes/js/colorpicker.js"></script>
<script type='text/javascript'>
// <![CDATA[
	function pickColor(color) {
		ColorPicker_targetInput.value = color;
		kUpdate(ColorPicker_targetInput.id);
	}
	function PopupWindow_populate(contents) {
		contents += '<br /><p style="text-align:center;margin-top:0px;"><input type="button" class="button-secondary" value="<?php echo attribute_escape(__('Close Color Picker', 'xpress')); ?>" onclick="cp.hidePopup(\'prettyplease\')"></input></p>';
		this.contents = contents;
		this.populated = false;
	}
	function PopupWindow_hidePopup(magicword) {
		if ( magicword != 'prettyplease' )
			return false;
		if (this.divName != null) {
			if (this.use_gebi) {
				document.getElementById(this.divName).style.visibility = "hidden";
			}
			else if (this.use_css) {
				document.all[this.divName].style.visibility = "hidden";
			}
			else if (this.use_layers) {
				document.layers[this.divName].visibility = "hidden";
			}
		}
		else {
			if (this.popupWindow && !this.popupWindow.closed) {
				this.popupWindow.close();
				this.popupWindow = null;
			}
		}
		return false;
	}
	function colorSelect(t,p) {
		if ( cp.p == p && document.getElementById(cp.divName).style.visibility != "hidden" )
			cp.hidePopup('prettyplease');
		else {
			cp.p = p;
			cp.select(t,p);
		}
	}
	function PopupWindow_setSize(width,height) {
		this.width = 162;
		this.height = 210;
	}

	var cp = new ColorPicker();
	function advUpdate(val, obj) {
		document.getElementById(obj).value = val;
		kUpdate(obj);
	}
	function kUpdate(oid) {
		if ( 'uppercolor' == oid || 'lowercolor' == oid ) {
			uc = document.getElementById('uppercolor').value.replace('#', '');
			lc = document.getElementById('lowercolor').value.replace('#', '');
			hi = document.getElementById('headerimage');
			hi.value = 'header-img.php?upper='+uc+'&lower='+lc;
			document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/'+hi.value+'") center no-repeat';
			document.getElementById('advuppercolor').value = '#'+uc;
			document.getElementById('advlowercolor').value = '#'+lc;
		}
		if ( 'fontcolor' == oid ) {
			document.getElementById('header').style.color = document.getElementById('fontcolor').value;
			document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value;
		}
		if ( 'fontdisplay' == oid ) {
			document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
		}
	}
	function toggleDisplay() {
		td = document.getElementById('fontdisplay');
		td.value = ( td.value == 'none' ) ? 'inline' : 'none';
		kUpdate('fontdisplay');
	}
	function toggleAdvanced() {
		a = document.getElementById('jsAdvanced');
		if ( a.style.display == 'none' )
			a.style.display = 'block';
		else
			a.style.display = 'none';
	}
	function kDefaults() {
		document.getElementById('headerimage').value = '';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#69aee7';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#4180b6';
		document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/xpressheader.jpg") center no-repeat';
		document.getElementById('header').style.color = '#FFFFFF';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '';
		document.getElementById('fontdisplay').value = 'inline';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kRevert() {
		document.getElementById('headerimage').value = '<?php echo js_escape(xpress_header_image()); ?>';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#<?php echo js_escape(xpress_upper_color()); ?>';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#<?php echo js_escape(xpress_lower_color()); ?>';
		document.getElementById('header').style.background = 'url("<?php echo js_escape(xpress_header_image_url()); ?>") center no-repeat';
		document.getElementById('header').style.color = '';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '<?php echo js_escape(xpress_header_color_string()); ?>';
		document.getElementById('fontdisplay').value = '<?php echo js_escape(xpress_header_display_string()); ?>';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kInit() {
		document.getElementById('jsForm').style.display = 'block';
		document.getElementById('nonJsForm').style.display = 'none';
	}
	addLoadEvent(kInit);
// ]]>
</script>
<style type='text/css'>
	#headwrap {
		text-align: center;
	}
	#xpress-header {
		font-size: 80%;
	}
	#xpress-header .hibrowser {
		width: 780px;
		height: 260px;
		overflow: scroll;
	}
	#xpress-header #hitarget {
		display: none;
	}
	#xpress-header #header h1 {
		font-family: 'Trebuchet MS', 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-weight: bold;
		font-size: 2em;
		text-align: center;
		padding-top: 70px;
		margin: 0;
	}

	#xpress-header #header .description {
		font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-size: 1.2em;
		text-align: center;
	}
	#xpress-header #header {
		text-decoration: none;
		color: <?php echo xpress_header_color_string(); ?>;
		padding: 0;
		margin: 0;
		height: 200px;
		text-align: center;
		background: url('<?php echo xpress_header_image_url(); ?>') center no-repeat;
	}
	#xpress-header #headerimg {
		margin: 0;
		height: 200px;
		width: 100%;
		display: <?php echo xpress_header_display_string(); ?>;
	}
	#jsForm {
		display: none;
		text-align: center;
	}
	#jsForm input.submit, #jsForm input.button, #jsAdvanced input.button {
		padding: 0px;
		margin: 0px;
	}
	#advanced {
		text-align: center;
		width: 620px;
	}
	html>body #advanced {
		text-align: center;
		position: relative;
		left: 50%;
		margin-left: -380px;
	}
	#jsAdvanced {
		text-align: right;
	}
	#nonJsForm {
		position: relative;
		text-align: left;
		margin-left: -370px;
		left: 50%;
	}
	#nonJsForm label {
		padding-top: 6px;
		padding-right: 5px;
		float: left;
		width: 100px;
		text-align: right;
	}
	.defbutton {
		font-weight: bold;
	}
	.zerosize {
		width: 0px;
		height: 0px;
		overflow: hidden;
	}
	#colorPickerDiv a, #colorPickerDiv a:hover {
		padding: 1px;
		text-decoration: none;
		border-bottom: 0px;
	}
	#footer_submit {
		margin-top: 20px;
		text-align: center;
	}
	#footer_form{
		padding-left: 40px;
	}

</style>
<?php
}

function xpress_theme_header_page() {
	if ( isset( $_REQUEST['saved'] ) ) echo '<div id="message" class="updated fade"><p><strong>'.__('Header Options saved.', 'xpress').'</strong></p></div>';
	if ( isset( $_REQUEST['footer_saved'] ) ) echo '<div id="message" class="updated fade"><p><strong>'.__('Footer Options saved.', 'xpress').'</strong></p></div>';
?>
<div class='wrap'>
	<div id="xpress-header">
	<h2><?php _e('Header Image and Color', 'xpress'); ?></h2>
		<div id="headwrap">
			<div id="header">
				<div id="headerimg">
					<h1><?php bloginfo('name'); ?></h1>
					<div class="description"><?php bloginfo('description'); ?></div>
				</div>
			</div>
		</div>
		<br />
		<div id="nonJsForm">
			<form method="post" action="">
				<?php wp_nonce_field('xpress-header'); ?>
				<div class="zerosize"><input type="submit" name="defaultsubmit" value="<?php echo attribute_escape(__('Save', 'xpress')); ?>" /></div>
					<label for="njfontcolor"><?php _e('Font Color:', 'xpress'); ?></label><input type="text" name="njfontcolor" id="njfontcolor" value="<?php echo attribute_escape(xpress_header_color()); ?>" /> <?php printf(__('Any CSS color (%s or %s or %s)', 'xpress'), '<code>red</code>', '<code>#FF0000</code>', '<code>rgb(255, 0, 0)</code>'); ?><br />
					<label for="njuppercolor"><?php _e('Upper Color:', 'xpress'); ?></label><input type="text" name="njuppercolor" id="njuppercolor" value="#<?php echo attribute_escape(xpress_upper_color()); ?>" /> <?php printf(__('HEX only (%s or %s)', 'xpress'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<label for="njlowercolor"><?php _e('Lower Color:', 'xpress'); ?></label><input type="text" name="njlowercolor" id="njlowercolor" value="#<?php echo attribute_escape(xpress_lower_color()); ?>" /> <?php printf(__('HEX only (%s or %s)', 'xpress'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<input type="hidden" name="hi" id="hi" value="<?php echo attribute_escape(xpress_header_image()); ?>" />
				<input type="submit" name="toggledisplay" id="toggledisplay" value="<?php echo attribute_escape(__('Toggle Text', 'xpress')); ?>" />
				<input type="submit" name="defaults" value="<?php echo attribute_escape(__('Use Defaults', 'xpress')); ?>" />
				<input type="submit" class="defbutton" name="submitform" value="&nbsp;&nbsp;<?php _e('Save', 'xpress'); ?>&nbsp;&nbsp;" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="njform" value="true" />
			</form>
		</div>
		<div id="jsForm">
			<form style="display:inline;" method="post" name="hicolor" id="hicolor" action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>">
				<?php wp_nonce_field('xpress-header'); ?>
	<input type="button" class="button-secondary" onclick="tgt=document.getElementById('fontcolor');colorSelect(tgt,'pick1');return false;" name="pick1" id="pick1" value="<?php echo attribute_escape(__('Font Color', 'xpress')); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('uppercolor');colorSelect(tgt,'pick2');return false;" name="pick2" id="pick2" value="<?php echo attribute_escape(__('Upper Color', 'xpress')); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('lowercolor');colorSelect(tgt,'pick3');return false;" name="pick3" id="pick3" value="<?php echo attribute_escape(__('Lower Color', 'xpress')); ?>"></input>
				<input type="button" class="button-secondary" name="revert" value="<?php echo attribute_escape(__('Revert', 'xpress')); ?>" onclick="kRevert()" />
				<input type="button" class="button-secondary" value="<?php echo attribute_escape(__('Advanced', 'xpress')); ?>" onclick="toggleAdvanced()" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="fontdisplay" id="fontdisplay" value="<?php echo attribute_escape(xpress_header_display()); ?>" />
				<input type="hidden" name="fontcolor" id="fontcolor" value="<?php echo attribute_escape(xpress_header_color()); ?>" />
				<input type="hidden" name="uppercolor" id="uppercolor" value="<?php echo attribute_escape(xpress_upper_color()); ?>" />
				<input type="hidden" name="lowercolor" id="lowercolor" value="<?php echo attribute_escape(xpress_lower_color()); ?>" />
				<input type="hidden" name="headerimage" id="headerimage" value="<?php echo attribute_escape(xpress_header_image()); ?>" />
				<p class="submit"><input type="submit" name="submitform" class="defbutton" value="<?php echo attribute_escape(__('Update Header &raquo;', 'xpress')); ?>" onclick="cp.hidePopup('prettyplease')" /></p>
			</form>
			<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;visibility:hidden;"> </div>
			<div id="advanced">
				<form id="jsAdvanced" style="display:none;" action="">
					<?php wp_nonce_field('xpress-header'); ?>
					<label for="advfontcolor"><?php _e('Font Color (CSS):', 'xpress'); ?> </label><input type="text" id="advfontcolor" onchange="advUpdate(this.value, 'fontcolor')" value="<?php echo attribute_escape(xpress_header_color()); ?>" /><br />
					<label for="advuppercolor"><?php _e('Upper Color (HEX):');?> </label><input type="text" id="advuppercolor" onchange="advUpdate(this.value, 'uppercolor')" value="#<?php echo attribute_escape(xpress_upper_color()); ?>" /><br />
					<label for="advlowercolor"><?php _e('Lower Color (HEX):'); ?> </label><input type="text" id="advlowercolor" onchange="advUpdate(this.value, 'lowercolor')" value="#<?php echo attribute_escape(xpress_lower_color()); ?>" /><br />
					<input type="button" class="button-secondary" name="default" value="<?php echo attribute_escape(__('Select Default Colors', 'xpress')); ?>" onclick="kDefaults()" /><br />
					<input type="button" class="button-secondary" onclick="toggleDisplay();return false;" name="pick" id="pick" value="<?php echo attribute_escape(__('Toggle Text Display', 'xpress')); ?>"></input><br />
				</form>
			</div>
		</div>
	</div>
	<div id="xpress-footer">
	<h2><?php _e('Footer', 'xpress'); ?></h2>
		<form id="footer_setting" style="display:inline;" method="post" action="">
			<div id="footer_form">
				<?php wp_nonce_field('xpress-footer'); ?>
				<?php
				if ( function_exists('register_sidebar') ){
					echo __('Set number of the sidebar to display in the footer.','xpress');
					echo '<select name="xpress_footer_sidebars_count">'."\n";
						$side_bar_num = get_option('xpress_footer_sidebars_count');
						if (empty($side_bar_num)) $side_bar_num = 0;
						for($i=0;$i<=5;$i++){
							if ($i== 0) $sel_name = __('none','xpress'); else $sel_name = $i;
							if ($i == $side_bar_num) $selected = ' selected '; else $selected = '';
							echo "\t\t\t\t<option value=\"{$i}\" label=\"{$i}\" {$selected}>{$sel_name}</option>\n";
						}
					echo "</select>\n";
				}
				?>
				<input type="hidden" name="action" value="update_footer" />
			</div>
			<div id="footer_submit">
			<input type="submit" name="footer_submit" id="footer_submit" value="<?php echo attribute_escape(__('Update Footer &raquo;', 'xpress')); ?>" />
			</div>
		</form>
	</div>

</div>
<?php } 

function footer_widgets_init() {
	if (!function_exists('register_sidebar') ) return;

	$side_bar_num = get_option('xpress_footer_sidebars_count');
	if (empty($side_bar_num)) return;
	for($i=1;$i <= $side_bar_num;$i++){
		switch($i){
			case 1:
				$name = __( 'First Footer Widget Area', 'kubrick' );
				$description = __( 'The first footer widget area', 'kubrick' );
				break;
			case 2:
				$name = __( 'Second Footer Widget Area', 'kubrick' );
				$description = __( 'The second footer widget area', 'kubrick' );
				break;
			case 3:
				$name = __( 'Third Footer Widget Area', 'kubrick' );
				$description = __( 'The third footer widget area', 'kubrick' );
				break;
			case 4:
				$name = __( 'Fourth Footer Widget Area', 'kubrick' );
				$description = __( 'The fourth footer widget area', 'kubrick' );
				break;
			case 5:
				$name = __( 'Fifth Footer Widget Area', 'kubrick' );
				$description = __( 'The fifth footer widget area', 'kubrick' );
				break;
				
		}

		register_sidebar( array(
			'name' => $name,
			'id' => 'footer-widget-area-'.$i,
			'description' => $description,
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
}

add_action( 'widgets_init', 'footer_widgets_init' );



?>
