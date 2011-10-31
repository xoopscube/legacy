<?php
// plugin converter for xpwiki

$funcname_reg = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(?=[ \t]*\()/';

$func_reg = '/(?:(?:(?:\/\/|#|<\?php).*?(?:\r\n|\r|\n)|\r\n|\r|\n))*(?:function[ \t&]*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s*\(.*?\)\s*)*\{((?:(?>[^{}]+)|(?R))*)\}\s+(?:(?:(?:\/\/|#|\?>).*?(?:\r\n|\r|\n)|\r\n|\r|\n))*/is';

$keys_reg = '/^(and|or|xor|exception|php_user_filter|array|as|break|case|class|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|eval|exit|extends|for|foreach|function|global|if|include|include_once|isset|list|new|print|require|require_once|return|static|switch|unset|use|var|while|final|php_user_filter|interface|implements|extends|public|private|protected|abstract|clone|try|catch|throw|cfunction|old_function|this)$/i';

$defines = $func_all = $global_all = array();
$other_all = $output_all = "";

$const_init = <<<EOD

		\$this->xpwiki =& \$xpwiki;
		\$this->root   =& \$xpwiki->root;
		\$this->cont   =& \$xpwiki->cont;
		\$this->func   =& \$xpwiki->func;

EOD;

/*
$func_init = <<<EOD

		//\$root  =& \$this->root;
		//\$const =& \$this->cont;
		//\$func  =& \$this->func;

EOD;
*/
$func_init = "";

$files = array();
$indir = "$mydirpath/private/cache/in/";
$outdir = "$mydirpath/private/cache/out/";

$cachedir = "$mydirpath/private/cache/";

$isupload = 0;

if (!empty($_FILES['userfile']['name'])) {
	$files[] = basename($_FILES['userfile']['name']);
	$isupload = 1;
} else {
	if ($handle = @ opendir($indir)) {
		while (false !== ($file = readdir($handle))) {
			if (!is_dir($indir.$file)) {
				$files[] = $file;
			}
		}
		closedir($handle);
	}
}

if (!$files) {
	echo <<<EOD
<h1>Skin converter from PukiWiki 1.4 to xpWiki</h1>
<form enctype="multipart/form-data" action="index.php?page=skin_conv" method="POST">
    PukiWiki 1.4 skin file:<br /><input name="userfile" type="file" size="60" /><br />
    <input type="submit" value="Do convert & Download!" onClick="this.style.visibility='hidden';return true;" />
	Click &amp; Wait...
</form>
<hr />
EOD;
	return;
}

$consts = file($cachedir."consts.dat");
$consts = array_map("trim", $consts);

foreach($files as $input) {
	set_time_limit(60);
	$output = $outdir . $input;
	//echo $output;
	$output_other = $outdir ."other_{$input}";

	if (file_exists($indir . $input)) {
		$org_file = $indir . $input;
	} else {
		$org_file = $_FILES['userfile']['tmp_name'];
	}

	$dat = file($org_file);

	// 自己関数名の取得
	preg_match_all("/^\s*function\s+(\w+)/im",join('',$dat),$match);
	$my_funcs = $match[1];

	// cat_body() のグローバル変数
	$globals = array(
		'$script',
		'$vars',
		'$arg',
		'$defaultpage',
		'$whatsnew',
		'$help_page',
		'$hr',
		'$attach_link',
		'$related_link',
		'$cantedit',
		'$function_freeze',
		'$search_word_color',
		'$_msg_word',
		'$foot_explain',
		'$note_hr',
		'$head_tags',
		'$trackback',
		'$trackback_javascript',
		'$referer',
		'$javascript',
		'$nofollow',
		'$_LANG',
		'$_LINK',
		'$_IMAGE',
		'$pkwk_dtd',
		'$page_title',
		'$do_backup',
		'$modifier',
		'$modifierlink'
	);

	$i = 0;
	$out = '';
	$out_other = '';
	$st_class = $st_func = 0;
	$nest = 0;
	$statics = array();
	$count = 0;
	$block_comment = 0;
	$here = "";
	$cache = "";
	$line_cache = "";
	$class_cnt = 0;
	$class_out = array();
	$now_class_name = $now_func_name = "";
	$rename_classes = array();

	$need_xpwiki_classes = array();
	$noprc = 0;

	$do_conv = FALSE;

	$dat = join("",$dat);
	$dat = str_replace(array("\r\n","\r"),"\n",$dat);
	$dat = str_replace(array("<?php","?>"),array("\x07","\x08"),$dat);
	preg_match_all("/([^\x07]+?\x08?)?([^\x07\x08]+)?(\x07[^\x07\x08]*?\x08?)?/",$dat,$match,PREG_SET_ORDER);

	//echo "<pre>";
	foreach($match as $arg) {
		$arg = str_replace(array("\x07","\x08"),array("<?php","?>"),$arg);

		if (isset($arg[1]) && isset($arg[3])) {
			// [1][3]がPHP
			//if (isset($arg[1])) echo "<span style=\"color:red;\">".htmlspecialchars($arg[1])."</span>";
			//if (isset($arg[2])) echo "<span>".htmlspecialchars($arg[2])."</span>";
			//if (isset($arg[3])) echo "<span style=\"color:red;\">".htmlspecialchars($arg[3])."</span>";
			if (isset($arg[1])) $out .= _convert_skin($arg[1]);
			if (isset($arg[2])) $out .= $arg[2];
			if (isset($arg[3])) $out .= _convert_skin($arg[3]);
		} else {
			// [2]がPHP
			//if (isset($arg[1])) echo "<span>".htmlspecialchars($arg[1])."</span>";
			//if (isset($arg[2])) echo "<span style=\"color:red;\">".htmlspecialchars($arg[2])."</span>";
			//if (isset($arg[3])) echo "<span>".htmlspecialchars($arg[3])."</span>";
			if (isset($arg[1])) $out .= $arg[1];
			if (isset($arg[2])) $out .= _convert_skin($arg[2]);
			if (isset($arg[3])) $out .= $arg[3];
		}
	}
	//echo "</pre>";

	$out = trim($out);

	//echo "<pre>";
	//echo htmlspecialchars($out);
	//echo "</pre>";

	// 元ファイル削除
	//unlink($org_file);

	if (!$isupload) {
		if ($out && $fp = fopen($output,"wb")) {
			fwrite($fp, rtrim($out));
			fclose($fp);
		}
		echo "<pre>";
		echo htmlspecialchars($out);
		echo "</pre>";

	} else {
		// 元ファイル削除
		@unlink($org_file);

		while( ob_get_level() ) {
			if (! ob_end_clean()) {
				break;
			}
		}

		header('Content-Disposition: attachment; filename="' . $input . '"');
		header('Content-Length: ' . strlen($out));
		header('Content-Type: plain/text');

		echo $out;
		exit;
	}

}

function _for_quote_replace($str,$tgt,$mode,$ext='"') {
	//echo $str;
	$str = str_replace('\\'.$ext,$ext,$str);
	if ($mode == "in")
		return str_replace($tgt,"\x08",$str);
	else {
		return str_replace("\x08",$tgt,$str);
	}
}

function _for_quote_replace2($str,$tgt,$mode,$ext='"') {
	//echo $str;
	$str = str_replace('\\'.$ext,$ext,$str);
	if ($mode == "in")
		return str_replace($tgt,"\x07",$str);
	else {
		return str_replace("\x07",$tgt,$str);
	}
}

function _global_replace($global,$str) {
	//echo $str;
	$str = str_replace('\\"','"',$str);
	$str = preg_replace("/\{?".preg_quote($global,"/")."((?:\[[^\]]+\])*)(?![a-zA-Z0-9_\x7f-\xff])\}?/i","{\$this->root->".substr($global,1)."$1}",$str);
	return $str;
}

function _convert_skin ($str) {
	global $defines, $consts, $globals, $funcname_reg, $keys_reg, $my_funcs;

	$out = "";

	foreach (preg_split("/(\r\n|\r|\n)/",$str) as $line) {

		if (!trim($line) || preg_match("/^\s*function\s+\w+/",$line)) {
			$out .= $line."\n";
			continue;
		}

		//echo htmlspecialchars($line)."<hr>";

		$_line =  preg_replace("/(\".*?\"|'.*?'|(\/\/|#).*$)/s","",trim($line));
		// define 書き換え
		$line = preg_replace("/defined\('(\w+)'\)/i","isset(\$this->cont['$1'])",$line);
		if (preg_match("/define\s*\(\s*(?:[\"'])(.+?)(?:[\"'])\s*,\s*(.+?)\s*\)\s*;/is",$line,$match)) {
			$defines[$match[1]] = $match[2];
			$line = preg_replace("/define\s*\(\s*(?:[\"'])(.+?)(?:[\"'])\s*,(\s*.+?\s*)\)(\s*;)/is","\$this->cont['$1'] = $2$3",$line);
		}
		// $GLOBALS を書き換え
		$line = preg_replace("/\\\$GLOBALS\[(\"|')?([^\]\\1]+?)\\1?\]/i","\$this->root->$2",$line);

		// global変数書き換え
		if (preg_match("/(?:^|\s*)global(.+);/s",$line,$match)) {
			$_globals = array_unique(explode(",",preg_replace("/\s/","",$match[1])));
			$globals = array_merge($globals,$_globals);
			$line = "//".$line;
		} else {
			// '' 内をエスケープ
			$line = preg_replace("/'.*?'/se","_for_quote_replace('$0','\$','in')",$line);
			$line = preg_replace("/'.*?'/se","_for_quote_replace2('$0','\"','in')",$line);
			foreach ($globals as $global) {
				// "" 内
				$line = preg_replace('/(?<!\\\\)(".*?(?<!\\\\)")/ie',"_global_replace('$global','$0')",$line);

				// その他
				$line = preg_replace("/".preg_quote($global,"/")."(?![a-zA-Z0-9_\x7f-\xff])/",'$this->root->'.substr($global,1),$line);
			}
			// '' 内をエスケープ解除
			$line = preg_replace("/'.*?'/se","_for_quote_replace2('$0','\"','out')",$line);
			$line = preg_replace("/'.*?'/se","_for_quote_replace('$0','\$','out')",$line);
		}
		//関数名書き換え
		//echo htmlspecialchars($_line)."<br>";
		if (preg_match_all($funcname_reg,$_line,$match,PREG_PATTERN_ORDER))
		{
			$funcs = array_unique($match[0]);
			foreach ($funcs as $func_name) {
				if (!function_exists($func_name) && !preg_match($keys_reg,$func_name)) {
					// 自己関数？
					if (array_search($func_name,$my_funcs) !== FALSE) {
						//なにもしない
					} else {
						$line = preg_replace("/(?<!\->|new |::|\\\$)(".preg_quote($func_name,"/").")([ \t]*\()/i", "\$this->$1$2", $line);
					}
				}
			}
		}
		// 定数の書き換え
		$consts = array_merge($consts, array_keys($defines));
		$consts = array_unique($consts);
		rsort($consts);
		foreach ($consts as $const) {
			// '' 内をエスケープ
			$key = $const[0];
			$line = preg_replace("/'.*?'/e","_for_quote_replace('$0','$key','in')",$line);

			$line = preg_replace("/(?<![\w'\"])".$const."(?![\w'\"])/","\$this->cont['$0']",$line);

			// '' 内をエスケープ解除
			$line = preg_replace("/'.*?'/e","_for_quote_replace('$0','$key','out')",$line);
		}

		$out.= $line."\n";
	}
	return rtrim($out);
}
?>