<?php 
// $Id: xoops_version.php,v 1.8 2005/06/03 01:35:02 phppp Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: phppp (D.J.)                                                      //
// URL: http://xoopsforge.com, http://xoops.org.cn                           //
// ------------------------------------------------------------------------- //
//include_once 'cp_functions.php';

function admin_check_user_meta_prefix($is_report = false){
	global $xoopsModule;
	$xoopsDB =& Database::getInstance();
	
	$mydirname = basename(dirname(dirname(__FILE__)));
	$my_dirpath = dirname(dirname(__FILE__));
	$wp_prefix_only = preg_replace('/wordpress/','wp',$mydirname);
	$db_prefix = $xoopsDB->prefix($wp_prefix_only);

	$usermeta_tbl = $db_prefix . '_usermeta';	
	$meta_key_pattern = '_' . $wp_prefix_only . '_';

	$sql = "SELECT count(umeta_id) as data_count ,meta_key FROM $usermeta_tbl GROUP BY meta_key HAVING meta_key LIKE '%" . $meta_key_pattern ."%'" ;
	$res =  $xoopsDB->query($sql, 0, 0);
	
	if ($res === false){
		$check_str = _AM_XP2_USER_META_NONE . "<br />\n";
	} else {
		$error =false;
		$check_str = '';
		while($row = $xoopsDB->fetchArray($res)){
			$data_count  = $row['data_count'];
			$meta_key = $row['meta_key'];
			if ( !preg_match('/^'.$db_prefix. '_.*/',$meta_key , $maches)){
				$check_str .= sprintf(_AM_XP2_USER_META_ERR , $meta_key,$data_count) ."<br /> \n";
				$error = true;
			}
		}
		if (!$error)
			$check_str = _AM_XP2_USER_META_OK ;
	}
	if ($is_report) {
		echo "******** "  . _AM_XP2_USER_META_KEY . "********" . "<br />\n";
		echo $check_str . "<br />\n<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_USER_META_KEY . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo $check_str;
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}

}

function get_xpress_plugin_data( $plugin_file, $markup = true, $translate = true ) {
	// We don't need to write to the file, so just open for reading.
	$fp = fopen($plugin_file, 'r');

	// Pull only the first 8kiB of the file in.
	$plugin_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose($fp);

	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
	preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
	preg_match( '|Version:(.*)|i', $plugin_data, $version );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );
	preg_match( '|Text Domain:(.*)$|mi', $plugin_data, $text_domain );
	preg_match( '|Domain Path:(.*)$|mi', $plugin_data, $domain_path );

	foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_uri', 'text_domain', 'domain_path' ) as $field ) {
		if ( !empty( ${$field} ) )
			${$field} = trim(${$field}[1]);
		else
			${$field} = '';
	}

	$plugin_data = array(
				'Name' => $name, 'Title' => $name, 'PluginURI' => $uri, 'Description' => $description,
				'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version,
				'TextDomain' => $text_domain, 'DomainPath' => $domain_path
				);
//	if ( $markup || $translate )
//		$plugin_data = _get_plugin_data_markup_translate($plugin_data, $markup, $translate);
	return $plugin_data;
}


function get_xpress_active_plugin_list($before_str = '')
{
	global $xoopsModule;
	$xoopsDB =& Database::getInstance();
	
	$mydirname = basename(dirname(dirname(__FILE__)));
	$my_dirpath = dirname(dirname(__FILE__));
	$prefix = preg_replace('/wordpress/','wp',$mydirname);
	$wp_prefix = $xoopsDB->prefix($prefix);

	$option_table = $wp_prefix . '_options';
	
	$sql = "SELECT option_value FROM $option_table WHERE option_name = 'active_plugins'";
	$res =  $xoopsDB->query($sql, 0, 0);
	if ($res === false){
	    return ;
	} else {
		$row = $xoopsDB->fetchArray($res);
		$active_plugins = @unserialize($row['option_value']);
		$output = '';
		foreach($active_plugins as $active_plugin_path){
			$file_name =  $my_dirpath . '/wp-content/plugins/' . $active_plugin_path;
			$active_plugin = get_xpress_plugin_data($file_name);
			$output .= $before_str . $active_plugin['Name'] . ':   Version ' . $active_plugin['Version'] . ':  (' .$active_plugin['PluginURI'] . ')<br />';
		}
		
		
		return $output;
	}

	
}

function xpress_active_plugin_list($is_report = false)
{
	if ($is_report) {
		echo "******** "  . _AM_XP2_PLUGIN . "********" . "<br />\n";
		echo get_xpress_active_plugin_list('') . "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_PLUGIN . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo get_xpress_active_plugin_list();
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}
}

function xpress_sys_info($is_report = false)
{
	global $xoopsModule;
	include(dirname(__FILE__) . '/../wp-includes/version.php');
	require_once dirname(dirname( __FILE__ )).'/include/memory_limit.php' ;

	if ($is_report) {
		echo "******** "  . _AM_XP2_SYSTEM_INFO . "********" . "<br />\n";
		echo "SERVER:  ". $_SERVER['SERVER_SOFTWARE']. "<br />\n";
		echo "PHP Version:   " . phpversion() . "<br />\n";
		echo 'libxml Version:  ';
		if (defined('LIBXML_DOTTED_VERSION')) echo LIBXML_DOTTED_VERSION ; else echo "Can't detect.";
		echo "<br />\n";;
		echo "MySQL Version:   " . mysql_get_server_info() . "</text><br />";
		echo "XOOPS Version:   " . XOOPS_VERSION . "</text><br />";
		echo "XPressME Version:   " . $xoopsModule->getInfo('version') . ' ' . $xoopsModule->getInfo('codename') . "<br />\n";
		echo "WordPress Version:   " . $wp_version . "<br />\n";
		echo "WP DB Version:   " . $wp_db_version . "<br />\n";
		echo "<br />\n";
		echo "safemode:   " ;
		echo ( ini_get( 'safe_mode' ) ) ? "ON" : "OFF";
		echo "<br />\n";
		echo "register_globals:   " ;
		echo ( ini_get( 'register_globals' )) ? "ON" : "OFF" ;
		echo "<br />\n";
		echo "allow_url_fopen:   " ;
		echo ( ini_get( 'allow_url_fopen' )) ? "ON" : "OFF" ;
		echo "<br />\n";
		echo "magic_quotes_gpc:   " ;
		echo ( ini_get( 'magic_quotes_gpc' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "XML extension:   " ;
		echo ( extension_loaded( 'xml' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "default memory_limit:   " ;
		echo  ini_get( 'memory_limit' );
		echo "<br />\n";
		xpress_set_memory_limmit();
		echo "change memory_limit:   " ;
		echo  ini_get( 'memory_limit' );
		echo "<br />\n";
		
		echo "post_max_size:   " ;
		echo  ini_get( 'post_max_size' );
		echo "<br />\n";
		echo "upload_max_filesize:   " ;
		echo  ini_get( 'upload_max_filesize' );
		echo "<br />\n";
		echo "display_errors:   " ;
		echo ( ini_get( 'display_errors' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "MB extension:   " ;
		echo ( extension_loaded( 'mbstring' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "mbstring.language:   " ;
		echo  ini_get( 'mbstring.language' );
		echo "<br />\n";
		echo "mbstring.encoding_translation:   " ;
		echo  ( ini_get( 'mbstring.encoding_translation' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "mbstring.internal_encoding:   " ;
		echo  ini_get( 'mbstring.internal_encoding' );
		echo "<br />\n";
		echo "mbstring.http_input:   " ;
		echo  ini_get( 'mbstring.http_input' );
		echo "<br />\n";
		echo "mbstring.http_output:   " ;
		echo  ini_get( 'mbstring.http_output' );
		echo "<br />\n";
		echo "mbstring.detect_order:   " ;
		echo  ini_get( 'mbstring.detect_order' );
		echo "<br />\n";
		echo "mbstring.substitute_character:   " ;
		echo  ini_get( 'mbstring.substitute_character' );
		echo "<br />\n";
		echo "mbstring.func_overload:   " ;
		echo  ( ini_get( 'mbstring.func_overload' )) ? "ON" : "OFF";
		echo "<br />\n";
		echo "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_SYSTEM_INFO . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo "<label>" . "<strong>SERVER:</strong>" . ":</label><text>" . $_SERVER['SERVER_SOFTWARE'] . "</text><br />";
		echo "<label>" . "<strong>PHP Version:</strong>" . ":</label><text>" . phpversion() . "</text><br />";
		echo "<label>" . "<strong>libxml Version:</strong>" . ":</label><text>";
		if (defined('LIBXML_DOTTED_VERSION')) echo LIBXML_DOTTED_VERSION ; else echo "Can't detect.";
		echo "</text><br />";
		echo "<label>" . "<strong>MySQL Version:</strong>" . ":</label><text>" . mysql_get_server_info() . "</text><br />";
		echo "<label>" . "<strong>XOOPS Version:</strong>" . ":</label><text>" . XOOPS_VERSION . "</text><br />";
		echo "<label>" . "<strong>XPressME Version:</strong>" . ":</label><text>" . $xoopsModule->getInfo('version') . ' ' . $xoopsModule->getInfo('codename') . "</text><br />";
		echo "<label>" . "<strong>WordPress Version:</strong>" . ":</label><text>" . $wp_version . "</text><br />";
		echo "<label>" . "<strong>WP DB Version:</strong>" . ":</label><text>" . $wp_db_version . "</text><br />";

		echo "</div>";
		echo "<div style='padding: 8px;'>";
		echo "<label>safemode:</label><text>";
		echo ( ini_get( 'safe_mode' ) ) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>register_globals:</label><text>";
		echo ( ini_get( 'register_globals' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>allow_url_fopen:</label><text>";
		echo ( ini_get( 'allow_url_fopen' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>magic_quotes_gpc:</label><text>";
		echo ( ini_get( 'magic_quotes_gpc' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>XML extension:</label><text>";
		echo ( extension_loaded( 'xml' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>default memory_limit:</label><text>";
		echo  ini_get( 'memory_limit' );
		echo "</text><br />";
		xpress_set_memory_limmit();
		echo "<label>change memory_limit:</label><text>";
		echo  ini_get( 'memory_limit' );
		echo "</text><br />";

		echo "<label>post_max_size:</label><text>";
		echo  ini_get( 'post_max_size' );
		echo "</text><br />";
		echo "<label>upload_max_filesize:</label><text>";
		echo  ini_get( 'upload_max_filesize' );
		echo "</text><br />";
		echo "<label>display_errors:</label><text>";
		echo ( ini_get( 'display_errors' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>MB extension:</label><text>";
		echo ( extension_loaded( 'mbstring' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>mbstring.language:</label><text>";
		echo  ini_get( 'mbstring.language' );
		echo "</text><br />";
		echo "<label>mbstring.encoding_translation:</label><text>";
		echo  ( ini_get( 'mbstring.encoding_translation' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "<label>mbstring.internal_encoding:</label><text>";
		echo  ini_get( 'mbstring.internal_encoding' );
		echo "</text><br />";
		echo "<label>mbstring.http_input:</label><text>";
		echo  ini_get( 'mbstring.http_input' );
		echo "</text><br />";
		echo "<label>mbstring.http_output:</label><text>";
		echo  ini_get( 'mbstring.http_output' );
		echo "</text><br />";
		echo "<label>mbstring.detect_order:</label><text>";
		echo  ini_get( 'mbstring.detect_order' );
		echo "</text><br />";
		echo "<label>mbstring.substitute_character:</label><text>";
		echo  ini_get( 'mbstring.substitute_character' );
		echo "</text><br />";
		echo "<label>mbstring.func_overload:</label><text>";
		echo  ( ini_get( 'mbstring.func_overload' )) ? "ON" : "OFF";
		echo "</text><br />";
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}
}


function xpress_config_report_view()
{
	require_once dirname(dirname( __FILE__ )).'/class/config_from_xoops.class.php' ;
	$xoops_config = new ConfigFromXoops;
	echo 'XOOPS_ROOT_PATH:  ' ; 
	if(XOOPS_ROOT_PATH !== $xoops_config->xoops_root_path)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_TRUST_PATH:  ' ; 
	if(XOOPS_TRUST_PATH !== $xoops_config->xoops_trust_path)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_URL:  ' ; 
	if(XOOPS_URL !== $xoops_config->xoops_url)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	if (defined('XOOPS_SALT')){
		echo 'XOOPS_SALT:  ' ; 
		if(XOOPS_SALT !== $xoops_config->xoops_salt)
			echo 'ERROR ';
		else
			echo 'OK ';	
		echo "<br />\n";
	}

	if (defined('XOOPS_DB_SALT')){
		echo 'XOOPS_DB_SALT:  ' ; 
		if(XOOPS_DB_SALT !== $xoops_config->xoops_db_salt)
			echo 'ERROR ';
		else
			echo 'OK ';	
		echo "<br />\n";
	}

	echo 'XOOPS_DB_HOST:  ' ; 
	if(XOOPS_DB_HOST !== $xoops_config->xoops_db_host)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_DB_USER:  ' ; 
	if(XOOPS_DB_USER !== $xoops_config->xoops_db_user)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_DB_PASS:  ' ; 
	if(XOOPS_DB_PASS !== $xoops_config->xoops_db_pass)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_DB_NAME:  ' ; 
	if(XOOPS_DB_NAME !== $xoops_config->xoops_db_name)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";

	echo 'XOOPS_DB_PREFIX:  ' ; 
	if(XOOPS_DB_PREFIX !== $xoops_config->xoops_db_prefix)
		echo 'ERROR ';
	else
		echo 'OK ';	
	echo "<br />\n";
}

function xpress_config_nomal_view()
{
	require_once dirname(dirname( __FILE__ )).'/class/config_from_xoops.class.php' ;
	$xoops_config = new ConfigFromXoops;
	
	echo '<table width="400" cellspacing="1" cellpadding="1" border="1">';
	echo '<tbody>';
	echo '<tr>';
	echo '<td>Define item</td>';
	echo '<td>XOOPS setting value</td>';
	echo '<td>xoops_config get value</td>';
	echo '</tr>';
	echo '<tr>';
	if(XOOPS_ROOT_PATH !== $xoops_config->xoops_root_path)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_ROOT_PATH</span></strong></td>';
	else
		echo '<td>XOOPS_ROOT_PATH</td>';	
	echo '<td>' . XOOPS_ROOT_PATH . '</td>';
	echo '<td>' . $xoops_config->xoops_root_path . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_TRUST_PATH !== $xoops_config->xoops_trust_path)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_TRUST_PATH</span></strong></td>';
	else
		echo '<td>XOOPS_TRUST_PATH</td>';	
	echo '<td>' . XOOPS_TRUST_PATH . '</td>';
	echo '<td>' . $xoops_config->xoops_trust_path . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_URL !== $xoops_config->xoops_url)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_URL</span></strong></td>';
	else
		echo '<td>XOOPS_URL</td>';	
	echo '<td>' . XOOPS_URL . '</td>';
	echo '<td>' . $xoops_config->xoops_url . '</td>';
	echo '</tr>';

	if (defined('XOOPS_SALT')){
		echo '<tr>';
		if(XOOPS_SALT !== $xoops_config->xoops_salt)
			echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_SALT</span></strong></td>';
		else
			echo '<td>XOOPS_SALT</td>';
		echo '<td>' . XOOPS_SALT . '</td>';
		echo '<td>' . $xoops_config->xoops_salt . '</td>';
		echo '</tr>';
	}

	if (defined('XOOPS_DB_SALT')){
		echo '<tr>';
		if(XOOPS_DB_SALT !== $xoops_config->xoops_db_salt)
			echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_SALT</span></strong></td>';
		else
			echo '<td>XOOPS_DB_SALT</td>';
		echo '<td>' . XOOPS_DB_SALT . '</td>';
		echo '<td>' . $xoops_config->xoops_db_salt . '</td>';
		echo '</tr>';
	}

	echo '<tr>';
	if(XOOPS_DB_HOST !== $xoops_config->xoops_db_host)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_HOST</span></strong></td>';
	else
		echo '<td>XOOPS_DB_HOST</td>';
	echo '<td>' . XOOPS_DB_HOST . '</td>';
	echo '<td>' . $xoops_config->xoops_db_host . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_DB_USER !== $xoops_config->xoops_db_user)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_USER</span></strong></td>';
	else
		echo '<td>XOOPS_DB_USER</td>';
	echo '<td>' . XOOPS_DB_USER . '</td>';
	echo '<td>' . $xoops_config->xoops_db_user . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_DB_PASS !== $xoops_config->xoops_db_pass)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_PASS</span></strong></td>';
	else
		echo '<td>XOOPS_DB_PASS</td>';
	echo '<td>' . XOOPS_DB_PASS . '</td>';
	echo '<td>' . $xoops_config->xoops_db_pass . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_DB_NAME !== $xoops_config->xoops_db_name)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_NAME</span></strong></td>';
	else
		echo '<td>XOOPS_DB_NAME</td>';
	echo '<td>' . XOOPS_DB_NAME . '</td>';
	echo '<td>' . $xoops_config->xoops_db_name . '</td>';
	echo '</tr>';

	echo '<tr>';
	if(XOOPS_DB_PREFIX !== $xoops_config->xoops_db_prefix)
		echo '<td><strong><span style="color: rgb(255, 0, 0);">XOOPS_DB_PREFIX</span></strong></td>';
	else
		echo '<td>XOOPS_DB_PREFIX</td>';
	echo '<td>' . XOOPS_DB_PREFIX . '</td>';
	echo '<td>' . $xoops_config->xoops_db_prefix . '</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
}
function xpress_config_from_xoops_view($is_report = false)
{
	global $xoopsUserIsAdmin,$xoopsUser;

	$user_groups = $xoopsUser->getGroups();
	$is_admin_group = in_array('1',$user_groups);
	
	require_once dirname(dirname( __FILE__ )).'/class/config_from_xoops.class.php' ;
	$xoops_config = new ConfigFromXoops;
	if ($is_report) {
		echo "******** "  . _AM_XP2_XOOPS_CONFIG_INFO . "********" . "<br />\n";
		xpress_config_report_view();
		echo "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_XOOPS_CONFIG_INFO . "</legend>";
		echo "<div style='padding: 8px;'>";
		if ($xoopsUserIsAdmin && $is_admin_group){
			xpress_config_nomal_view();
		} else {
			xpress_config_report_view();
		}
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}
}

function xpress_state($is_report = false)
{
	global $xoopsModule;
	include(dirname(__FILE__) . '/../wp-includes/version.php');
	include_once(dirname(__FILE__) . '/../include/general_functions.php');

	$xoopsDB =& Database::getInstance();
	
	$xp_prefix = $GLOBALS['xoopsModule']->getInfo('dirname');
	$xp_prefix = preg_replace('/wordpress/','wp',$xp_prefix);

	$prefix = $xoopsDB->prefix($xp_prefix . '_');
	
	$posts_tables = get_table_list($prefix,'posts');
	$blogname = array();
	$count_article = array();
	$count_author = array();
	$count_category = array();
	$array_index = 0;
	foreach( $posts_tables as $posts_table){
		$sql = "SELECT COUNT(DISTINCT post_author) AS count_author, COUNT(*) AS count_article FROM ". $posts_table . " WHERE post_type = 'post' AND (post_status = 'publish' OR post_status = 'private')";
		$result = $xoopsDB->query($sql);
		if($myrow = $xoopsDB->fetchArray($result)){
			$count_article[$array_index] = $myrow["count_article"];
			$count_author[$array_index] = $myrow["count_author"];
		} else {
			$count_article[$array_index] = 0;
			$count_author[$array_index] = 0;
		}
		$mid_prefix = get_multi_mid_prefix($prefix,'posts' , $posts_table);
		
		$sql = "SELECT option_value AS blogname FROM ".$prefix. $mid_prefix . "options" . " WHERE option_name = 'blogname'";
		$result = $xoopsDB->query($sql);
		if($myrow = $xoopsDB->fetchArray($result)){
			$blogname[$array_index] = $myrow["blogname"];
		} else {
			$blogname[$array_index] = 'none name';
		}
		
		if ($wp_db_version < 6124){
			
			$sql = "SELECT COUNT(*) AS count_category FROM ".$prefix. $mid_prefix . "categories";
		} else {
			$sql = "SELECT COUNT(*) AS count_category FROM ".$prefix. $mid_prefix . "term_taxonomy" . " WHERE taxonomy = 'category'";
		}
		$result = $xoopsDB->query($sql);
		if($myrow = $xoopsDB->fetchArray($result)){
			$count_category[$array_index] = $myrow["count_category"];
		} else {
			$count_category[$array_index] = 0;
		}
		$array_index++;
	}
	for ($i = 0 ; $i < $array_index ; $i++){
		if ($is_report){
			echo "******** " . $blogname[$i] . _AM_XP2_STATS . "********" . "<br />\n";
			echo _AM_XP2_CATEGORIES .":  ".@$count_category[$i]. "<br />\n";
			echo _AM_XP2_ARTICLES .":  ". $count_article[$i]. "<br />\n";
			echo _AM_XP2_AUTHORS .":  ". $count_author[$i]. "<br />\n";
			
		} else {
			echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . $blogname[$i] . _AM_XP2_STATS . "</legend>";
			echo "<div style='padding: 8px;'>";
			echo "<label>" . _AM_XP2_CATEGORIES .":</label><text>".@$count_category[$i];
			echo "</text><br />";
			echo "<label>" . _AM_XP2_ARTICLES .":</label><text>". $count_article[$i];
			echo "</text><br />";
			echo "<label>" . _AM_XP2_AUTHORS .":</label><text>". $count_author[$i];
			echo "</text>";
			echo "</div>";
			echo '</legend>';
			echo "</fieldset>";
		}
	}
}

function xpress_group_role_state($is_report = false)
{
	global $xoopsModule;
	$xoopsDB =& Database::getInstance();
	$xp_prefix = $GLOBALS['xoopsModule']->getInfo('dirname');
	$xp_prefix = preg_replace('/wordpress/','wp',$xp_prefix);
	$prefix = $xoopsDB->prefix($xp_prefix . '_');
	$group_role_tables = $prefix.'group_role';
	$sql = "SELECT groupid , name AS xoops_groupe ,group_type, role , login_all FROM ". $group_role_tables;
	$result = $xoopsDB->query($sql);
	if ($is_report){
		echo "******** " . _AM_XP2_GROUP_ROLE . "********" . "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_GROUP_ROLE . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo '<table width="400" cellspacing="1" cellpadding="1" border="1">';
		echo '<tbody>';
		echo '<tr>';
		echo '<td>GROUP</td>';
		echo '<td>GROUPE TYPE</td>';
		echo '<td>ROLE</td>';
		echo '<td>Allways Check</td>';
		echo '</tr>';
	}
	$groupe_list = '';
	while ($myrow = $xoopsDB->fetchArray($result)){
		$xoops_groupe = $myrow["xoops_groupe"] ;
		$group_type = empty($myrow["group_type"]) ? "None" : $myrow["group_type"] ;
		$role = empty($myrow["role"]) ? "inhibit register" : $myrow["role"] ;
		$login_all = empty($myrow["login_all"]) ? 'No' : 'Yes' ;
		if (!empty($groupe_list)) $groupe_list .= ',';
		$groupe_list .= $myrow["groupid"];
		if ($is_report){
			echo $xoops_groupe . ' : ';
			echo '(' . $group_type. ') : ';
			echo $role;
			echo '(' . $login_all. ') : ';
			echo '<br />';
		} else {
			echo '<tr>';
			echo '<td>' . $xoops_groupe . '</td>';
			echo '<td>' . $group_type . '</td>';
			echo '<td>' . $role . '</td>';
			echo '<td>' . $login_all . '</td>';
			echo '</tr>';
		}
	}
	if ($is_report){
		echo "<br />";
	} else {
		echo '</tbody>';
		echo '</table>';
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}
}
function xpress_block_state($is_report = false)
{
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

	include_once(dirname(dirname(__FILE__) ). '/class/check_blocks_class.php');
	$xoops_block_check =& xoops_block_check::getInstance();
	$xoops_block_check->check_blocks($mydirname);
	if ($is_report){
		echo "******** " . _AM_XP2_BLOCK_STATS . "********" . "<br />\n";
		echo $xoops_block_check->get_message();
		echo "<br />\n";
		echo "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_BLOCK_STATS . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo $xoops_block_check->get_message();
		echo "</div>";
		echo '</legend>';
		echo "</fieldset><br />";
	}		
}

function xpress_block_options($is_report = false)
{
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

	$module_objs = & get_module_objects($mydirname);
	$module_obj = $module_objs[0];
	$mod_id = $module_obj->getVar('mid', 'n');
	$blocks = & get_block_object_orber_num_bymodule_id( $mod_id );
	$infos    =& $module_obj->getInfo('blocks');
	if ($is_report){
		echo "******** " . _AM_XP2_BLOCK_OPTIONS . "********" . "<br />\n";
		foreach ( $blocks as $block )
		{
			echo $block->getVar('title') . ' : ' . $block->getVar('options') . '<br />';
		}
		echo "<br />\n";
	} else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XP2_BLOCK_OPTIONS . "</legend>";
		echo "<div style='padding: 8px;'>";
		echo '<table width="400" cellspacing="1" cellpadding="1" border="1">';
		echo '<tbody>';
		echo '<tr>';
		echo '<td>Title</td>';
		echo '<td>Options</td>';
		echo '</tr>';
		foreach ( $blocks as $block )
		{
			echo '<tr>';
			echo '<td>' . $block->getVar('title') . '</td>';
			echo '<td>' . $block->getVar('options') . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
		echo '</legend>';
		echo "</fieldset><br />";
	}

}
//--------------------------------------------------------
// module handler
//--------------------------------------------------------
function &get_module_objects($module_dir)
{
	$criteria = new CriteriaCompo();
	$criteria->add( new Criteria('isactive', '1', '=') );
	$criteria->add( new Criteria('dirname', $module_dir, '=') );

	$module_handler =& xoops_gethandler('module');
	$objs           =& $module_handler->getObjects( $criteria );
	return $objs;
}

//--------------------------------------------------------
// block handler
//--------------------------------------------------------
function &get_block_object_orber_num_bymodule_id( $mid )
{
	$arr  = array();
	$objs =& get_block_object_bymodule_id( $mid );
	foreach ( $objs as $obj )
	{
		$arr[ $obj->getVar('func_num', 'n') ] = $obj;
	}
	return $arr;
}

function &get_block_object_bymodule_id( $mid, $asobject=true )
{
	if ( defined('ICMS_VERSION_BUILD') && ICMS_VERSION_BUILD > 27  ) { /* ImpressCMS 1.2+ */
		$block_handler =& xoops_gethandler ('block');
		$objs =& $block_handler->getByModule( $mid, $asobject );
	} else { /* legacy support */
		$objs =& XoopsBlock::getByModule( $mid, $asobject ) ; /* from class/xoopsblock.php */
	}
	return $objs;
}



$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$mydirpath = dirname( dirname( __FILE__ ) ) ;
//require_once($mydirpath.'/wp-config.php');
	
require_once '../../../include/cp_header.php' ;
//require_once '../include/gtickets.php' ;
//define( '_MYMENU_CONSTANT_IN_MODINFO' , '_MI_XP2_NAME' ) ;

// branch for altsys
if( defined( 'XOOPS_TRUST_PATH' ) && ! empty( $_GET['lib'] ) ) {

	// common libs (eg. altsys)
	$lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;
	
	if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
	} else {
		die( 'wrong request' ) ;
	}
	exit ;
}

// beggining of Output
xoops_cp_header();
include( './mymenu.php' ) ;

echo "
	<style type=\"text/css\">
	label,text {
		display: block;
		float: left;
		margin-bottom: 2px;
	}
	label {
		text-align: right;
		width: 200px;
		padding-right: 20px;
	}
	br {
		clear: left;
	}
	</style>
";

if (!empty($_POST['submit_report'])) $report = true; else $report = false;
xpress_sys_info($report);
xpress_config_from_xoops_view($report);
xpress_active_plugin_list($report);
xpress_block_state($report);
xpress_block_options($report);
xpress_group_role_state($report);
admin_check_user_meta_prefix($report);
xpress_state($report);
echo '<form method="POST">'."\n";
echo '<input type="submit" name="submit_report" value="' . _AM_XP2_SYS_REPORT .' " />'.'&emsp;';
echo '<input type="submit" name="submit_normal" value="' . _AM_XP2_SYS_NORMAL .' " />'."<br />\n";
echo "</form>\n";

xoops_cp_footer();

	
?>