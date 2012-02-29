<?php
//
// Created on 2006/10/25 by nao-pon http://hypweb.net/
// $Id: loader.php,v 1.73 2012/02/19 07:48:51 nao-pon Exp $
//

ignore_user_abort(FALSE);
error_reporting(0);

if (! isset($_GET['src'])) exit();

// ブラウザキャッシュ有効時間(秒)
$maxage = 86400; // 60*60*24 (1day)

// スマイリーキャッシュ有効時間(秒)
$face_tag_maxage = 86400; // 60*60*24 (1day)

// clear output buffer
while( ob_get_level() ) {
	if (! ob_end_clean()) {
		break;
	}
}

// 変数初期化
$src = preg_replace('/[^\w.%, -]+/', '', $_GET['src']);
$src = str_replace(' ', ',', $src);

if ($src === 'favicon') {
	require XOOPS_TRUST_PATH.'/class/hyp_common/favicon/favicon.php';
	exit();
}

$prefix = (isset($_GET['b']))? 'b_' : '';
$prefix = (isset($_GET['r']))? 'r_' : $prefix;
$prefix = (isset($_GET['f']))? 'fck' : $prefix;

$nocache = (isset($_GET['nc']));
$js_lang = $charset = $pre_width = $cache_file = $gzip_fname = $dir = $out = $type = $src_file = '';
$addcss = array();
$length = $addtime = 0;
$face_remake = $replace = false;
$root_path = dirname($skin_dirname);
$cache_path = $root_path.'/private/cache/';
$face_tag_ver = 1.2;
$method = empty($_SERVER['REQUEST_METHOD'])? 'GET' : strtoupper($_SERVER['REQUEST_METHOD']);
$pre_id = '';
$js_replaces = array();

if (preg_match('/^(.+)\.([^.]+)$/',$src,$match)) {
	$type = $match[2];
	$src = $match[1];
	if (substr($src, -5) === '.page') {
		$type = 'pagecss';
		$src = substr($src, 0, strlen($src) - 5);
	}
	if (substr($src, -7) === '.pcache') {
		//$src = substr($src, 0, strlen($src) - 7);
		$src_file = $cache_path . 'plugin/' . $src . '.' . $type;
	}
}

if (!$type || !$src) {
	header( 'HTTP/1.1 404 Not Found' );
	header( 'Content-Length: 0' );
	exit();
}

$basedir = ($type === 'png' || $type === 'gif')? 'image/' : '';

// 'js','png','gif','swf' は html側に指定ファイルがあれば、それにリダイレクト
if (in_array($type, array('js','png','gif','swf'))) {
	$_localFile = $basedir.$type.'/'.$src.'.'.$type;
	if (is_file($skin_dirname.'/'.$_localFile)) {
		header('Location: '.$_localFile);
		exit();
	}
}

define('UNIX_TIME', (isset($_SERVER['REQUEST_TIME'])? $_SERVER['REQUEST_TIME'] : time()));

switch ($type) {
	case 'css':
		$c_type = 'text/css';

		$pre_id = preg_replace('/[^\w_\-#]+/', '', @ $_GET['pre']);

		// Skin dir
		$skin = isset($_GET['skin']) ? preg_replace('/[^\w.-]+/','',$_GET['skin'])  : 'default';
		if (!$skin) $skin = 'default';

		$_is_tdiary = (substr($skin, 0, 3) === 'tD-');

		$dir = $prefix.basename($root_path);

		$src_files = array();
		$srcs = array();
		foreach (explode(',', $src) as $_src) {
			// Default CSS
			if ($_src === 'main') {
				// Default charset
				if (isset($_GET['charset'])) $charset = preg_replace('/[^\w.-]+/','',$_GET['charset']);
				$c_type = 'text/css' . ($charset ? '; charset=' . $charset : '');
				// tDiary
				if ($_is_tdiary) {
					$_src .= '_tdiary';
				}
				// Media
				$media = isset($_GET['media'])? $_GET['media'] : '';
				$media = ($media === 'print')? '_print' : '';
				$_src .= $media;
				// Pre Width
				$pre_width = (isset($_GET['pw']) && preg_match('/^([0-9]{2,4}(px|%)|auto)$/',$_GET['pw']))? $_GET['pw'] : 'auto';
			}

			// tDiary's Skin
			if ($_is_tdiary) {
				$skin = 'tdiary_theme';
			}

			// CSS over write (css dir)
			$addcss_file = $skin_dirname.'/'.$basedir.'css/'.$_src.'.css';
			if (is_file($addcss_file)) {
				$addcss[$_src][] = $addcss_file;
				$addtime = filemtime($addcss_file);
			}
			// CSS over write (skin dir)
			$addcss_file = $skin_dirname.'/'.$basedir.$skin.'/'.$_src.'.css';
			if (is_file($addcss_file)) {
				$addcss[$_src][] = $addcss_file;
				$addtime = max($addtime, filemtime($addcss_file));
			}
			if ($prefix) {
				$css_src = ($prefix === 'b_') ? $_src . '_block' : $_src . '_render';
				// CSS over write (css dir)
				$addcss_file = $skin_dirname.'/'.$basedir.'css/'.$css_src.'.css';
				if (is_file($addcss_file)) {
					$addcss[$_src][] = $addcss_file;
					$addtime = max($addtime, filemtime($addcss_file));
				}
				// CSS over write (skin dir)
				$addcss_file = $skin_dirname.'/'.$basedir.$skin.'/'.$css_src.'.css';
				if (is_file($addcss_file)) {
					$addcss[$_src][] = $addcss_file;
					$addtime = max($addtime, filemtime($addcss_file));
				}
			}
			$src_file = dirname(__FILE__).'/skin/'.$basedir.$type.'/'.$_src.'.'.$type;
			if (is_file($src_file)) {
				$srcs[] = $_src;
				$src_files[$_src] = $src_file;
			}
		}
		$src = join(',', $srcs);
		$src_file = $src_files;

		$replace = true;
		$cache_file = $cache_path.$skin.'_'.$src.'_'.$dir.($pre_width?'_'.$pre_width:'').($pre_id?'_'.$pre_id:'').($charset?'_'.$charset:'').'.'.$type;
		$gzip_fname = $cache_file.'.gz';
		break;
	case 'js':
		$module_url = XOOPS_URL.'/'.basename(dirname($root_path));
		$wikihelper_root_url = $module_url . '/' . basename($root_path);
		$wikihelper_root_url_md5 = md5($wikihelper_root_url);
		$face_cache = $cache_path . $wikihelper_root_url_md5 .'_facemarks.js';
		$fckeditor = $skin_dirname . '/js/fckeditor/fckeditor.js';
		$src_files = array();
		$replace = true;
		foreach(explode(',', $src) as $_src) {
			$src_file = '';
			if (substr($_src, 0, 7) === 'default') {
				$js_replaces[] = $_src;
				$js_lang = substr($_src, 8);
				$src_file = $root_path . '/language/xpwiki/' . $js_lang . '/' . 'default.js';
				// Check Trust
				if (! is_file($src_file)) {
					$src_file = dirname(__FILE__) . '/language/xpwiki/' . $js_lang . '/' . 'default.js';
				}
				// none
				if (! is_file($src_file)) {
					$src_file = dirname(__FILE__) . '/language/xpwiki/en/default.js';
				}
			} else 	if ($_src === 'main') {
				$js_replaces[] = $_src;
				$face_remake = (!is_file($face_cache) || filemtime($face_cache) + $face_tag_maxage < UNIX_TIME);
				if ($face_remake) {
					$addtime = UNIX_TIME;
				} else {
					$chk = array();
					$chk[] = $face_cache;
					$chk[] = XOOPS_TRUST_PATH . '/modules/xpwiki/ini/pukiwiki.ini.php';
					$chk[] = $root_path . '/private/ini/pukiwiki.ini.php';
					$chk[] = $cache_path . 'pukiwiki.ini.php';
					$addtime = get_filemtime($chk);
				}
			} else if ($_src === 'wikihelper_loader') {
				$js_replaces[] = $_src;
			} else if ($_src === 'option') {
				if (is_file($skin_dirname . '/js/option.js')) {
					$src_file = $skin_dirname . '/js/option.js';
				}
			}
			if (!$src_file) {
				$src_file = dirname(__FILE__).'/skin/'.$basedir.$type.'/'.$_src.'.'.$type;
			}
			if (is_file($src_file)) {
				$src_files[$_src] = $src_file;
			}
		}
		$src_file = $src_files;
		$c_type = 'application/x-javascript';
		$cache_file = $cache_path . $src . ($js_replaces? '_' . $wikihelper_root_url_md5 : '') . '.' . $type;
		$gzip_fname = $cache_file . '.gz';
		break;
	case 'png':
		$c_type = 'image/png';
		break;
	case 'gif':
		$c_type = 'image/gif';
		break;
	case 'pagecss':
		$c_type = 'text/css';
		$dir = $prefix.basename($root_path);
		$src_file = $root_path . '/private/cache/' . $src . '.css';
		$replace = true;
		$cache_file = $cache_path.$src.'_'.$dir.'.'.$type;
		$gzip_fname = $cache_file.'.gz';
		break;
	case 'xml':
		$c_type = 'application/xml; charset=utf-8';
		break;
	case 'html':
		$charset = strtolower(preg_replace('/[^\w_\-]+/','',@ $_GET['charset']));
		$c_type = 'text/html; charset=' . $charset;
		break;
	case 'swf':
		$c_type = 'application/x-shockwave-flash';
		break;
	default:
		exit();
}

if (!$src_file) {
	$src_file = dirname(__FILE__).'/skin/'.$basedir.$type.'/'.$src.$pre_id.'.'.$type;
}

$expires = 'Expires: ' . gmdate( 'D, d M Y H:i:s', UNIX_TIME + $maxage ) . ' GMT';

if ($type === 'js' || $type === 'css' || is_file($src_file)) {

	$filetime = max(filemtime(__FILE__), get_filemtime($src_file), $addtime);

	$etag = md5($type.$dir.$pre_width.$charset.$src.$filetime.$pre_id);

	// ブラウザのキャッシュをチェック
	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === $_SERVER['HTTP_IF_NONE_MATCH']) {
		header( 'HTTP/1.1 304 Not Modified' );
		if ($nocache) {
			header( 'Expires: Thu, 01 Dec 1994 16:00:00 GMT' );
			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
		} else {
			header( 'Cache-Control: public, max-age=' . $maxage );
		}
		header( 'Etag: '. $etag );
		exit();
	}

	// gzip 受け入れ不可能?
	if ($type === 'swf' || ! preg_match('/\b(gzip)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'])
//		|| strpos(strtolower(@ $_SERVER['HTTP_USER_AGENT']), 'safari') !== false
	) {
		$gzip_fname = '';
	}

	// キャッシュ判定
	if ($gzip_fname && is_file($gzip_fname) && filemtime($gzip_fname) >= $filetime) {
		// html側/private/cache に 有効な gzip ファイルがある場合
		header( 'Content-Type: ' . $c_type );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $filetime ) . ' GMT' );
		header( 'Cache-Control: max-age=' . $maxage );
		header( 'Etag: '. $etag );
		header( 'Content-length: '.filesize($gzip_fname) );
		header( 'Content-Encoding: gzip' );
		header( 'Vary: Accept-Encoding' );

		if ($method !== 'HEAD') loader_readfile($gzip_fname, TRUE);
		exit();
	} else if ($replace && is_file($cache_file) && filemtime($cache_file) >= $filetime) {
		// html側/private/cache に 有効なキャッシュファイルがある場合
		header( 'Content-Type: ' . $c_type );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $filetime ) . ' GMT' );
		header( 'Cache-Control: max-age=' . $maxage );
		header( $expires );
		header( 'Etag: '. $etag );
		header( 'Content-length: '.filesize($cache_file) );

		if ($method !== 'HEAD') loader_readfile($cache_file);
		exit();
	}

	// 置換処理が必要?
	if ($replace) {
		if ($type === 'pagecss') {

			$out = file_get_contents($src_file);

			xpwiki_pagecss_filter($out);

			if ($pre_id) $pre_id .= ' ';

			$out = str_replace(array('$dir', '$class', '$pre_width', '$charset'),
								array($dir, $pre_id.'div.xpwiki_'.$dir, $pre_width, $charset),
								$out);
		}
		if ($type === 'css') {
			$out = '';
			if ($pre_id) $pre_id .= ' ';
			$conf_file = $skin_dirname.'/'.$basedir.$skin.'/css.conf';
			if (is_file($conf_file)) {
				$conf = parse_ini_file($conf_file, true);
			} else {
				$conf = array();
			}

			foreach($src_file as $_src => $_file) {

				$replace_src = 0;

				if (! empty($conf[$_src]['replace'])) {
					$replace_src = 1;
					$_file = $skin_dirname.'/'.$basedir.$skin.'/'.$_src.'.css';
				}

				$_out = file_get_contents($_file);

				$addcss_src = '';
				if (! $replace_src && $addcss[$_src]) {
					foreach ($addcss[$_src] as $_file) {
						$addcss_src .= file_get_contents($_file) . "\n";
					}
				}

				$class = ($prefix === 'fck')? 'body' : $pre_id.'div.xpwiki_'.$dir;
				$_out = str_replace(array('$dir', '$class', '$pre_width', '$charset'),
									array($dir, $class, $pre_width, $charset),
									$_out . "\n" . $addcss_src);

				$out .= $_out;
			}
		}
		if ($type === 'js') {
			$out = '';
			$xpwiki = null;
			foreach($src_file as $_src => $_file) {
				$_out = file_get_contents($_file) . "\n";
				if ($_src === 'main') {
					if (! isset($xpwiki)) {
						chdir($root_path);
						$GLOBALS['xoopsOption']['nocommon'] = false;
						include_once XOOPS_ROOT_PATH.'/include/common.php';
						chdir($skin_dirname);
						include_once dirname( __FILE__ ) . '/include.php';
						$xpwiki = new XpWiki(basename($root_path));
						$xpwiki->init('#RenderMode');
					}
					$encode_hint = $xpwiki->cont['PKWK_ENCODING_HINT'];
					if (!$face_remake) {
						$face_tag_ver .= $xpwiki->root->image_pack_name;
						@ list($face_tag, $face_tag_full, $_face_tag_ver, $fck_smileys) = array_pad(file($face_cache), 3, '');
						if (!$face_tag_full) $face_tag_full = $face_tag;
						if (trim($_face_tag_ver) != $face_tag_ver) {
							$face_remake = true;
						}
					}
					if ($face_remake) {
						list($face_tag, $face_tag_full, $_face_tag_ver, $fck_smileys) = xpwiki_make_facemarks ($xpwiki, $skin_dirname, $face_cache, $face_tag_ver);
					}
					$ieDomLoadedDisabled = $xpwiki->root->ieDomLoadedDisabled? 'true' : 'false';
					$UseWikihelperAtAll = $xpwiki->root->render_UseWikihelperAtAll? 'true' : 'false';
					if (defined('XPWIKI_RENDERER_DIR')) {
						$RendererDir = XPWIKI_RENDERER_DIR;
						if (defined('XPWIKI_RENDERER_USE_WIKIHELPER')) {
							$UseWikihelperAtAll = XPWIKI_RENDERER_USE_WIKIHELPER? 'true' : 'false';
						}
						if ($xpwiki->root->mydirname === XPWIKI_RENDERER_DIR) {
							$RendererPage = $xpwiki->root->render_attach;
							$skinname = $xpwiki->cont['SKIN_NAME'];
						} else {
							$renderer = new XpWiki(XPWIKI_RENDERER_DIR);
							$renderer->init('#RenderMode');
							$RendererPage = $renderer->root->render_attach;
							$skinname = $renderer->cont['SKIN_NAME'];
						}
						$skinname = 'XpWiki.SkinName[\''.$RendererDir.'\'] = \''.$skinname.'\';';
					} else {
						$skinname = $RendererDir = $RendererPage = '';
					}
					$fckeditor_path = '';
					if ($xpwiki->root->fckeditor_path) {
						$fckeditor_path =  $xpwiki->cont['ROOT_URL'] . trim($xpwiki->root->fckeditor_path, '/') . '/';
					}
					$fckxpwiki_path = $xpwiki->cont['ROOT_URL'] . trim($xpwiki->root->fckxpwiki_path, '/') . '/';
					$ie6JsPass = ($xpwiki->root->ie6JsPass)? 'true' : 'false';
					$imageDir = $xpwiki->cont['IMAGE_DIR'];
					if ($xpwiki->root->use_root_image_manager && is_file( $xpwiki->cont['ROOT_PATH'] . 'imagemanager.php')) {
						$filemanagerTag = str_replace('\'', '\\\'', '<span title="ImageManager" onclick="XpWiki.fireImageManager(openWithSelfMain,[\''.$xpwiki->cont['ROOT_URL'].'imagemanager.php?target=[TARGET]&amp;cb=xpwiki\',\'imgmanager\','.$xpwiki->root->root_image_manager_width.','.$xpwiki->root->root_image_manager_height.'],0)"><img src="'.$xpwiki->cont['ROOT_URL'].'images/image.gif" alt="Img" /></a></span>');
					} else {
						$filemanagerTag = '';
					}
					$_out = str_replace(
						array('$face_tag_full', '$face_tag', '$fck_smileys', '$module_url', '$encode_hint', '$charset',                       '$ieDomLoadedDisabled', '$faviconSetClass',                   '$faviconReplaceClass',                   '$UseWikihelperAtAll', '$RendererDir', '$RendererPage', '$fckeditor_path', '$fckxpwiki_path', '$skinname', '$ie6JsPass', '$imageDir', '$filemanagerTag'),
						array( $face_tag_full,   $face_tag,   $fck_smileys,   $module_url,   $encode_hint,   $xpwiki->cont['SOURCE_ENCODING'], $ieDomLoadedDisabled,   $xpwiki->root->favicon_set_classname, $xpwiki->root->favicon_replace_classname, $UseWikihelperAtAll,   $RendererDir,   $RendererPage,   $fckeditor_path,   $fckxpwiki_path,   $skinname,   $ie6JsPass,   $imageDir,   $filemanagerTag ),
					$_out);
				}
				if (in_array($_src, $js_replaces)) {
					$_out = str_replace('$wikihelper_root_url', $wikihelper_root_url, $_out);
				}
				$out .= $_out;
			}
		}
		$length = strlen($out);

		// 置換処理した場合は、通常の形式でもキャッシュする
		@ unlink($cache_file);
		if ($fp = fopen($cache_file, 'wb')) {
			fwrite($fp, $out);
			fclose($fp);
			touch($cache_file, $filetime);
		}
	}

	// html側/private/cache に gzip 圧縮してキャッシュする
	$is_gz = false;
	if ($gzip_fname && extension_loaded('zlib')) {
		if (! $replace) {
			$out = file_get_contents($src_file);
		}
		if ($gzip_out = gzencode($out)) {
			@ unlink($gzip_fname);
			if ($fp = fopen($gzip_fname, 'wb')) {
				fwrite($fp, $gzip_out);
				fclose($fp);
				touch($gzip_fname, $filetime);
				$is_gz = true;
				$replace = true;
				$out = $gzip_out;
				$length = strlen($out);
			}
		}
	}

	if (!$length) { $length = filesize($src_file); }

	header( 'Content-Type: ' . $c_type );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $filetime ) . ' GMT' );
	if ($nocache) {
		header( 'Expires: Thu, 01 Dec 1994 16:00:00 GMT' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
	} else {
		header( 'Cache-Control: public, max-age=' . $maxage );
		header( $expires );
	}
	header( 'Etag: '. $etag );
	header( 'Content-length: '.$length );
	if ($is_gz) {
		header( 'Content-Encoding: gzip' );
		header( 'Vary: Accept-Encoding' );
	}

	if ($method !== 'HEAD') {
		if ($replace) {
			echo $out;
		} else {
			if ($is_gz) {
				loader_readfile($src_file, TRUE);
			} else {
				loader_readfile($src_file);
			}
		}
	}
	exit();
} else {
	header( 'HTTP/1.1 404 Not Found' );
	header( 'Content-Length: 0' );
	exit();
}

function xpwiki_make_facemarks (& $wiki, $skin_dirname, $cache, $face_tag_ver) {
	$fck_face = $tags_full = $tags = array();
	foreach($wiki->root->wikihelper_facemarks as $key => $img) {
		$key = htmlspecialchars($key, ENT_QUOTES);
		$q_key = str_replace("'", "\\'", $key);
		if ($img{0} === '*') {
			$img = substr($img, 1);
			$tags_full[] = '\'<img src="'.$img.'" border="0" title="'.$key.'" alt="'.$key.'" onClick="javascript:wikihelper_face(\\\''.$q_key.'\\\');return false;" />\'';
			$fck_face[] = "'$q_key'";
			$fck_face[] = "'$img'";
			continue;
		}
		$tags[] = '\'<img src="'.$img.'" border="0" title="'.$key.'" alt="'.$key.'" onClick="javascript:wikihelper_face(\\\''.$q_key.'\\\');return false;" />\'';
		$tags_full[] = '\'<img src="'.$img.'" border="0" title="'.$key.'" alt="'.$key.'" onClick="javascript:wikihelper_face(\\\''.$q_key.'\\\');return false;" />\'';
		$fck_face[] = "'$q_key'";
		$fck_face[] = "'$img'";
	}
	$tags = array(join('+', $tags) ,join('+', $tags_full), $face_tag_ver, '[' . join(',', $fck_face) . ']');
	if ($fp = fopen($cache, 'wb')) {
		fwrite($fp, join("\n", $tags));
		fclose($fp);
	}
	return $tags;
}

function xpwiki_pagecss_filter (& $css, $chrctor) {
	if (! extension_loaded('mbstring')) {
		if (! function_exists('XC_CLASS_EXISTS')) {
			include XOOPS_TRUST_PATH . '/class/hyp_common/XC_CLASS_EXISTS.inc.php';
		}
		if (! XC_CLASS_EXISTS('HypMBString')) {
			include XOOPS_TRUST_PATH . '/class/hyp_common/mbemulator/mb-emulator.php';
		}
	}
	$css = mb_convert_kana($css, 'asKV', mb_detect_encoding($css));
	$css = preg_replace('/(expression|javascript|vbscript|@import|cookie|eval|behavior|behaviour|binding|include-source|@i|[\x00-\x08\x0e-\x1f\x7f]+|\\\(?![\'"{};:()#A*]))/i', '', $css);
	$css = str_replace(array('*/', '<', '>', '&#'), array('*/  ', '&lt;', '&gt;', ''), $css);
}

function get_filemtime ($file) {
	if (! is_array($file)) {
		return filemtime($file);
	} else {
		$time = 0;
		foreach($file as $f) {
			if (is_file($f)) {
				$time = max($time, filemtime($f));
			}
		}
		return $time;
	}
}

// file_get_contents -- Reads entire file into a string
// (PHP 4 >= 4.3.0, PHP 5)
if (! function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null)
	{
		if (false === $fh = fopen($filename, 'rb', $incpath)) {
			trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
			return false;
		}

		clearstatcache();
		if ($fsize = @filesize($filename)) {
			$data = fread($fh, $fsize);
		} else {
			$data = '';
			while (!feof($fh)) {
				$data .= fread($fh, 8192);
			}
		}

		fclose($fh);
		return $data;
	}
}

function loader_readfile($file, $use_content_encoding = FALSE) {
	if (! defined('HYP_X_SENDFILE_MODE')) {
		// load HYP_X_SENDFILE_MODE
		$conf = XOOPS_TRUST_PATH . '/class/hyp_common/config/hyp_common.conf.php';
		if (is_file($conf)) {
			include_once $conf;
		}
	}
	if (defined('HYP_X_SENDFILE_MODE')) {
		if (HYP_X_SENDFILE_MODE === 3 || (! $use_content_encoding && HYP_X_SENDFILE_MODE === 2)) {
			if ( $use_content_encoding && HYP_X_SENDFILE_MODE === 3) {
				header('X-Sendfile-Use-CE: Yes');
			}
			header('X-Sendfile: ' . $file);
			return;
		} else if (HYP_X_SENDFILE_MODE === 1) {
			header('X-LIGHTTPD-send-file: ' . $file);
			return;
		}
	}
	@readfile($file);
	return;
}
?>