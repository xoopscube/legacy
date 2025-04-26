<?php
/**
 * Class to "clean up" text for various uses
 * Singleton
 * @package    kernel
 * @subpackage core
 * @version    XCL 2.5.0
 * @author     Nuno Luciano (aka gigamaster), 2020 XCL/PHP7
 * @author     Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @author     Goghs Cheng
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This version uses XOOPSCube XCube Delegate
 */


class MyTextSanitizer
{
    public $censorConf;

    /**
     * @var XCube_TextFilter
     */
    public $mTextFilter = null;

    /**
     * @var XCube_Delegate
     * @deprecated
     */
    public $mMakeClickablePostFilter = null;

    /**
     * @var XCube_Delegate
     * @deprecated
     */
    public $mXoopsCodePostFilter = null;

    /**
    * Constructor of this class
    *
    * Gets allowed html tags from admin config settings
    * <br> should not be allowed since nl2br will be used
    * when storing data.
    *
    * @access   private
    *
    * @todo So far, this does nuttin' ;-)
    */
    public function __construct()
    {
        $this->mMakeClickablePostFilter =new XCube_Delegate();
        $this->mMakeClickablePostFilter->register('MyTextSanitizer.MakeClickablePostFilter');

        $this->mXoopsCodePostFilter =new XCube_Delegate();
        $this->mXoopsCodePostFilter->register('MyTextSanitizer.XoopsCodePostFilter');

        $root =& XCube_Root::getSingleton();
        $this->mTextFilter =& $root->getTextFilter();
    }

    /**
     * Access the only instance of this class
     *
     * @return  object
     *
     * @static
     * @staticvar   object
     */
    public static function &sGetInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new MyTextSanitizer();
        }
        return $instance;
    }

    /**
     * Get the smileys
     *
     * @return  array
     */
    public function getSmileys()
    {
        return $this->mTextFilter->getSmileys();
    }

    /**
     * Replace emoticons in the message with smiley images
     *
     * @param $text
     * @return  string
     */
    public function &smiley($text)
    {
        $text = $this->mTextFilter->smiley($text);
        return $text;
    }

    /**
     * Make links in the text clickable
     *
     * @param   string  $text
     * @return  string
     **/
    public function &makeClickable($text)
    {
        $text = $this->mTextFilter->makeClickable($text);

        //  RaiseEvent : 'MyTextSanitizer.MakeClickablePostFilter'
        //  Delegate may convert output text with quickApplyFilter rule
        //  Args :
        //      'string'       [I/O] : Text to convert;
        //
        $this->mMakeClickablePostFilter->call(new XCube_Ref($text));
        return $text;
    }

    /**
     * Replace XoopsCodes with their equivalent HTML formatting
     *
     * @param string $text
     * @param int    $allowimage    Allow images in the text?
     *                              On FALSE, uses links to images.
     * @return  string
     */
    public function &xoopsCodeDecode($text, $allowimage = 1)
    {
        $text = $this->mTextFilter->convertXCode($text, $allowimage);

        // RaiseEvent : 'MyTextSanitizer.XoopsCodePostFilter'
        //  Delegate may convert output text with quickApplyFilter rule
        //  Args :
        //      'string'       [I/O] : Text to convert;
        //      'allowimage'   [I]   : xoopsCodeDecode $allowimage parameter
        //
        $this->mXoopsCodePostFilter->call(new XCube_Ref($text), $allowimage);
        return $text;
    }

    /**
     * Filters out invalid strings included in URL, if any
     *
     * @param   array  $matches
     * @return  string
     */
    public function _filterImgUrl($matches)
    {
        if ($this->checkUrlString($matches[2])) {
            return $matches[0];
        }

        return '';
    }

    /**
     * Checks if invalid strings are included in URL
     *
     * @param   string  $text
     * @return  bool
     */
    public function checkUrlString($text)
    {
        // Check control code
        if (preg_match("/[\\0-\\31]/", $text)) {
            return false;
        }
        // check black pattern(deprecated)
        return !preg_match('/^(javascript|vbscript|about):/i', $text);
    }

    /**
     * Convert linebreaks to <br> tags
     *
     * @param   string  $text
     *
     * @return  string
     */
    public function &nl2Br($text)
    {
        $ret = $this->mTextFilter->nl2Br($text);
        return $ret;
    }

    /**
     * Add slashes to the text.
     *
     * @param   string  $text
     * @return  string
     **/
    public function addSlashes($text)
    {
        // Ensure proper UTF-8 encoding before adding slashes
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        
        // Use mb_addslashes equivalent for UTF-8 safety
        if (function_exists('mb_ereg_replace')) {
            return mb_ereg_replace('([\'\"\\\\])', '\\\\\\1', $text);
        } else {
            return addslashes($text);
        }
    }
    
    /*
    * if magic_quotes_gpc is on, strip back slashes
    *
    * @param    string  $text
    *
    * @return   string
    */
    public function &stripSlashesGPC($text)
    {
        // Ensure $text is a string
        $text = (string)$text;
    
        // Ensure proper UTF-8 encoding
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        
        // Use mb_stripslashes equivalent for UTF-8 safety
        if (function_exists('mb_ereg_replace')) {
            $text = mb_ereg_replace('\\\\([\'\"\\\\])', '\\1', (string)$text);
        }
    
        return $text;
    }

    /*
    *  for displaying data in html textbox forms
    *
    * @param    string  $text
    * @param    bool    $forEdit (experimental)
    *
    * @return   string
    */
    public function &htmlSpecialChars($text, $forEdit=false)
    {
        if (!$forEdit) {
            $ret = $this->mTextFilter->toShow($text, true);
        } else {
            $ret = $this->mTextFilter->toEdit($text);
        }
        return $ret;
    }

    /**
     * Reverses {@link htmlSpecialChars()}
     *
     * @param   string  $text
     * @return  string
     * @deprecated
     **/
    public function &undoHtmlSpecialChars($text)
    {
        $ret = preg_replace(['/&gt;/i', '/&lt;/i', '/&quot;/i', '/&#039;/i'], ['>', '<', '"', "'"], $text);
        return $ret;
    }

    /**
     * Filters textarea data for display
     * (This method makes overhead but needed for compatibility)
     *
     * @param string $text
     * @param int    $html   allow html?
     * @param int    $smiley allow smileys?
     * @param int    $xcode  allow xoopscode?
     * @param int    $image  allow inline images?
     * @param int    $br     convert linebreaks?
     * @return  string
     */

    public function _ToShowTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text = $this->codePreConv($text, $xcode);
        if (1 !== $html) {
            $text = $this->htmlSpecialChars($text);
        }
        $text = $this->makeClickable($text);
        if (0 !== $smiley) {
            $text = $this->smiley($text);
        }
        if (0 !== $xcode) {
            $text = $this->xoopsCodeDecode($text, $image);
        }
        if (0 !== $br) {
            $text = $this->nl2Br($text);
        }
        $text = $this->codeConv($text, $xcode, $image);
        return $text;
    }

    /**
     * Filters textarea form data in DB for display
     *
     * @param string $text
     * @param int    $html   allow html?
     * @param int    $smiley allow smileys?
     * @param int    $xcode  allow xoopscode?
     * @param int    $image  allow inline images?
     * @param int    $br     convert linebreaks?
     * @return  string
     */
    public function &displayTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text = $this->mTextFilter->toShowTarea($text, $html, $smiley, $xcode, $image, $br, true);
        return $text;
    }

    /**
     * Filters textarea form data submitted for preview
     *
     * @param string $text
     * @param int    $html   allow html?
     * @param int    $smiley allow smileys?
     * @param int    $xcode  allow xoopscode?
     * @param int    $image  allow inline images?
     * @param int    $br     convert linebreaks?
     * @return  string
     */
    public function &previewTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text =& $this->stripSlashesGPC($text);
        $text = $this->mTextFilter->toPreviewTarea($text, $html, $smiley, $xcode, $image, $br, true);
        return $text;
    }

    /**
     * Replaces banned words in a string with their replacements
     *
     * @param   string $text
     * @return  string
     *
     * @deprecated
     **/
    public function &censorString($text)
    {
        if (!isset($this->censorConf)) {
            $config_handler =& xoops_gethandler('config');
            $this->censorConf =& $config_handler->getConfigsByCat(XOOPS_CONF_CENSOR);
        }
        if (1 == $this->censorConf['censor_enable']) {
            $replacement = $this->censorConf['censor_replace'];
            foreach ($this->censorConf['censor_words'] as $bad) {
                if (!empty($bad)) {
                    $bad = quotemeta($bad);
                    $patterns[] = "/(\s)".$bad . '/siU';
                    $replacements[] = "\\1".$replacement;
                    $patterns[] = '/^' . $bad . '/siU';
                    $replacements[] = $replacement;
                    $patterns[] = "/(\n)".$bad . '/siU';
                    $replacements[] = "\\1".$replacement;
                    $patterns[] = '/]' . $bad . '/siU';
                    $replacements[] = ']' . $replacement;
                    $text = preg_replace($patterns, $replacements, $text);
                }
            }
        }
        return $text;
    }

    /**#@+
     * Sanitizing of [code] tag
     * @param     $text
     * @param int $xcode
     * @return
     */
    public function codePreConv($text, $xcode = 1)
    {
        if (0 !== $xcode) {
            $text = $this->mTextFilter->preConvertXCode($text, $xcode);
        }
        return $text;
    }

    public function codeConv($text, $xcode = 1, $image = 1)
    {
        if (0 !== $xcode) {
            $text = $this->mTextFilter->postConvertXCode($text, $xcode);
        }
        return $text;
    }

##################### Deprecated Methods ######################

    /**#@+
     * @param     $text
     * @param int $allowhtml
     * @param int $smiley
     * @param int $bbcode
     * @return string
     * @deprecated
     */
    public function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $text = $this->_ToShowTarea($text, $allowhtml, $smiley, $bbcode, 1, 1);
        return $text;
    }

    public function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $text = $this->oopsStripSlashesGPC($text);
        $text = $this->_ToShowTarea($text, $allowhtml, $smiley, $bbcode, 1, 1);
        return $text;
    }

    public function makeTboxData4Save($text)
    {
        return $this->addSlashes($text);
    }

    public function makeTboxData4Show($text, $smiley=0)
    {
	    return $this->mTextFilter->toShow($text, true);
    }

    public function makeTboxData4Edit($text)
    {
        return $this->mTextFilter->toEdit($text);
    }

    public function makeTboxData4Preview($text, $smiley=0)
    {
        $text = $this->stripSlashesGPC($text);
        $text = $this->mTextFilter->toShow($text, true);
        return $text;
    }

    public function makeTboxData4PreviewInForm($text)
    {
        $text = $this->stripSlashesGPC($text);
        return $this->mTextFilter->toEdit($text);
    }

    public function makeTareaData4Save($text)
    {
        return $this->addSlashes($text);
    }

    public function &makeTareaData4Show($text, $html=1, $smiley=1, $xcode=1)
    {
        $ret = $this->displayTarea($text, $html, $smiley, $xcode);
        return $ret;
    }

    public function makeTareaData4Edit($text)
    {
        return $this->mTextFilter->toEdit($text);
    }

    public function &makeTareaData4Preview($text, $html=1, $smiley=1, $xcode=1)
    {
        $ret = $this->previewTarea($text, $html, $smiley, $xcode);
        return $ret;
    }

    public function makeTareaData4PreviewInForm($text)
    {
        //if magic_quotes_gpc is on, do stipslashes
        $text = $this->stripSlashesGPC($text);
        return $this->mTextFilter->toEdit($text);
    }

    public function makeTareaData4InsideQuotes($text)
    {
        return $this->mTextFilter->toShow($text, true);
    }

    public function &oopsStripSlashesGPC($text)
    {
        $ret = $this->stripSlashesGPC($text);
        return $ret;
    }

    public function &oopsStripSlashesRT($text)
    {
        if (get_magic_quotes_runtime()) {
            $text =& stripslashes($text);
        }
        return $text;
    }

    public function &oopsAddSlashes($text)
    {
        $ret = $this->addSlashes($text);
        return $ret;
    }

    public function &oopsHtmlSpecialChars($text)
    {
        $ret = $this->mTextFilter->toShow($text, true);
        return $ret;
    }

    public function &oopsNl2Br($text)
    {
        $ret = $this->nl2br($text);
        return $ret;
    }

    public static function &getInstance()
    {
        $ret = self::sGetInstance();
        return $ret;
    }
    /**#@-*/
}
