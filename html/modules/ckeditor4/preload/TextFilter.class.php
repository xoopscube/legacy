<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.4.0
 * @author     Naoki Sawada (aka nao-pon) https://xoops.hypweb.net/
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

class ckeditor4_TextFilter extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_TextFilter.MakeXCodeConvertTable', [&$this, 'filter']);
	}

	function filter(&$patterns, &$replacements)
	{
		$allow_callback = (function_exists('property_exists') && class_exists('Legacy_TextFilter') && property_exists('Legacy_TextFilter', 'mXCodeCallbacks'));

		// <!--ckeditor4FlgSource-->
		$patterns[] = '/&lt;!--ckeditor4FlgSource--&gt;/';
		$replacements[0][] =
		$replacements[1][] = '';

		// [img align=left title=hoge width=10 height=10]
		$patterns[] = '/\[img(?:\s+align=(&quot;|&#039;)?(left|center|right)(?(1)\1))?(?:\s+title=(&quot;|&#039;)?((?(3)[^]]*|[^\]\s]*))(?(3)\3))?(?:\s+w(?:idth)?=(&quot;|&#039;)?([\d]+?)(?(5)\5))?(?:\s+h(?:eight)?=(&quot;|&#039;)?([\d]+?)(?(7)\7))?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/img\]/US';
		$replacements[0][] = '<a href="$9" title="$4" target="_blank">$9</a>';
		$replacements[1][] = '<img src="$9" align="$2" width="$6" height="$8" alt="$4" title="$4">';

		// [siteimg align=left title=hoge width=10 height=10]
		$patterns[] = '/\[siteimg(?:\s+align=(&quot;|&#039;)?(left|center|right)(?(1)\1))?(?:\s+title=(&quot;|&#039;)?((?(3)[^]]*|[^\]\s]*))(?(3)\3))?(?:\s+w(?:idth)?=(&quot;|&#039;)?([\d]+?)(?(5)\5))?(?:\s+h(?:eight)?=(&quot;|&#039;)?([\d]+?)(?(7)\7))?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/siteimg\]/US';
		$replacements[0][] =
		$replacements[1][] = '<img src="'.XOOPS_URL.'/$9" align="$2" width="$6" height="$8" alt="$4" title="$4">';

		// [pagebreak]
		$patterns[] = '/\[pagebreak\]/';
		$replacements[0][] =
		$replacements[1][] = '<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>';

		// [list] nested allow
		/// pre convert
		$patterns[] = '/\[list/';
		$replacements[0][] = $replacements[1][] = "\x01";
		$patterns[] = '/\[\/list\]/';
		$replacements[0][] = $replacements[1][] = "\x02";
		/// outer matting
		if ($allow_callback) {
			$patterns[] = '/\x01(?:\=([^\]]+))?\](?:\r\n|[\r\n])((?:(?>[^\x01\x02]+)|(?R))*)\x02(?:\r\n|[\r\n]|$)/S';
			$replacements[0][] = $replacements[1][] = 'ckeditor4_TextFilter::get_list_tag';
		} else {
			$patterns[] = '/\x01(?:\=([^\]]+))?\](?:\r\n|[\r\n])((?:(?>[^\x01\x02]+)|(?R))*)\x02(?:\r\n|[\r\n]|$)/eS';
			$replacements[0][] = $replacements[1][] = 'ckeditor4_TextFilter::get_list_tag(array(\'$0\', \'$1\', \'$2\'))';
		}
		/// [*] to <li></li>
		$patterns[] = '/\[\*\](.*?)(?:\r\n|[\r\n])([\r\n]*)(?=(?:\\[\*\]|<\/[uo]l>|[\x01\x02]))/sS';
		$replacements[0][] = $replacements[1][] = '<li>$1$2</li>';
		/// post convert 1
		$patterns[] = '/<\/li>\x01[^\]]*\](?:\r\n|[\r\n])/';
		$replacements[0][] = $replacements[1][] = '<ul>';
		/// post convert 2
		$patterns[] = '/\x02(?:\r\n|[\r\n])/';
		$replacements[0][] = $replacements[1][] = '</ul></li>';
	}

	static public function get_list_tag($m) {
		switch($m[1]) {
			case '1':
				$tag = 'ol';
				$style = '';
				break;
            case 'R':
            case 'a':
				$tag = 'ol';
				$style = ' style="list-style-type:lower-alpha"';
				break;
			case 'A':
				$tag = 'ol';
				$style = ' style="list-style-type:upper-alpha"';
				break;
			case 'r':
				$tag = 'ol';
				$style = ' style="list-style-type:lower-roman"';
				break;
            case 'd':
				$tag = 'ol';
				$style = ' style="list-style-type:decimal"';
				break;
			case 'D':
				$tag = 'ol';
				$style = ' style="list-style-type:disc"';
				break;
            case 'S':
            case 'C':
				$tag = 'ol';
				$style = ' style="list-style-type:circle"';
				break;
            default:
				$tag = 'ul';
				$style = '';
		}
		return '<'.$tag.$style.'>' . $m[2] . '</'.$tag.'>';
	}
}
