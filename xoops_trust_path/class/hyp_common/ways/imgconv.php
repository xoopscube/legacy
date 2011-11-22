<?php
/*
 * Created on 2008/07/24 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: imgconv.php,v 1.10 2011/11/22 09:07:53 nao-pon Exp $
 */

// clear output buffer
while( ob_get_level() ) {
	ob_end_clean() ;
}

$url = (isset($_GET['u']))? $_GET['u'] : '';
$mode = (isset($_GET['m']))? $_GET['m'] : '';
$maxsize = (isset($_GET['s']))? intval($_GET['s']) : 0;
$png = (isset($_GET['p']))? 1 : 0;
$gc = (isset($_GET['gc']));
$cc = (isset($_GET['cc']));
if (! $maxsize) $maxsize = 200;
define('UNIX_TIME', (isset($_SERVER['REQUEST_TIME'])? $_SERVER['REQUEST_TIME'] : time()));

switch($mode) {
	case 'i4k':
		$maxage = 86400;   // Browser side cache TTL: 1day
		$TTL = 10 * 86400; // Server side cache TTL: 10days
		if ($url) {
			if (isset($_GET['c'])) $TTL = 0;

			$basename = md5(join("\t", array($url, $maxsize, $png))) . '.i4k';
			$file = $cachepath . '/' .  $basename;
			$size_file = $file . 's';

			if (! $cc && is_file($file) && filemtime($file) + $TTL > UNIX_TIME) {
				if (filesize($file)) {
					$mime = '';
					if (is_file($size_file) && $size = file($size_file)) {
						if (isset($size[1])) {
							$mime = trim($size[1]);
						}
					}
					if (! $mime) {
						$mime = 'image';
						if ($size = getimagesize($file)) {
							save_i4ks($size_file, $size, $mime);
						}
					}

					include_once $trustpath . '/class/hyp_common/hyp_common_func.php';

					header('Content-Type: ' . $mime);
					header('Content-Length: ' . filesize($file));
					header('Cache-Control:max-age=' . $maxage);
					header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + $maxage ) . ' GMT');
					HypCommonFunc::readfile($file);
					exit();
				} else {
					header('HTTP/1.1 301 Moved Permanently');
					header('Status: 301 Moved Permanently');
					header('Location: ' . $url);
				}
				exit();
			}

			include_once $trustpath . '/class/hyp_common/hyp_common_func.php';

			// GC
			$gc = $cachepath . '/i4k.gc';
			if (! is_file($gc) || filemtime($gc) < UNIX_TIME - $maxage) {
				GC_i4k($cachepath, $TTL);
			}

			$h = new Hyp_HTTP_Request();

			$h->url = $url;
			$h->connect_timeout = 3;
			$h->read_timeout = 5;
			$h->get();
			if ($h->rc === 200) {
				if (! HypCommonFunc::flock_put_contents($file, $h->data)) {
					header('Location: ' . $url);
					exit();
				}
				clearstatcache();
				$org_size = getimagesize($file);
				if ($org_size) {
					$mime = isset($org_size['mime'])? $org_size['mime'] : 'image/' . $org_size[2];
					save_i4ks($size_file, $org_size, $mime);

					$quality = 50;
					if ($maxsize >= 300 && $org_size[0] >= 300) $quality = 30;
					if ($maxsize >= 400 && $org_size[0] >= 400) $quality = 15;
				}

				$notImageHeader = (! preg_match('#^Content-Type: *image/(?:gif|jpeg|png)#mi', $h->header));
				if (HypCommonFunc::img4ktai($file, $maxsize, $png, $notImageHeader, $quality)) {
					$size = getimagesize($file);

					$mime = 'image';
					if ($size) {
						$mime = isset($size['mime'])? $size['mime'] : 'image/' . $size[2];
						save_i4ks($size_file, $size, $mime);
					}

					header('Content-Type: ' . $mime);
					header('Content-Length: ' . filesize($file));
					header('Cache-Control:max-age=' . $maxage);
					header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + $maxage ) . ' GMT');
					HypCommonFunc::readfile($file);
					exit();
				}
				HypCommonFunc::flock_put_contents($file, '');
				header('Location: ' . $url);
				exit();
			} else {
				HypCommonFunc::flock_put_contents($file, '');
				if ($h->rc !== 404) {
					HypCommonFunc::touch($file, (UNIX_TIME - $TTL + 86400)); // Set TTL 1 day.
				}
				exit();
			}
		} else if ($gc) {
			include_once $trustpath . '/class/hyp_common/hyp_common_func.php';
			GC_i4k($cachepath, $TTL, TRUE);
		}
		break;
}

function save_i4ks($size_file, $size, $mime) {
	HypCommonFunc::flock_put_contents($size_file, $size[0] . 'x' . $size[1] . "\n" . $mime);
}

function GC_i4k($cachepath, $TTL, $showResult = FALSE) {
	HypCommonFunc::touch($cachepath . '/i4k.gc');
	$i = 0;
	$i2 = 0;
	if ($handle = opendir($cachepath)) {
		$iua = ignore_user_abort(true);
		while (false !== ($file = readdir($handle))) {
			if (substr($file, -4) === '.i4k' || substr($file, -5) === '.i4ks') {
				$i2++;
				$target = $cachepath . '/' . $file;
				$del = false;
				if ($del || filemtime($target) < UNIX_TIME - $TTL) {
					unlink($target);
					$i++;
				}
			}
		}
		closedir($handle);
		ignore_user_abort($iua);
	}
	if ($showResult) echo $i . '/' . $i2 . ' files removed.';
}