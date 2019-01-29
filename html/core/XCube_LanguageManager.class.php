<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_LanguageManager.class.php,v 1.4 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * This class manages resources of each languages. By requests of other
 * components, this class loads files, or checks the existence of the specified
 * resource, or composes filepath to access real files. And, it manages some
 * locale informations.
 * 
 * Rules about language are different at each bases. So it's important that a 
 * base defines the sub class of this class if it can't use this class directly.
 * 
 * And, XCube or bases have to make each languages possible to have its sub
 * class. By that, languages become able to implement their logic to solve
 * problems.
 * 
 * This class calls sub directories of each languages 'section'. 'section' is
 * used to load image files and etc.
 */
class XCube_LanguageManager
{
    /**
     * @access protected
     * @var string
     */
    public $mLanguageName;
    
    /**
     * @access protected
     * @var string
     */
    public $mLocaleName;
    // !Fix PHP7
    public function __construct()
    //public function XCube_LanguageManager()
    {
        $this->mLanguageName = $this->getFallbackLanguage();
        $this->mLocaleName = $this->getFallbackLocale();
    }

    /**
     * Normally, this member function is called soon, after constructor.
     * To follow the base, initialize.
     */
    public function prepare()
    {
    }
    
    /**
     * Set locale name.
     * 
     * @param string $local locale name
     */
    public function setLocale($locale)
    {
        $this->mLanguageName = $locale;
    }
    
    /**
     * Get locale name.
     * 
     * @return string  locale name
     */
    public function getLocale()
    {
        return $this->mLanguageName;
    }

    /**
     * Set language name.
     * 
     * @param string $language language name
     */
    public function setLanguage($language)
    {
        $this->mLanguageName = $language;
    }
    
    /**
     * Get language name.
     * 
     * @return string  language name
     */
    public function getLanguage()
    {
        return $this->mLanguageName;
    }

    /**
     * Load the global message catalog which is defined in the base module.
     */
    public function loadGlobalMessageCatalog()
    {
    }
    
    /**
     * Load the module message catalog which is defined in the specified
     * module.
     * 
     * @param string $moduleName A name of module.
     */
    public function loadModuleMessageCatalog($moduleName)
    {
    }
    
    /**
     * Load the theme message catalog which is defined in the specified module.
     * 
     * @param string $themeName A name of theme.
     */
    public function loadThemeMessageCatalog($themeName)
    {
    }
    
    /**
     * check the exstence of the specified file in the specified section.
     * 
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return bool
     */
    public function existFile($section, $filename)
    {
    }
    
    /**
     * Return the file path by the specified section and the specified file.
     * 
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return string
     */
    public function getFilepath($section, $filename)
    {
    }

    /**
     * Get file contents and return it.
     * 
     * @access public
     * @param string $section  A name of section.
     * @param string $filename A name of file
     * @return string
     */
    public function loadTextFile($section, $filename)
    {
    }

    /**
     * Return translated message.
     * 
     * @param  string $word
     * @return string
     * 
     * @note This member function is test.
     */
    public function translate($word)
    {
        return $word;
    }

    /**
     * Return default language name.
     * 
     * @access protected
     * @return string
     */
    public function getFallbackLanguage()
    {
        return "eng";
    }

    /**
     * Return default locale name.
     * 
     * @access protected
     * @return string
     */
    public function getFallbackLocale()
    {
        return "EG";
    }
    
    public function encodeUTF8($str)
    {
        return $str;
    }

    public function decodeUTF8($str)
    {
        return $str;
    }
}
