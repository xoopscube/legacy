<?php
/*
 * Created on 2008/10/23 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: w2x.php,v 1.20 2011/07/29 07:14:25 nao-pon Exp $
 */

//
//	guiedit - PukiWiki Plugin
//
//	License:
//		GNU General Public License Version 2 or later (GPL)
//		http://www.gnu.org/licenses/gpl.html
//
//	Copyright (C) 2006-2008 garand
//	PukiWiki : Copyright (C) 2001-2006 PukiWiki Developers Team
//	FCKeditor : Copyright (C) 2003-2008 Frederico Caldeira Knabben
//
//
//	File:
//	  wiki2xhtml.php
//	  PukiWiki の構文を XHTML に変換
//

error_reporting(0);

$source = isset($_POST['s'])? $_POST['s'] : '';
$line_break = isset($_POST['lb'])? strval($_POST['lb']) : '';
$page = isset($_POST['page'])? $_POST['page'] : '';

if (get_magic_quotes_gpc()) {
	$source = stripslashes($source);
	$page = stripslashes($page);
}

define('DEBUG', (! empty($_GET['debug'])));

if ($source || $line_break === '') {

	if ($source) {
		$source = str_replace(array("\r\n", "\r"), "\n", $source);
		$source = rtrim($source) . "\n";
	}

	include_once $mytrustdirpath . '/include.php';

	$xpwiki = new XpWiki($mydirname);
	$xpwiki->root->fckediting = true;
	$xpwiki->init('#RenderMode');

	if ($page) {
		$e_page = mb_convert_encoding($page,  $xpwiki->cont['SOURCE_ENCORDING'], 'UTF-8');
		$xpwiki->cont['PageForRef'] = $xpwiki->root->vars['page'] = $xpwiki->root->post['page'] = $xpwiki->root->get['page'] = $e_page;
	}

	// 定数設定
	define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', $xpwiki->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK']);
	define('MSIE', (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE));
	define('COLORS_REG', 'aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent');

	// globals
	if ($line_break === '') $line_break = $xpwiki->root->line_break;

	$hr = $xpwiki->root->hr;
	$_ul_left_margin = $xpwiki->root->_ul_left_margin;
	$_ul_margin = $xpwiki->root->_ul_margin;
	$_ol_left_margin = $xpwiki->root->_ol_left_margin;
	$_ol_margin = $xpwiki->root->_ol_margin;
	$_dl_left_margin = $xpwiki->root->_dl_left_margin;
	$_dl_margin = $xpwiki->root->_dl_margin;
	$_list_pad_str = $xpwiki->root->_list_pad_str;
	$preformat_ltrim = $xpwiki->root->preformat_ltrim;

	$guiedit_line_rules = $xpwiki->root->line_rules;
	// Over write
	$guiedit_line_rules['%%%(?!%)((?:(?!%%%).)*)%%%'] 	= '<u>$1</u>';
	$guiedit_line_rules['%%(?!%)((?:(?!%%).)*)%%'] 		= '<strike>$1</strike>';
	$guiedit_line_rules["'''(?!')((?:(?!''').)*)'''"] 	= '<em>$1</em>';
	$guiedit_line_rules["''(?!')((?:(?!'').)*)''"] 		= '<strong>$1</strong>';
	$guiedit_line_rules["\r"]                           = '<br />' . "\n";

	$source = guiedit_convert_html($source);
}

Send_xml($source, strval($line_break));

function debug($data){
	$file = dirname(__FILE__) . '/debug.txt';
	@ unlink($file);
	file_put_contents($file, $data);
}

//	XML 形式で出力
function Send_xml($body, $line_break)
{
	// clear output buffer
	while( ob_get_level() ) {
		if (! ob_end_clean()) {
			break;
		}
	}
	$out = '';
	$out .= '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
	$out .= '<data>';
	$out .= '<res><![CDATA[' . $body . ']]></res>';
	$out .= '<lb>' . $line_break . '</lb>';
	$out .= '</data>';

	//	出力
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	if (DEBUG) {
		header('Content-Type: text/plain; charset=UTF-8');
	} else {
		header('Content-Type: application/xml; charset=UTF-8');
	}
	header('Content-Length: ' . strlen($out));
	echo $out;
	exit();
}

//	PukiWiki の構文を XHTML に変換
function guiedit_convert_html($source) {
	if (DEBUG) error_reporting(E_ALL);

	if ($source) {
		$lines = explode("\n", $source);
		$body = & new BodyEx();
		$body->parse($lines);

		$html = $body->toString();
	} else {
		$html = '';
	}

	if (trim($html)) $html = $html . '<div></div>';
	return $html;
}

// インライン要素の変換
function guiedit_make_link($line)
{
	static $obj = null;
	if (is_null($obj)) {
		$obj = new InlineConverterEx();
	}
	return $obj->convert($line);
}

// 添付ファイルプラグインの変換
function guiedit_convert_ref($args, $div = TRUE) {

	$args_org = $args;
	$body = $argbody = '';
	if (! $div) {
		$body = array_pop($args);
		if ($body) $argbody = '{' . htmlspecialchars($body) . '}';
	}

	$options = htmlspecialchars(join(',', $args));

	$filename = array_shift($args);
	$_title = array();
	$params = array(
		'left'   => 0, // 左寄せ
		'center' => 0, // 中央寄せ
		'right'  => 0, // 右寄せ
		'wrap'   => 0, // TABLEで囲む
		'nowrap' => 0, // TABLEで囲まない
		'around' => 0, // 回り込み
		'noicon' => 0, // アイコンを表示しない
		'nolink' => 0, // 元ファイルへのリンクを張らない
		'noimg'  => 0, // 画像を展開しない
		'zoom'   => 0, // 縦横比を保持する
		'_w'     => 0,     // 幅
		'_h'     => 0,     // 高さ
		'_size'  => 0,
		'_mw'    => 0,
		'_mh'    => 0,
	);

	// パラメータ解析
	foreach ($args as $arg) {
		$s_arg = trim(strtolower($arg));
		if (array_key_exists($s_arg, $params)) {
			$params[$s_arg] = 1;
		} else if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
			$params['_w'] = $matches[1];
			$params['_h'] = $matches[2];
		} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
			$params['_size'] = $matches[1];
		} else if (preg_match('/^(m)?w:([0-9]+)(?:px)?$/', $arg, $matches) && $matches[2] > 0) {
			if (empty($matches[1])) {
				$params['_w'] = $matches[2];
			} else {
				$params['_mw'] = $matches[2];
			}
		} else if (preg_match('/^(m)?h:([0-9]+)(?:px)?$/', $arg, $matches) && $matches[2] > 0) {
			if (empty($matches[1])) {
				$params['_h'] = $matches[2];
			} else {
				$params['_mh'] = $matches[2];
			}
		} else {
			$_title[] = $arg;
		}
	}

	$align = '';
	if ($params['left']) {
		$align = 'left';
	} else if ($params['center']) {
		$align = 'center';
	} else if ($params['right']) {
		$align = 'right';
	}

	$other = !empty($_title) ? htmlspecialchars(join(',', $_title)) : '';
	$other = preg_replace("/^,/", '', $other);

	$attribute = 'class="ref" contenteditable="false" style="cursor:default"';
	$attribute .= ' _filename="' . $filename . '"';
	$attribute .= ' _othor="' . $other . '"';
	$attribute .= ' _alt="' . htmlspecialchars($body) . '"';
	$attribute .= ' _width="' . ($params['_w'] ? $params['_w'] : '') . '"';
	$attribute .= ' _height="' . ($params['_h'] ? $params['_h'] : '') . '"';
	$attribute .= ' _mw="' . ($params['_mw'] ? $params['_mw'] : '') . '"';
	$attribute .= ' _mh="' . ($params['_mh'] ? $params['_mh'] : '') . '"';
	$attribute .= ' _size="' . $params['_size'] . '"';
	$attribute .= ' _align="' . $align . '"';
	$attribute .= ' _wrap="' . $params['wrap'] . '"';
	$attribute .= ' _around="' . $params['around'] . '"';
	$attribute .= ' _nolink="' . $params['nolink'] . '"';
	$attribute .= ' _noicon="' . $params['noicon'] . '"';
	$attribute .= ' _noimg="' . $params['noimg'] . '"';
	$attribute .= ' _zoom="' . $params['zoom'] . '"';

	if ($div) {
		$attribute .= ' _source="' . htmlspecialchars("#ref($options)") . '"';
		$tags = "<div $attribute>#ref($options)</div>";
	} else {
		$inner = "&ref($options)$argbody;";
		$attribute .= ' _source="' . htmlspecialchars($inner) . '"';
		$html = get_ref_html($args_org, false);
		if (preg_match('#^<img[^>]+?'.'>$#', $html)) {
			$attribute = str_replace('contenteditable="false"', 'contenteditable="true"', $attribute);
			$tags = str_replace('<img', '<img ' . $attribute, $html);
		} else {
			$tags = "<span $attribute>$inner</span>";
		}
	}

	return $tags;
}

function get_ref_html($args, $div = TRUE) {
	global $xpwiki;
	mb_convert_variables($xpwiki->cont['SOURCE_ENCODING'], 'UTF-8', $args);
	if (! $div) {
		$retvar = $xpwiki->func->do_plugin_inline('ref', csv_implode(',', $args));
	} else {
		$retvar = $xpwiki->func->do_plugin_convert('ref', csv_implode(',', $args));
	}
	$retvar = mb_convert_encoding($retvar, 'UTF-8', $xpwiki->cont['SOURCE_ENCODING']);
	$retvar = trim(preg_replace('#</?a[^>]*?'.'>#is', '', $retvar));
	return $retvar;
}

function guiedit_make_line_rules($line) {
	global $guiedit_line_rules;
	static $pattern, $replace;

	if (!isset($pattern)) {
		$pattern = array_map(create_function('$a', 'return \'/\' . $a . \'/\';'), array_keys($guiedit_line_rules));
		$replace = array_values($guiedit_line_rules);
		unset($guiedit_line_rules);
	}

	return preg_replace($pattern, $replace, $line);
}

// Explode Comma-Separated Values to an array
function csv_explode($separator, $string)
{
	$retval = $matches = array();

	$_separator = preg_quote($separator, '/');
	if (! preg_match_all('/("[^"]*(?:""[^"]*)*"|[^' . $_separator . ']*)' .
	    $_separator . '/', $string . $separator, $matches))
		return array();

	foreach ($matches[1] as $str) {
		$len = strlen($str);
		if ($len > 1 && $str{0} == '"' && $str{$len - 1} == '"')
			$str = str_replace('""', '"', substr($str, 1, -1));
		$retval[] = $str;
	}
	return $retval;
}

// Implode an array with CSV data format (escape double quotes)
function csv_implode($glue, $pieces)
{
	$_glue = ($glue !== '') ? '\\' . $glue{0} : '';
	$arr = array();
	foreach ($pieces as $str) {
		if (ereg('[' . $_glue . '"' . "\n\r" . ']', $str))
			$str = '"' . str_replace('"', '""', $str) . '"';
		$arr[] = $str;
	}
	return join($glue, $arr);
}

function unhtmlspecialchars ($str, $quote_style = ENT_COMPAT) {
	$fr = array('&lt;', '&gt;');
	$tr = array('<',    '>');
	if ($quote_style !== ENT_NOQUOTES) {
		$fr[] = '&quot;';
		$tr[] = '"';
	}
	if ($quote_style === ENT_QUOTES) {
		$fr[] = '&#039;';
		$tr[] = '\'';
	}
	$fr[] = '&amp;';
	$tr[] = '&';
	return str_replace($fr, $tr, $str);
}

// インライン変換クラス
class InlineConverterEx {
	function get_clone($obj) {
		static $clone_func;

		if (!isset ($clone_func)) {
			if (version_compare(PHP_VERSION, '5.0.0', '<')) {
				$clone_func = create_function('$a', 'return $a;');
			} else {
				$clone_func = create_function('$a', 'return clone $a;');
			}
		}
		return $clone_func ($obj);
	}

	function convert($line, $link = TRUE, $enc = TRUE) {
		global $xpwiki;

		if ($enc) {
			$line = htmlspecialchars($line);
		}

		// easy ref ( {{filename|alias}} )
		if ($xpwiki->root->easy_ref_syntax) {
			$line = preg_replace('/\{\{([^{}\r\n]+?)(?:\|([^{}\r\n]*?))?\}\}/', '&amp;ref($1){$2};', $line);
		}

		// インライン・プラグイン
		$pattern = '/&amp;([0-9a-zA-Z_-]+)(?:\(((?:(?!\)[;{]).)*)\))?(?:\{((?:(?R)|(?!};).)*)\})?;/';
		$line = preg_replace_callback($pattern, array(&$this, 'convert_plugin'), $line);

		// ルールの変換
		$line = guiedit_make_line_rules($line);

		// 文字サイズの変換
		$pattern = "/<span\s(style=\"font-size:(\d+)px|class=\"size([1-7])).*?>/";
		$line = preg_replace_callback($pattern, array(&$this, 'convert_size'), $line);
		// 色の変換
		$pattern = "/<sapn\sstyle=\"color:([#0-9a-z]+)(; background-color:([#0-9a-z]+))?\">/";
		$line = preg_replace_callback($pattern, array(&$this, 'convert_color'), $line);

		// リンク
		if ($link) {
			$line = $this->make_link($line);
		}

		return $line;
	}

	// 文からリンクを検出し、link_replace を呼び出す
	function make_link($line) {
		$link_rules = "/(
			(?:\[\[((?:(?!\]\]).)+):)?
			((?:https?|ftp|news)(?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+))
			(?(2)\]\])
			|
			 (\[\[
			  (?:
			   (?:((?:(?!\]\]).)+))
			   (?:&gt;)
			  )?
			  (?:
			   (\#(?:[a-zA-Z][\w-]*)?)
			   |
			   ((?:(?!\]\]).)*)
			  )?
			 \]\])
		)/xS";

		return preg_replace_callback($link_rules, array(&$this,'link_replace'), $line);
	}

	// make_link で検出したリンクにリンクタグを付加する
	function link_replace($matches) {
		if ($matches[3] != '') {
			if (!$matches[2]) {
				return $matches[3];
			}
			$url = $matches[3];
			$alias = empty($matches[2]) ? $url : $matches[2];
			return "<a href=\"$url\">$alias</a>";
		}
		if ($matches[6] != '') {
			$str = empty($matches[5]) ? $matches[6] : $matches[5];
			return '<a href="' . $matches[6] . '">' . "$str</a>";
		}
		if ($matches[7] != '') {
			$str = empty($matches[5]) ? $matches[7] : $matches[5];
			return '<a href="' . $matches[7] . '">' . $str . '</a>';
		}
		return $matches[0];
	}

	// インラインプラグイン処理メソッド
	function convert_plugin($matches) {
		$aryargs = (isset($matches[2]) && $matches[2] !== '') ? csv_explode(',', unhtmlspecialchars($matches[2])) : array();
		$name = strtolower($matches[1]);
		$body = (isset($matches[3]))? $matches[3] : '';

		//	プラグインが存在しない場合はそのまま返す。
		global $xpwiki;
		if (! $xpwiki->func->exist_plugin_inline($name)) {
			// ルールの変換
			$matches[0] = guiedit_make_line_rules($matches[0]);
			// 数値参照文字(10進)
			$matches[0] = preg_replace('/(&amp;#[0-9]+?;)+/e', '"<span class=\"chrref10\">".str_replace(\'&amp;\',\'&\',\'$0\')."</span>"', $matches[0]);
			// 文字実体参照
			$matches[0] = preg_replace('/(&amp;[a-z]+?;)+/ie', '"<span class=\"chrref\">".str_replace(\'&amp;\',\'&\',\'$0\')."</span>"', $matches[0]);
			return $matches[0];
		}

		switch ($name) {
			case 'aname':
				return "<a name=\"$aryargs[0]\">$body</a>";
			case 'br':
				return '<br class="inline" />';
			case 'font':
				$class = $style = "";
				$color_type = true;
				$decoration = array();
				foreach ($aryargs as $prm)
				{
					$size = $color = array();
					if ($prm == "")
						$color_type = false;
					elseif (preg_match("/^i(talic)?$/i",$prm))
						$style .= "font-style:italic;";
					elseif (preg_match("/^b(old)?$/i",$prm))
						$style .= "font-weight:bold;";
					elseif (preg_match("/^bl(ink)?$/i",$prm))
						$decoration[] = "blink";
					elseif (preg_match("/^u(nderline)?$/i",$prm))
						$decoration[] = "underline";
					elseif (preg_match("/^o(verline)?$/i",$prm))
						$decoration[] = "overline";
					elseif (preg_match("/^l(ine-through)?$/i",$prm))
						$decoration[] = "line-through";
					elseif (preg_match('/^(#[0-9a-f]+|'.COLORS_REG.')$/i',$prm,$color))
					{
						if ($color_type)
						{
							$style .= "color:".htmlspecialchars($color[1]).";";
							$color_type = false;
						} else {
							$style .= "background-color:".htmlspecialchars($color[1]).";";
						}
					}
					elseif (preg_match('/^(\d+(%|px|pt|em))$/',$prm,$size))
						$style .= "font-size:".htmlspecialchars($size[1]).";line-height:130%;";
					elseif (preg_match('/^(\d+)$/',$prm,$size))
						$style .= "font-size:".htmlspecialchars($size[1])."px;line-height:130%;";
					elseif (preg_match('/^class:(.+)$/',$prm,$arg))
						$class = ' class="' . str_replace('"' , '', htmlspecialchars($arg[1])) . '"';
				}
				if (count($decoration))
					$style .= "text-decoration:".join(" ",$decoration).";";

				if (! $style && ! $class) return $body;

				return '<span style="' . $style . '"' . $class . '>' . $this->convert($body, TRUE, FALSE) . '</span>';
			case 'color':
				$color = $aryargs[0];
				$bgcolor = $aryargs[1];
				if ($body == '')
					return '';
				if ($color != '' && !preg_match('/^(#[0-9a-f]+|[\w-]+)$/i', $color))
					return $body;
				if ($bgcolor != '' && !preg_match('/^(#[0-9a-f]+|[\w-]+)$/i', $bgcolor))
					return $body;
				if ($color != '')
					$color = "color:$color";
				if ($bgcolor != '')
					$bgcolor = ($color ? "; " : "") . "background-color:$bgcolor";
				return "<span style=\"$color$bgcolor\">" . $this->convert($body, TRUE, FALSE) . "</span>";
			case 'size':
				$size = $aryargs[0];
				if ($size == '' || $body == '')
					return '';
				if (!preg_match('/^\d+$/', $size))
					return $body;
				return '<span style="font-size:' . $size . 'px;line-height:130%">' .
				       $this->convert($body, TRUE, FALSE) . "</span>";
			case 'ref':
				$aryargs[] = $body;
				return guiedit_convert_ref($aryargs, FALSE);
			case 'sub':
				if (! $body && isset($aryargs[0])) {
					$body = htmlspecialchars($aryargs[0]);
				}
				if ($body) {
					return '<sub>' . $this->convert($body, TRUE, FALSE) . '</sub>';
				}
				break;
			case 'sup':
				if (! $body && isset($aryargs[0])) {
					$body = htmlspecialchars($aryargs[0]);
				}
				if ($body) {
					return '<sup>' . $this->convert($body, TRUE, FALSE) . '</sup>';
				}
				break;
		}

		$inner = '&amp;' . $matches[1] . ($matches[2] ? "($matches[2])" : '') . ($body ? '{' . "$body}" : '') . ";";
		$style = (MSIE) ? ' style="cursor:default"' : '';

		return "<span class=\"plugin\" contenteditable=\"true\"$style>$inner</span>";
	}

	// 色の変換
	function convert_color($matches) {
		$color = $matches[1];
		$bgcolor = $matches[3];
		if ($bgcolor && preg_match("/^#[0-9a-z]{3}$/i", $bgcolor)) {
			$bgcolor = "; background-color:" . preg_replace('/[0-9a-f]/i', "$0$0", $bgcolor);
		}
		if (preg_match("/^#[0-9a-z]{3}$/i", $color)) {
			$color = preg_replace('/[0-9a-f]/i', "$0$0", $color);
		}

		return "<sapn\sstyle=\"color:$color$bgcolor\">";
	}

	// 文字サイズの変換
	function convert_size($matches) {
		if ($matches[2]) {
			$size = $matches[2];

			if      ($size <=  8) $size = 8;
			else if ($size <=  9) $size = 9;
			else if ($size <= 10) $size = 10;
			else if ($size <= 11) $size = 11;
			else if ($size <= 12) $size = 12;
			else if ($size <= 14) $size = 14;
			else if ($size <= 16) $size = 16;
			else if ($size <= 18) $size = 18;
			else if ($size <= 22) $size = 20;
			else if ($size <= 26) $size = 24;
			else if ($size <= 30) $size = 28;
			else if ($size <= 36) $size = 32;
			else if ($size <= 44) $size = 40;
			else if ($size <= 52) $size = 48;
			else				  $size = 60;

			return '<span style="font-size:' . $size . 'px; line-height:130%">';
		}

		switch ($matches[3]) {
			case 1:	$size = "xx-small";
			case 2: $size = "x-small";
			case 3:	$size = "small";
			case 4:	$size = "medium";
			case 5:	$size = "large";
			case 6:	$size = "x-large";
			case 7:	$size = "xx-large";
		}

		return "<span style=\"font-size:$size; line-height:130%\">";
	}
}


// Block elements
class ElementEx
{
	var $parent;
	var $elements; // References of childs
	var $last;     // Insert new one at the back of the $last

	function ElementEx()
	{
		$this->elements = array();
		$this->last     = & $this;
	}

	function setParent(& $parent)
	{
		$this->parent = & $parent;
	}

	function & add(& $obj)
	{
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		} else {
			return $this->parent->add($obj);
		}
	}

	function & insert(& $obj)
	{
		$obj->setParent($this);
		$this->elements[] = & $obj;

		return $this->last = & $obj->last;
	}

	function canContain($obj)
	{
		return TRUE;
	}

	function wrap($string, $tag, $param = '', $canomit = TRUE)
	{
		return ($canomit && $string == '') ? '' :
			($tag? '<' . $tag . $param . '>' . $string . '</' . $tag . '>' : $string);
	}

	function toString()
	{
		$ret = '';
		foreach ($this->elements as $value) {
			if ($ret !== '') $ret .= "\n";
			$ret .= $value->toString();
		}
		return $ret;
	}

	function dump($indent = 0)
	{
		$ret = str_repeat(' ', $indent) . get_class($this) . "\n";
		$indent += 2;
		foreach (array_keys($this->elements) as $key) {
			$ret .= is_object($this->elements[$key]) ?
				$this->elements[$key]->dump($indent) : '';
				//str_repeat(' ', $indent) . $this->elements[$key];
		}
		return $ret;
	}
}

// Returns inline-related object
function & Factory_InlineEx($text)
{
	// Check the first letter of the line
	if (substr($text, 0, 1) === '~') {
		$ret = & new ParagraphEx(' ' . substr($text, 1));
	} else {
		$ret = & new InlineEx($text);
	}
	return $ret;
}

function & Factory_DListEx(& $root, $text)
{
	$out = explode('|', ltrim($text), 2);
	if (count($out) < 2) {
		$ret = & Factory_InlineEx($text);
	} else {
		$ret = & new DListEx($out);
	}
	return $ret;
}

// '|'-separated table
function & Factory_TableEx(& $root, $text)
{
	if (! preg_match('/^\|(.+)\|([hHfFcC]?)$/', $text, $out)) {
		$ret = & Factory_InlineEx($text);
	} else {
		$ret = & new TableEx($out);
	}
	return $ret;
}

// Comma-separated table
function & Factory_YTableEx(& $root, $text)
{
	if ($text == ',') {
		$ret = & Factory_InlineEx($text);
	} else {
		$ret = & new YTableEx(csv_explode(',', substr($text, 1)));
	}
	return $ret;
}

function & Factory_DivEx(& $root, $text)
{
	$matches = array();

	// Seems block plugin?
	if (PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK) {
		// Usual code
		if (preg_match('/^\#([^\(]+)(?:\((.*)\))?/', $text, $matches)) {
			$ret = & new DivEx($matches);
			return $ret;
		}
	} else {
		// Hack code
		if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $text, $matches)) {
			$len  = strlen($matches[3]);
			$body = array();
			if ($len == 0) {
				$ret = & new DivEx($matches); // Seems legacy block plugin
			} else if (preg_match('/\{{' . $len . '}\s*\r(.*)\r\}{' . $len . '}/', $text, $body)) {
				$matches[3] .= "\r" . $body[1] . "\r" . str_repeat('}', $len);
				$ret = & new DivEx($matches); // Seems multiline-enabled block plugin
			} else {
				$ret = & new ParagraphEx($text);
			}
			return $ret;
		}
	}

	$ret = & new ParagraphEx($text);
	return $ret;
}

// InlineEx elements
class InlineEx extends ElementEx
{
	function InlineEx($text)
	{
		parent::ElementEx();
		$this->elements[] = trim((substr($text, 0, 1) == "\n") ?
			$text : guiedit_make_link($text));
	}

	function & insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function canContain($obj)
	{
		return is_a($obj, 'InlineEx');
	}

	function toString()
	{
		global $line_break;
		return join(($line_break ? '<br />' . "\n" : "\n&zwnj;"), $this->elements);
	}

	function & toPara($class = '')
	{
		$obj = & new ParagraphEx('', $class);
		$obj->insert($this);
		return $obj;
	}
}

// ParagraphEx: blank-line-separated sentences
class ParagraphEx extends ElementEx
{
	var $param;

	function ParagraphEx($text, $param = '')
	{
		parent::ElementEx();
		$this->param = $param;
		if ($text == '') return;

		if (substr($text, 0, 1) == '~')
			$text = ' ' . substr($text, 1);

		$this->insert(Factory_InlineEx($text));
	}

	function canContain($obj)
	{
		return is_a($obj, 'InlineEx');
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}

// * HeadingEx1
// ** HeadingEx2
// *** HeadingEx3
class HeadingEx extends ElementEx
{
	var $level;
	var $id;
	var $msg_top;

	function HeadingEx(& $root, $text)
	{
		parent::ElementEx();

		$this->level = min(5, strspn($text, '*'));

		$text = substr($text, $this->level);
		if (preg_match('/\s*\[#(\w+)\]/', $text, $matches)) {
			$this->id = $matches[1];
		}
		$text = preg_replace('/\s*\[#\w+\]/', '', $text);

		$this->insert(Factory_InlineEx($text));
		$this->level++; // h2,h3,h4
	}

	function & insert(& $obj)
	{
		parent::insert($obj);
		return $this->last = & $this;
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		return $this->wrap(parent::toString(),
			'h' . $this->level, ' id="' . $this->id . '"');
	}
}

// ----
// Horizontal Rule
class HRuleEx extends ElementEx
{
	function HRuleEx(& $root, $text)
	{
		parent::ElementEx();
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		global $hr;
		return $hr;
	}
}

// Lists (UL, OL, DL)
class ListContainerEx extends ElementEx
{
	var $tag;
	var $tag2;
	var $level;
	var $style;
	var $margin;
	var $left_margin;

	function ListContainerEx($tag, $tag2, $head, $text)
	{
		parent::ElementEx();

		$var_margin      = '_' . $tag . '_margin';
		$var_left_margin = '_' . $tag . '_left_margin';
		global $$var_margin, $$var_left_margin;

		$this->margin      = $$var_margin;
		$this->left_margin = $$var_left_margin;

		$this->tag   = $tag;
		$this->tag2  = $tag2;
		//$this->level = min(3, strspn($text, $head));
		$this->level = strspn($text, $head);
		$text = ltrim(substr($text, $this->level));

		$style = '';
		if (substr($text, -1) === "\x08") {
			$tag2 = 'li';
			$style = ' class="list_none"';
			$text = '';
		}

		parent::insert(new ListElementEx($this->level, $tag2, $style));

		if ($text !== '') {
			$this->last = & $this->last->insert(Factory_InlineEx($text));
		}
	}

	function canContain(& $obj)
	{
		return (! is_a($obj, 'ListContainerEx')
			|| ($this->tag === $obj->tag && $this->level === $obj->level));
	}

	function setParent(& $parent)
	{
		global $_list_pad_str;

		parent::setParent($parent);

		$step = $this->level;
		if (isset($parent->parent) && is_a($parent->parent, 'ListContainerEx'))
			$step -= $parent->parent->level;

		$margin = $this->margin * $step;
		if ($step === $this->level)
			$margin += $this->left_margin;

		$this->style = sprintf($_list_pad_str, $this->level, $margin, $margin);
	}

	function & insert(& $obj)
	{
		if (! is_a($obj, get_class($this)))
			return $this->last = & $this->last->insert($obj);

		// Break if no elements found (BugTrack/524)
		if (count($obj->elements) === 1 && empty($obj->elements[0]->elements))
			return $this->last->parent; // up to ListElementEx

		// Move elements
		foreach(array_keys($obj->elements) as $key) {
			parent::insert($obj->elements[$key]);
		}

		return $this->last;
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->tag, $this->style);
	}
}

class ListElementEx extends ElementEx
{
	function ListElementEx($level, $head, $style = '')
	{
		parent::ElementEx();
		$this->level = $level;
		$this->head  = $head;
		$this->style = $style;
	}

	function canContain(& $obj)
	{
		return (! is_a($obj, 'ListContainerEx') || ($obj->level > $this->level));
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->head, $this->style);
	}
}

// - One
// - Two
// - Three
class UListEx extends ListContainerEx
{
	function UListEx(& $root, $text)
	{
		parent::ListContainerEx('ul', 'li', '-', $text);
	}
}

// + One
// + Two
// + Three
class OListEx extends ListContainerEx
{
	function OListEx(& $root, $text)
	{
		parent::ListContainerEx('ol', 'li', '+', $text);
	}
}

// : definition1 | description1
// : definition2 | description2
// : definition3 | description3
class DListEx extends ListContainerEx
{
	function DListEx($out)
	{
		parent::ListContainerEx('dl', 'dt', ':', $out[0]);
		$this->last = & ElementEx::insert(new ListElementEx($this->level, 'dd'));
		if ($out[1] != '')
			$this->last = & $this->last->insert(Factory_InlineEx($out[1]));
	}
}

// > Someting cited
// > like E-mail text
class BQuoteEx extends ElementEx
{
	var $level;

	function BQuoteEx(& $root, $text)
	{
		parent::ElementEx();

		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		if ($head == '<') { // Blockquote close
			$level       = $this->level;
			$this->level = 0;
			$this->last  = & $this->end($root, $level);
			if ($text != '')
				$this->last = & $this->last->insert(Factory_InlineEx($text));
		} else {
			$this->insert(Factory_InlineEx($text));
		}
	}

	function canContain(& $obj)
	{
		return (! is_a($obj, get_class($this)) || $obj->level >= $this->level);
	}

	function & insert(& $obj)
	{
		// BugTrack/521, BugTrack/545
		if (is_a($obj, 'InlineEx')) {
			return parent::insert($obj->toPara(' class="quotation"'));
		}

		if (is_a($obj, 'BQuoteEx') && $obj->level == $this->level && count($obj->elements)) {
			$obj = & $obj->elements[0];
			if (is_a($this->last, 'ParagraphEx') && count($obj->elements)) {
				$obj = & $obj->elements[0];
			}
		}
		return parent::insert($obj);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'blockquote');
	}

	function & end(& $root, $level)
	{
		$parent = & $root->last;

		while (is_object($parent)) {
			if (is_a($parent, 'BQuoteEx') && $parent->level == $level)
				return $parent->parent;
			$parent = & $parent->parent;
		}
		return $this;
	}
}

class TableCellEx extends ElementEx
{
	var $tag = 'td'; // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style; // is array('width'=>, 'align'=>...);
	var $is_template;

	function TableCellEx($text, $is_template = FALSE)
	{
		global $xpwiki;
		parent::ElementEx();
		$this->style = $matches = array();
		$this->is_template = $is_template;

		if ($xpwiki->root->extended_table_format) {
			$text = $this->get_cell_style($text);
		}

		while (preg_match('/^(?:(LEFT|CENTER|RIGHT)|(BG)?COLOR\(([#\w]+)\)|SIZE\((\d+)\)):(.*)$/',
		    $text, $matches)) {
			if ($matches[1]) {
				$this->style['align'] = ' align="' . strtolower($matches[1]) . '"';
				$text = $matches[5];
			} else if ($matches[3]) {
				$name = $matches[2] ? 'background-color' : 'color';
				$color = $matches[3];
				if (preg_match("/^#[0-9a-f]{3}$/i", $color)) {
					$color = preg_replace("/[0-9a-f]/i", "$0$0", $color);
				}
				$this->style[$name] = $name . ':' . htmlspecialchars($color) . ';';
				$text = $matches[5];
			} else if ($matches[4]) {
				$this->style['size'] = 'font-size:' . htmlspecialchars($matches[4]) . 'px;';
				$text = $matches[5];
			}
		}

		// Text alignment
		if (empty($this->style['align'])) {
			if ($xpwiki->root->symbol_cell_align && preg_match('/^(<|=|>)(.+)$/', rtrim($text), $matches)) {
			// Text alignment with "<" or "=" or ">".
				if ($matches[1] === '=') {
					$this->style['align'] = ' align="center"';
				} else if ($matches[1] === '>') {
					$this->style['align'] = ' align="right"';
				} else if ($matches[1] === '<') {
					$this->style['align'] = ' align="left"';
				}
				$text = $matches[2];
			} else if ($xpwiki->root->space_cell_align && preg_match('/^(\s+)?(.+?)(\s+)?$/', $text, $matches)) {
			// Text alignment with 1 or more spaces.
				if ($matches[2] !== '~') {
					if (! empty($matches[1]) && ! empty($matches[3])) {
						$this->style['align'] = ' align="center"';
					} else if (! empty($matches[1])) {
						$this->style['align'] = ' align="right"';
					} else if (! empty($matches[3])) {
						$this->style['align'] = ' align="left"';
					}
					if (! empty($this->style['align'])) {
						$text = $matches[2];
					}
				}
			}
		}

		if ($is_template && is_numeric($text))
			$this->style['width'] = ' width="' . $text . '"';

		if (rtrim($text) === '<' || ($xpwiki->root->empty_cell_join && $text === '')) {
			$this->colspan = -1;
		} else if (rtrim($text) === '>') {
			$this->colspan = 0;
		} else {
			if (in_array($text, array('~', '^'))) {
				$this->rowspan = 0;
			} else {
				if (substr($text, 0, 1) === '~') {
					$this->tag = 'th';
					$text = substr($text, 1);
				}
			}
		}

		if ($is_template) {
			$this->tag = 'col';
		}
		else if ($text == '~') {
			$this->rowspan = 0;
		} else if (substr($text, 0, 1) == '~') {
			$this->tag = 'th';
			$text      = substr($text, 1);
		}

		if ($text != '' && $text{0} == '#') {
			// Try using DivEx class for this $text
			$obj = & Factory_DivEx($this, $text);
			if (is_a($obj, 'ParagraphEx'))
				$obj = & $obj->elements[0];
		} else {
			$obj = & Factory_InlineEx($text);
		}

		$this->insert($obj);
	}

	function setStyle(& $style)
	{
		foreach ($style as $key=>$value)
			if (! isset($this->style[$key]))
				$this->style[$key] = $value;
	}

	function toString()
	{

		if ($this->is_template) {
			$param = '';
		}
		else {
			if ($this->rowspan === 0 || $this->colspan < 1) return '';

			$param = ' class="style_' . $this->tag . '"';
			if ($this->rowspan > 1)
				$param .= ' rowspan="' . $this->rowspan . '"';
			if ($this->colspan > 1) {
				$param .= ' colspan="' . $this->colspan . '"';
				unset($this->style['width']);
			}
		}

		if (! empty($this->style)) {
			foreach($this->style as $key=>$value) {
				if ($key == 'align' || $key == 'width') {
					$param .= $value;
					unset($this->style[$key]);
				}
			}
			$param .= ' style="' . join(' ', $this->style) . '"';
		}

		return $this->wrap($this->is_template ? '' : parent::toString(), $this->tag, $param, FALSE);
	}

	function get_cell_style($string) {
		global $xpwiki;

		$cells = explode('|',$string,2);
		$colors_reg = COLORS_REG;

		// セル文字色
		if (preg_match("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['color'] = "color:".$tmp[1].";";
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?)/i","FC:$2",$cells[0]);
			$cells[0] = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}
		// セル規定背景色指定
		if (preg_match("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['background-color'] = "background-color:".$tmp[1].";";
			$cells[0] = preg_replace("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?)/i","CC:$2",$cells[0]);
			$cells[0] = preg_replace("/(?:[SCB]C):(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}
		// セル規定背景画指定
		if (preg_match("/(?:[SCB]C):\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?/i",$cells[0],$tmp)) {
			if (strpos($tmp[1], $xpwiki->cont['ROOT_URL']) === 0) {
				$tmp[1] = htmlspecialchars($tmp[1]);
				$this->style['background-image'] = "background-image: url(".$tmp[1].");";
				if (!empty($tmp[2])) $this->style['bgcolor'] .= "background-repeat: no-repeat;";
			}
			$cells[0] = preg_replace("/(?:[SCB]C):\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?/i","",$cells[0]);
		}
		// セル ボーダー
		if (preg_match("/K:([0-9]+),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i",$cells[0],$tmp)) {
			if (array_key_exists (3,$tmp)) {
				switch (strtolower($tmp[3])) {
					case 'one':
					case 's':
					case 'solid':
				 		$border_type = "solid";
				 		break;
					case 'two':
					case 'd':
					case 'double':
						$border_type = "double";
				 		break;
					case 'boko':
					case 'g':
					case 'groove':
						$border_type = "groove";
				 		break;
					case 'deko':
					case 'r':
					case 'ridge':
						$border_type = "ridge";
				 		break;
					case 'in':
					case 'i':
					case 'inset':
						$border_type = "inset";
				 		break;
					case 'out':
					case 'o':
					case 'outset':
						$border_type = "outset";
				 		break;
					case 'dash':
					case 'da':
					case 'dashed':
						$border_type = "dashed";
				 		break;
					case 'dott':
					case 'do':
					case 'dotted':
						$border_type = "dotted";
				 		break;
					default:
						$border_type = "outset";
				}
			} else {
				$border_type = "outset";
			}
			//$this->table_style .= " border=\"".$tmp[1]."\"";
			if (array_key_exists (1,$tmp)) {
				if ($tmp[1]==="0"){
					$this->style['border'] = "border:none;";
				} else {
					$this->style['border'] = "border:".$border_type." ".$tmp[1]."px;";
				}
			}
			if (array_key_exists (2,$tmp)) {
				if ($tmp[2]!=""){
					$this->style['padding'] = " padding:".$tmp[2].";";
				} else {
					$this->style['padding'] = " padding:5px;";
				}
			}
			$cells[0] = preg_replace("/K:([0-9]+),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i","",$cells[0]);
		} else {
//			$this->style['border'] = "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i",$cells[0],$tmp)) {
			if ($tmp[1]==="0") $tmp[1]="transparent";
			$this->style['border-color'] = "border-color:".$tmp[1].";";
			$cells[0] = preg_replace("/KC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$cells[0]);
		}
		// セル規定文字揃え、幅指定
		if (preg_match("/(?:^ *)(?:(LEFT|CENTER|RIGHT)?:(TOP|MIDDLE|BOTTOM)?)?(?::([0-9]+[%]?))? ?/i",$cells[0],$tmp)) {
			//var_dump($tmp); echo "<br>\n";
			if (@$tmp[1] || @$tmp[2] || @$tmp[3]) {
				if (@$tmp[3]) {
					//if (!strpos($tmp[3],"%")) $tmp[3] .= "px";
					$this->style['width'] = ' width="' . $tmp[3] . '"';
				}
				if (@$tmp[1]) $this->style['align'] = ' align="'.strtolower($tmp[1]).'"';
				if (@$tmp[2]) $this->style['valign'] = "vertical-align:".strtolower($tmp[2]).";";
				$cells[0] = preg_replace("/(?:^ *)(?:(LEFT|CENTER|RIGHT)?:(TOP|MIDDLE|BOTTOM)?)?(?::([0-9]+[%]?))? ?/i","",$cells[0]);
			}
		}
		return implode('|',$cells);
	}
}

// | title1 | title2 | title3 |
// | cell1  | cell2  | cell3  |
// | cell4  | cell5  | cell6  |
class TableEx extends ElementEx
{
	var $type;
	var $types;
	var $col; // number of column

	function TableEx($out)
	{
		global $xpwiki;
		parent::ElementEx();

		$cells       = explode('|', $out[1]);
		$this->col   = count($cells);
		$this->type  = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = ($this->type == 'c');

		$this->table_style = '';
		$this->table_sheet = '';
		$this->div_style = '';

		if ($xpwiki->root->extended_table_format && $is_template) {
			$cells[0] = $this->get_table_style($cells[0]);
		}

		$row = array();
		foreach ($cells as $cell)
			$row[] = & new TableCellEx($cell, $is_template);
		$this->elements[] = $row;
	}

	function canContain(& $obj)
	{
		return is_a($obj, 'TableEx') && ($obj->col == $this->col);
	}

	function & insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		$this->types[]    = $obj->type;
		return $this;
	}

	function toString()
	{
		static $parts = array('h'=>'thead', 'f'=>'tfoot', ''=>'tbody');

		// Set rowspan (from bottom, to top)
		for ($ncol = 0; $ncol < $this->col; $ncol++) {
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow) {
				$row = & $this->elements[$nrow];
				if ($row[$ncol]->rowspan == 0) {
					++$rowspan;
					continue;
				}
				$row[$ncol]->rowspan = $rowspan;
				// Inherits row type
				while (--$rowspan)
					$this->types[$nrow + $rowspan] = $this->types[$nrow];
				$rowspan = 1;
			}
		}

		// Set colspan and style
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow) {
			$row = & $this->elements[$nrow];
			if ($this->types[$nrow] === 'c')
				$stylerow = & $row;
			$colspan = 1;
			$enable_col = NULL;
			foreach (array_keys($row) as $ncol) {
				if (! is_null($enable_col) && $row[$ncol]->colspan === -1) {
					++ $row[$enable_col]->colspan;
					continue;
				}
				if ($row[$ncol]->colspan < 1) {
					++ $colspan;
					continue;
				}
				$enable_col = $ncol;
				$row[$ncol]->colspan = $colspan;
				if ($stylerow !== NULL) {
					$row[$ncol]->setStyle($stylerow[$ncol]->style);
					// Inherits column style
					while (-- $colspan)
						$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
				}
				$colspan = 1;
			}
		}

		// toString
		$string = '';
		$part_string = '';
		$old_type = '';
		foreach (array_keys($this->elements) as $nrow) {
			if (($old_type != $this->types[$nrow]) && ($part_string != '')) {
				$string .= ($old_type == 'c') ? $part_string : $this->wrap($part_string, $parts[$old_type]);
				$part_string = '';
			}
			$row        = & $this->elements[$nrow];
			$row_string = '';
			foreach (array_keys($row) as $ncol) {
				$row_string .= $row[$ncol]->toString();
			}
			$part_string .= $this->wrap($row_string, (($this->types[$nrow] == 'c') ? 'colgroup' : 'tr'));
			$old_type = $this->types[$nrow];
		}
		$string .= ($old_type == 'c') ? $part_string : $this->wrap($part_string, $parts[$old_type]);

		//return $this->wrap($string, 'table', ' class="style_table" cellspacing="1" border="0" align="center"');
		$string = $this->wrap($string, 'table', ' class="style_table"' . "$this->table_style style=\"$this->table_sheet\"");
		return $this->wrap($string, 'div', ' class="ie5" '.$this->div_style);
	}

	function get_table_style($string) {
		global $xpwiki;

		$colors_reg = COLORS_REG;
		//$this->table_around = "<br clear=all /><br />";
		$this->table_around = "";
		// 回り込み指定
		if (preg_match("/AROUND ?/i",$string)) $this->table_around = "float:";
		// ボーダー指定
		if (preg_match("/B:([0-9]*),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i",$string,$reg)) {
			if (array_key_exists (3,$reg)) {
				switch (strtolower($reg[3])) {
					case 'one':
					case 's':
					case 'solid':
				 		$border_type = "solid";
				 		break;
					case 'two':
					case 'd':
					case 'double':
						$border_type = "double";
				 		break;
					case 'boko':
					case 'g':
					case 'groove':
						$border_type = "groove";
				 		break;
					case 'deko':
					case 'r':
					case 'ridge':
						$border_type = "ridge";
				 		break;
					case 'in':
					case 'i':
					case 'inset':
						$border_type = "inset";
				 		break;
					case 'out':
					case 'o':
					case 'outset':
						$border_type = "outset";
				 		break;
					case 'dash':
					case 'da':
					case 'dashed':
						$border_type = "dashed";
				 		break;
					case 'dott':
					case 'do':
					case 'dotted':
						$border_type = "dotted";
				 		break;
					default:
						$border_type = "outset";
				}
			} else {
				$border_type = "outset";
			}

			//$this->table_style .= " border=\"".$reg[1]."\"";
			if (array_key_exists (1,$reg)) {
				if ($reg[1]==="0"){
					$this->table_sheet .= "border:none;";
				} else {
					$this->table_sheet .= "border:".$border_type." ".$reg[1]."px;";
				}
			}
			if (array_key_exists (2,$reg)) {
				if ($reg[2]!=""){
					$this->table_style .= " cellspacing=\"".$reg[2]."\"";
				} else {
					$this->table_style .= " cellspacing=\"1\"";
				}
			}
			$string = preg_replace("/B:([0-9]*),?([0-9]*)\(?(one|s(?:olid)?|da(?:sh(?:ed)?)?|do(?:tt(?:ed)?)?|two|d(?:ouble)?|boko|g(?:roove)?|deko|r(?:idge)?|in?(?:set)?|o(?:ut(?:set)?)?)?\)? ?/i","",$string);
		} else {
			$this->table_style .= " border=\"0\" cellspacing=\"1\"";
			//$this->table_style .= " cellspacing=\"1\"";
			//$this->table_sheet .= "border:none;";
		}
		// ボーダー色指定
		if (preg_match("/BC:(#?[0-9a-f]{6}?|$colors_reg|0) ?/i",$string,$reg)) {
			$this->table_sheet .= "border-color:".$reg[1].";";
			$string = preg_replace("/BC:(#?[0-9abcdef]{6}?|$colors_reg) ?/i","",$string);
		}
		// テーブル背景色指定
		if (preg_match("/TC:(#?[0-9a-f]{6}?|$colors_reg|0) ?/i",$string,$reg)) {
			if ($reg[1]==="0") $reg[1]="transparent";
			$this->table_sheet .= "background-color:".$reg[1].";";
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?)/i","TC:$2",$string);
			$string = preg_replace("/TC:(#?[0-9abcdef]{6}?|$colors_reg|0) ?/i","",$string);
		}
		// テーブル背景画像指定
		if (preg_match("/TC:\(([^),]*)(,(?:no|one(?:ce)?|1))?\) ?/i",$string,$reg)) {
			//$reg[1] = str_replace("http","HTTP",$reg[1]);
			if (strpos($reg[1], $xpwiki->cont['ROOT_URL']) === 0) {
				$reg[1] = htmlspecialchars($reg[1]);
				$this->table_sheet .= "background-image: url(".$reg[1].");";
				if (!empty($reg[2])) $this->table_sheet .= "background-repeat: no-repeat;";
			}
			$string = preg_replace("/TC:\(([^),]*)(,once|,1)?\) ?/i","",$string);
		}
		// 配置・幅指定
		if (preg_match("/T(LEFT|RIGHT)/i",$string,$reg)) {
			$this->table_align = strtolower($reg[1]);
			//$this->table_style .= " align=\"".$this->table_align."\"";
			$this->div_style = " style=\"text-align:".$this->table_align."\"";
			if ($this->table_align === "left"){
				$this->table_sheet .= "margin-left:10px;margin-right:auto;";
			} else {
				$this->table_sheet .= "margin-left:auto;margin-right:10px;";
			}
			if ($this->table_around) {
				$this->table_sheet .= $this->table_around . $this->table_align . ';';
			}
		}
		if (preg_match("/T(CENTER)/i",$string,$reg)) {
			//$this->table_style .= " align=\"".strtolower($reg[1])."\"";
			$this->div_style = " style=\"text-align:".strtolower($reg[1])."\"";
			$this->table_sheet .= "margin-left:auto;margin-right:auto;";
			//$this->table_around = "";
		}
		if (preg_match("/T(LEFT|CENTER|RIGHT)?:([0-9]+(%|px)?) ?/i",$string,$reg)) {
			$this->table_sheet .= "width:".$reg[2].";";
		}
		$string = preg_replace("/^(TLEFT|TCENTER|TRIGHT|T):([0-9]+(%|px)?)? ?/i","",$string);
		return ltrim($string);
	}

	function setStyleInherit(& $style)
	{
		foreach ($style as $key=>$value)
			$style[$key] = 'inherit';
	}
}

// , title1 , title2 , title3
// , cell1  , cell2  , cell3
// , cell4  , cell5  , cell6
class YTableEx extends ElementEx
{
	var $col;

	function YTableEx($_value)
	{
		parent::ElementEx();

		$align = $value = $matches = array();
		foreach($_value as $val) {
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/', $val, $matches)) {
				$align[] =($matches[1] != '') ?
					((isset($matches[3]) && $matches[3] != '') ?
						' align="center"' :
						' align="right"'
					) : '';
				$value[] = $matches[2];
			} else {
				$align[] = '';
				$value[] = $val;
			}
		}
		$this->col = count($value);
		$colspan = array();
		foreach ($value as $val)
			$colspan[] = ($val == '==') ? 0 : 1;
		$str = '';
		$count = count($value);
		for ($i = 0; $i < $count; $i++) {
			if ($colspan[$i]) {
				while ($i + $colspan[$i] < $count && $value[$i + $colspan[$i]] == '==')
					$colspan[$i]++;
				$colspan[$i] = ($colspan[$i] > 1) ? ' colspan="' . $colspan[$i] . '"' : '';
				$str .= '<td class="style_td"' . $align[$i] . $colspan[$i] . '>' . guiedit_make_link($value[$i]) . '</td>';
			}
		}
		$this->elements[] = $str;
	}

	function canContain(& $obj)
	{
		return is_a($obj, 'YTableEx') && ($obj->col == $this->col);
	}

	function & insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function toString()
	{
		$rows = '';
		foreach ($this->elements as $str)
			$rows .= "\n" . '<tr class="style_tr">' . $str . '</tr>' . "\n";
		$rows = $this->wrap($rows, 'table', ' class="style_table" cellspacing="1" border="0"');
		return $this->wrap($rows, 'div', ' class="ie5"');
	}
}

// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
// ' 'Space-beginning sentence
class PreEx extends ElementEx
{
	function PreEx(& $root, $text)
	{
		global $preformat_ltrim;
		parent::ElementEx();
		$this->elements[] = htmlspecialchars(
			(! $preformat_ltrim || $text == '' || $text{0} != ' ') ? $text : substr($text, 1));
		$this->class = $root->comment? ' class="comment"' : '';
	}

	function canContain(& $obj)
	{
		return is_a($obj, 'PreEx');
	}

	function & insert(& $obj)
	{
		$this->elements[] = str_replace(' ', '&nbsp;', $obj->elements[0]);
		return $this;
	}

	function toString()
	{
		return $this->wrap(join("\n", $this->elements), 'pre', $this->class);
	}
}

// Block plugin: #something (started with '#')
class DivEx extends ElementEx
{
	var $text;
	var $name;
	var $param;

	function DivEx($out)
	{
		parent::ElementEx();
		list(, $this->name, $this->param, $this->text) = array_pad($out, 4, '');
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		$styles = array();
		switch ($this->name) {
			case 'br':
				return '<p><br class="block" /></p>';
			case 'hr':
				return '<hr class="short_line" />';
			case 'ref':
				$param = ($this->param != '') ? csv_explode(',', $this->param) : array();
				return guiedit_convert_ref($param);
			case 'clear':
				$styles[] = 'clear:both;';
		}

		if ($this->text) {
			$this->text = str_replace(' ', '&nbsp;',htmlspecialchars($this->text));
			$this->text = preg_replace("/\r/", "<br />", $this->text);
		}

		$this->param = htmlspecialchars($this->param);
		$inner = "#$this->name" . ($this->param ? "($this->param)" : '') . $this->text;
		if (MSIE) $styles[] = 'cursor:default;';

		$style = '';
		if ($styles) {
			$style = ' style="' . join('', $styles) . '"';
		}

		//$inner = '<pre>'. $inner . '</pre>';
		global $xpwiki;
		$attr = ($xpwiki->func->exist_plugin_convert($this->name))? ' class="plugin" contenteditable="true"' : '';
		return $this->wrap($inner, 'div', $attr . $style);
	}
}

// LEFT:/CENTER:/RIGHT:
class AlignEx extends ElementEx
{
	var $align;

	function AlignEx($align)
	{
		parent::ElementEx();
		$this->align = $align;
	}

	function canContain(& $obj)
	{
		return is_a($obj, 'InlineEx');
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'div', ' style="text-align: ' . $this->align . '"');
	}
}

// BodyEx
class BodyEx extends ElementEx
{
	var $classes = array(
		'-' => 'UListEx',
		'+' => 'OListEx',
		'>' => 'BQuoteEx',
		'<' => 'BQuoteEx');
	var $factories = array(
		':' => 'DListEx',
		'|' => 'TableEx',
		',' => 'YTableEx',
		'#' => 'DivEx');

	function BodyEx()
	{
		parent::ElementEx();
	}

	function parse(& $lines)
	{
		$this->last = & $this;
		$matches = array();
		$last_level = 0;

		while (! empty($lines)) {
			$line = rtrim(array_shift($lines), "\r\n");

			$this->comment = false;

			// Empty
			if ($line === '') {
				$this->last = & $this;
				$last_level = 0;
				continue;
			}

			// Escape comments
			//if (substr($line, 0, 2) == '//') continue;
			if (substr($line, 0, 2) === '//') {
				$this->comment = true;
				$line = ' ' . $line;
			}

			// The first character
			$head = $line[0];

			if ($head === ',') {
				$this->comment = true;
				$line = ' ' . $line;
				$head = ' ';
			}

			// LEFT, CENTER, RIGHT
			if ($head === 'R' || $head === 'C' || $head === 'L') {

				if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/', $line, $matches)) {
					// <div style="text-align:...">
					$this->last = & $this->last->add(new AlignEx(strtolower($matches[1])));
					if ($matches[2] === '') {
						continue;
					}
					$line = $matches[2];
					$head = $line[0];
				}
			}

			switch ($head) {

			// Horizontal Rule
			case '-':
				if (preg_match('/-{4,}$/', $line)) {
					$this->insert(new HRuleEx($this, $line));
					$last_level = 0;
					continue 2;
				}
				break;

			// Multiline-enabled block plugin
			case '#':
				if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
				    preg_match('/^#[^{]+(\{\{+)\s*$/', $line, $matches)) {
					$len = strlen($matches[1]);
					$line .= "\r"; // Delimiter
					while (! empty($lines)) {
						$next_line = preg_replace("/[\r\n]*$/", '', array_shift($lines));
						if (preg_match('/\}{' . $len . '}/', $next_line)) {
							$line .= $next_line;
							break;
						} else {
							$line .= $next_line .= "\r"; // Delimiter
						}
					}
				}
				break;

			// HeadingEx
			case '*':
				$this->insert(new HeadingEx($this, $line));
				$last_level = 0;
				continue 2;
				break;

			// PreEx
			case ' ':
			case "\t":
				$this->last = & $this->last->add(new PreEx($this, $line));
				continue 2;
				break;

			// <, <<, <<< only to escape blockquote.
			case '<':
				if ($head === '<' && ! preg_match('/^<{1,3}\s*$/', $line)) {
					$head = '';
				}
				break;

			}

			// Line Break
			if (substr($line, -1) === '~')
				$line = substr($line, 0, -1) . "\r";

			// Other Character
			if (isset($this->classes[$head])) {
				$classname  = $this->classes[$head];

				$this_level = strspn($line, $head);
				if ($this_level - $last_level > 1) {
					for($_lev = $last_level+1; $_lev < $this_level; $_lev++ ) {
						$this->last = & $this->last->add(new $classname ($this, str_repeat($head, $_lev)."\x08"));
					}
				}
				$last_level = $this_level;

				$this->last = & $this->last->add(new $classname($this, $line));
				continue;
			}

			// Other Character
			if (isset($this->factories[$head])) {

				if ($head === ':') {
					$this_level = strspn($line, $head);
					if ($this_level - $last_level > 1) {
						for($_lev = $last_level+1; $_lev < $this_level; $_lev++ ) {
							$this->last = & $this->last->add(Factory_DListEx($this, ':|'));
						}
					}
					$last_level = $this_level;
				}

				$factoryname = 'Factory_' . $this->factories[$head];
				$this->last  = & $this->last->add($factoryname($this, $line));
				continue;
			}

			// Default
			$this->last = & $this->last->add(Factory_InlineEx($line));
		}
	}

	function & insert(& $obj)
	{
		if (is_a($obj, 'InlineEx')) $obj = & $obj->toPara();
		return parent::insert($obj);
	}

	function toString()
	{
		global $vars;

		$text = parent::toString();

		return $text . "\n";
	}
}
