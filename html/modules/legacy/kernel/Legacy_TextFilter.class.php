<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_TextFilter.class.php,v 1.9 2008/09/25 15:11:57 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @public
 * @brief The text filter for Legacy.
 */
class Legacy_TextFilter extends XCube_TextFilter
{
    /**
     * @var XCube_Delegate
     */
    public $mMakeXCodeConvertTable = null;
    /**
     * @var XCube_Delegate
     */
    public $mMakeXCodeCheckImgPatterns = null;
    /**
     * @var XCube_Delegate
     */
    public $mMakeClickableConvertTable = null;
    /**
     * @var XCube_Delegate
     */
    public $mMakePreXCodeConvertTable = null;
    /**
     * @var XCube_Delegate
     */
    public $mMakePostXCodeConvertTable = null;
    /**
     * @var XCube_Delegate
     * @deprecated	keep compatible with XC2.1 Beta3
     */
    public $mXCodePre = null;
    /**
     * @var XCube_Delegate
     * @deprecated	keep compatible with XC2.1 Beta3. Legacy 2.2.0 will not support this.
     * @todo
     *	  This is a deprecated member.
     */
    public $mMakeClickablePre = null;


    public $mClickablePatterns = array();
    public $mClickableReplacements = array();

    public $mXCodePatterns = array();
    public $mXCodeReplacements = array();
    public $mXCodeCheckImgPatterns = array();
    public $mXCodeCallbacks = array();
    public $mXCodeHasCallback = array();

    public $mPreXCodePatterns = array();
    public $mPreXCodeReplacements = array();
    public $mPreXCodeCallbacks = array();
    public $mPreXCodeHasCallback = false;

    public $mPostXCodePatterns = array();
    public $mPostXCodeReplacements = array();
    public $mPostXCodeCallbacks = array();
    public $mPostXCodeHasCallback = array();
    
    public $mSmileys = array();
    public $mSmileysConvTable = array();

    /**
     * @public
     * @brief Constructor
     * @todo
     *	  This method keeps a deprecated delegate.
     */
          // !Fix PHP7
          public function __construct()
    //public function Legacy_TextFilter()
    {
        $obj = $this->mMakeClickableConvertTable = new XCube_Delegate;
        $obj->register('Legacy_TextFilter.MakeClickableConvertTable');
        $obj->add('Legacy_TextFilter::sMakeClickableConvertTable', XCUBE_DELEGATE_PRIORITY_2);

        $obj = $this->mMakeXCodeConvertTable = new XCube_Delegate;
        $obj->register('Legacy_TextFilter.MakeXCodeConvertTable');
        $obj->add('Legacy_TextFilter::sMakeXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

        $obj = $this->mMakeXCodeCheckImgPatterns = new XCube_Delegate;
        $obj->register('Legacy_TextFilter.MakeXCodeCheckImgPatterns');
        $obj->add('Legacy_TextFilter::sMakeXCodeCheckImgPatterns', XCUBE_DELEGATE_PRIORITY_2);

        $obj = $this->mMakePreXCodeConvertTable = new XCube_Delegate;
        $obj->register('Legacy_TextFilter.MakePreXCodeConvertTable');
        $obj->add('Legacy_TextFilter::sMakePreXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

        $obj = $this->mMakePostXCodeConvertTable = new XCube_Delegate;
        $obj->register('Legacy_TextFilter.MakePostXCodeConvertTable');
        $obj->add('Legacy_TextFilter::sMakePostXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_2);

        //@deprecated
        //Todo: For keeping compatible with XC2.1 Beta3
        $this->mMakeClickablePre = new XCube_Delegate();
        $this->mMakeClickablePre->register('MyTextSanitizer.MakeClickablePre');

        $this->mXCodePre = new XCube_Delegate();
        $this->mXCodePre->register('MyTextSanitizer.XoopsCodePre');
    }
    
    public function getInstance(&$instance)
    {
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
    public function toShow($text, $x2comat=false)
    {
        if ($x2comat) {
            //ToDo: &nbsp; patern is defined for XOOPS2.0 compatiblity. But what is it?
            //		This comatiblity option is used from method from MyTextSanitizer.
            //
            return preg_replace(array("/&amp;(#[0-9]+|#x[0-9a-f]+|[a-z]+[0-9]*);/i", "/&nbsp;/i"), array('&\\1;', '&amp;nbsp;'), htmlspecialchars($text, ENT_QUOTES, _CHARSET));
        } else {
            return preg_replace("/&amp;(#[0-9]+|#x[0-9a-f]+|[a-z]+[0-9]*);/i", '&\\1;', htmlspecialchars($text, ENT_QUOTES, _CHARSET));
        }
    }

    /**
     * Filters for editing text
     *
     * @param	string	$text
     * @return	string
     *
     **/
    public function toEdit($text)
    {
        return preg_replace("/&amp;(#0?[0-9]{4,6};)/i", '&$1', htmlspecialchars($text, ENT_QUOTES, _CHARSET));
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
    public function toShowTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat=false)
    {
        $text = $this->preConvertXCode($text, $xcode);
        if ($html != 1) {
            $text = $this->toShow($text, $x2comat);
        }
        $text = $this->makeClickable($text);
        if ($smiley != 0) {
            $text = $this->smiley($text);
        }
        if ($xcode != 0) {
            $text = $this->convertXCode($text, $image);
        }
        if ($br != 0) {
            $text = $this->nl2Br($text);
        }
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
    public function toPreviewTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat=false)
    {
        return $this->toShowTarea($text, $html, $smiley, $xcode, $image, $br, $x2comat);
    }

    /**
     * purifyHtml
     * 
     * @param	string	$html
     * @param	string	$encoding
     * @param	string	$doctype
     * @param	object	$config
     * 
     * @return	string
     **/
    public function purifyHtml(/*** string ***/ $html, /*** string ***/ $encoding=null, /*** string ***/ $doctype=null, /*** object ***/ $config=null)
    {
        require_once XOOPS_LIBRARY_PATH.'/htmlpurifier/library/HTMLPurifier.auto.php';
        $encoding = $encoding ? $encoding : _CHARSET;
        $doctypeArr = array("HTML 4.01 Strict","HTML 4.01 Transitional","XHTML 1.0 Strict","XHTML 1.0 Transitional","XHTML 1.1");
    
        if (is_null($config) || !is_object($config) || !($config instanceof HTMLPurifier_Config)) {
            $config = HTMLPurifier_Config::createDefault();
        }
        if (in_array($doctype, $doctypeArr)) {
            $config->set('HTML.Doctype', $doctype);
        }
    
        if ($_conv = ($encoding !== 'UTF-8' && function_exists('mb_convert_encoding'))) {
            $_substitute = mb_substitute_character();
            mb_substitute_character('none');
            $html = mb_convert_encoding($html, 'UTF-8', $encoding);
            $config->set('Core.Encoding', 'UTF-8');
        } else {
            $config->set('Core.Encoding', $encoding);
        }
    
        $purifier = new HTMLPurifier($config);
        $html = $purifier->purify($html);
    
        if ($_conv) {
            $html = mb_convert_encoding($html, $encoding, 'UTF-8');
            mb_substitute_character($_substitute);
        }
    
        return $html;
    }


    /**
     * Get the smileys
     *
     * @return	array
     */
    public function getSmileys()
    {
        if (count($this->mSmileys) == 0) {
            $this->mSmileysConvTable[0] = $this->mSmileysConvTable[1] = array();
            $db =& Database::getInstance();
            if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles"))) {
                while ($smile = $db->fetchArray($getsmiles)) {
                    $this->mSmileys[] = $smile;
                    $this->mSmileysConvTable[0][] = $smile['code'];
                    $this->mSmileysConvTable[1][] = '<img src="'.XOOPS_UPLOAD_URL.'/'.htmlspecialchars($smile['smile_url'], ENT_COMPAT, _CHARSET).'" alt="" />';
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
    public function smiley($text)
    {
        if (count($this->mSmileys) == 0) {
            $this->getSmileys();
        }
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
    public function makeClickable($text)
    {
        if (empty($this->mClickablePatterns)) {
            // Delegate Call 'Legacy_TextFilter.MakeClickableConvertTable'
            //	Delegate may replace makeClickable conversion table
            //	Args : 
            //		'patterns'	   [I/O] : &Array of pattern RegExp
            //		'replacements' [I/O] : &Array of replacing string or callable
            //
            $this->mMakeClickableConvertTable->call(new XCube_Ref($this->mClickablePatterns), new XCube_Ref($this->mClickableReplacements));

            // Delegate Call 'MyTextSanitizer.MakeClickablePre'
            //	Delegate may replace makeClickable conversion table
            //	Args : 
            //		'patterns'	   [I/O] : &Array of pattern RegExp
            //		'replacements' [I/O] : &Array of replacing string or callable
            //
            //	Todo: For Compatiblitiy to XC2.1 Beta3
            //
            $this->mMakeClickablePre->call(new XCube_Ref($this->mClickablePatterns), new XCube_Ref($this->mClickableReplacements));
        }
        $text = preg_replace($this->mClickablePatterns, $this->mClickableReplacements, $text);
        return $text;
    }

    public static function sMakeClickableConvertTable(&$patterns, &$replacements)
    {
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
     * @deprecated
     **/
    public function makeClickableConvertTable(&$patterns, &$replacements)
    {
        self::sMakeClickableConvertTable($patterns, $replacements);
    }

    /**
     * Replace XoopsCodes with their equivalent HTML formatting
     *
     * @param	string	$text
     * @param	bool	$allowimage Allow images in the text?
     *								On FALSE, uses links to images.
     * @return	string
     **/
    public function convertXCode($text, $allowimage = 1)
    {
        if (empty($this->mXCodePatterns)) {
            // Delegate Call 'Legacy_TextFilter.MakeXCodeConvertTable'
            //	Delegate may replace makeClickable conversion table
            //	Args : 
            //		'patterns'	   [I/O] : &Array of pattern RegExp
            //		'replacements' [I/O] : &Array[0..1] of Array of replacing string or callable
            //							   replacements[0] for $allowimage = 0;
            //							   replacements[1] for $allowimage = 1;
            //
            $this->mMakeXCodeConvertTable->call(new XCube_Ref($this->mXCodePatterns), new XCube_Ref($this->mXCodeReplacements));

            // RaiseEvent 'MyTextSanitizer.XoopsCodePre'
            //	Delegate may replace conversion table
            //	Args : 
            //		'patterns'	   [I/O] : &Array of pattern RegExp
            //		'replacements' [I/O] : &Array of replacing string or callable
            //		'allowimage'   [I]	 : xoopsCodeDecode $allowimage parameter
            //
            //Todo: For Compatiblitiy to XC2.1 Beta3
            $this->mXCodePre->call(new XCube_Ref($this->mXCodePatterns), new XCube_Ref($this->mXCodeReplacements[0]), 0);
            $dummy = array();
            $this->mXCodePre->call(new XCube_Ref($dummy), new XCube_Ref($this->mXCodeReplacements[1]), 1);
            for ($idx = 0; $idx < 2; ++$idx) {
                $this->mXCodeHasCallback[$idx] = false;
                foreach ($this->mXCodeReplacements[$idx] as $i => $replacements) {
                    if (is_callable($replacements)) {
                        !$this->mXCodeHasCallback[$idx] && $this->mXCodeHasCallback[$idx] = true;
                        $this->mXCodeCallbacks[$idx][$i] = $replacements;
                        $this->mXCodeReplacements[$idx][$i] = null;
                    } else {
                        $this->mXCodeCallbacks[$idx][$i] = null;
                    }
                }
            }
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
        if ($this->mXCodeHasCallback[$replacementsIdx] === true) {
            foreach ($this->mXCodePatterns as $i => $patterns) {
                if (is_null($this->mXCodeCallbacks[$replacementsIdx][$i])) {
                    $text =  preg_replace($patterns, $this->mXCodeReplacements[$replacementsIdx][$i], $text);
                } else {
                    $text =  preg_replace_callback($patterns, $this->mXCodeCallbacks[$replacementsIdx][$i], $text);
                }
            }
        } else {
            $text =  preg_replace($this->mXCodePatterns, $this->mXCodeReplacements[$replacementsIdx], $text);
        }
        return $text;
    }
    
    public static function sMakeXCodeCheckImgPatterns(&$patterns)
    {
        $patterns[] = "/\[img( align=\w+)]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
    }
    /**
     * @deprecated
     **/
    public function makeXCodeCheckImgPatterns(&$patterns)
    {
        self::sMakeXCodeCheckImgPatterns($patterns);
    }

    public static function sMakeXCodeConvertTable(&$patterns, &$replacements)
    {
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
     * @deprecated
     **/
    public function makeXCodeConvertTable(&$patterns, &$replacements)
    {
        self::sMakeXCodeConvertTable($patterns, $replacements);
    }

    /**
     * Filters out invalid strings included in URL, if any
     *
     * @param	array  $matches
     * @return	string
     */
    public function _filterImgUrl($matches)
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
    public function _checkUrlString($text)
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
    public function nl2Br($text)
    {
        return preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $text);
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
    public function preConvertXCode($text, $xcode = 1)
    {
        if ($xcode != 0) {
            if (empty($this->mPreXCodePatterns)) {
                // RaiseEvent 'Legacy_TextFilter.MakePreXCodeConvertTable'
                //	Delegate may replace conversion table
                //	Args : 
                //		'patterns'	   [I/O] : &Array of pattern RegExp
                //		'replacements' [I/O] : &Array of replacing string or callable
                //
                $this->mMakePreXCodeConvertTable->call(new XCube_Ref($this->mPreXCodePatterns), new XCube_Ref($this->mPreXCodeReplacements));
                foreach ($this->mPreXCodeReplacements as $i => $replacements) {
                    if (is_callable($replacements)) {
                        !$this->mPreXCodeHasCallback && $this->mPreXCodeHasCallback = true;
                        $this->mPreXCodeCallbacks[$i] = $replacements;
                        $this->mPreXCodeReplacements[$i] = null;
                    } else {
                        $this->mPreXCodeCallbacks[$i] = null;
                    }
                }
            }
            if ($this->mPreXCodeHasCallback === true) {
                foreach ($this->mPreXCodePatterns as $i => $patterns) {
                    if (is_null($this->mPreXCodeCallbacks[$i])) {
                        $text =  preg_replace($patterns, $this->mPreXCodeReplacements[$i], $text);
                    } else {
                        $text =  preg_replace_callback($patterns, $this->mPreXCodeCallbacks[$i], $text);
                    }
                }
            } else {
                $text =  preg_replace($this->mPreXCodePatterns, $this->mPreXCodeReplacements, $text);
            }
        }
        return $text;
    }
    
    public static function sMakePreXCodeConvertTable(&$patterns, &$replacements)
    {
        $patterns[] = "/\[code\](.*)\[\/code\]/sU";
        $replacements[] = 'self::_sCodeToBase64Encode';
    }
    
    protected static function _sCodeToBase64Encode($match)
    {
        return '[code]' . base64_encode($match[1]) . '[/code]';
    }
    
    /**
     * @deprecated
     **/
    public function makePreXCodeConvertTable(&$patterns, &$replacements)
    {
        self::sMakePreXCodeConvertTable($patterns, $replacements);
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
    public function postConvertXCode($text, $xcode=1, $image=1)
    {
        if ($xcode != 0) {
            if (empty($this->mPostXCodePatterns)) {
                // RaiseEvent 'Legacy_TextFilter.MakePostXCodeConvertTable'
                //	Delegate may replace conversion table
                //	Args : 
                //		'patterns'	   [I/O] : &Array of pattern RegExp
                //		'replacements' [I/O] : &Array[0..1] of Array of replacing string or callable
                //							   replacements[0] for $allowimage = 0;
                //							   replacements[1] for $allowimage = 1;
                //	Caution :
                //	   - Conversion table order should be reverse order with codePreConv conversion table.
                //		 So, conversion rule for[code] is defined after call delegate function.
                //	   - Conversion rule should treat input string as raw text with single quote escape.(not sanitized).
                //
                $this->mMakePostXCodeConvertTable->call(new XCube_Ref($this->mPostXCodePatterns), new XCube_Ref($this->mPostXCodeReplacements));
                for ($idx = 0; $idx < 2; ++$idx) {
                    $this->mPostXCodeHasCallback[$idx] = false;
                    foreach ($this->mPostXCodeReplacements[$idx] as $i => $replacements) {
                        if (is_callable($replacements)) {
                            !$this->mPostXCodeHasCallback[$idx] && $this->mPostXCodeHasCallback[$idx] = true;
                            $this->mPostXCodeCallbacks[$idx][$i] = $replacements;
                            $this->mPostXCodeReplacements[$idx][$i] = null;
                        } else {
                            $this->mPostXCodeCallbacks[$idx][$i] = null;
                        }
                    }
                }
            }
            $replacementsIdx = ($image == 0) ? 0 : 1;
            if ($this->mPostXCodeHasCallback[$replacementsIdx] === true) {
                foreach ($this->mPostXCodePatterns as $i => $patterns) {
                    if (is_null($this->mPostXCodeCallbacks[$replacementsIdx][$i])) {
                        $text =  preg_replace($patterns, $this->mPostXCodeReplacements[$replacementsIdx][$i], $text);
                    } else {
                        $text =  preg_replace_callback($patterns, $this->mPostXCodeCallbacks[$replacementsIdx][$i], $text);
                    }
                }
            } else {
                $text =  preg_replace($this->mPostXCodePatterns, $this->mPostXCodeReplacements[$replacementsIdx], $text);
            }
        }
        return $text;
    }

    public static function sMakePostXCodeConvertTable(&$patterns, &$replacements)
    {
        $patterns[] = "/\[code\](.*)\[\/code\]/sU";
        if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
            $replacements[0][] = 'Legacy_TextFilter::codeSanitizerCallback0';
            $replacements[1][] = 'Legacy_TextFilter::codeSanitizerCallback1';
        } else {
            $root =& XCube_Root::getSingleton();
            $me =& $root->getTextFilter();
            $replacements[0][] = array(&$me, 'Legacy_TextFilter::codeSanitizerCallback0');
            $replacements[1][] = array(&$me, 'Legacy_TextFilter::codeSanitizerCallback1');
        }
    }
    /**
     * @deprecated
     **/
    public function makePostXCodeConvertTable(&$patterns, &$replacements)
    {
        self::sMakePostXCodeConvertTable($patterns, $replacements);
    }

    private function codeSanitizerCallback($m, $image)
    {
        $text = $this->convertXCode(htmlspecialchars(base64_decode($m[1]), ENT_QUOTES, _CHARSET), $image);
        return '<div class="xoopsCode"><pre><code>'.$text.'</code></pre></div>';
    }

    private function codeSanitizerCallback0($m)
    {
        return $this->codeSanitizerCallback($m, 0);
    }

    private function codeSanitizerCallback1($m)
    {
        return $this->codeSanitizerCallback($m, 1);
    }
    
    public function codeSanitizer($text, $image = 1)
    {
        return $this->convertXCode(htmlspecialchars(str_replace('\"', '"', base64_decode($text)), ENT_QUOTES, _CHARSET), $image);
    }
}
