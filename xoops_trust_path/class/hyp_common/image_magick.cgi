<?php
error_reporting(0);

if (php_sapi_name() == "cli")
{
	echo "Content-Type: text/plain\n\n";
}
else
{
	header("Content-Type: text/plain");
}

// 戻り値
$ret = "ERROR: 9";

if (empty($_SERVER['QUERY_STRING'])) exit($ret);

// 有効なパラメーター
$allows = array("m","p","z","q","u","o","s");



// CLI版の場合 $_GET では取得できないので独自取得
foreach(explode('&',$_SERVER['QUERY_STRING']) as $prm)
{
	list($key,$val) = array_pad(explode('=',$prm),2,'');

	$key = str_replace("\0","",$key);

	// 必要ないパラメータは捨てる
	if (!in_array($key,$allows)) continue;
	$val = rawurldecode(str_replace("\0","",$val));

	$$key = $val;
}

// リサイズ
if ($m == 'r')
{
	// 必要なパラメーターがあるかどうか
	$needs = array("p","z","q","o","s");
	foreach($needs as $key)
	{
		if (empty($$key)) exit($ret);
	}

	$q = intval($q);
	$z = intval($z);
	$p = escapeshellcmd($p);

	// 変数チェック

	// ディレクトリ遡りパターン検出
	if (preg_match("/([\|\s]|\.\.\/)/",$p.$o.$s)) exit($ret);

	// コマンドと元ファイルの存在確認
	if (!file_exists($p."convert") || !file_exists($o)) exit($ret);

	// イメージファイルか？
	$size = @getimagesize($o);
	if (!$size) exit($ret); //画像ファイルではない

	// ズームの範囲
	if ($z < 1 || $z > 100) exit($ret);

	// quality の範囲
	if ($q < 1 || $q > 100) exit($ret);

	// unshrap の値
	if (empty($u) || preg_match('/^[0-9.|]+$/', trim($u))) {
		$u = '';
	} else {
		$u = trim($u);
	}
	list($amount, $radius, $threshold) = array_pad(explode('|', $u), 3, '');
	$amount    = ($amount            ? $amount    : 80);
	$radius    = ($radius            ? $radius    : 0.5);
	$threshold = (strlen($threshold) ? $threshold : 3);
	$u = ' -unsharp ' . number_format(($radius * 2) - 1, 2).'x1+'.number_format($amount / 100, 2).'+'.number_format($threshold / 100, 2);

	// 元画像のサイズ
	$w = $size[0];
	$h = $size[1];

	// 実行
	$out = array();
	exec( "{$p}convert -thumbnail {$z}%  -quality {$q}{$u} {$o} {$s}" , $out) ;

	if ($out)
	{
		$ret = "ERROR: 1";
	}
	else
	{
		$ret = "ERROR: 0";
		@chmod($s, 0606);
	}

	// 完了
	exit($ret);
}

// Round Corner
else if ($m == 'ro')
{
	// 必要なパラメーターがあるかどうか
	$needs = array("p","z","q","o","s");
	foreach($needs as $key)
	{
		if (empty($$key)) exit($ret);
	}

	$edge = intval($q);
	$corner = intval($z);
	$p = escapeshellcmd($p);

	// ディレクトリ遡りパターン検出
	if (preg_match("/([\|\s]|\.\.\/)/",$p.$o.$s)) exit($ret);

	// コマンドと元ファイルの存在確認
	if (!file_exists($p."convert") || !file_exists($o)) exit($ret);

	// 出力ファイルが存在する(CGIを直接叩かれてる?)
	if (file_exists($s)) exit($ret);

	// イメージファイルか？
	$size = @getimagesize($o);
	if (!$size) exit($ret); //画像ファイルではない

	// 元画像のサイズ
	$imw = $size[0];
	$imh = $size[1];
	$im_half = floor((min($imw, $imh)/2));

	// check value
	$edge = min($edge, $im_half);
	$corner = min($corner, $im_half);

	$tmpfile = $s . '_tmp.png';

	$out = array();
	$cmd = 'convert -size '.$imw.'x'.$imh.' xc:none -channel RGBA -fill white -draw "roundrectangle '.max(0,($edge-1)).','.max(1,($edge-1)).' '.($imw-$edge).','.($imh-$edge).' '.$corner.','.$corner.'" '.$o.' -compose src_in -composite '.$tmpfile;
	exec( $p . $cmd, $out ) ;
	if ($out) $ret = "ERROR: 1";

	if (!$out && $edge) {
		$out = array();
		$cmd = 'convert -size '.$imw.'x'.$imh.' xc:none -fill none -stroke white -strokewidth '.$edge.' -draw "roundrectangle '.($edge-1).','.($edge-1).' '.($imw-$edge).','.($imh-$edge).' '.$corner.','.$corner.'" -shade 135x25 -blur 0x1 -normalize '.$tmpfile.' -compose overlay -composite '.$tmpfile;
		exec( $p . $cmd, $out ) ;
		if ($out) $ret = "ERROR: 1";
	}

	if (!$out) {
		copy ($tmpfile, $s);
		unlink($tmpfile);
		@chmod($s, 0606);
		$ret = "ERROR: 0";
	}

	// 完了
	exit($ret);
}

// 回転
else if ($m == 'rj' || $m == 'ri')
{
	// 必要なパラメーターがあるかどうか
	$needs = array("p","z","q","s");
	foreach($needs as $key)
	{
		if (empty($$key)) exit($ret);
	}

	$q = intval($q);
	$z = intval($z);
	$p = escapeshellcmd($p);

	// 変数チェック

	// ディレクトリ遡りパターン検出
	if (preg_match("/([\|\s]|\.\.\/)/",$p.$s)) exit($ret);

	// イメージファイルか？
	$size = @getimagesize($s);
	if (!$size) exit($ret); //画像ファイルではない

	// 回転の範囲
	if ($z < 90 || $z > 270) exit($ret);

	// quality の範囲
	if ($q < 1 || $q > 100) exit($ret);

	// 元画像のサイズ
	$w = $size[0];
	$h = $size[1];

	if ($m == "rj")
	{
		// コマンドと元ファイルの存在確認
		if (!file_exists($p."jpegtran") || !file_exists($s)) exit($ret);

		$tmpfname = @tempnam(dirname($s), "tmp_");
		exec( "{$p}jpegtran -rotate {$z} -copy all {$s} > {$tmpfname}" );
		if ( ! @filesize($tmpfname) || ! @unlink($s) )
		{
			$ret = "ERROR: 1";
		}
		else
		{
			$ret = "ERROR: 0";
			copy($tmpfname, $s);
			chmod($s, 0606);
		}
		unlink($tmpfname);
	}
	else
	{
		// コマンドと元ファイルの存在確認
		if (!file_exists($p."convert") || !file_exists($s)) exit($ret);

		$out = array();
		// 実行
		exec( "{$p}convert -size {$w}x{$h} -rotate +{$z} -quality {$q} {$s} {$s}", $out) ;

		if ($out)
		{
			$ret = "ERROR: 1";
		}
		else
		{
			$ret = "ERROR: 0";
			@chmod($s, 0606);
		}
	}

	// 完了
	exit($ret);
}


exit($ret);
