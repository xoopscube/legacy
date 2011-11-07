<?php
/**
 *
 * @package CubeUtils
 * @version $Id: xoops_version.php 1294 2008-01-31 05:32:20Z nobunobu $
 * @copyright Copyright 2006-2008 NobuNobuXOOPS Project <http://sourceforge.net/projects/nobunobuxoops/>
 * @author NobuNobu <nobunobu@nobunobu.com>
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * Multi Language Enabler Action Filter
 *
 * Following Multi Language is based on "EMLH(The Easiest Multi-Language Hack) for XOOPS 2.0.x" by GIJOE
 *   (http://www.peak.ne.jp/xoops/)
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();
if (!class_exists('CubeUtil_MultiLanguage')) {
    if (file_exists(XOOPS_ROOT_PATH.'/modules/cubeUtils/include/conf_ml.php')) {
        require_once XOOPS_ROOT_PATH.'/modules/cubeUtils/include/conf_ml.php';
    } else if (file_exists(XOOPS_ROOT_PATH.'/settings/cubeUtil_conf.php')) {
        require_once XOOPS_ROOT_PATH.'/settings/cubeUtil_conf.php';
    } else {
        require_once XOOPS_ROOT_PATH.'/modules/cubeUtils/include/conf_ml.dist.php';
    }
    if (file_exists(XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_LanguageManager.class.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_LanguageManager.class.php';
    } else {
        require_once XOOPS_ROOT_PATH . '/modules/base/kernel/Legacy_LanguageManager.class.php';
    }

    class CubeUtil_MultiLanguage extends XCube_ActionFilter
    {
        var $mLanguage;
        var $mLanguages;
        var $mLanguageNames;
        var $mCookiePath;
        var $mQueryString;
        
        function CubeUtil_MultiLanguage()
        {
            $this->mCookiePath = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , '$1' , XOOPS_URL ) ;
            if( $this->mCookiePath == XOOPS_URL ) $this->mCookiePath = '/' ;
            if( substr( $this->mCookiePath , -1 ) != '/' ) $this->mCookiePath .= '/' ;

            $this->mLanguages = explode( ',' , CUBE_UTILS_ML_LANGS ) ;
            $this->mLanguageNames = explode(',', CUBE_UTILS_ML_LANGNAMES);
            if (!empty($_GET[CUBE_UTILS_ML_PARAM_NAME])) {
                $_SERVER['QUERY_STRING'] = preg_replace('/(^|&)'.CUBE_UTILS_ML_PARAM_NAME.'\=(.*?)(&|$)/','',$_SERVER['QUERY_STRING']);
                $_SERVER['QUERY_STRING'] = preg_replace('/[&\s]*$/','', $_SERVER['QUERY_STRING']);
                $_SERVER['argv'][0] = preg_replace('/(^|&)'.CUBE_UTILS_ML_PARAM_NAME.'\=.*$/','',@$_SERVER['argv'][0]);
                $_SERVER['REQUEST_URI'] = preg_replace('/(^|\?|&)'.CUBE_UTILS_ML_PARAM_NAME.'\=.*$/','',$_SERVER['REQUEST_URI']);
            }

            $this->mQueryString = $_SERVER['QUERY_STRING'];
            $this->mRequestURI = $_SERVER['REQUEST_URI'];
            
        }
        
        function getLanguageName(&$language)
        {
            // check the current language
            if(!empty($_GET[CUBE_UTILS_ML_PARAM_NAME]) && in_array($_GET[CUBE_UTILS_ML_PARAM_NAME], $this->mLanguages)) {
                $this->mLanguage = $_GET[CUBE_UTILS_ML_PARAM_NAME] ;
            } else if(!empty($_COOKIE[CUBE_UTILS_ML_PARAM_NAME]) && in_array($_COOKIE[CUBE_UTILS_ML_PARAM_NAME], $this->mLanguages)) {
                $this->mLanguage = $_COOKIE[CUBE_UTILS_ML_PARAM_NAME];
            } else if ($browserAccept = $this->getLangBrowserAccept()){
                $this->mLanguage = $this->getLangBrowserAccept();
            } else {
                $this->mLanguage = $this->getLangByName(CUBE_UTILS_ML_DEFAULT_LANGNAME);
            }
            
            if (!empty($this->mLanguage)) {
                $_COOKIE[CUBE_UTILS_ML_PARAM_NAME] = $this->mLanguage;
                setcookie(CUBE_UTILS_ML_PARAM_NAME, $this->mLanguage, time()+CUBE_UTILS_ML_COOKIELIFETIME, $this->mCookiePath, '', 0);
                $languageName = $this->getLangName($this->mLanguage);
                if ($languageName) {
                    $language = $languageName;
                    setcookie(CUBE_UTILS_ML_COOKIE_NAME, $language, time()+CUBE_UTILS_ML_COOKIELIFETIME, $this->mCookiePath, '', 0);
                }
                if (empty($_GET[CUBE_UTILS_ML_PARAM_NAME]) || $_GET[CUBE_UTILS_ML_PARAM_NAME]!='raw') {
                    ob_start(array(&$this, 'obFilter'));
                }
            }
            $GLOBALS['xoopsConfig']['language'] = $language;
        }
        function getLangName($language = '')
        {
            include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
            $idx = array_search($language,  $this->mLanguages);
            $languageName = $this->mLanguageNames[$idx];
            $availableLangs = XoopsLists::getLangList();
            If (($languageName != '') && (in_array($languageName, $availableLangs))) {
                return $languageName;
            }
            return false;
        }
        
        function getLangByName($languageName = '')
        {
            include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
            $idx = array_search($languageName,  $this->mLanguageNames);
            $language = $this->mLanguages[$idx];
            $availableLangs = XoopsLists::getLangList();
            If (($language != '') && (in_array($languageName, $availableLangs))) {
                return $language;
            }
            return false;
        }
        
        function getLangBrowserAccept()
        {
            $language = false;
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $acceptLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach ($acceptLangs as $acceptLang) {
                    $acceptLang = strtolower($acceptLang);
                    $acceptLangLength = strlen($acceptLang);
                    if ($acceptLangLength) {
                        if (preg_match("/([a-z]{2})(-[a-zA-Z]{2})*(;q=[0-9.]+)*$/", $acceptLang, $match)) {
                            $language = htmlspecialchars($match[1], ENT_QUOTES);
                            if (in_array($language, $this->mLanguages)) {
                                break;
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
            return false;
        }

        // ob filter
        function obFilter( $s )
        {
            // protection against some injection
            if( ! in_array( $this->mLanguage , $this->mLanguages ) ) {
                $this->mLanguage = $this->mLanguages[0] ;
            }

            // escape brackets inside of <input type="text" value="...">
            $s = preg_replace_callback( '/(\<input)([^>]*)(\>)/isU' , array(&$this,'escapeBracketTextBox') , $s ) ;

            // escape brackets inside of <textarea></textarea>
            $s = preg_replace_callback( '/(\<textarea[^>]*\>)(.*)(<\/textarea\>)/isU' , array(&$this,'escapeBracket') , $s ) ;

            // multilanguage image tag
            $langimages = explode( ',' , CUBE_UTILS_ML_LANGIMAGES ) ;
            $langnames = explode( ',' , CUBE_UTILS_ML_LANGNAMES ) ;
            @list($url, $query) = explode('?',$this->mRequestURI);
            if( empty( $query ) ) {
                $link_base = '?'.CUBE_UTILS_ML_PARAM_NAME.'=' ;
            } else if( ( $pos = strpos($query, CUBE_UTILS_ML_PARAM_NAME.'=') ) === false ) {
                $link_base = '?'.htmlspecialchars($query, ENT_QUOTES).'&amp;'.CUBE_UTILS_ML_PARAM_NAME.'=' ;
            } else if( $pos < 2 ) {
                $link_base = '?'.CUBE_UTILS_ML_PARAM_NAME.'=' ;
            } else {
                $link_base = '?'.htmlspecialchars(substr($query, 0, $pos-1),ENT_QUOTES).'&amp;'.CUBE_UTILS_ML_PARAM_NAME.'=' ;
            }
            $link_base = $url. $link_base;
            $langimage_html = '' ;
            foreach( $this->mLanguages as $l => $lang ) {
                $langimage_html .= '<a rel="nofollow" href="'.$link_base.$lang.'"><img src="'.XOOPS_URL.'/'.$langimages[$l].'" alt="flag" title="'.$langnames[$l].'" /></a>' ;
            }
            $s = preg_replace( '/\['.CUBE_UTILS_ML_IMAGETAG.'\]/' , $langimage_html , $s ) ;

            $s = preg_replace( '/\['.CUBE_UTILS_ML_URLTAG.':([^\]]*?)\]/' , $link_base."$1" , $s ) ;

            // simple pattern to strip selected lang_tags
            $s = preg_replace( '/\[(\/)?([^\]]+\|)?'.preg_quote($this->mLanguage).'(\|[^\]]+)?\](\<br \/\>)?/i' , '' , $s ) ;

            // eliminate description between the other language tags.
            foreach( $this->mLanguages as $lang ) {
                if( $this->mLanguage == $lang ) continue ;
                $s = preg_replace_callback( '/\[(?:^\/[^\]]+\|)?'.preg_quote($lang).'(?:\|[^\]]+)?\].*\[\/(?:^\/[^\]]+\|)?'.preg_quote($lang).'(?:\|[^\]]+)?(?:\]\<br \/\>|\])/isU' , array(&$this,'checkNeverCross') , $s ) ;
            }

            // escape brackets inside of <input type="text" value="...">
            $s = preg_replace_callback( '/(\<input)([^>]*)(\>)/isU' , array(&$this,'unEscapeBracketTextBox') , $s ) ;

            // escape brackets inside of <textarea></textarea>
            $s = preg_replace_callback( '/(\<textarea[^>]*\>)(.*)(<\/textarea\>)/isU' , array(&$this,'unEscapeBracket') , $s ) ;
            return $s ;
        }

        function escapeBracketTextBox( $matches )
        {
        	if( preg_match( '/type=["\']?(?=text|hidden)["\']?/i' , $matches[2] ) ) {
        		return $matches[1].str_replace('[','__ml[ml__',$matches[2]).$matches[3] ;
        	} else {
        		return $matches[1].$matches[2].$matches[3] ;
        	}
        }
        function escapeBracket( $matches )
        {
            return $matches[1].str_replace('[','__ml[ml__',$matches[2]).$matches[3] ;
        }

        function unEscapeBracketTextBox( $matches )
        {
        	if( preg_match( '/type=["\']?(?=text|hidden)["\']?/i' , $matches[2] ) ) {
        		return $matches[1].str_replace('__ml[ml__','[', $matches[2]).$matches[3] ;
        	} else {
        		return $matches[1].$matches[2].$matches[3] ;
        	}
        }
        function unEscapeBracket( $matches )
        {
            return $matches[1].str_replace('__ml[ml__','[',$matches[2]).$matches[3] ;
        }

        function checkNeverCross( $matches )
        {
            return preg_match( CUBE_UTILS_ML_NEVERCROSSREGEX , $matches[0] ) ? $matches[0] : '' ;
        }
    }
    
    function cubeUtil_MLConvert($str) {
        if (!empty($GLOBALS['cubeUtilMlang'])) {
            return $GLOBALS['cubeUtilMlang']->obfilter($str);
        }
        return $str;
    }
}
?>
