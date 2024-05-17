<?php
/**
 * Sitemap block map and address
 * Customize block with map and address
 * @package    Sitemap
 * @version    2.4.0
 * @author     gigamaster, 2020 XCL/PHP7
 * @copyright  (c) 2005-2024 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/docs/GPL_V2.txt
 */

function b_sitemap_map_show( $options )
{
	global $xoopsConfig, $xoopsDB, $xoopsUser, $xoopsUserIsAdmin;
	global $sitemap_configs ;

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname('sitemap');
	$config_handler =& xoops_gethandler('config');
	$sitemap_configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	$myts =& MyTextSanitizer::getInstance();
	
	$block_map = [];
	
	// options
	$show_site_name		= empty( $options[0] ) ? false : true;
	$show_site_slogan	= empty( $options[1] ) ? false : true;
	$show_site_map		= empty( $options[2] ) ? false : true;
	$show_site_address	= empty( $options[3] ) ? false : true;


	if( $show_site_name == 1 ) {	
		$show_site_name  = $myts->makeTboxData4Show($xoopsConfig['sitename']);
	}
	if( $show_site_slogan == 1 ) {
		$show_site_slogan  = $myts->makeTboxData4Show($xoopsConfig['slogan']);
	}
	if( $show_site_map == 1 ) {
		$show_site_map     = $myts->displayTarea( $sitemap_configs['show_map'] , 1 ) ;
	}
	if( $show_site_address == 1 ) {
		$show_site_address  = $myts->displayTarea( $sitemap_configs['show_address'] , 1 ) ;
	}

	$myts =& MyTextSanitizer::getInstance();

	$block_map = [
		'sitename'		=> $show_site_name ?? '',
		'slogan'		=> $show_site_slogan ?? '',
		'map'			=> $show_site_map ?? '',
		'address'		=> $show_site_address ?? '',
	];

	return $block_map;
}

function b_sitemap_map_edit( $options ) {

	$show_site_name 	= empty( $options[0] ) ? false : true;
	$show_site_slogan	= empty( $options[1] ) ? false : true;
	$show_site_map		= empty( $options[2] ) ? false : true;
	$show_site_address  = empty( $options[3] ) ? false : true;

	if ( $show_site_name ) {
		$site_yes = "checked";
		$site_no  = '';
	} else {
		$site_no  = "checked'";
		$site_yes = '';
	}

	if ( $show_site_slogan ) {
		$slogan_yes = "checked";
		$slogan_no  = '';
	} else {
		$slogan_no  = "checked'";
		$slogan_yes = '';
	}

	if ( $show_site_map ) {
		$map_yes = "checked";
		$map_no  = '';
	} else {
		$map_no  = "checked'";
		$map_yes = '';
	}

	if ( $show_site_address ) {
		$address_yes = "checked";
		$address_no  = '';
	} else {
		$address_no  = "checked'";
		$address_yes = '';
	}

	return "<p>"._MI_SHOW_SITENAME."
	<label for='site_yes'><input type='radio' name='options[0]' id='site_yes' value='1' $site_yes>" . _YES . "</label>
<label for='site_no'><input type='radio' name='options[0]' id='site_no' value='0' $site_no>" . _NO . "</label></p>

<p>"._MI_SHOW_SLOGAN."
<label for='slogan_yes'><input type='radio' name='options[1]' id='slogan_yes' value='1' $slogan_yes>" . _YES . "</label>
<label for='slogan_no'><input type='radio' name='options[1]' id='slogan_no' value='0' $slogan_no>" . _NO . "</label></p>

<p>"._MI_SHOW_MAP."
<label for='map_yes'><input type='radio' name='options[2]' id='map_yes' value='1' $map_yes>" . _YES . "</label>
<label for='map_no'><input type='radio' name='options[2]' id='map_no' value='0' $map_no>" . _NO . "</label></p>

<p>"._MI_SHOW_ADDRESS."
<label for='address_yes'><input type='radio' name='options[3]' id='address_yes' value='1' $address_yes>" . _YES . "</label>
<label for='address_no'><input type='radio' name='options[3]' id='address_no' value='0' $address_no>" . _NO . "</label>
</p>";

}
