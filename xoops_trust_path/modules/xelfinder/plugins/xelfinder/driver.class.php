<?php

/**
 * elFinder driver for local filesystem.
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 * @author Naoki Sawada
 **/
class elFinderVolumeXoopsXelfinder extends elFinderVolumeLocalFileSystem {

	/**
	 * Put file stat in cache and return it
	 *
	 * @param  string  $path   file path
	 * @param  array   $stat   file stat
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function updateCache($path, $stat) {
		$stat = parent::updateCache($path, $stat);
		if ($stat && $stat['mime'] !== 'directory') {
			if (strpos($path, (string) XOOPS_TRUST_PATH) === 0) {
				$stat['_localpath'] = str_replace(XOOPS_ROOT_PATH, 'T', $path );
			} else {
				$stat['_localpath'] = str_replace(XOOPS_ROOT_PATH, 'R', $path );
			}
		}
		return $this->cache[$path] = $stat;
	}
	
}