<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_LanguageManager.class.php,v 1.6 2008/09/25 15:11:57 kilica Exp $
 * @copyright (c) 2005-2022 XOOPSCube Project
 * @license   GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_LanguageManager.class.php';

class Legacy_LanguageManager extends XCube_LanguageManager
{
    public function prepare()
    {
        parent::prepare();

        $this->_setupDatabase();
        $this->loadGlobalMessageCatalog();

        $this->_setupMbstring();
    }

    /**
     * Load the additional file to control DB.
     */
    public function _setupDatabase()
    {
        $filename = XOOPS_MODULE_PATH . '/legacy/language/' . $this->mLanguageName . '/charset_' . XOOPS_DB_TYPE . '.php';
        if (file_exists($filename)) {
            require_once($filename);
        }
    }

    public function _setupMbstring()
    {
        #ifdef _MBSTRING_LANGUAGE
        if (defined('_MBSTRING_LANGUAGE') && function_exists('mb_language')) {
            if (false != @mb_language(_MBSTRING_LANGUAGE) && false != @mb_internal_encoding(_CHARSET)) {
                define('MBSTRING', true);
            } else {
                mb_language('neutral');
                mb_internal_encoding('ISO-8859-1');
                if (!defined('MBSTRING')) {
                    define('MBSTRING', false);
                }
            }

            if (function_exists('mb_regex_encoding')) {
                @mb_regex_encoding(_CHARSET);
            }

            ini_set('mbstring.substitute_character', 'none');
            ini_set('default_charset', _CHARSET);
            ini_set('mbstring.substitute_character', 'none');
            //if (PHP_VERSION_ID < 50600) {
               // ini_set('mbstring.http_input', 'pass');
              //  ini_set('mbstring.http_output', 'pass');
            //} else {
                @ini_set('mbstring.internal_encoding', '');
               // @ini_set('mbstring.http_input', ''); deprecated
               // @ini_set('mbstring.http_output', ''); deprecated 
/**
* default_charset string
* "UTF-8" is the default value and its value is used as the default character encoding for 
* htmlentities(), html_entity_decode() and htmlspecialchars() 
* if the encoding parameter is omitted. The value of default_charset will also be used to set 
* the default character set for iconv functions if the iconv.input_encoding, iconv.output_encoding 
* and iconv.internal_encoding configuration options are unset, and for mbstring functions if the 
* mbstring.http_input mbstring.http_output mbstring.internal_encoding configuration option is unset.
* https://www.php.net/manual/en/ini.core.php#ini.default-charset
**/
           // }
        }
        #endif

        if (!defined('MBSTRING')) {
            define('MBSTRING', false);
        }
    }

    public function loadGlobalMessageCatalog()
    {
        $lpath = XOOPS_ROOT_PATH . '/modules/legacy/language/' . $this->mLanguageName;
        if (!$this->_loadFile($lpath . '/global.php')) {
            $this->_loadFile(XOOPS_ROOT_PATH . '/modules/legacy/language/' . $this->getFallbackLanguage() . '/global.php');
        }
        if (!$this->_loadFile($lpath . '/setting.php')) {
            $this->_loadFile(XOOPS_ROOT_PATH . '/modules/legacy/language/' . $this->getFallbackLanguage() . '/setting.php');
        }

        //
        // Now, if XOOPS_USE_MULTIBYTES isn't defined, set zero to it.
        //
        if (!defined('XOOPS_USE_MULTIBYTES')) {
            define('XOOPS_USE_MULTIBYTES', 0);
        }
    }

    /**
     * Load the special message catalog that is defined as the XOOPS2 generation.
     *
     * @access public
     * @param string $type
     */
    public function loadPageTypeMessageCatalog(string $type)
    {
        if (false === strpos($type, '.')) {
            if (!$this->_loadFile(XOOPS_ROOT_PATH . '/language/' . $this->mLanguageName . '/' . $type . '.php')) {
                $this->_loadFile(XOOPS_ROOT_PATH . '/language/' . $this->getFallbackLanguage() . '/' . $type . '.php');
            }
        }
    }

    /**
     * Load the message catalog of the specified module.
     *
     * @access public
     * @param string $moduleName
     */
    public function loadModuleMessageCatalog(string $moduleName)
    {
        $this->_loadLanguage($moduleName, 'main');
    }

    /**
     * Load the message catalog of the specified module for admin.
     *
     * @access public
     * @param $dirname /dirname of module.
     */
    public function loadModuleAdminMessageCatalog($dirname)
    {
        $this->_loadLanguage($dirname, 'admin');
    }

    /**
     * Load the message catalog of the specified module for block.
     *
     * @access public
     * @param $dirname /dirname of module.
     */
    public function loadBlockMessageCatalog($dirname)
    {
        $this->_loadLanguage($dirname, 'blocks');
    }

    /**
     * Load the message catalog of the specified module for modinfo.
     *
     * @access public
     * @param $dirname /dirname of module.
     */
    public function loadModinfoMessageCatalog($dirname)
    {
        $this->_loadLanguage($dirname, 'modinfo');
    }

    /**
     * @access protected
     * @param $dirname      /module directory name
     * @param $fileBodyName /language file body name
     */
    public function _loadLanguage($dirname, $fileBodyName)
    {
        static $trust_dirnames = [];
        if (!isset($trust_dirnames[$dirname])) {
            $trust_dirnames[$dirname] = Legacy_Utils::getTrustDirnameByDirname($dirname);
        }
        (
            $this->_loadFile(XOOPS_MODULE_PATH . '/' . $dirname . '/language/' . $this->mLanguageName . '/' . $fileBodyName . '.php')
            ||
            $this->_loadFile(XOOPS_MODULE_PATH . '/' . $dirname . '/language/' . $this->getFallbackLanguage() . '/' . $fileBodyName . '.php')
            ||
            (
                $trust_dirnames[$dirname] &&
                (
                    $this->_loadFile(XOOPS_TRUST_PATH . '/modules/' . $trust_dirnames[$dirname] . '/language/' . $this->mLanguageName . '/' . $fileBodyName . '.php', $dirname)
                    ||
                    $this->_loadFile(XOOPS_TRUST_PATH . '/modules/' . $trust_dirnames[$dirname] . '/language/' . $this->getFallbackLanguage() . '/' . $fileBodyName . '.php', $dirname)
                )
            )
        );
    }

    /**
     * @access protected
     * @param $filename /filename.
     * @param null $mydirname
     * @return bool
     */
    public function _loadFile($filename, $mydirname = null)
    {
        if (file_exists($filename)) {
            require_once $filename;
            return true;
        }

        return false;
    }

    /**
     * check the exstence of the specified file in the specified section.
     *
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return bool
     */
    public function existFile(string $section, string $filename)
    {
        return file_exists(XOOPS_ROOT_PATH . '/languages/' . $this->mLanguageName . ($section?"/$section/$filename":"/$filename"));
    }

    /**
     * Return the file path by the specified section and the specified file.
     *
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return string
     */
    public function getFilepath(string $section, string $filename)
    {
        $filepath = XOOPS_ROOT_PATH . '/languages/' . $this->mLanguageName . ($section?"/${section}/${filename}":"/${filename}");

        if (file_exists($filepath)) {
            return $filepath;
        } else {
            return XOOPS_ROOT_PATH . '/languages/' . $this->getFallbackLanguage() . ($section?"/${section}/${filename}":"/${filename}");
        }
    }

    /**
     * Get file contents and return it.
     *
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return string
     */
    public function loadTextFile(string $section, string $filename)
    {
        $filepath = $this->getFilepath($section, $filename);
        return file_get_contents($filepath);
    }

    public function getFallbackLanguage() : string
    {
        return 'english';
    }

    public function encodeUTF8($text)
    {
        if ((XOOPS_USE_MULTIBYTES == 1) && function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($text, 'UTF-8', _CHARSET);
        }

        return utf8_encode($text);
    }

    public function decodeUTF8($text)
    {
        if ((XOOPS_USE_MULTIBYTES == 1) && function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($text, _CHARSET, 'UTF-8');
        }

        return utf8_decode($text);
    }
}
