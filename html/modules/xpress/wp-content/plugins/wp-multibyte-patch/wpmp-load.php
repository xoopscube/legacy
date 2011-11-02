<?php
// To load WP Multibyte Patch as a must-use plugin, this file must be directly under the mu-plugins directory.
if(!defined('WP_INSTALLING') && defined('WPMU_PLUGIN_DIR') && defined('WPMU_PLUGIN_URL'))
	require_once(WPMU_PLUGIN_DIR . '/wp-multibyte-patch/wp-multibyte-patch.php');
?>