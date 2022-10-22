<?php
if (version_compare(PHP_VERSION, '5.4.0', '>=') && class_exists('\Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter')) {
	if (! isset($extOptions['ext_cache_expire']) || $extOptions['ext_cache_expire']) {
		if (! trait_exists('\Hypweb\Flysystem\Cached\Extra\Hasdir') ||
			! trait_exists('\Hypweb\Flysystem\Cached\Extra\DisableEnsureParentDirectories')) {
			if ($this->isAdmin) {
				throw new Exception('[Error: X-elFinder] Need vendor update.');
			}
			return;
		}
	}
	include __DIR__ . '/volume_real.php';
}
