<?php
/*
 * Created on 2008/09/04 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: redirect.php,v 1.4 2011/11/22 09:07:53 nao-pon Exp $
 */

// clear output buffer
while( ob_get_level() ) {
	ob_end_clean() ;
}

define('UNIX_TIME', (isset($_SERVER['REQUEST_TIME'])? $_SERVER['REQUEST_TIME'] : time()));

if (isset($_GET['l'])) {
	$url = $_GET['l'];
	$info = get_url_info($url);

	$mobile = $info['handheld']? '<p>Mobile: <a href="'.$info['handheld'].'">'.$info['handheld'].'</a><p>' : '';

	$lasturl = $type = $size = '';
	if ($info['header']) {
		if (preg_match('/^Content-Type:\s*(.+)$/mi', $info['header'], $match)) {
			$type = '<p>Content type: ' . htmlspecialchars($match[1]) . '</p>';
		}
		if (preg_match('/^Content-Length:\s*([\d]+)/mi', $info['header'], $match)) {
			$size = '<p>Content size: ' . floor($match[1] / 1024) . 'KB' . '</p>';
		}
	}

	if ($info['lasturl']) {
		$lasturl = '<br />(' .  htmlspecialchars($info['lasturl']) . ')';
	}

	$google = 'http://www.google.co.jp/gwt/n?u=' . rawurlencode($url);
	$url = str_replace('&amp;', '&',htmlspecialchars($_GET['l']));

	$lang = XOOPS_TRUST_PATH . '/class/hyp_common/language/' . $xoopsConfig['language'] . '/redirect.lng.php';
	if (!is_file($lang)) {
		$lang = XOOPS_TRUST_PATH . '/class/hyp_common/language/english/redirect.lng.php';
	}
	include_once $lang;

	header('Content-type: text/html; charset=Shift_JIS');
	echo '<html><head><title>' . HYP_LANG_REDIRECT_TITLE . '</title></head>' .
			'<body>' .
			'<p>' . HYP_LANG_REDIRECT_DESC . '</p>' .
			'<p><a href="'.$url.'">'.$url.'</a>'.$lasturl.'</p>' .
			$type .
			$size .
			$mobile .
			'<a href="'.$google.'">' . HYP_LANG_REDIRECT_USE_GOOGLE . '</a>' .
			'</body></html>';
}

function get_url_info ($url) {

	$ttl = 60 * 60 * 24; // 1day
	$cachepath = XOOPS_ROOT_PATH . '/class/hyp_common/cache';
	$cache = $cachepath . '/' . md5($url) . '.rdi';

	if (is_file($cache) && filemtime($cache) + $ttl > UNIX_TIME) {
		$ret = unserialize(file_get_contents($cache));
		if (isset($ret['lasturl'])) return $ret;
	}

	include_once XOOPS_TRUST_PATH . '/class/hyp_common/hyp_common_func.php';

	// GC
	$gc = $cachepath . '/rdi.gc';
	if (! is_file($gc) || filemtime($gc) < UNIX_TIME - 86400) {
		GC_rdi($cachepath, 86400 * 30);
	}


	$h = new Hyp_HTTP_Request();
	$h->url = $url;
	$h->getSize = 4096;
	$h->get();

	$ret = array(
		'header' => '',
		'handheld' => '',
		'lasturl' => ''
	);
	if ($h->rc === 200 || $h->rc === 206) {
		$html = $h->data;
		$ret['header'] = $h->header;
		$ret['lasturl'] = ($url === $h->url)? '' : $h->url;
		if (strpos($html, '<body') !== FALSE) {
			list($head, $dum) = explode('<body', $html, 2);
			if (preg_match_all('/<link [^>]*?rel=(\'|")alternate\\1[^>]*?>/i', $head, $match)) {
				foreach ($match[0] as $rel) {
					if (preg_match('/media=(\'|")handheld\\1/i', $rel) && preg_match('/href=(\'|")(.+)?\\1/i', $rel, $link)) {
						$ret['handheld'] = str_replace('&amp;', '&', $link[2]);
					}
				}
			}
		}
	}

	if ($fp = fopen($cache, 'wb')) {
		fwrite($fp, serialize($ret));
		fclose($fp);
	}

	return $ret;
}

function GC_rdi($cachepath, $TTL, $showResult = FALSE) {
	HypCommonFunc::touch($cachepath . '/rdi.gc');
	$i = 0;
	$i2 = 0;
	if ($handle = opendir($cachepath)) {
		while (false !== ($file = readdir($handle))) {
			if (substr($file, -4) === '.rdi') {
				$i2++;
				$target = $cachepath . '/' . $file;
				if (filemtime($target) < UNIX_TIME - $TTL) {
					unlink($target);
					$i++;
				}
			}
		}
		closedir($handle);
	}
	if ($showResult) echo $i . '/' . $i2 . ' files removed.';
}