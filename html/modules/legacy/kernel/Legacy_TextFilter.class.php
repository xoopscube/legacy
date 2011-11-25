<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_TextFilter.class.php,v 1.9 2008/09/25 15:11:57 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * @public
 * @brief The text filter for Legacy.
 */
class Legacy_TextFilter extends XCube_TextFilter
{
	/**
	 * @var XCube_Delegate
	 */
	var $mMakeXCodeConvertTable = null;
	/**
	 * @var XCube_Delegate
	 */
	var $mMakeXCodeCheckImgPatterns = null;
	/**
	 * @var XCube_Delegate
	 */
	var $mMakeClickableConvertTable = null;
	/**
	 * @var XCube_Delegate
	 */
	var $mMakePreXCodeConvertTable = null;
	/**
	 * @var XCube_Delegate
	 */
	var $mMakePostXCodeConvertTable = null;
	/**
	 * @var XCube_Delegate
	 * @deprecated	keep compatible with XC2.1 Beta3
	 */
	var $mXCodePre = null;
	/**
	 * @var XCube_Delegate
	 * @deprecated	keep compatible with XC2.1 Beta3. Legacy 2.2.0 will not support this.
	 * @todo
	 *	  This is a deprecated member.
	 */
	var $mMakeClickablePre = null;


	var $mClickablePatterns = array();
	var $mClickableReplacements = array();

	var $mXCodePatterns = array();
	var $mXCodeReplacements = array();
	var $mXCodeCheckImgPatterns = array();

	var $mPreXCodePatterns = array();
	var $mPreXCodeReplacements = array();

	var $mPostXCodePatterns = array();
	var $mPostXCodeReplacements = array();
	
	var $mSmileys = array();
	var $mSmileysConvTable = array();

	/**
	 * @public
	 * @brief Constructor
	 * @todo
	 *	  This method keeps a deprecated delegate.
	 */
	function Legacy_TextFilter()
	{
		$this->mMakeClickableConvertTable = new XCube_Delegate;
		$this->mMakeClickableConvertTable->register('Legacy_TextFilter.MakeClickableConvertTable');
		$this->mMakeClickableConvertTable->add('Legacy_TextFilter::makeClickableConvertTable', XCUBE_DELEGATE_PRIORITY_2);

		$this->mMakeXCodeConvertTable = new XCube_Delegate;
		$this->mMakeXCodeConvertTable->register('Legacy_TextFilter.MakeXCodeConvertTable');
		$this->mMakeXCodeConvertTable->add('Legacy_TextFilter::makeXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

		$this->mMakeXCodeCheckImgPatterns = new XCube_Delegate;
		$this->mMakeXCodeCheckImgPatterns->register('Legacy_TextFilter.MakeXCodeCheckImgPatterns');
		$this->mMakeXCodeCheckImgPatterns->add('Legacy_TextFilter::makeXCodeCheckImgPatterns', XCUBE_DELEGATE_PRIORITY_2);

		$this->mMakePreXCodeConvertTable = new XCube_Delegate;
		$this->mMakePreXCodeConvertTable->register('Legacy_TextFilter.MakePreXCodeConvertTable');
		$this->mMakePreXCodeConvertTable->add('Legacy_TextFilter::makePreXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

		$this->mMakePostXCodeConvertTable = new XCube_Delegate;
		$this->mMakePostXCodeConvertTable->register('Legacy_TextFilter.MakePostXCodeConvertTable');
		$this->mMakePostXCodeConvertTable->add('Legacy_TextFilter::makePostXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

		//@deprecated
		//Todo: For keeping compatible with XC2.1 Beta3
		$this->mMakeClickablePre = new XCube_Delegate();
		$this->mMakeClickablePre->register('MyTextSanitizer.MakeClickablePre');

		$this->mXCodePre = new XCube_Delegate();
		$this->mXCodePre->register('MyTextSanitizer.XoopsCodePre');
	}
	
	function getInstance(&$instance) {
		if (empty($instance)) {
			$instance = new Legacy_TextFilter();
		}
	}
	
	/**
	 * Filters for editing text
	 *
	 * @param	string	$text
	 * @param	string	$x2comat
	 * @return	string
	 *
	 **/
	function toShow($text, $x2comat=false) {
		if ($x2comat) {
			//ToDo: &nbsp; patern is defined for XOOPS2.0 compatiblity. But what is it?
			//		This comatiblity option is used from method from MyTextSanitizer.
			//
			return preg_replace(array("/&amp;(#[0-9]+|#x[0-9a-f]+|[a-z]+[0-9]*);/i", "/&nbsp;/i"), array('&\\1;', '&amp;nbsp;'), htmlspecialchars($text, ENT_QUOTES));
		} else {
			return preg_replace("/&amp;(#[0-9]+|#x[0-9a-f]+|[a-z]+[0-9]*);/i", '&\\1;', htmlspecialchars($text, ENT_QUOTES));
		}
	}

	/**
	 * Filters for editing text
	 *
	 * @param	string	$text
	 * @return	string
	 *
	 **/
	function toEdit($text) {
		return preg_replace("/&amp;(#0?[0-9]{4,6};)/i", '&$1', htmlspecialchars($text, ENT_QUOTES));
	}

	/**
	 * Filters textarea data for display
	 *
	 * @param	string	$text
	 * @param	bool	$html	allow html?
	 * @param	bool	$smiley allow smileys?
	 * @param	bool	$xcode	allow xoopscode?
	 * @param	bool	$image	allow inline images?
	 * @param	bool	$br 	convert linebreaks?
	 * @param	string	$x2comat
	 * @return	string
	 **/
	function toShowTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat=false) {
		$text = $this->preConvertXCode($text, $xcode);
		if ($html != 1) $text = $this->toShow($text, $x2comat);
		$text = $this->makeClickable($text);
		if ($smiley != 0) $text = $this->smiley($text);
		if ($xcode != 0) $text = $this->convertXCode($text, $image);
		if ($br != 0) $text = $this->nl2Br($text);
		$text = $this->postConvertXCode($text, $xcode, $image);
		return $text;
	}

	/**
	 * Filters textarea data for preview
	 *
	 * @param	string	$text
	 * @param	bool	$html	allow html?
	 * @param	bool	$smiley allow smileys?
	 * @param	bool	$xcode	allow xoopscode?
	 * @param	bool	$image	allow inline images?
	 * @param	bool	$br 	convert linebreaks?
	 * @param	string	$x2comat
	 * @return	string
	 **/
	function toPreviewTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat=false)
	{
		return $this->toShowTarea($text, $html, $smiley, $xcode, $image, $br, $x2comat);
	}

	/**
	 * purifyHtml
	 * 
	 * @param	string	$html
	 * @param	string	$encoding
	 * @param	string	$doctype
	 * 
	 * @return	string
	 **/
	public function purifyHtml(/*** string ***/ $html, /*** string ***/ $encoding=null, /*** string ***/ $doctype=null)
	{
		require_once XOOPS_LIBRARY_PATH.'/htmlpurifier/library/HTMLPurifier.auto.php';
		$encoding = $encoding ? $encoding : _CHARSET; 
		$doctypeArr = array("HTML 4.01 Strict","HTML 4.01 Transitional","XHTML 1.0 Strict","XHTML 1.0 Transitional","XHTML 1.1");
	
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', $encoding);
		if(in_array($doctype, $doctypeArr)){
			$config->set('HTML.Doctype', $doctype);
		}
	
		$purifier = new HTMLPurifier($config);
		return $purifier->purify($html);
	}


	/**
	 * Get the smileys
	 *
	 * @return	array
	 */
	function getSmileys() {
		if (count($this->mSmileys) == 0) {
			$this->mSmileysConvTable[0] = $this->mSmileysConvTable[1] = array();
			$db =& Database::getInstance();
			if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles"))){
				while ($smile = $db->fetchArray($getsmiles)) {
					$this->mSmileys[] = $smile;
					$this->mSmileysConvTable[0][] = $smile['code'];
					$this->mSmileysConvTable[1][] = '<img src="'.XOOPS_UPLOAD_URL.'/'.htmlspecialchars($smile['smile_url']).'" alt="" />';
				}
			}
		}
		return $this->mSmileys;
	}

	/**
	 * Replace emoticons in the message with smiley images
	 *
	 * @param	string	$message
	 *
	 * @return	string
	 */
	function smiley($text) {
		if (count($this->mSmileys) == 0) $this->getSmileys();
		if (count($this->mSmileys) != 0) {
			$text = str_replace($this->mSmileysConvTable[0], $this->mSmileysConvTable[1], $text);
		}
		return $text;
	}

	/**
	 * Make links in the text clickable
	 *
	 * @param	string	$text
	 * @return	string
	 **/
	function makeClickable($text) {
		if (empty($this->mClickablePatterns)) {
			// Delegate Call 'Legacy_TextFilter.MakeClickableConvertTable'
			//	Delegate may replace makeClickable conversion table
			//	Args : 
			//		'patterns'	   [I/O] : &Array of pattern RegExp
			//		'replacements' [I/O] : &Array of replacing string
			//
			$this->mMakeClickableConvertTable->call(new XCube_Ref($this->mClickablePatterns), new XCube_Ref($this->mClickableReplacements));

			// Delegate Call 'MyTextSanitizer.MakeClickablePre'
			//	Delegate may replace makeClickable conversion table
			//	Args : 
			//		'patterns'	   [I/O] : &Array of pattern RegExp
			//		'replacements' [I/O] : &Array of replacing string
			//
			//	Todo: For Compatiblitiy to XC2.1 Beta3
			//
			$this->mMakeClickablePre->call(new XCube_Ref($this->mClickablePatterns), new XCube_Ref($this->mClickableReplacements));
		}
		$text = preg_replace($this->mClickablePatterns, $this->mClickableReplacements, $text);
		return $text;
	}

	function makeClickableConvertTable(&$patterns, &$replacements) {
		// URI accept class ref. RFC 1738 (but not strict here)
		$hpath = "[-_.!~*\'()a-z0-9;\/?:\@&=+\$,%#]+";
		$patterns[] = "/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/($hpath)/i";
		$replacements[] = "\\1<a href=\"\\2://\\3\" rel=\"external\">\\2://\\3</a>";
		$patterns[] = "/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.($hpath)/i";
		$replacements[] = "\\1<a href=\"http://www.\\2.\\3\" rel=\"external\">www.\\2.\\3</a>";
		$patterns[] = "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.($hpath)/i";
		$replacements[] = "\\1<a href=\"ftp://ftp.\\2.\\3\" rel=\"external\">ftp.\\2.\\3</a>";
		$patterns[] = "/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([a-z0-9!#\$%&'\*\+\-\/=\?^_\`{\|}~\.]+)/i";
		$replacements[] = "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>";
	}

	/**
	 * Replace XoopsCodes with their equivalent HTML formatting
	 *
	 * @param	string	$text
	 * @param	bool	$allowimage Allow images in the text?
	 *								On FALSE, uses links to images.
	 * @return	string
	 **/
	function convertXCode($text, $allowimage = 1) {
		if (empty($this->mXCodePatterns)) {
			// Delegate Call 'Legacy_TextFilter.MakeXCodeConvertTable'
			//	Delegate may replace makeClickable conversion table
			//	Args : 
			//		'patterns'	   [I/O] : &Array of pattern RegExp
			//		'replacements' [I/O] : &Array[0..1] of Array of replacing string
			//							   replacements[0] for $allowimage = 0;
			//							   replacements[1] for $allowimage = 1;
			//
			$this->mMakeXCodeConvertTable->call(new XCube_Ref($this->mXCodePatterns), new XCube_Ref($this->mXCodeReplacements));

			// RaiseEvent 'MyTextSanitizer.XoopsCodePre'
			//	Delegate may replace conversion table
			//	Args : 
			//		'patterns'	   [I/O] : &Array of pattern RegExp
			//		'replacements' [I/O] : &Array of replacing string
			//		'allowimage'   [I]	 : xoopsCodeDecode $allowimage parameter
			//
			//Todo: For Compatiblitiy to XC2.1 Beta3
			$this->mXCodePre->call(new XCube_Ref($this->mXCodePatterns), new XCube_Ref($this->mXCodeReplacements[0]), 0);
			$dummy = array();
			$this->mXCodePre->call(new XCube_Ref($dummy), new XCube_Ref($this->mXCodeReplacements[1]), 1);
		}
		if (empty($this->mXCodeCheckImgPatterns)) {
			// RaiseEvent 'Legacy_TextFilter.MakeXCodeCheckImgPatterns'
			//	Delegate may replace conversion table
			//	Args : 
			//		'patterns'	   [I/O] : &Array of pattern RegExp
			//		'replacements' [I/O] : &Array of replacing string
			//
			$this->mMakeXCodeCheckImgPatterns->call(new XCube_Ref($this->mXCodeCheckImgPatterns));
		}
		$text = preg_replace_callback($this->mXCodeCheckImgPatterns, array($this, '_filterImgUrl'), $text);
		$replacementsIdx = ($allowimage == 0) ? 0 : 1;
		$text = preg_replace($this->mXCodePatterns, $this->mXCodeReplacements[$replacementsIdx], $text);
		return $text;
	}
	
	function makeXCodeCheckImgPatterns(&$patterns) {
		$patterns[] = "/\[img( align=\w+)]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
	}

	function makeXCodeConvertTable(&$patterns, &$replacements) {
		$patterns[] = "/\[siteurl\=(['\"]?)([^\"'<>]*)\\1\](.*)\[\/siteurl\]/sU";
		$replacements[0][] = $replacements[1][] = '<a href="'.XOOPS_URL.'/\\2" rel="external">\\3</a>';
		$patterns[] = "/\[url\=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1\](.*)\[\/url\]/sU";
		$replacements[0][] = $replacements[1][] = '<a href="\\2" rel="external">\\3</a>';
		$patterns[] = "/\[url\=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1\](.*)\[\/url\]/sU";
		$replacements[0][] = $replacements[1][] = '<a href="\\2" rel="external">\\3</a>';
		$patterns[] = "/\[url\=(['\"]?)([^\"'<>]*)\\1\](.*)\[\/url\]/sU";
		$replacements[0][] = $replacements[1][] = '<a href="http://\\2" rel="external">\\3</a>';
		$patterns[] = "/\[color\=(['\"]?)([a-zA-Z0-9]*)\\1\](.*)\[\/color\]/sU";
		$replacements[0][] = $replacements[1][] = '<span style="color: #\\2;">\\3</span>';
		$patterns[] = "/\[size\=(['\"]?)([a-z-]*)\\1\](.*)\[\/size\]/sU";
		$replacements[0][] = $replacements[1][] = '<span style="font-size: \\2;">\\3</span>';
		$patterns[] = "/\[font\=(['\"]?)([^;<>\*\(\)\"']*)\\1\](.*)\[\/font\]/sU";
		$replacements[0][] = $replacements[1][] = '<span style="font-family: \\2;">\\3</span>';
		$patterns[] = "/\[email\]([^;<>\*\(\)\"']*)\[\/email\]/sU";
		$replacements[0][] = $replacements[1][] = '<a href="mailto:\\1">\\1</a>';
		$patterns[] = "/\[b\](.*)\[\/b\]/sU";
		$replacements[0][] = $replacements[1][] = '<b>\\1</b>';
		$patterns[] = "/\[i\](.*)\[\/i\]/sU";
		$replacements[0][] = $replacements[1][] = '<i>\\1</i>';
		$patterns[] = "/\[u\](.*)\[\/u\]/sU";
		$replacements[0][] = $replacements[1][] = '<u>\\1</u>';
		$patterns[] = "/\[d\](.*)\[\/d\]/sU";
		$replacements[0][] = $replacements[1][] = '<del>\\1</del>';
		$patterns[] = "/\[img align\=(['\"]?)(left|center|right)\\1\]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$replacements[0][] = '<a href="\\3" rel="external">\\3</a>';
		$replacements[1][] = '<img src="\\3" align="\\2" alt="" />';
		$patterns[] = "/\[img\]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$replacements[0][] = '<a href="\\1" rel="external">\\1</a>';
		$replacements[1][] = '<img src="\\1" alt="" />';
		$patterns[] = "/\[img align\=(['\"]?)(left|center|right)\\1 id\=(['\"]?)([0-9]*)\\3\]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$replacements[0][] = '<a href="'.XOOPS_URL.'/image.php?id=\\4" rel="external">\\5</a>';
		$replacements[1][] = '<img src="'.XOOPS_URL.'/image.php?id=\\4" align="\\2" alt="\\5" />';
		$patterns[] = "/\[img id\=(['\"]?)([0-9]*)\\1\]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
		$replacements[0][] = '<a href="'.XOOPS_URL.'/image.php?id=\\2" rel="external">\\3</a>';
		$replacements[1][] = '<img src="'.XOOPS_URL.'/image.php?id=\\2" alt="\\3" />';
		$patterns[] = "/\[quote\]/sU";
		$replacements[0][] = $replacements[1][] = _QUOTEC.'<div class="xoopsQuote"><blockquote>';
		$patterns[] = "/\[\/quote\]/sU";
		$replacements[0][] = $replacements[1][] = '</blockquote></div>';
		$patterns[] = "/javascript:/si";
		$replacements[0][] = $replacements[1][] = "java script:";
		$patterns[] = "/about:/si";
		$replacements[0][] = $replacements[1][] = "about :";
	}

	/**
	 * Filters out invalid strings included in URL, if any
	 *
	 * @param	array  $matches
	 * @return	string
	 */
	function _filterImgUrl($matches)
	{
		if ($this->_checkUrlString($matches[2])) {
			return $matches[0];
		} else {
			return "";
		}
	}

	/**
	 * Checks if invalid strings are included in URL
	 *
	 * @param	string	$text
	 * @return	bool
	 */
	function _checkUrlString($text)
	{
		// Check control code
		if (preg_match('/[\x0-\x1f\x7f]/', $text)) {
			return false;
		}
		// check black pattern(deprecated)
		return !preg_match("/^(javascript|vbscript|about):/i", $text);
	}

	/**
	 * Convert linebreaks to <br /> tags
	 *
	 * @param	string	$text
	 *
	 * @return	string
	 */
	function nl2Br($text)
	{
		return preg_replace("/(\015\012)|(\015)|(\012)/","<br />",$text);
	}

	/**
	 * Pre XCode Converting
	 *	 By default, keep content within [code][/code] tags
	 *
	 * @param	string	$text
	 * @param	string	$xcode
	 *
	 * @return	string
	 */
	function preConvertXCode($text, $xcode = 1) {
		if($xcode != 0){
			if (empty($this->mPreXCodePatterns)) {
				// RaiseEvent 'Legacy_TextFilter.MakePreXCodeConvertTable'
				//	Delegate may replace conversion table
				//	Args : 
				//		'patterns'	   [I/O] : &Array of pattern RegExp
				//		'replacements' [I/O] : &Array of replacing string
				//
				$this->mMakePreXCodeConvertTable->call(new XCube_Ref($this->mPreXCodePatterns), new XCube_Ref($this->mPreXCodeReplacements));
			}
			$text =  preg_replace($this->mPreXCodePatterns, $this->mPreXCodeReplacements, $text);
		}
		return $text;
	}
	
	function makePreXCodeConvertTable(&$patterns, &$replacements) {
		$patterns[] = "/\[code\](.*)\[\/code\]/esU";
		$replacements[] = "'[code]'.base64_encode('$1').'[/code]'";
	}

	/**
	 * Post XCode Convering
	 *	 By default, convert content about [code][/code] tags
	 *
	 * @param	string	$text
	 * @param	string	$xcode
	 * @param	string	$image
	 *
	 * @return	string
	 */
	function postConvertXCode($text, $xcode=1, $image=1){
		if($xcode != 0){
			if (empty($this->mPostXCodePatterns)) {
				// RaiseEvent 'Legacy_TextFilter.MakePostXCodeConvertTable'
				//	Delegate may replace conversion table
				//	Args : 
				//		'patterns'	   [I/O] : &Array of pattern RegExp
				//		'replacements' [I/O] : &Array[0..1] of Array of replacing string
				//							   replacements[0] for $allowimage = 0;
				//							   replacements[1] for $allowimage = 1;
				//	Caution :
				//	   - Conversion table order should be reverse order with codePreConv conversion table.
				//		 So, conversion rule for[code] is defined after call delegate function.
				//	   - Conversion rule should treat input string as raw text with single quote escape.(not sanitized).
				//
				$this->mMakePostXCodeConvertTable->call(new XCube_Ref($this->mPostXCodePatterns), new XCube_Ref($this->mPostXCodeReplacements));
			}
			$replacementsIdx = ($image == 0) ? 0 : 1;
			$text =  preg_replace($this->mPostXCodePatterns, $this->mPostXCodeReplacements[$replacementsIdx], $text);
		}
		return $text;
	}

	function makePostXCodeConvertTable(&$patterns, &$replacements) {
		$patterns[] = "/\[code\](.*)\[\/code\]/esU";
		$replacements[0][] = "'<div class=\"xoopsCode\"><pre><code>'.Legacy_TextFilter::codeSanitizer('$1', 0).'</code></pre></div>'";
		$replacements[1][] = "'<div class=\"xoopsCode\"><pre><code>'.Legacy_TextFilter::codeSanitizer('$1', 1).'</code></pre></div>'"; 
	}

	function codeSanitizer($text, $image = 1){
		return $this->convertXCode(htmlspecialchars(str_replace('\"', '"', base64_decode($text)),ENT_QUOTES), $image);
	}
}
?>
