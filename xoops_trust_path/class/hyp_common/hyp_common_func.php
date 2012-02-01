<?php
// $Id: hyp_common_func.php,v 1.85 2012/01/30 11:45:26 nao-pon Exp $
// HypCommonFunc Class by nao-pon http://hypweb.net
////////////////////////////////////////////////

if (! function_exists('XC_CLASS_EXISTS')) {
	require dirname(__FILE__) . '/XC_CLASS_EXISTS.inc.php';
}

if( ! XC_CLASS_EXISTS( 'HypCommonFunc' ) )
{

define('HYP_COMMON_ROOT_PATH', dirname(__FILE__));

if (is_file(HYP_COMMON_ROOT_PATH . '/config/hyp_common.conf.php')) {
	include_once HYP_COMMON_ROOT_PATH . '/config/hyp_common.conf.php';
}
// define
//if (! defined('HYP_IMAGEMAGICK_UNSHARP')) define('HYP_IMAGEMAGICK_UNSHARP', '1.5x1.2+1.0+0.10');
if (! defined('HYP_IMAGEMAGICK_UNSHARP')) define('HYP_IMAGEMAGICK_UNSHARP', '100|0.5|3');

// PATH_SEPARATOR
if (! defined('PATH_SEPARATOR')) {
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		define('PATH_SEPARATOR', ':');
	} else {
		define('PATH_SEPARATOR', ';');
	}
}

class HypCommonFunc
{
	function get_version() {
		static $version = FALSE;
		if (! $version) {
			include (HYP_COMMON_ROOT_PATH . '/version.php');
		}
		return $version;
	}

	function loadClass($name) {
		if (XC_CLASS_EXISTS($name)) return TRUE;

		$ret = FALSE;
		switch($name) {
			case 'HypSimpleAmazon':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/hsamazon/hyp_simple_amazon.php';
				break;
			case 'HypPinger':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/hyppinger/hyppinger.php';
				break;
			case 'HypGetQueryWord':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/hyp_get_engine.php';
				break;
			case 'Hyp_KAKASHI':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/hyp_kakasi.php';
				break;
			case 'HypSimpleXML':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/hyp_simplexml.php';
				break;
			case 'HypKTaiRender':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/ktairender/hyp_ktai_render.php';
				break;
			case 'HypRss2Html':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/rss2html/hyp_rss2html.php';
				break;
			case 'MobilePictogramConverter':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/mpc/MobilePictogramConverter.php';
				break;
			case 'IXR_Client':
			case 'IXR_Server':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/IXR_Library/IXR_Library.inc.php';
				break;
			case 'TwitterOAuth':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/twitteroauth/twitteroauth.php';
				if ($ret) $ret = include_once HYP_COMMON_ROOT_PATH . '/twitteroauth/OAuth.php';
				break;
			case 'MySQLDump':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/lib_dump/lib_dump.php';
				break;
			case 'getID3':
				$ret = include_once HYP_COMMON_ROOT_PATH . '/getid3/getid3.php';
				break;
			default:
				$ret = FALSE;
		}
		return $ret;
	}

	// 1バイト文字をエンティティ化
	function str_to_entity(&$str)
	{
		$e_mail = "";
		$i = 0;
		while(isset($str[$i]))
		{
			$e_mail .= "&#".ord((string)$str[$i]).";";
			$i++;
		}
		$str = $e_mail;
		return $str;
	}

	// ",' で括ったフレーズ対応スプリット
	function phrase_split($str)
	{
		$words = array();
		$str = preg_replace("/(\"|')(.+?)(?:\\1)/e","str_replace(' ','\x08','$2')",$str);
		$words = preg_split('/\s+/',$str,-1,PREG_SPLIT_NO_EMPTY);
		$words = str_replace("\x08"," ",$words);
		return $words;
	}

	// 配列対応 & gpc 対応のstripslashes
	function stripslashes_gpc(&$v)
	{
		if(ini_get("magic_quotes_gpc"))
		{
			if (is_array($v))
			{
				$arr =array();
				foreach($v as $k=>$m)
				{
					$arr[$k] = HypCommonFunc::stripslashes_gpc($m);
				}
				$v = $arr;
			}
			else
			{
				$v = stripslashes($v);
			}
		}
		return $v;
	}

	// RSS関連のキャッシュを削除する
	function clear_rss_cache($files=array())
	{
		include_once XOOPS_ROOT_PATH.'/class/template.php';

		if (empty($files) || !is_array($files))
		{
			$files = array(
				'db:BopComments_rss.html',
				'db:whatsnew_rss.html',
				'db:whatsnew_atom.html',
				'db:whatsnew_rdf.html',
				'db:whatsnew_pda.html',
				'db:whatsnew_block_bop.html',
				'db:whatsnew_block_mod.html',
				'db:whatsnew_block_date.html',
			);
		}

		$tpl = new XoopsTpl();
		$tpl->xoops_setCaching(2);
		foreach($files as $tgt)
		{
			if ($tgt) {$tpl->clear_cache($tgt);}
		}
	}

	// RPC Update Ping を打つ
	function update_rpc_ping($to = '')
	{
		global $xoopsConfig;

		//RSSキャッシュファイルを削除
		HypCommonFunc::clear_rss_cache();

		if (! $to) {
			$to = 'http://api.my.yahoo.co.jp/RPC2 http://ping.myblog.jp http://ping.bloggers.jp/rpc/ http://blog.goo.ne.jp/XMLRPC http://ping.cocolog-nifty.com/xmlrpc http://rpc.technorati.jp/rpc/ping';
		}

		$update_ping = preg_split ( "/[\s,]+/" , $to);

		$ping_blog_name = $xoopsConfig['sitename'];
		$ping_url		= XOOPS_URL."/";

		HypCommonFunc::loadClass('HypPinger');
		$p = new HypPinger(
			$ping_blog_name,
			$ping_url
			);
		$p->setEncording(_CHARSET);

		foreach($update_ping as $to) {
			list($url, $extended) = array_pad(explode(' ', trim($to)), 2, '');
			$url = trim($url);
			$extended = $extended? TRUE : FALSE;
			$p->addSendTo($url, $extended);
		}

		$p->send();

		$p = NULL;
		unset($p);
	}

	function make_context($text, $words=array(), $l=255, $parts=3, $delimiter='...', $caseInsensitive = TRUE, $whitespaceCompress = TRUE, $encode = null)
	{
		if (! $encode) {
			if (defined(_CHARSET)) {
				$encode = _CHARSET;
			} else {
				$encode = mb_internal_encoding();
			}
		}

		static $strcut = '';
		if (!$strcut) $strcut = (function_exists('mb_substr'))? 'mb_substr' : 'substr';

		static $strlen = '';
		if (!$strlen) $strlen = (function_exists('mb_strlen'))? 'mb_strlen' : 'strlen';

		$limit = $parts + 1;
		$ret = str_replace(array('&lt;','&gt;','&quot;','&#039;','&amp;'),array('<','>','"',"'",'&'), $text);

		// short text
		if ($strlen($ret) <= $l) return htmlspecialchars($ret);

		if (is_array($words)) {
			$words = join(' ', $words);
		}
		$words = preg_replace('/\s+/', ' ', $words);
		if ($words) {
			$q_word = str_replace(' ', '|', preg_quote($words, '/'));
		} else {
			$q_word = '?!';
		}

		$match = array();
		$reg = '/(' . $q_word . ')/S';
		if ($encode === 'UTF-8') $reg .= 'u';
		if ($caseInsensitive) {
			$reg .= 'i';
		}
		if (preg_match($reg, $text, $match)) {
			if ($whitespaceCompress) {
				$ret = ltrim(preg_replace('/\s+/', ' ', $text));
			}
			$arr = preg_split($reg, $ret, $limit, PREG_SPLIT_DELIM_CAPTURE);
			$count = count($arr);

			$ret = '';
			if ($count === 1) {
				$ret = $arr[0];
			} else {
				$m = intval($l / max((($count - 1) / 2), 2));
				$add = 0;
				for($i = 0; $i < $count; $i = $i + 2) {
					$mc = $m;
					if ($add) {
						$mc += $add;
					}
					$key = $i + 1;
					if (isset($arr[$key])) {
						$mc = $mc - $strlen($arr[$key], $encode);
					}
					if (isset($arr[$i-1]) && isset($arr[$key])) {
						$type = 'middle';
					} else {
						if (isset($arr[$key])) {
							$type = 'first';
							if ($count > 3) $mc = $mc / 2;
						} else if (isset($arr[$i-1])) {
							$type = 'last';
						}
					}
					$len = $strlen($arr[$i], $encode);
					if ($len > $mc && $type !== 'last') {
						if ($type === 'middle') {
							// キーワードとキーワードで挟まれた部分
							$mc = $mc - $strlen($delimiter, $encode);
							$ret .= $strcut($arr[$i], 0, $mc / 2, $encode);
							$ret .= $delimiter;
							$ret .= $strcut($arr[$i], max($len - $mc / 2 + 1, 0), $mc / 2, $encode);
						} else {
							// 最初の部分
							$mc = $mc - $strlen($delimiter, $encode);
							$ret .= $delimiter;
							$ret .= $strcut($arr[$i], max($len - $mc + 1 , 0), $mc, $encode);
						}
						$add = 0;
					} else {
						$add += $mc - $len;
						$ret .= $arr[$i];
					}
					if (isset($arr[$key])) {
						$ret .= $arr[$key];
					}
				}
			}
		}

		if ($strlen($ret, $encode) > $l) {
			$l = $l - $strlen($delimiter, $encode);
			$ret = $strcut($ret, 0, $l, $encode);
			$ret = preg_replace('/&#?[A-Za-z0-9]{0,6}$/', '', $ret);
			$ret .= $delimiter;
		}

		$ret = htmlspecialchars($ret);
		$ret = preg_replace('/&amp;(#?[A-Za-z0-9]{2,6}?;)/', '&$1', $ret);

		return $ret;
	}

	function set_need_refresh($mode)
	{
		if ($mode)
		{
			setcookie ("HypNeedRefresh", "1");
		}
		else
		{
			setcookie ("HypNeedRefresh", "", time() - 3600);
		}
	}

	// HTML の meta タグから文字エンコーディングを取得する
	function get_encoding_by_meta($html, $ret_empty = FALSE)
	{
		$ret = $ret_empty? '' : 'EUC-JP,UTF-8,Shift_JIS,JIS';
		$codesets = array(
			'shift_jis' => 'Shift_JIS',
			'shift-jis' => 'Shift_JIS',
			'x-sjis' => 'Shift_JIS',
			'euc-jp' => 'EUC-JP',
			'x-euc-jp' => 'EUC-JP',
			'iso-2022-jp' => 'JIS',
			'utf-8' => 'UTF-8',
			'iso-8859-1' => 'ISO-8859-1',
		);
		$match = array();
		if (preg_match("/<meta[^>]*content=(?:\"|')[^\"'>]*charset=([^\"'>]+)(?:\"|')[^>]*>/is",$html,$match))
		{
			$encode = strtolower($match[1]);
			if (array_key_exists($encode,$codesets))
			{
				return $codesets[$encode];
			}
			else
			{
				return $ret? ($match[1] . ',' . $ret) : $encode;
			}
		}
		else
		{
			return $ret;
		}
	}

	// 携帯用に画像を(リサイズして)変換する
	function img4ktai($file, $maxsize = 128, $allowpng = FALSE, $allconvert = FALSE, $quality = 50) {
		//GD のバージョンを取得
		static $gd_ver = null;
		if (is_null($gd_ver)) {
			$gd_ver = HypCommonFunc::gdVersion();
		}

		// gd fuction のチェック
		if ($gd_ver < 1 || !function_exists("imagecreate")) return FALSE;//gdをサポートしていない

		// リサイズ
		$resized = HypCommonFunc::ImageResize($file, $maxsize . 'x' . $maxsize, $quality);

		$size = @ getimagesize($file);
		if (! $size) return FALSE;

		// Image type
		$imgtype = $size[2];
		$allowtypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG);
		if ($allowpng) $allowtypes[] = IMAGETYPE_PNG;

		// JPEG以外はファイルサイズが大きければとりあえず変換する ( > 10k)
		if ($imgtype !== IMAGETYPE_JPEG && filesize($file) > 10240) {
			$allconvert = TRUE;
		}

		// リサイズされなかったなら変換しない
		if (! $allconvert && ! $resized && in_array($imgtype, $allowtypes)) return FALSE;

		$src_im = @ imagecreatefromstring(file_get_contents($file));
		if (! $src_im) return FALSE;

		// gd のバージョンによる関数名の定義
		$imagecreate = ($gd_ver >= 2)? "imagecreatetruecolor" : "imagecreate";
		$imageresize = ($gd_ver >= 2)? "imagecopyresampled" : "imagecopyresized";

		$width = $size[0];
		$height = $size[1];

		if (imagecolorstotal($src_im)) {
			// PaletteColor
			$colortransparent = imagecolortransparent($src_im);
			if ($colortransparent > -1) {
				// 透過色あり
				$dst_im = imagecreate($width,$height);
				imagepalettecopy ($dst_im, $src_im);
				imagefill($dst_im,0,0,$colortransparent);
				imagecolortransparent($dst_im, $colortransparent);
			} else {
				// 透過色なし
				$dst_im = $imagecreate($width,$height);
			}
		} else {
			// True color
			$dst_im = $imagecreate($width, $height);
			$colortransparent = imagecolortransparent($src_im);
			if ($colortransparent > -1) {
				imagecolortransparent($dst_im, $colortransparent);
			}
		}
		imagecopymerge($dst_im, $src_im, 0, 0, 0, 0, $width, $height, 100);

		// とりあえず JPEG で保存
		imagejpeg($dst_im, $file);
		clearstatcache();

		if ($imgtype !== IMAGETYPE_JPEG) {
			// GIF or PNG で保存してサイズ比較
			$temp = tempnam(dirname($file), 'i4k');
			if ($allowpng && $imgtype === IMAGETYPE_PNG) {
				imagepng($dst_im, $temp);
			} else {
				imagegif($dst_im, $temp);
			}
			if (filesize($temp) < filesize($file)) {
				unlink($file);
				copy($temp, $file);
				clearstatcache();
			}
			unlink($temp);
		}

		@ imagedestroy($dst_im);
		@ imagedestroy($src_im);

		return TRUE;
	}

	// 携帯用にリサイズした画像のサイズを得る
	function get_imagesize4ktai($url, $maxsize, $allowpng) {
		$cachepath = XOOPS_ROOT_PATH . '/class/hyp_common/cache';
		$png = ($allowpng)? 1 : 0;
		$basename = md5(join("\t", array($url, $maxsize, $png))) . '.i4ks';
		$size_file = $cachepath . '/' .  $basename;
		if (is_file($size_file)) {
			list($ret) = file($size_file);
			$ret = trim($ret);
			return $ret;
		}
		return '';
	}

	// 携帯用にリサイズした画像のファイルサイズを得る
	function get_imagefilesize4ktai($url, $maxsize, $allowpng) {
		$cachepath = XOOPS_ROOT_PATH . '/class/hyp_common/cache';
		$png = ($allowpng)? 1 : 0;
		$basename = md5(join("\t", array($url, $maxsize, $png))) . '.i4k';
		$size_file = $cachepath . '/' .  $basename;
		if (is_file($size_file)) {
			return filesize($size_file);
		} else {
			return false;
		}
	}

	// サムネイル画像を作成。
	// 成功ならサムネイルのファイルのパス、不成功なら元ファイルパスを返す
	function make_thumb($o_file, $s_file, $max_width, $max_height, $zoom_limit="1,95", $refresh=FALSE, $quality=75)
	{
		// すでに作成済み
		if (! $refresh && is_file($s_file)) {
			// make_thumb.chk のタイムスタンプと比較
			$make_thumb_chk = HYP_COMMON_ROOT_PATH . '/config/remake_thumb.chk';
			if (! is_file($make_thumb_chk) || filemtime($make_thumb_chk) < filemtime($s_file)) {
				 return $s_file;
			}
		}

		$size = @getimagesize($o_file);
		if (!$size) return $o_file;//画像ファイルではない

		// 元画像のサイズ
		$org_w = $size[0];
		$org_h = $size[1];

		if ($max_width >= $org_w && $max_height >= $org_h) return $o_file;//指定サイズが元サイズより大きい

		// 縮小率の設定
		list($zoom_limit_min,$zoom_limit_max) = explode(",",$zoom_limit);
		$zoom = min(($max_width/$org_w),($max_height/$org_h));
		if (!$zoom || $zoom < $zoom_limit_min/100 || $zoom > $zoom_limit_max/100) return $o_file;//ZOOM値が範囲外

		@unlink($s_file);

		if (defined('HYP_IMAGEMAGICK_PATH') && HYP_IMAGEMAGICK_PATH)
		{
			// ImageMagick を使用
			return HypCommonFunc::make_thumb_imagemagick($o_file, $s_file, $zoom, $quality, $size[2], $org_w, $org_h);
		}
		else
		{
			if (!HypCommonFunc::check_memory4gd($org_w,$org_h))
			{
				// メモリー制限に引っ掛かりそう。（マージン 1MB）
				return $o_file;
			}
			return HypCommonFunc::make_thumb_gd($o_file, $s_file, $zoom, $quality, $size[2], $org_w, $org_h);
		}
	}

	function make_thumb_gd($o_file, $s_file, $zoom, $quality, $type ,$org_w, $org_h)
	{
		//GD のバージョンを取得
		static $gd_ver = null;
		if (is_null($gd_ver))
		{
			$gd_ver = HypCommonFunc::gdVersion();
		}

		// gd fuction のチェック
		if ($gd_ver < 1 || !function_exists("imagecreate")) return $o_file;//gdをサポートしていない

		// gd のバージョンによる関数名の定義
		$imagecreate = ($gd_ver >= 2)? "imagecreatetruecolor" : "imagecreate";
		$imageresize = ($gd_ver >= 2)? "imagecopyresampled" : "imagecopyresized";

		$width = $org_w * $zoom;
		$height = $org_h * $zoom;

		// サムネイルのファイルタイプが指定されている？(.jpg)
		$s_ext = "";
		$s_ext = preg_replace("/\.([^\.]+)$/","$1",$s_file);

		switch($type)
		{
			case "1": //gif形式
				if (function_exists ("imagecreatefromgif"))
				{
					$src_im = @ imagecreatefromgif($o_file);
					if ($src_im) {
						$colortransparent = imagecolortransparent($src_im);
						if ($s_ext != "jpg" && $colortransparent > -1)
						{
							// 透過色あり
							$dst_im = imagecreate($width,$height);
							imagepalettecopy ($dst_im, $src_im);
							imagefill($dst_im,0,0,$colortransparent);
							imagecolortransparent($dst_im, $colortransparent);
							HypCommonFunc::gd_resizer('imagecopyresized',$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
						}
						else
						{
							// 透過色なし
							$dst_im = $imagecreate($width,$height);
							HypCommonFunc::gd_resizer($imageresize,$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
							if (function_exists('imagetruecolortopalette')) imagetruecolortopalette ($dst_im, false, imagecolorstotal($src_im));
						}
						HypCommonFunc::touch($s_file);
						if ($s_ext == "jpg")
						{
							imagejpeg($dst_im,$s_file,$quality);
						}
						else
						{
							if (function_exists("imagegif"))
							{
								imagegif($dst_im,$s_file);
							}
							else
							{
								imagepng($dst_im,$s_file);
							}
						}
						$o_file = $s_file;
					}
				}
				break;
			case "2": //jpg形式
				$src_im = @ imagecreatefromjpeg($o_file);
				if ($src_im) {
					$dst_im = $imagecreate($width,$height);
					HypCommonFunc::gd_resizer($imageresize,$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
					HypCommonFunc::touch($s_file);
					imagejpeg($dst_im,$s_file,$quality);
					$o_file = $s_file;
				}
				break;
			case "3": //png形式
				$src_im = @ imagecreatefrompng($o_file);
				if ($src_im) {
					if (imagecolorstotal($src_im))
					{
						// PaletteColor
						$colortransparent = imagecolortransparent($src_im);
						if ($s_ext != "jpg" && $colortransparent > -1)
						{
							// 透過色あり
							$dst_im = imagecreate($width,$height);
							imagepalettecopy ($dst_im, $src_im);
							imagefill($dst_im,0,0,$colortransparent);
							imagecolortransparent($dst_im, $colortransparent);
							HypCommonFunc::gd_resizer('imagecopyresized',$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
						}
						else
						{
							// 透過色なし
							$dst_im = $imagecreate($width,$height);
							HypCommonFunc::gd_resizer($imageresize,$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
							if (function_exists('imagetruecolortopalette')) imagetruecolortopalette ($dst_im, false, imagecolorstotal($src_im));
						}
					}
					else
					{
						// TrueColor
						$dst_im = $imagecreate($width,$height);
						HypCommonFunc::gd_resizer($imageresize,$gd_ver,$dst_im,$src_im,$width,$height,$org_w,$org_h);
					}
					HypCommonFunc::touch($s_file);
					if ($s_ext == "jpg")
					{
						imagejpeg($dst_im,$s_file,$quality);
					}
					else
					{
						imagepng($dst_im,$s_file);
					}
					$o_file = $s_file;
				}
				break;
			default:
				break;
		}
		@imagedestroy($dst_im);
		@imagedestroy($src_im);
		//chmod($o_file, 0666);
		return $o_file;
	}

	function gd_resizer($func, $gd_ver, $dst_im, $src_im, $width, $height, $org_w, $org_h) {
		$func($dst_im,$src_im,0,0,0,0,$width,$height,$org_w,$org_h);
		if ($gd_ver >= 2) {
			list($amount, $radius, $threshold) = HypCommonFunc::get_unsharp_mask_params();
			HypCommonFunc::UnsharpMask($dst_im ,$amount ,$radius ,$threshold);
		}
	}

	function get_unsharp_mask_params() {
		list($amount, $radius, $threshold) = array_pad(explode('|', HYP_IMAGEMAGICK_UNSHARP), 3, '');
		$amount    = ($amount            ? $amount    : 80);
		$radius    = ($radius            ? $radius    : 0.5);
		$threshold = (strlen($threshold) ? $threshold : 3);
		return array($amount, $radius, $threshold);
	}

	function UnsharpMask ( $img , $amount , $radius , $threshold )    {

		////////////////////////////////////////////////////////////////////////////////////////////////
		////
		////                  Unsharp Mask for PHP - version 2.1.1
		////
		////    Unsharp mask algorithm by Torstein Hønsi 2003-07.
		////             thoensi_at_netcom_dot_no.
		////               Please leave this notice.
		////
		///////////////////////////////////////////////////////////////////////////////////////////////



		// $img is an image that is already created within php using
		// imgcreatetruecolor. No url! $img must be a truecolor image.

		// Attempt to calibrate the parameters to Photoshop:
		if ( $amount > 500 ) $amount = 500 ;
		$amount = $amount * 0.016 ;
		if ( $radius > 50 ) $radius = 50 ;
		$radius = $radius * 2 ;
		if ( $threshold > 255 ) $threshold = 255 ;

		$radius = abs ( round ( $radius )); // Only integers make sense.
		if ( $radius == 0 ) {
			return $img ;
			imagedestroy ( $img );
			break;
		}
		$w = imagesx ( $img ); $h = imagesy ( $img );
		$imgCanvas = imagecreatetruecolor ( $w , $h );
		$imgBlur = imagecreatetruecolor ( $w , $h );


		// Gaussian blur matrix:
		//
		//    1    2    1
		//    2    4    2
		//    1    2    1
		//
		//////////////////////////////////////////////////


		if ( function_exists ( 'imageconvolution' )) { // PHP >= 5.1
			$matrix = array(
				array( 1 , 2 , 1 ),
				array( 2 , 4 , 2 ),
				array( 1 , 2 , 1 )
			);
			imagecopy ( $imgBlur , $img , 0 , 0 , 0 , 0 , $w , $h );
			imageconvolution ( $imgBlur , $matrix , 16 , 0 );
		}
		else {

			// Move copies of the image around one pixel at the time and merge them with weight
			// according to the matrix. The same matrix is simply repeated for higher radii.
			for ( $i = 0 ; $i < $radius ; $i ++)    {
				imagecopy ( $imgBlur , $img , 0 , 0 , 1 , 0 , $w - 1 , $h ); // left
				imagecopymerge ( $imgBlur , $img , 1 , 0 , 0 , 0 , $w , $h , 50 ); // right
				imagecopymerge ( $imgBlur , $img , 0 , 0 , 0 , 0 , $w , $h , 50 ); // center
				imagecopy ( $imgCanvas , $imgBlur , 0 , 0 , 0 , 0 , $w , $h );

				imagecopymerge ( $imgBlur , $imgCanvas , 0 , 0 , 0 , 1 , $w , $h - 1 , 33.33333 ); // up
				imagecopymerge ( $imgBlur , $imgCanvas , 0 , 1 , 0 , 0 , $w , $h , 25 ); // down
			}
		}

		if( $threshold > 0 ){
			// Calculate the difference between the blurred pixels and the original
			// and set the pixels
			for ( $x = 0 ; $x < $w - 1 ; $x ++)    { // each row
				for ( $y = 0 ; $y < $h ; $y ++)    { // each pixel

					$rgbOrig = ImageColorAt ( $img , $x , $y );
					$rOrig = (( $rgbOrig >> 16 ) & 0xFF );
					$gOrig = (( $rgbOrig >> 8 ) & 0xFF );
					$bOrig = ( $rgbOrig & 0xFF );

					$rgbBlur = ImageColorAt ( $imgBlur , $x , $y );

					$rBlur = (( $rgbBlur >> 16 ) & 0xFF );
					$gBlur = (( $rgbBlur >> 8 ) & 0xFF );
					$bBlur = ( $rgbBlur & 0xFF );

					// When the masked pixels differ less from the original
					// than the threshold specifies, they are set to their original value.
					$rNew = ( abs ( $rOrig - $rBlur ) >= $threshold )
					? max ( 0 , min ( 255 , ( $amount * ( $rOrig - $rBlur )) + $rOrig ))
					: $rOrig ;
					$gNew = ( abs ( $gOrig - $gBlur ) >= $threshold )
					? max ( 0 , min ( 255 , ( $amount * ( $gOrig - $gBlur )) + $gOrig ))
					: $gOrig ;
					$bNew = ( abs ( $bOrig - $bBlur ) >= $threshold )
					? max ( 0 , min ( 255 , ( $amount * ( $bOrig - $bBlur )) + $bOrig ))
					: $bOrig ;



					if (( $rOrig != $rNew ) || ( $gOrig != $gNew ) || ( $bOrig != $bNew )) {
						$pixCol = ImageColorAllocate ( $img , $rNew , $gNew , $bNew );
						ImageSetPixel ( $img , $x , $y , $pixCol );
					}
				}
			}
		}
		else{
			for ( $x = 0 ; $x < $w ; $x ++)    { // each row
				for ( $y = 0 ; $y < $h ; $y ++)    { // each pixel
					$rgbOrig = ImageColorAt ( $img , $x , $y );
					$rOrig = (( $rgbOrig >> 16 ) & 0xFF );
					$gOrig = (( $rgbOrig >> 8 ) & 0xFF );
					$bOrig = ( $rgbOrig & 0xFF );

					$rgbBlur = ImageColorAt ( $imgBlur , $x , $y );

					$rBlur = (( $rgbBlur >> 16 ) & 0xFF );
					$gBlur = (( $rgbBlur >> 8 ) & 0xFF );
					$bBlur = ( $rgbBlur & 0xFF );

					$rNew = ( $amount * ( $rOrig - $rBlur )) + $rOrig ;
					if( $rNew > 255 ){ $rNew = 255 ;}
					elseif( $rNew < 0 ){ $rNew = 0 ;}
					$gNew = ( $amount * ( $gOrig - $gBlur )) + $gOrig ;
					if( $gNew > 255 ){ $gNew = 255 ;}
					elseif( $gNew < 0 ){ $gNew = 0 ;}
					$bNew = ( $amount * ( $bOrig - $bBlur )) + $bOrig ;
					if( $bNew > 255 ){ $bNew = 255 ;}
					elseif( $bNew < 0 ){ $bNew = 0 ;}
					$rgbNew = ( $rNew << 16 ) + ( $gNew << 8 ) + $bNew ;
					ImageSetPixel ( $img , $x , $y , $rgbNew );
				}
			}
		}
		imagedestroy ( $imgCanvas );
		imagedestroy ( $imgBlur );

		return $img ;

	}

	function make_thumb_imagemagick($o_file, $s_file, $zoom, $quality, $type ,$org_w, $org_h)
	{
		$zoom = intval($zoom * 100);
		$quality = intval($quality);
		$org_w = intval($org_w);
		$org_h = intval($org_h);

		$ro_file = realpath($o_file);
		if (! is_file($ro_file)) return $o_file;
		$rs_file = realpath(dirname($s_file))."/".basename($s_file);

		// Make Thumb and check success
		if ( ini_get('safe_mode') != "1" )
		{
			list($amount, $radius, $threshold) = HypCommonFunc::get_unsharp_mask_params();
			exec( HYP_IMAGEMAGICK_PATH."convert -thumbnail {$zoom}% -quality {$quality} -unsharp ".number_format(($radius * 2) - 1, 2).'x1+'.number_format($amount / 100, 2).'+'.number_format($threshold / 100, 2)." \"{$ro_file}\" \"{$rs_file}\"" ) ;
			//@chmod($s_file, 0666);
		}
		else
		{
			// safeモードの場合は、CGIを起動して取得してみる

			$cmds = "?m=r".
					"&p=".rawurlencode(HYP_IMAGEMAGICK_PATH).
					"&z=".$zoom.
					"&q=".$quality.
					"&u=".rawurlencode(HYP_IMAGEMAGICK_UNSHARP).
					"&o=".rawurlencode($ro_file).
					"&s=".rawurlencode($rs_file);

			HypCommonFunc::exec_image_magick_cgi($cmds);
		}

		if( ! is_readable( $s_file ) )
		{
			// can't exec convert, big thumbs!
			return $o_file;
		}
		return $s_file;
	}

	// 画像をリサイズする
	function ImageResize($img, $isize='', $quality=75) {

		$size = @getimagesize($img);
		if (!$size) return false;//画像ファイルではない

		$img = realpath($img);

		if (!preg_match('/^([\d]+)?x([\d]+)?|([\d]+)%?$/i', trim($isize), $arg)) return false;

		if (!empty($arg[3])) {
			$zoom = round($arg[3] / 100);
		} else {
			$w = (empty($arg[1]))? $size[0] : $arg[1];
			$h = (empty($arg[2]))? $size[1] : $arg[2];
			$zoom_w = $w / $size[0];
			$zoom_h = $h / $size[1];
			$zoom = min($zoom_w, $zoom_h);
		}

		if ($zoom >= 1) return FALSE;

		$w = round($size[0] * $zoom);
		$h = round($size[1] * $zoom);

		$tmp = $img . '.tmp';
		$done = HypCommonFunc::make_thumb($img, $tmp, $w, $h, '1,99', TRUE, $quality);

		if ($done === $img) return false;

		unlink($img);
		copy($tmp, $img);
		unlink($tmp);
		clearstatcache();

		return true;
	}

	// 画像を角丸にする
	function ImageMagickRoundCorner($o_file, $s_file = '', $corner = 10, $edge = 0, $refresh = FALSE) {

		if (!defined('HYP_IMAGEMAGICK_PATH') || !HYP_IMAGEMAGICK_PATH) return $o_file;

		if ($o_file === $s_file) $s_file = '';

		// すでに作成済み
		if (!$refresh && $s_file && file_exists($s_file)) return $s_file;

		$is_own = FALSE;
		if (!$s_file) {
			// CGI を直接叩かれて悪戯されないように一時ファイルを利用
			$s_file = $o_file . '.tmp';
			$is_own = TRUE;
		}

		$ro_file = realpath($o_file);
		if (! is_file($ro_file)) return $o_file;
		$rs_file = realpath(dirname($s_file))."/".basename($s_file);

		$size = @ getimagesize($ro_file);
		if (!$size) return $o_file;//画像ファイルではない

		if (file_exists($rs_file)) unlink($rs_file);

		// Make Thumb and check success
		if ( ini_get('safe_mode') != "1" ) {
			// 元画像のサイズ
			$imw = $size[0];
			$imh = $size[1];
			$im_half = floor((min($imw, $imh)/2));

			// check value
			$edge = min($edge, $im_half);
			$corner = min($corner, $im_half);

			$tmpfile = $rs_file . '_tmp.png';

			$cmd = 'convert -size '.$imw.'x'.$imh.' xc:none -channel RGBA -fill white -draw "roundrectangle '.max(0,($edge-1)).','.max(1,($edge-1)).' '.($imw-$edge).','.($imh-$edge).' '.$corner.','.$corner.'" "'.$ro_file.'" -compose src_in -composite "'.$tmpfile.'"';
			exec( HYP_IMAGEMAGICK_PATH . $cmd ) ;

			if ($edge) {
				$cmd = 'convert -size '.$imw.'x'.$imh.' xc:none -fill none -stroke white -strokewidth '.$edge.' -draw "roundrectangle '.($edge-1).','.($edge-1).' '.($imw-$edge).','.($imh-$edge).' '.$corner.','.$corner.'" -shade 135x25 -blur 0x1 -normalize "'.$tmpfile.'" -compose overlay -composite "'.$tmpfile.'"';
				exec( HYP_IMAGEMAGICK_PATH . $cmd ) ;
			}
			copy ($tmpfile, $rs_file);
			unlink($tmpfile);
		} else {
			// safeモードの場合は、CGIを起動して取得してみる

			$cmds = "?m=ro".
					"&p=".rawurlencode(HYP_IMAGEMAGICK_PATH).
					"&z=".$corner.
					"&q=".$edge.
					"&o=".rawurlencode($ro_file).
					"&s=".rawurlencode($rs_file);

			HypCommonFunc::exec_image_magick_cgi($cmds);
		}

		if( ! is_readable( $rs_file ) ) {
			if (file_exists($rs_file)) unlink($rs_file);
			return $ro_file;
		}

		if ($is_own) {
			unlink($ro_file);
			copy($rs_file, $ro_file);
			unlink($rs_file);
		}

		return $rs_file;
	}

	// GD のバージョンを取得
	// RETURN 0:GDなし, 1:Ver 1, 2:Ver 2
	function gdVersion($user_ver = 0)
	{
		if (! extension_loaded('gd')) { return 0; }
		static $gd_ver = 0;
		// Just accept the specified setting if it's 1.
		if ($user_ver == 1) { $gd_ver = 1; return 1; }
		// Use the static variable if function was called previously.
		if ($user_ver !=2 && $gd_ver > 0 ) { return $gd_ver; }
		// Use the gd_info() function if possible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			$match = array();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		// If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];
		return $match[0];
	}

	function check_memory4gd($w,$h)
	{
		// GDで処理可能なメモリーサイズ
		static $memory_limit = NULL;
		if (is_null($memory_limit))
		{
			$memory_limit = HypCommonFunc::return_bytes(ini_get('memory_limit'));
		}
		if ($memory_limit)
		{
			// ビットマップ展開時のメモリー上のサイズ
			$bitmap_size = $w * $h * 3 + 54;

			$now_use_mem = intval(memory_get_usage(true));
			if (!$now_use_mem) {
				$now_use_mem = 4 * 1024 * 1024;
			}
			if ($bitmap_size > ($memory_limit - $now_use_mem - (1 * 1024 * 1024)))
			{
				// メモリー制限に引っ掛かりそう。（マージン 1MB）
				return false;
			}
		}
		return true;
	}

	// イメージを回転
	function rotateImage($src, $count = 1, $quality = 95)
	{
		$src = realpath($src);

		if (! is_file($src)) {
			return false;
		}

		list($w, $h, $type) = @getimagesize($src);

		if (!$w || !$h || ((!defined('HYP_IMAGEMAGICK_PATH') || !HYP_IMAGEMAGICK_PATH) && $type != 2)) return false;

		$angle = (($count > 0 && $count < 4) ? $count : 0 ) * 90;
		if (!$angle) return false;

		if (defined('HYP_JPEGTRAN_PATH') && HYP_JPEGTRAN_PATH && $type == 2)
		{
			// jpegtran を使用
			if (ini_get('safe_mode') != "1")
			{
				$ret = true;
				$tmpfname = @tempnam(dirname($src), "tmp_");
				exec( HYP_JPEGTRAN_PATH."jpegtran -rotate {$angle} -copy all \"{$src}\" ") . '>' . " \"{$tmpfname}\"" ;
				if ( ! @filesize($tmpfname) || ! @unlink($src) )
				{
					$ret = false;
				}
				else
				{
					rename($tmpfname, $src);
					//chmod($src, 0666);
				}
				@unlink($tmpfname);
				return $ret;
			}
			else
			{
				$cmds = "?m=rj".
						"&p=".rawurlencode(HYP_JPEGTRAN_PATH).
						"&z=".$angle.
						"&q=".$quality.
						"&s=".rawurlencode($src);

				return HypCommonFunc::exec_image_magick_cgi($cmds);
			}
		}
		else if (defined('HYP_IMAGEMAGICK_PATH') && HYP_IMAGEMAGICK_PATH)
		{
			// image magick を使用
			if (ini_get('safe_mode') != "1")
			{
				$ret = true;
				$out = array();
				exec( HYP_IMAGEMAGICK_PATH."convert -size {$w}x{$h} -rotate +{$angle} -quality {$quality} \"{$src}\" \"{$src}\"", $out ) ;
				if ($out)
				{
					$ret = false;
				}
				else
				{
					//chmod($src, 0666);
				}
				return $ret;
			}
			else
			{
				$cmds = "?m=ri".
						"&p=".rawurlencode(HYP_IMAGEMAGICK_PATH).
						"&z=".$angle.
						"&q=".$quality.
						"&s=".rawurlencode($src);

				return HypCommonFunc::exec_image_magick_cgi($cmds);
			}
		}
		else
		{
			// GD を使用

			// メモリーチェック
			if (!HypCommonFunc::check_memory4gd($w,$h)) return false;

			$angle = 360 - $angle;
			if (($in = imageCreateFromJpeg($src)) === false) {
				return false;
			}
			if ($w == $h || $angle == 180) {
				$out = imageRotate($in, $angle, 0);
			} elseif ($angle == 90 || $angle == 270) {
				$size = ($w > $h ? $w : $h);

				$portrait = ($h > $w)? true : false;

				// Create a square image the size of the largest side of our src image
				if (($tmp = imageCreateTrueColor($size, $size)) == false) {
					//echo "Failed create square trueColor<br>";
					return false;
				}

				// Exchange sides
				if (($out = imageCreateTrueColor($h, $w)) == false) {
					//echo "Failed create trueColor<br>";
					return false;
				}

				// Now copy our src image to tmp where we will rotate and then copy that to $out
				imageCopy($tmp, $in, 0, 0, 0, 0, $w, $h);
				$tmp2 = imageRotate($tmp, $angle, 0);

				// Now copy tmp2 to $out;
				imageCopy($out, $tmp2, 0, 0, (($angle == 270 && !$portrait) ? abs($w - $h) : 0), (($angle == 90 && $portrait) ? abs($w - $h) : 0), $h, $w);
				imageDestroy($tmp);
				imageDestroy($tmp2);
			} elseif ($angle == 360) {
				imageDestroy($in);
				return true;
			}
			unlink($src);
			imageJpeg($out, $src, $quality);
			imageDestroy($in);
			imageDestroy($out);
			//chmod($src, 0666);
			return true;
		}
	}

	// image_magick.cgi へアクセス
	function exec_image_magick_cgi($cmds)
	{
		if (defined('HYP_IMAGE_MAGICK_URL'))
		{
			$url = HYP_IMAGE_MAGICK_URL;
		}
		else
		{
			die('ERROR: "image_magick.cgi" path is not set.');
		}

		$url .= $cmds;

		$d = new Hyp_HTTP_Request();

		$d->url = $url;
		$d->connect_try = 2;
		$d->connect_timeout = 5;
		$d->read_timeout = 60;

		$d->get();

		if ($d->rc != 200) die("'".$url."' is NG. Not found or access denied.");

		$ret = trim((string)$d->data);
		$ret = ($ret == "ERROR: 0")? true : false;

		return $ret;
	}

	// 外部実行コマンドのパスを設定
	function set_exec_path($dir)
	{
		HypCommonFunc::set_jpegtran_path($dir);
		HypCommonFunc::set_imagemagick_path($dir);
		HypCommonFunc::set_hyp_image_magic_url();
	}

	// Image Magick のパスを設定(定数化)
	function set_imagemagick_path($dir)
	{
		// すでに設定済み
		if (defined('HYP_IMAGEMAGICK_PATH')) return;

		if (substr($dir, -1) != "/") $dir .= "/";
		if (@ file_exists($dir."convert"))
		{
			define ('HYP_IMAGEMAGICK_PATH', $dir);
		}
		return;
	}

	// jpegtran のパスを設定(定数化)
	function set_jpegtran_path($dir)
	{
		// すでに設定済み
		if (defined('HYP_JPEGTRAN_PATH')) return;
		if (substr($dir, -1) != "/") $dir .= "/";
		if (@ file_exists($dir."jpegtran"))
		{
			define ('HYP_JPEGTRAN_PATH', $dir);
		}
		return;
	}


	function set_hyp_image_magic_url($url='')
	{
		// すでに設定済み
		if (defined('HYP_IMAGE_MAGICK_URL')) return;

		if ($url)
		{
			define('HYP_IMAGE_MAGICK_URL', $url);
		}
		else
		{
			// セーフモード時は、image_magick.cgi へのURLを探索してみる
			if ( ini_get('safe_mode') == "1" )
			{
				if (defined('XOOPS_URL'))
				{
					//XOOPS環境下
					$moddir = basename(dirname($_SERVER['REQUEST_URI']));
					if (file_exists(XOOPS_ROOT_PATH."/class/hyp_common/image_magick.cgi"))
					{
						define('HYP_IMAGE_MAGICK_URL', XOOPS_URL."/class/hyp_common/image_magick.cgi");
					}
					else if (file_exists(XOOPS_ROOT_PATH."/modules/{$moddir}/include/hyp_common/image_magick.cgi"))
					{
						define('HYP_IMAGE_MAGICK_URL', XOOPS_URL."/modules/{$moddir}/include/hyp_common/image_magick.cgi");
					}
				}
				else
				{
					$url  = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://'); // scheme
					$url .= $_SERVER['HTTP_HOST'];	// host
					$url .= ($_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT']);  // port

					// DOCUMENT_ROOT と このファイル位置から URL を計算
					if (!empty($_SERVER['DOCUMENT_ROOT']))
					{
						$path = str_replace($_SERVER['DOCUMENT_ROOT'],"",dirname(__FILE__));
						$url .= $path."/image_magick.cgi";
						define('HYP_IMAGE_MAGICK_URL', $url);
					}
				}
			}
		}
		return;
	}

	// 2ch BBQ あらしお断りシステム にリスティングされているかチェック
	function IsBBQListed($safe_reg = '/^$/', $msg = true, $ip = NULL, $checker = array('list.dsbl.org', 'niku.2ch.net'))
	{
		if (is_null($ip)) $ip = $_SERVER['REMOTE_ADDR'];
		if(! preg_match($safe_reg, $ip))
		{
			$host = array_reverse(explode('.', $ip));
			foreach($checker as $chk) {
				if (!$chk) continue;
				if (is_array($chk)) {
					$reg = $chk[1];
					$chk = $chk[0];
				} else {
					$reg = '/^127\.0\.0/';
				}
				$addr = sprintf("%d.%d.%d.%d.". $chk,
					$host[0],$host[1],$host[2],$host[3]);
				$addr = gethostbyname($addr);
				if(preg_match($reg,$addr)) return $msg;
			}
		}
		return false;
	}

	// 2ch BBQ チェック用汎用関数
	function BBQ_Check($safe_reg = "/^(127\.0\.0\.1)/", $msg = true, $ip = NULL, $checker = array('list.dsbl.org', 'niku.2ch.net'))
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$_msg = HypCommonFunc::IsBBQListed($safe_reg, $msg, $ip, $checker);
			if ($_msg !== false)
			{
				exit ($_msg);
			}
		}
		return;
	}

	// URL Check
	function URL_Check(& $post) {
		static $func = NULL;
		$counter = 0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (! $func) {
				$func = create_function('$match','
$ok = FALSE;
$parsed = parse_url($match[0]);
if (isset($parsed[\'host\'])) {
	$ip = gethostbyname($parsed[\'host\']);
	$ok = (preg_match(\'/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/\', $ip));
}
return ($ok)? $match[0] : ($match[1] . "\x08" . $match[2]);');
			}
			if (is_array($post)) {
				foreach (array_keys($post) as $key) {
					$counter += HypCommonFunc::URL_Check($post[$key]);
				}
			} else {
				$post = preg_replace_callback('#(https?://)([^/][^\s]+)#i', $func, $post);
				$counter += substr_count($post, "\x08");
				$post = str_replace("\x08", ' ', $post);
			}
		}
		return $counter;
	}


	// POST SPAM Check
	function PostSpam_Check($post, $encode = '', $encodehint = '')
	{
		if (function_exists('mb_convert_variables') && $encode) {
			// 文字エンコード変換
			if ($encodehint && isset($post[$encodehint])) {
				$post_enc = mb_detect_encoding($post[$encodehint]);
				if ($encode !== $post_enc) {
					mb_convert_variables($encode, mb_detect_encoding($post[$encodehint]), $post);
				}
			} else {
				// Key:url, excerpt があればトラックバックかも->文字コード変換
				if (isset($post['url']) && isset($post['excerpt']) && function_exists('mb_convert_variables')) {
					if (isset($post['charset']) && $post['charset'] != '') {
						// TrackBack Ping で指定されていることがある
						// うまくいかない場合は自動検出に切り替え
						if (mb_convert_variables($encode,
						    $post['charset'], $post) !== $post['charset']) {
							mb_convert_variables($encode, 'auto', $post);
						}
					} else if (! empty($post)) {
						// 全部まとめて、自動検出／変換
						mb_convert_variables($encode, 'auto', $post);
					}
				}
			}
		}

		static $filters = NULL;
		if (is_null($filters)) {$filters = HypCommonFunc::PostSpam_filter();}
		$counts = array();
		$counts[0] = $counts[1] = $counts[2] = $counts[3] = 0;

		if (isset($filters['pass_keys'])) {
			$ignore_keys = $filters['pass_keys'];
			unset($filters['pass_keys']);
		} else {
			$ignore_keys = array();
		}

		foreach($post as $key => $dat)
		{
			if (in_array($key, $ignore_keys)) continue;
			$tmp = array();
			$tmp['a'] = $tmp['bb'] = $tmp['url'] = $tmp['filter'] = 0;
			if (is_array($dat))
			{
				list($tmp['a'],$tmp['bb'],$tmp['url'],$tmp['filter']) = HypCommonFunc::PostSpam_Check($dat);
			}
			else
			{
				// NULLバイト削除
				$dat = str_replace("\0", '', $dat);

				// <a> タグの個数
				$tmp['a'] = count(preg_split("/<a.+?\/a>/is",$dat)) - 1;
				// [url] タグの個数
				$tmp['bb'] = count(preg_split("/\[url=.+?\/url\]/is",$dat)) - 1;
				// URL の個数
				$tmp['url'] = count(preg_split("/(ht|f)tps?:\/\/[^\s]+/i",$dat)) - 1;
				// フィルター
				if ($filters)
				{
					foreach($filters as $reg => $point)
					{
						if ($reg === 'array_rule') {
							if (isset($point['ignore_fileds'])) {
								foreach($point['ignore_fileds'][0] as $checkkey => $targets) {
									foreach($targets as $target) {
										if (strtolower($checkkey) === strtolower($key) && $post[$key] ){
											if (!$target || preg_match('/'.preg_quote($target,'/').'/i',$_SERVER['PHP_SELF'])) {
												$tmp['filter'] += $point['ignore_fileds'][1];
											}
										}
									}
								}
							}
						} else {
							$tmp['filter'] += (count(preg_split($reg,$dat)) - 1) * $point;
						}
					}
				}
			}
			$counts[0] = max($counts[0], $tmp['a']);
			$counts[1] = max($counts[1], $tmp['bb']);
			$counts[2] = max($counts[2], $tmp['url']);
			$counts[3] = max($counts[3], $tmp['filter']);
		}
		return $counts;
	}

	// POST SPAM フィルター
	function PostSpam_filter($reg="", $point=1)
	{
		static $regs = array();
		if (empty($reg)) {return $regs;}
		$regs[$reg] = $point;
	}

	// POST SPAM Check 汎用関数
	function get_postspam_avr($alink=1,$bb=1,$url=1,$encode='EUC-JP',$encodehint='')
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			list($a_p,$bb_p,$url_p,$filter_p) = HypCommonFunc::PostSpam_Check($_POST, $encode, $encodehint);
			return $a_p * $alink + $bb_p * $bb + $url_p * $url + $filter_p;
		}
		else
		{
			return 0;
		}
	}

	// Input フィルター
	// $strength - 0: null 以外許可, 1: SoftBankの絵文字と\t,\r,\n は許可, 2: \t,\r,\n のみ許可
	function input_filter($param, $strength = 2, $encode = null) {

		static $done = array('POST' => 0, 'GET' => 0);

		if (is_array($param)) {
			$is = array();
			// Check done
			if ($param === $_GET) {
				if ($done['GET'] > $strength) return $param;
				$is['GET'] = true;
			} elseif ($param === $_POST) {
				if ($done['POST'] > $strength) return $param;
				$is['POST'] = true;
			}
			foreach ($param as $key => $val) {
				$param[$key] = HypCommonFunc::input_filter($val, $strength, $encode);
			}
			if (isset($is['GET'])) {
				$done['GET'] = $strength + 1;
			} elseif (isset($is['POST'])) {
				$done['POST'] = $strength + 1;
			}
			return $param;
		} else {
			$param = str_replace(array("\0", '&#8203;'), '', $param);
			if ($encode === 'UTF-8') {
				// \xEF\xBB\xBF: BOM, \xE2\x80\x8B: ZERO WIDTH SPACE(&#8203;)
				$param = str_replace(array("\xEF\xBB\xBF", "\xE2\x80\x8B"), '', $param);
			}
			if (defined('HYP_COMMON_INPUT_FILTER_REGEX')) {
				$param = preg_replace(HYP_COMMON_INPUT_FILTER_REGEX, '', $param);
			} else {
				switch((int)$strength) {
					case 1:
						$param = preg_replace('/[\x01-\x08\x0b-\x0c\x0e\x10-\x1a\x1c-\x1f\x7f]+/', '', $param);
						break;
					case 2:
						$param = preg_replace('/[\x01-\x08\x0b\x0c\x0e-\x1f\x7f]+/', '', $param);
						break;
					default:
				}
			}
			if (defined('HYP_COMMON_INPUT_FILTER_STRIPSLASHES')) $param = stripslashes($param);
			return $param;
		}
	}

	// 機種依存文字フィルター
	function dependence_filter($post)
	{
		if (!isset($post) || !function_exists("mb_ereg_replace")) {return $post;}

		if (!defined('HYP_POST_ENCODING') || (HYP_POST_ENCODING !== 'EUC-JP' && HYP_POST_ENCODING !== 'EUCJP-WIN' && HYP_POST_ENCODING !== 'UTF-8')) {return $post;}

		static $bef = null;
		static $aft = null;
		static $mac = null;

		if (is_null($mac)) {
			$mac = (empty($_SERVER["HTTP_USER_AGENT"]))? FALSE : strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),"mac");
		}

		if ($mac && HYP_POST_ENCODING !== 'UTF-8') {return $post;}

		if (is_null($bef)) {
			$enc = (HYP_POST_ENCODING === 'UTF-8')? '_utf8' : '';

			$datfile = ($mac === FALSE)? dirname(__FILE__).'/dat/win_ext'.$enc.'.dat' : dirname(__FILE__).'/dat/mac_ext'.$enc.'.dat';

			if (file_exists($datfile)) {
				$bef = $aft = array();
				foreach(file($datfile) as $line) {
					if ($line[0] != "/" && $line[0] != "#") {
						list($bef[],$aft[]) = explode("\t",rtrim($line));
					}
				}
			}
		}

		//if (is_array($post)) {
		if (HypCommonFunc::is_multi_array($post)) {
			foreach ($post as $_key=>$_val) {
				$post[$_key] = HypCommonFunc::dependence_filter($_val);
			}
		} else {
			mb_regex_encoding(HYP_POST_ENCODING);

			// 半角カナを全角に
			//$post = mb_convert_kana($post, "KV", "EUC-JP");

			// 変換テーブル
			for ($i=0; $i<sizeof($bef); $i++) {
				if (is_array($post)) {
					foreach ($post as $_key=>$_val) {
						$post[$_key] = mb_ereg_replace($bef[$i], $aft[$i], $_val);
					}
				} else {
					$post = mb_ereg_replace($bef[$i], $aft[$i], $post);
				}
			}
		}

		return $post;
	}

	// 文字エンコード変換前に範囲外の文字を実体参照値に変換する
	function encode_numericentity(& $arg, $toencode, $fromencode, $keys = array()) {
		$fromencode = strtoupper($fromencode);
		$toencode = strtoupper($toencode);
		if ($fromencode === $toencode || $toencode === 'UTF-8') return;
		if ($toencode === 'EUC-JP') $toencode = 'eucJP-win';
		if (is_array($arg)) {
			foreach (array_keys($arg) as $key) {
				if (!$keys || in_array($key, $keys)) {
					HypCommonFunc::encode_numericentity($arg[$key], $toencode, $fromencode, $keys);
				}
			}
		} else {
			if ($arg === mb_convert_encoding(mb_convert_encoding($arg, $toencode, $fromencode), $fromencode, $toencode)) {
				return;
			}
			if (extension_loaded('mbstring')) {
				$_sub = mb_substitute_character();
				mb_substitute_character('long');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/', "\x08$1", $arg);
				if ($fromencode !== 'UTF-8') $arg = mb_convert_encoding($arg, 'UTF-8', $fromencode);
				$arg = mb_convert_encoding($arg, $toencode, 'UTF-8');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/e', '"&#".base_convert("$1",16,10).";"', $arg);
				$arg = preg_replace('/\x08([0-9A-F]{2,5})/', 'U+$1', $arg);
				mb_substitute_character($_sub);
				$arg = mb_convert_encoding($arg, $fromencode, $toencode);
			} else {
				$str = '';
				$max = mb_strlen($arg, $fromencode);
				$convmap = array(0x0080, 0x10FFFF, 0, 0xFFFFFF);
				for ($i = 0; $i < $max; $i++) {
					$org = mb_substr($arg, $i, 1, $fromencode);
					if ($org === mb_convert_encoding(mb_convert_encoding($org, $toencode, $fromencode), $fromencode, $toencode)) {
						$str .= $org;
					} else {
						$str .= mb_encode_numericentity($org, $convmap, $fromencode);
					}
				}
				$arg = $str;
			}
		}
		return;
	}

	// リファラーから検索語と検索エンジンを取得し定数に定義する
	function set_query_words($qw="HYP_QUERY_WORD",$qw2="HYP_QUERY_WORD2",$en="HYP_SEARCH_ENGINE_NAME",$tmpdir="",$enc='EUC-JP',$use_kakasi=TRUE)
	{
		if (!defined($qw))
		{
			if (file_exists(dirname(__FILE__)."/hyp_get_engine.php"))
			{
				include_once(dirname(__FILE__)."/hyp_get_engine.php");
				HypGetQueryWord::set_constants($qw,$qw2,$en,$tmpdir,$enc);
			}
			else
			{
				define($qw , "");
				define($qw2, "");
				define($en , "");
			}
			define('HYP_QUERY_WORD_CONST_NAME', $qw);
			define('HYP_QUERY_WORD2_CONST_NAME', $qw2);
			define('HYP_SEARCH_ENGINE_NAME_CONST_NAME', $en);
		}
	}

	// php.ini のサイズ記述をバイト値に変換
	function return_bytes($val) {
		$val = trim(strval($val));
		if ($val === '-1') $val = 0;
		if ($val) {
			// for ex. 1mb, 1KB
			$val = rtrim($val, 'bB');
			$last = strtolower(substr($val, -1));
			switch($last) {
				// 'G' は、PHP 5.1.0 より有効となる
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			$val = floor($val);
		}
		return $val;
	}

	// 配列から正規表現を得る
	function get_reg_pattern($words)
	{
		return HypCommonFunc::get_matcher_regex_safe($words);
	}

	function get_matcher_regex_safe ($pages, $spliter = "\t", $array_fix = true, $nest = 0, $ci = 1) {
		if ($array_fix) {
			$pages = array_map('trim', $pages);
			if ($ci) $pages = array_map('strtolower', $pages);
			$pages = array_unique($pages);
			foreach(array_keys($pages, '') as $key) {
				unset($pages[$key]);
			}
			sort($pages, SORT_STRING);
		}

		++$nest;
		$reg = HYpCommonFunc::get_matcher_regex_safe_sub($pages);
		$regs = preg_split("/(\d+)\x08/", $reg, -1, PREG_SPLIT_DELIM_CAPTURE);
		$pats = array();
		$index = 0;
		reset($regs);
		while (list($key, $pat) = each($regs)) {
			list($key, $val) = each($regs);
			if (!$val) $val = count($pages);
			if (@ preg_match('/' . $pat. '/', '') === false) {
				if ($nest <= 10) {
					$count = $val - $index;
					$split = floor(($val - $index) / 2);
					$pages1 = array_slice($pages, $index, $split);
					$pages2 = array_slice($pages, $split, $count - $split);
					$pats[] = HYpCommonFunc::get_matcher_regex_safe($pages2, $spliter, false, $nest, $ci);
					$pats[] = HYpCommonFunc::get_matcher_regex_safe($pages1, $spliter, false, $nest, $ci);
					$index = $val;
				}
			} else {
				$pats[] = $pat;
			}
		}
		return join($spliter, $pats);
	}

	function get_matcher_regex_safe_sub (& $array, $offset = 0, $sentry = NULL, $pos = 0, $nest = 0)
	{
		++$nest;
		$limit = 1024 * 30;

		if (empty($array)) return '(?!)'; // Zero
		if ($sentry === NULL) $sentry = count($array);

		// Too short. Skip this
		$skip = ($pos >= mb_strlen($array[$offset]));
		if ($skip) ++$offset;

		// Generate regex for each value
		$regex = '';
		$index = $offset;
		$multi = FALSE;
		$reglen = 0;
		while ($index < $sentry) {
			if ($index !== $offset) {
				$multi = TRUE;
				if ($nest === 1 && strlen($regex) - $reglen > $limit) {
					$reglen = strlen($regex);
					$regex .= ')'.($index)."\x08(?:";
				} else {
					$regex .= '|'; // OR
				}
			}

			// Get one character from left side of the value
			$char = mb_substr($array[$index], $pos, 1);

			// How many continuous keys have the same letter
			// at the same position?
			for ($i = $index; $i < $sentry; ++$i)
				if (mb_substr($array[$i], $pos, 1) !== $char) break;

			if ($index < ($i - 1)) {
				// Some more keys found
				// Recurse
				$regex .= str_replace(array(' ', '#'), array('\\ ', '\\#'), preg_quote($char, '/')) .
				HypCommonFunc::get_matcher_regex_safe_sub($array, $index, $i, $pos + 1, $nest);
			} else {
				// Not found
				$regex .= str_replace(array(' ', '#'), array('\\ ', '\\#'),
				preg_quote(mb_substr($array[$index], $pos), '/'));
			}
			$index = $i;
		}

		if ($skip || $multi){
			$regex = '(?:' . $regex . ')';
		}
		if ($skip) $regex .= '?'; // Match for $pages[$offset - 1]
		return $regex;
	}

	function register_bad_ips( $ip = null, $protectorTTL = null )
	{
		if( empty( $ip ) ) $ip = $_SERVER['REMOTE_ADDR'] ;
		if( empty( $ip ) ) return false ;

		if (!is_null($protectorTTL) && XC_CLASS_EXISTS('Protector')) {
			global $xoopsUser;
			$protector =& Protector::getInstance();
			$conf = $protector->getConf() ;
			$can_ban = true;
			if (is_object($xoopsUser)) {
				$uid = $xoopsUser->getVar('uid') ;
				$can_ban = count( @array_intersect( $xoopsUser->getGroups() , @unserialize( @$conf['bip_except'] ) ) ) ? false : true ;
			}
			if ($can_ban) {
				$protectorTTL = intval($protectorTTL);
				if ($protectorTTL > 0) {
					$time = time() + $protectorTTL;
				} else {
					$time = 0;
				}
				$protector->register_bad_ips($time);
			}
		} else {
			$db = Database::getInstance() ;
			$rs = $db->query( "SELECT conf_value FROM ".$db->prefix("config")." WHERE conf_name='bad_ips' AND conf_modid=0 AND conf_catid=1" ) ;
			list( $bad_ips_serialized ) = $db->fetchRow( $rs ) ;
			$bad_ips = unserialize( $bad_ips_serialized ) ;
			$bad_ips[] = $ip ;

			$conf_value = addslashes( serialize( array_unique( $bad_ips ) ) ) ;
			$db->queryF( "UPDATE ".$db->prefix("config")." SET conf_value='$conf_value' WHERE conf_name='bad_ips' AND conf_modid=0 AND conf_catid=1" ) ;
		}
		return true ;
	}

	function html_wordwrap(& $html, $num = 36, $sep = '&#8203;') {
		$ret = preg_replace_callback('/(<(script|textarea|style|option|pre).+?<\/\\2>|<[^>]+?>)|((?>&#?[a-z0-9]+?;|\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]|[!=\x23-\x3b\x3f-\x7e]){'.$num.'})/isS',
		create_function('$arg',
			'if ($arg[1]) { return $arg[1]; } else { return $arg[3] . "'.$sep.'";}'
		),$html);
		if (! is_null($ret)) {
			$html = $ret;
		}
	}

	// $var が多元配列かを検査
	function is_multi_array($var) {
		if (!is_array($var)) return FALSE;
		$ret = FALSE;
		foreach($var as $chk) {
			if (is_array($chk)) {
				$ret = TRUE;
				break;
			}
		}
		return $ret;
	}

	// IDN ( Internationalized Domain Name ) encoder & decoder
	function convertIDN ($host, $mode = 'auto', $encode = '') {
		static $converted = array(); // For convert cache
		static $idn; // idna_convert object

		// build object
		if (! is_object($idn)) {
			if (!function_exists('mb_convert_encoding')) {
				include_once dirname(__FILE__) . '/mbemulator/mb-emulator.php';
			}
			require_once dirname(__FILE__) . '/idna/idna_convert.class.php';
			$idn = new idna_convert();
		}

		if (! $encode) {
			$encode = mb_internal_encoding();
		}

		if ($mode !== 'encode' && $mode !== 'decode') {
			if (preg_match('/[^A-Za-z0-9.-]/', $host)) {
				if (! $encode) {
					$encode = mb_detect_encoding($host);
				}
				$mode = 'encode';
			} else if (strtolower(substr($host, 0, 4)) === 'xn--') {
				$mode = 'decode';
			} else {
				$mode = 'pass';
			}
		}

		if ($mode === 'encode') {
			// Check cache
			if (isset($converted[$encode][$host])) {
				return $converted[$encode][$host];
			}
			// Do encode
			$encoded = mb_convert_encoding($host, 'UTF-8', $encode);
			//echo $encoded;
			if (strpos($encoded, '&') !== FALSE) {
				$convmap = array(0x0, 0x10000, 0, 0xfffff);
				//$convmap = array(0x0080, 0x10FFFF, 0, 0xFFFFFF);
				$encoded = mb_decode_numericentity($encoded, $convmap, 'UTF-8');
			}
			//exit( $encoded);
			$encoded = $idn->encode($encoded);
			$converted[$encode][$host] = $encoded;
			return $encoded;
		} else if ($encode && $mode === 'decode') {
			$encoded = strtolower($host);
			// Check cache
			if (isset($converted[$encode]) && $decoded = array_search($encoded, $converted[$encode])) {
				return $decoded;
			}
			// Do decode
			$decoded = $idn->decode($encoded);
			HypCommonFunc::encode_numericentity($decoded, $encode, 'UTF-8');
			$decoded = mb_convert_encoding($decoded, $encode, 'UTF-8');
			$converted[$encode][$decoded] = $encoded;
			return $decoded;
		}

		return $host;
	}

	// parse_url for IDN (simple version)
	function i18n_parse_url ($url) {
		$reg = '#^([A-Za-z0-9]+)://(?:([A-Za-z0-9_-]+):([A-Za-z0-9_-]+)@)?([^/"<>:]+):?([\d]*)([^?]*)\??([^\#]*)\#?(.*)$#';
		if (preg_match($reg, $url, $match)) {
			$ret = array();
			if (! empty($match[1])) $ret['scheme'] = $match[1];
			if (! empty($match[2])) $ret['user'] = $match[2];
			if (! empty($match[3])) $ret['pass'] = $match[3];
			if (! empty($match[4])) $ret['host'] = $match[4];
			if (! empty($match[5])) $ret['port'] = $match[5];
			if (! empty($match[6])) $ret['path'] = $match[6];
			if (! empty($match[7])) $ret['query'] = $match[7];
			if (! empty($match[8])) $ret['fragment'] = $match[8];
			if (preg_match('/[^A-Za-z0-9.-]/', $ret['host'])) {
				$ret['host'] = HypCommonFunc::convertIDN($ret['host'], 'encode');
			}
			if (isset($ret['scheme']) && strtolower($ret['scheme']) === 'https') {
				$ret['https'] = 'ssl://';
			} else {
				$ret['https'] = '';
			}
			return $ret;
		} else {
			return FALSE;
		}
	}

	// Make Emoji pad
	function make_emoji_pad ($id, $checkmsg = '', $clearDisplayId = '', $emojiurl = '', $writeJS = TRUE, $emj_list = NULL) {
		$useList = ($emj_list !== 'all');

		if ($useList && ! is_array($emj_list)) {
			$emj_list = array(
				140,141,142,143,144,1021,1022,1023,1024,1025,1026,1027,1029,1030,1031,1032,
				1033,1034,1035,1071,1072,1076,145,156,150,151,152,157,158,162,163,164,
				146,155,147,149,136,137,138,139,154,153,1028,86,87,88,85,84,
				1,2,3,4,5,6,8,1052,172,100,101,1068,1069,1070,1073,1074,
				9,10,11,12,13,14,15,16,17,18,19,20,80,81,82,83,
				1011,75,110,105,106,107,74,1012,76,103,1054,1056,1058,1059,1060,1061,
				115,116,123,125,126,127,128,129,130,131,132,133,134,1038,1039,1043,
				1010,113,114,119,120,135,1041,1044,59,104,89,90,48,49,50,94,
				38,39,40,41,42,43,44,1051,1053,45,46,47,1046,1048,66,67,
				1040,22,23,24,25,26,27,28,51,52,53,54,55,56,57,58,
				61,62,63,65,68,69,70,71,72,73,1063,1075,1066,1062,1019,1064,
				1065,77,79,91,92,1003,1005,1016,1015,1008,167,176,95,96,97,98,
				30,31,32,33,34,35,36,37,1018,102
			);
		}

		if (! $checkmsg) $checkmsg = 'Emoji pad';
		if (! $emojiurl) $emojiurl = ((defined('XOOPS_URL'))? XOOPS_URL : '.') . '/images/emoji';

		$html = <<<EOD
<div class="norich">
<input type="checkbox" id="emoji_onoff_$id" onclick="if(this.checked){xoopsGetElementById('emoji_buttons_pre_$id').style.display='block';xoopsGetElementById('$id').focus();}else{xoopsGetElementById('emoji_buttons_pre_$id').style.display='none'};" /><label for="emoji_onoff_$id">$checkmsg</label>
<div id="emoji_buttons_pre_$id" class="image_button_base" style="display:none;width:256px;" onclick="return false;">
EOD;
		if ($useList) {
			$i = 0;
			foreach($emj_list as $emjcnt) {
				$html .= '<span style="padding:1px;cursor:pointer;" onclick="hypEmojiPadSet(\''.$id.'\', \''.$emjcnt.'\'); return false;">[emj:'.$emjcnt.']</span>';
				$i++;
			}
		} else {
			for ($emjline = 1; $emjline < 1077; $emjline += 16) {
				if ($emjline == 177) $emjline = 1001;
				for ($emjcnt = $emjline; $emjcnt < $emjline + 16; $emjcnt++) {
					if ($emjcnt > 1076) break;
					$html .= '<span style="padding:1px;cursor:pointer;" onclick="hypEmojiPadSet(\''.$id.'\', \''.$emjcnt.'\'); return false;">[emj:'.$emjcnt.']</span>';
				}
			}
		}
		$html .= '</div></div>';
		if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
			HypCommonFunc::loadClass('MobilePictogramConverter');
		}
		$mpc =& MobilePictogramConverter::factory_common();
		$mpc->setImagePath($emojiurl);
		$mpc->setString($html, FALSE);
		$html = $mpc->autoConvertModKtai();

		$jshtml = $writeJS? str_replace(array("'", "\r\n", "\r", "\n"), array('\\\'', ''), $html) : '';
		$ret = $writeJS? '' : $html;

		$ret .= <<<EOD
<script type="text/javascript"><!--//
if (typeof hypEmojiPadSet != 'function') {
	var hypEmojiPadSet = function(id, emjCode) {
		var revisedMessage;
		var textareaDom = xoopsGetElementById(id);
		xoopsInsertText(textareaDom, "[emj:"+emjCode+"]");
		textareaDom.focus();
		return;
	};
}
(function(){
	var clearDisplayId = "$clearDisplayId";
	if (clearDisplayId && xoopsGetElementById(clearDisplayId)) xoopsGetElementById(clearDisplayId).style.display = '';
	var html = '{$jshtml}';
	if (html) {
		if (!!Prototype) {
			document.observe("dom:loaded", function(){
				$('emoji_button_pics_{$id}').innerHTML = html;
				if (!!XpWiki && Prototype.Browser.IE) {
					$('emoji_buttons_pre_$id').observe('mousedown', function(){wikihwlper_caretPos();});
				}
			});
		} else {
			document.write(html);
			if (!!XpWiki && Prototype.Browser.IE) {
				$('emoji_buttons_pre_$id').observe('mousedown', function(){wikihwlper_caretPos();});
			}
		}
	}
})();
// -->
</script>
<div id="emoji_button_pics_{$id}"></div>
EOD;
		return $ret;
	}

	// flock safty file_get_contents()
	function flock_get_contents ($filename, $maxRetry = 10) {
		$return = FALSE;
		if (is_string($filename) && !empty($filename)) {
			if (is_readable($filename)) {
				if ($handle = @fopen($filename, 'r')) {
					$i = 0;
					while ($return === FALSE && $maxRetry > $i++) {
						if (flock($handle, LOCK_SH)) {
							if ($return = file_get_contents($filename)) {
								flock($handle, LOCK_UN);
							}
						}
						if ($return === FALSE) usleep(50000); // Wait 50ms
					}
					fclose($handle);
				}
			}
		}
		return $return;
	}

	function flock_put_contents ($filename, $src, $mode = 'wb', $maxRetry = 10) {
		$return = FALSE;
		if (is_string($filename) && ! empty($filename)) {
			if ($handle = @ fopen($filename, $mode)) {
				$i = 0;
				while ($return === FALSE && $maxRetry > $i++) {
					if (flock($handle, LOCK_EX)) {
						$return = fwrite($handle, $src);
						flock($handle, LOCK_UN);
					}
					if ($return === FALSE) usleep(50000); // Wait 50ms
				}
				fclose($handle);
			}
		}
		return $return;
	}

	function readfile($file, $use_content_encoding = FALSE) {
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
		// Range: bytes=xxx-xxx
		if (isset($_SERVER['HTTP_RANGE'])) {
			$range = $_SERVER['HTTP_RANGE'];
			$fsize = filesize($file);
			if (preg_match('/^bytes=(\d+)\-(\d+)$/i', $range, $arr)) {
				$offset = $arr[1];
				$end = $arr[2];
				$len = $end - $offset + 1;
				header('HTTP/1.1 206 Partial Content');
				header('Accept-Ranges: bytes');
				header(sprintf('Content-Range: bytes %d-%d/%d',$offset, $end, $fsize));
				header('Content-Length: ' . $len);
				echo HypCommonFunc::file_get_contents($file, false, null, $offset, $len);
				return;
			}
		}
		@readfile($file);
	}

	function file_get_contents($filename, $incpath = false, $resource_context = null, $offset = -1, $maxlen = -1) {
		if (version_compare(PHP_VERSION, '5.1.0', '<')) {
			if (false === $fh = fopen($filename, 'rb', $incpath)) {
				trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
				return false;
			}

			if ($offset > -1 && $maxlen > -1) {
				$readsize = $offset + $maxlen;
			} else {
				$readsize = -1;
			}

			clearstatcache();
			$fsize = @filesize($filename);
			if ($readsize > -1 && $fsize > $readsize) {
				$data = fread($fh, $readsize);
				if ($offset > 0) {
					$data = substr($data, $offset);
				}
			} else {
				if ($fsize) {
					$data = fread($fh, $fsize);
				} else {
					$data = '';
					while (!feof($fh)) {
						$data .= fread($fh, 8192);
					}
				}
			}

			fclose($fh);
			return $data;
		} else {
			return file_get_contents($filename, $incpath, $resource_context, $offset, $maxlen);
		}
	}

	function chown($filename, $preserve_time = TRUE) {
		static $php_uid; // PHP's UID

		if (! isset($php_uid)) {
			if (extension_loaded('posix')) {
				$php_uid = posix_getuid(); // Unix
			} else {
				$php_uid = 0; // Windows
			}
		}

		// Check owner
		$stat = stat($filename) or
			die('HypCommonFunc::chown(): stat() failed for: '  . basename(htmlspecialchars($filename)));
		if ($stat[4] === $php_uid) {
			// NOTE: Windows always here
			$result = TRUE; // Seems the same UID. Nothing to do
		} else {

			$tmp = $filename . '.tmp';

			$i = 0;
			while($donot = is_file($tmp)) {
				if (++$i > 100) break;
				clearstatcache();
				usleep(50000); // wait 50ms
			}
			if ($donot) {
				if (filemtime($tmp) + 30 < time()) {
					if (! @ unlink($tmp)) {
						die('HypCommonFunc::chown(): failed. Not writable a flie. "'.basename(htmlspecialchars($tmp)).'"');
					}
				} else {
					die('HypCommonFunc::chown(): failed. Already exists "'.basename(htmlspecialchars($tmp)).'"');
				}
			}


			// Lock source $filename to avoid file corruption
			// NOTE: Not 'r+'. Don't check write permission here
			$ffile = fopen($filename, 'r') or
				die('HypCommonFunc::chown(): fopen() failed for: ' .
					basename(htmlspecialchars($filename)));

			// Try to chown by re-creating files
			// NOTE:
			//   * touch() before copy() is for 'rw-r--r--' instead of 'rwxr-xr-x' (with umask 022).
			//   * (PHP 4 < PHP 4.2.0) touch() with the third argument is not implemented and retuns NULL and Warn.
			//   * @unlink() before rename() is for Windows but here's for Unix only
			$i = 0;
			while(! $lock = flock($ffile, LOCK_EX)) {
				if (++$i > 100) break;
				usleep(50000); // wait 50ms
			}
			if ($lock) {
				$result = touch($tmp) && copy($filename, $tmp) &&
					($preserve_time ? (touch($tmp, $stat[9], $stat[8]) || touch($tmp, $stat[9])) : TRUE) &&
					rename($tmp, $filename);
				flock($ffile, LOCK_UN);
				fclose($ffile) or die('pkwk_chown(): fclose() failed');
				if ($result === FALSE) @unlink($tmp);
			} else {
				fclose($ffile);
				@unlink($tmp);
				die('HypCommonFunc::chown(): flock() failed for: ' .
					basename(htmlspecialchars($filename)));
			}
		}

		return $result;
	}

	function touch($filename, $time = FALSE, $atime = FALSE) {
		// Is the owner incorrected and unable to correct?
		if (! is_file($filename) || HypCommonFunc::chown($filename)) {
			if ($time === FALSE) {
				$result = touch($filename);
			} else if ($atime === FALSE) {
				$result = touch($filename, $time);
			} else {
				$result = touch($filename, $time, $atime);
			}
			return $result;
		} else {
			die('HypCommonFunc::touch(): Invalid UID and (not writable for the directory or not a flie): ' .
				htmlspecialchars(basename($filename)));
		}
	}

	// 検索語を展開する
	function get_search_words($words, $special=false, $enc='EUC-JP')
	{
		$retval = array();

		//if (defined('XOOPS_USE_MULTIBYTES') && XOOPS_USE_MULTIBYTES && (!function_exists('mb_strlen') || !function_exists('mb_substr'))) return $retval;

		// Perlメモ - 正しくパターンマッチさせる
		// http://www.din.or.jp/~ohzaki/perl.htm#JP_Match
		$eucpre = $eucpost = '';
		$enc = strtoupper($enc);
		$is_utf8 = false;
		if ($enc === 'EUC-JP' || $enc === 'EUCJP-WIN')
		{
			$eucpre = '(?<!\x8F)';
			// # JIS X 0208 が 0文字以上続いて # ASCII, SS2, SS3 または終端
			$eucpost = '(?=(?:[\xA1-\xFE][\xA1-\xFE])*(?:[\x00-\x7F\x8E\x8F]|\z))';
		} else if ($enc === 'UTF-8') {
			$is_utf8 = true;
		}
		// $special : htmlspecialchars()を通すか
		$quote_func = create_function('$str',$special ?
			'return preg_quote($str,"/");' :
			'return preg_quote(htmlspecialchars($str),"/");'
		);
		// LANG=='ja'で、mb_convert_kanaが使える場合はmb_convert_kanaを使用
		$convert_kana_exists = function_exists('mb_convert_kana');
		$convert_kana = create_function('$str,$option,$enc',
			($convert_kana_exists) ?
				'return mb_convert_kana($str,$option,$enc);' : 'return $str;'
		);
		$mb_strlen = create_function('$str,$enc',
			(function_exists('mb_strlen')) ?
				'return mb_strlen($str,$enc);' : 'return strlen($str);'
		);
		$mb_substr = create_function('$str,$start,$len,$enc',
			(function_exists('mb_substr')) ?
				'return mb_substr($str,$start,$len,$enc);' : 'return substr($str,$start,$len);'
		);

		foreach ($words as $word)
		{
			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			$word_zk = $convert_kana($word,'aKCV',$enc);
			$chars = array();
			for ($pos = 0; $pos < $mb_strlen($word_zk,$enc);$pos++)
			{
				$char = $mb_substr($word_zk,$pos,1,$enc);
				$arr = array($quote_func($char));
				if (strlen($char) == 1) // 英数字
				{
					$arr[] = $quote_func($char); // 英文字
					if ($convert_kana_exists) {
						$arr[] = $quote_func($convert_kana(strtoupper($char),"A",$enc)); // 全角大文字
						$arr[] = $quote_func($convert_kana(strtolower($char),"A",$enc)); // 全角小文字
					}
				}
				else // マルチバイト文字
				{
					$arr[] = $quote_func($convert_kana($char,"c",$enc)); // ひらがな
					$arr[] = $quote_func($convert_kana($char,"k",$enc)); // 半角カタカナ
				}
				if ($is_utf8) {
					$chars[] = '['.join('',array_unique($arr)).']';
				} else {
					$chars[] = '(?:'.join('|',array_unique($arr)).')';
				}
			}
			$retval[$word] = $eucpre.join('',$chars).$eucpost;
		}
		return $retval;
	}
}

/*
 *   HTTPリクエストを発行し、データを取得する
 * $url     : http://から始まるURL(http://user:pass@host:port/path?query)
 * $method  : GET, POST, HEADのいずれか(デフォルトはGET)
 * $headers : 任意の追加ヘッダ
 * $post    : POSTの時に送信するデータを格納した配列('変数名'=>'値')
 * $redirect_max : HTTP redirectの回数制限
*/

if( ! XC_CLASS_EXISTS( 'Hyp_HTTP_Request' ) )
{
class Hyp_HTTP_Request
{
	var $url='';
	var $method='GET';
	var $headers='';
	var $post=array();
	var $ua='';

	var $uri='';
	var $hash = '';

	var $iniLoaded = FALSE;

	// リダイレクト回数制限
	var $redirect_max=10;
	// 同期モード or 非同期モード
	var $blocking=TRUE;
	// 接続試行回数
	var $connect_try=1;
	// 接続時タイムアウト
	var $connect_timeout=3;
	// 通信時タイムアウト
	var $read_timeout=10;
	// POST文字エンコード
	var $content_charset='';

	var $network_reg = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';

	// プロキシ使用？
	var $use_proxy=0;

	// proxy ホスト
	var $proxy_host='proxy.xxx.yyy.zzz';

	// proxy ポート番号
	var $proxy_port='';

	// プロキシサーバを使用しないホストのリスト
	var $no_proxy=array(
		'127.0.0.1',
		'localhost',
		//'192.168.1.0/24',
		//'no-proxy.com',
	);

	// プロキシ認証
	var $need_proxy_auth=0;
	var $proxy_auth_user='';
	var $proxy_auth_pass='';

	// result
	var $query = '';     // Query String
	var $rc = '';        // Response Code
	var $header = '';    // Header
	var $data = '';      // Data
	var $getSize = null; // Get size
	function Hyp_HTTP_Request()
	{
		$this->ua = 'PHP/'.PHP_VERSION;

		// Load "http_request.ini"
		$ini_file = dirname(__FILE__) . '/ini/http_request.ini';
		if (file_exists($ini_file)) {
			$ini_array = parse_ini_file($ini_file);

			$keys = array(
				'use_proxy',
				'proxy_host',
				'proxy_port',
				'need_proxy_auth',
				'proxy_auth_user',
				'proxy_auth_pass',
				'no_proxy'
			);

			foreach($keys as $key) {
				if (isset($ini_array[$key])) {
					if ($key === 'no_proxy') {
						$this->$key = explode(' ', $ini_array[$key]);
					} else {
						$this->$key = $ini_array[$key];
					}
				}
			}

			$this->iniLoaded = TRUE;
		}
	}

	function init()
	{
		$this->url     = '';
		$this->method  = 'GET';
		$this->headers = '';
		$this->post    = array();
		$this->ua      = "PHP/".PHP_VERSION;
		$this->getSize = null;

		// result
		$this->query = '';   // Query String
		$this->rc = '';      // Response Code
		$this->header = '';  // Header
		$this->data = '';    // Data
	}
	function get()
	{
		$max_execution_time = ini_get('max_execution_time');
		$max_execution_time = ($max_execution_time)? $max_execution_time : 30;

		$rc = array();
		$arr = HypCommonFunc::i18n_parse_url($this->url);
		if (!$arr)
		{
			$this->query  = $this->url;
			$this->rc     = 400;
			$this->header = '';
			$this->data   = 'Bad Request';
			return;
		}

		if (!$this->connect_try) $this->connect_try = 1;

		$via_proxy = $this->use_proxy ? ! $this->in_the_net($this->no_proxy, $arr['host']) : FALSE;

		// query
		$arr['query'] = isset($arr['query']) ? '?'.$arr['query'] : '';
		// port
		$arr['port'] = isset($arr['port']) ? $arr['port'] : ($arr['https']? 443 : 80);

		$url_base = $arr['scheme'].'://'.$arr['host'].':'.$arr['port'];
		$url_path = isset($arr['path']) ? $arr['path'] : '/';
		$this->uri = ($via_proxy ? $url_base : '').$url_path.$arr['query'];
		$this->method = strtoupper($this->method);
		$method = ($this->method == 'HEAD')? 'GET' : $this->method;
		$readsize = ($this->method == 'HEAD')? 1024 : 4096;

		$query = $method.' '.$this->uri." HTTP/1.0\r\n";
		$query .= "Host: ".$arr['host']."\r\n";
		if (!empty($this->ua)) $query .= "User-Agent: ".$this->ua."\r\n";
		if (!is_null($this->getSize)) $query .= 'Range: bytes=0-' . ($this->getSize - 1) . "\r\n";

		// proxyのBasic認証
		if ($this->need_proxy_auth and isset($this->proxy_auth_user) and isset($this->proxy_auth_pass))
		{
			$query .= 'Proxy-Authorization: Basic '.
				base64_encode($this->proxy_auth_user.':'.$this->proxy_auth_pass)."\r\n";
		}

		// Basic 認証用
		if (isset($arr['user']) and isset($arr['pass']))
		{
			$query .= 'Authorization: Basic '.
				base64_encode($arr['user'].':'.$arr['pass'])."\r\n";
		}

		$query .= $this->headers;

		// POST 時は、urlencode したデータとする
		if ($this->method == 'POST')
		{
			if (is_array($this->post))
			{
				$_send = array();
				foreach ($this->post as $name=>$val)
				{
					$_send[] = $name.'='.urlencode($val);
				}
				$data = join('&',$_send);

				if (preg_match('/^[a-zA-Z0-9_-]+$/', $this->content_charset)) {
					// Legacy but simple
					$query .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
				} else {
					// With charset (NOTE: Some implementation may hate this)
					$query .= 'Content-Type: application/x-www-form-urlencoded' .
						'; charset=' . strtolower($this->content_charset) . "\r\n";
				}

				$query .= 'Content-Length: '.strlen($data)."\r\n";
				$query .= "\r\n";
				$query .= $data;
			}
			else
			{
				$query .= 'Content-Length: '.strlen($this->post)."\r\n";
				$query .= "\r\n";
				$query .= $this->post;
			}
		}
		else
		{
			$query .= "\r\n";
		}

		//set_time_limit($this->connect_timeout * $this->connect_try + 60);
		$fp = $connect_try_count = 0;
		while( !$fp && $connect_try_count < $this->connect_try )
		{
			//@set_time_limit($this->connect_timeout + $max_execution_time);

			//if ($now_execution_time = ini_get('max_execution_time')) {
			//	$this->connect_timeout = min($this->connect_timeout, max(5, $now_execution_time - 10));
			//}

			$errno = 0;
			$errstr = "";
			$fp = @ fsockopen(
				$via_proxy ? $this->proxy_host : $arr['https'].$arr['host'],
				$via_proxy ? $this->proxy_port : $arr['port'],
				$errno,$errstr,$this->connect_timeout);
			if ($fp) break;
			$connect_try_count++;
			if (connection_aborted()) exit();
			sleep(1); //1秒待つ
		}
		if (!$fp)
		{
			$this->query  = $query;  // Query String
			$this->rc     = $errno;  // エラー番号
			$this->header = '';      // Header
			$this->data   = $errstr; // エラーメッセージ
			return;
		}

		// 非同期モード
		if (!$this->blocking) {
			socket_set_blocking($fp, 0);
		}

		$fwrite = 0;
		for ($written = 0; $written < strlen($query); $written += $fwrite) {
			$fwrite = fwrite($fp, substr($query, $written));
			if (!$fwrite) {
				break;
			}
		}

		// 非同期モード
		if (!$this->blocking)
		{
			fclose($fp);
			$this->query  = $query;
			$this->rc     = 200;
			$this->header = '';
			$this->data   = 'Blocking mode is FALSE';
			return;
		}

		$response = '';

		if ($this->read_timeout) {
			socket_set_timeout($fp, $this->read_timeout);
		}

		$_response = true;
		while ($_response
			&& ($this->method !== 'HEAD' || strpos($response,"\r\n\r\n") === FALSE)
			&& (is_null($this->getSize) || strlen($response) < $this->getSize)
		)
		{
			if (connection_aborted()) exit();
			if ($_response = fread($fp, $readsize)) {
				$response .= $_response;
			}
		}

		if ($this->read_timeout) {
			$_status = socket_get_status($fp);
			if ($_status['timed_out']) {
				fclose($fp);
				$this->query  = $query;
				$this->rc     = 408;
				$this->header = '';
				$this->data   = 'Request Time-out';
				return;
			}
		}

		fclose($fp);
		$resp = array_pad(explode("\r\n\r\n",$response,2), 2, '');
		$rccd = array_pad(explode(' ',$resp[0],3), 3, ''); // array('HTTP/1.1','200','OK\r\n...')
		$rc = (integer)$rccd[1];

		// Redirect
		switch ($rc)
		{
			case 307: // Temporary Redirect
			case 303: // See Other
			case 302: // Moved Temporarily
			case 301: // Moved Permanently
				$matches = array();
				if (preg_match('/^Location: (.+?)(#.+)?$/im',$resp[0],$matches)
					and --$this->redirect_max > 0)
				{
					$this->url = trim($matches[1]);
					$this->hash = isset($matches[2])? trim($matches[2]) : '';
					if (!preg_match('/^https?:\//',$this->url)) // no scheme
					{
						if ($this->url{0} != '/') // Relative path
						{
							// to Absolute path
							$this->url = substr($url_path,0,strrpos($url_path,'/')).'/'.$this->url;
						}
						// add sheme,host
						$this->url = $url_base.$this->url;
					}
					return $this->get();
				}
		}

		$this->query = $query;    // Query String
		$this->rc = $rc;          // Response Code
		$this->header = $resp[0]; // Header
		$this->data = $resp[1];   // Data
		return;
	}

	// プロキシを経由する必要があるかどうか判定
	// Check if the $host is in the specified network(s)
	function in_the_net($networks = array(), $host = '')
	{
		if (empty($networks) || $host == '') return FALSE;
		if (! is_array($networks)) $networks = array($networks);

		$matches = array();

		if (preg_match($this->network_reg, $host, $matches)) {
			$ip = $matches[1];
		} else {
			$ip = gethostbyname($host); // May heavy
		}
		$l_ip = ip2long($ip);

		foreach ($networks as $network) {
			if (preg_match($this->network_reg, $network, $matches) &&
			    is_long($l_ip) && long2ip($l_ip) == $ip) {
				// $host seems valid IPv4 address
				// Sample: '10.0.0.0/8' or '10.0.0.0/255.0.0.0'
				$l_net = ip2long($matches[1]); // '10.0.0.0'
				$mask  = isset($matches[2]) ? $matches[2] : 32; // '8' or '255.0.0.0'
				$mask  = is_numeric($mask) ?
					pow(2, 32) - pow(2, 32 - $mask) : // '8' means '8-bit mask'
					ip2long($mask);                   // '255.0.0.0' (the same)

				if (($l_ip & $mask) == $l_net) return TRUE;
			} else {
				// $host seems not IPv4 address. May be a DNS name like 'foobar.example.com'?
				foreach ($networks as $network)
					if (preg_match('/\.?\b' . preg_quote($network, '/') . '$/', $host))
						return TRUE;
			}
		}

		return FALSE; // Not found
	}
}
}

// create a instance in global scope
//$GLOBALS['hypCommonFunc'] = new HypCommonFunc() ;

// Make context for search by nao-pon
if (!function_exists('xoops_make_context'))
{
function xoops_make_context($text,$words=array(),$l=255)
{
	return HypCommonFunc::make_context($text,$words,$l);
}
}

if (!function_exists('xoops_update_rpc_ping'))
{
function xoops_update_rpc_ping($to = "")
{
	return HypCommonFunc::update_rpc_ping($to);
}
}

if( !function_exists('memory_get_usage') )
{
function memory_get_usage()
{
	$output = array();
	//If its Windows
	//Tested on Win XP Pro SP2. Should work on Win 2003 Server too
	//Doesn't work for 2000
	//If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
	if ( substr(PHP_OS,0,3) == 'WIN')
	{
		exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
		$mem = (empty($output[5]))? 0 : intval(preg_replace( '/[\D]/', '', $output[5] ));
		$mem = $mem * 1024;
	}
	else
	{
		//We now assume the OS is UNIX
		//Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
		//This should work on most UNIX systems
		$pid = getmypid();
		exec("ps -eo%mem,rss,pid | grep $pid", $output);
		$output = explode("  ", $output[0]);
		$mem = (empty($output[1]))? 0 : intval($output[1]);
		//rss is given in 1024 byte units
		$mem = $mem * 1024;
	}
	return $mem;
}
}

// 初期化作業
// ImageMagick のパス設定ファイルがあれば読み込む
if (file_exists(dirname(__FILE__)."/execpath.inc.php"))
{
	include_once(dirname(__FILE__)."/execpath.inc.php");
}
// ImageMagick のパスを指定 (多くは /usr/bin/ ?)
HypCommonFunc::set_exec_path("/usr/bin/");

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

// htmlspecialchars_decode (PHP 5 >= 5.1.0)
if (! function_exists('htmlspecialchars_decode')) {
function htmlspecialchars_decode($string, $quote_style = null)
{
    // Sanity check
    if (!is_scalar($string)) {
        user_error('htmlspecialchars_decode() expects parameter 1 to be string, ' .
            gettype($string) . ' given', E_USER_WARNING);
        return;
    }

    if (!is_int($quote_style) && $quote_style !== null) {
        user_error('htmlspecialchars_decode() expects parameter 2 to be integer, ' .
            gettype($quote_style) . ' given', E_USER_WARNING);
        return;
    }

    // The function does not behave as documented
    // This matches the actual behaviour of the function
    if ($quote_style & ENT_COMPAT || $quote_style & ENT_QUOTES) {
        $from = array('&quot;', '&#039;', '&lt;', '&gt;', '&amp;');
        $to   = array('"', "'", '<', '>', '&');
    } else {
        $from = array('&lt;', '&gt;', '&amp;');
        $to   = array('<', '>', '&');
    }

    return str_replace($from, $to, $string);
}
}