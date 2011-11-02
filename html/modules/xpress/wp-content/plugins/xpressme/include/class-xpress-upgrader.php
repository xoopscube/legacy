<?php
include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

class Xpress_Upgrader extends WP_Upgrader {

	function upgrade_strings() {
		$this->strings['up_to_date'] = __('XPressME Integration Kit is at the latest version.', 'xpressme');
		$this->strings['no_package'] = __('Upgrade package not available.', 'xpressme');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;', 'xpressme');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;', 'xpressme');
		$this->strings['copy_failed'] = __('Could not copy files.', 'xpressme');
		$this->strings['make_config'] = __('Delete source wp-config.php.', 'xpressme');
		$this->strings['delete_failed'] = __('Could not delete files.', 'xpressme');
		$this->strings['package_wrong'] = __('The structure of the package is wrong. ', 'xpressme');
	}

	function upgrade($current) {
		global $wp_filesystem;

		$this->init();
		$this->upgrade_strings();

		if ( !empty($feedback) )
			add_filter('update_feedback', $feedback);

		// Is an update available?
		if ( !isset( $current->response ) || $current->response == 'latest' )
			return new WP_Error('up_to_date', $this->strings['up_to_date']);

		$res = $this->fs_connect( array(ABSPATH, WP_CONTENT_DIR) );
		if ( is_wp_error($res) )
			return $res;

		$wp_dir = trailingslashit($wp_filesystem->abspath());

		$download = $this->download_package( $current->package );
		if ( is_wp_error($download) )
			return $download;

		$working_dir = $this->unpack_package( $download );
		if ( is_wp_error($working_dir) )
			return $working_dir;
		$kit_dir = $working_dir .'/xpressme_integration_kit/';
		if (!file_exists($kit_dir)){	// search sub dir.
			$kit_dir = '';
			$subdirs = $wp_filesystem->dirlist($working_dir,false);
			foreach($subdirs as $subdir){
				if (file_exists($working_dir .'/' .$subdir['name'] .'/xpressme_integration_kit/')){
					$kit_dir = $working_dir .'/' .$subdir['name'] .'/xpressme_integration_kit/';
					continue;
				}
			}
			if (empty($kit_dir)){
				$wp_filesystem->delete($working_dir, true);
				return new WP_Error('package_wrong', $this->strings['package_wrong']);
			}
		}
		// Copy update-core.php from the new version into place.
		$update_xpress_file = 'wp-content/plugins/xpressme/include/update_xpress.php';
		if ( !$wp_filesystem->copy($kit_dir . $update_xpress_file, $wp_dir . $update_xpress_file, true) ) {
			$wp_filesystem->delete($working_dir, true);
			return new WP_Error('copy_failed', $this->strings['copy_failed']);
		}
		$wp_filesystem->chmod($wp_dir . $update_xpress_file, FS_CHMOD_FILE);

		require(ABSPATH . $update_xpress_file);
		return update_xpress($kit_dir, $wp_dir);
	}
}
?>