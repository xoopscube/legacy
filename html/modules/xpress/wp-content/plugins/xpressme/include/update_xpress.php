<?php
/**
 * XPressME Integration Kit upgrade functionality.
 */

/**
 * Stores files to be deleted.
 */
global $_old_xpress_files;

$_old_xpress_files = array(
// 2.03	
'wp-content/themes/xpress_default/images/titleline.jpg',
'wp-content/themes/xpress_default/ja.mo',
'wp-content/themes/xpress_default/ja.po',
// 2.3.0
'wp-content/themes/xpress_default/ja_EUC.mo',
'wp-content/themes/xpress_default/ja_EUC.po',
'wp-content/themes/xpress_default/ja_UTF.mo',
'wp-content/themes/xpress_default/ja_UTF.po',
);

/**
 * Upgrade the XPressME .
 *
 * @param string $from New release unzipped path.
 * @param string $to Path to old WordPress installation.
 * @return WP_Error|null WP_Error on failure, null on success.
 */
function update_xpress($from, $to) {
	global $wp_filesystem, $_old_xpress_files, $wpdb;
	show_message( __('Disable overwrite of wp-config.php...', 'xpressme') );
	// remove wp-config.php from the new version into place.
	$wp_config = $from . 'wp-config.php';
	if ( !$wp_filesystem->delete($wp_config, true)){
		return new WP_Error('delete_failed', $this->strings['delete_failed']);
	}

	// Copy new versions of XPressME Integration Kit files into place.
	show_message( __('Copy new versions of XPressME Integration Kit files into place...', 'xpressme') );
	$result = copy_dir($from . $distro, $to);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($maintenance_file);
		$wp_filesystem->delete($from, true);
		return $result;
	}

	// Remove old files
	show_message( __('Remove an unnecessary, old file...', 'xpressme') );
	foreach ( $_old_xpress_files as $old_file ) {
		$old_file = $to . $old_file;
		if ( !$wp_filesystem->exists($old_file) )
			continue;
		$wp_filesystem->delete($old_file, true);
	}
	show_message( __('Set templates directory chmod 777', 'xpressme') );
	$wp_filesystem->chmod($to . 'templates/', 0777);

	// Remove working directory
	$working_dir = dirname(dirname($from));
	show_message( sprintf(__('Remove working directory(%s)...', 'xpressme'),$working_dir) );
	$wp_filesystem->delete($working_dir, true);

}

?>
