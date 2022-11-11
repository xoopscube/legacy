<?php
/**
 * XCube_LanguageManager.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      This class manages resources of each languages.
 * By requests of other components, this class loads files, or checks the existence of the specified
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

class XCube_LanguageManager {
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

	public function __construct() {
		$this->mLanguageName = $this->getFallbackLanguage();
		$this->mLocaleName   = $this->getFallbackLocale();
	}

	/**
	 * Normally, this member function is called promptly, after the constructor.
	 * To follow the base, initialize.
	 */
	public function prepare() {
	}

    /**
     * Set string locale name.
     *
     * @param $locale
     */
	public function setLocale( $locale ) {
		$this->mLanguageName = $locale;
	}

	/**
	 * Get locale name.
	 *
	 * @return string  locale name
	 */
	public function getLocale(): string
    {
		return $this->mLanguageName;
	}

	/**
	 * Set language name.
	 *
	 * @param string $language language name
	 */
	public function setLanguage(string $language ) {
		$this->mLanguageName = $language;
	}

	/**
	 * Get language name.
	 *
	 * @return string  language name
	 */
	public function getLanguage(): string
    {
		return $this->mLanguageName;
	}

	/**
	 * Load the global message catalog which is defined in the base module.
	 */
	public function loadGlobalMessageCatalog() {
	}

	/**
	 * Load the module message catalog which is defined in the specified
	 * module.
	 *
	 * @param string $moduleName A name of module.
	 */
	public function loadModuleMessageCatalog(string $moduleName ) {
	}

	/**
	 * Load the theme message catalog which is defined in the specified module.
	 *
	 * @param string $themeName A name of theme.
	 */
	public function loadThemeMessageCatalog(string $themeName ) {
	}

	/**
	 * check the existence of the specified file in the specified section.
	 *
	 * @access public
	 *
	 * @param string $section A name of section.
	 * @param string $filename A name of file
	 *
	 * @return void
	 */
	public function existFile(string $section, string $filename ) {
	}

	/**
	 * Return the file path by the specified section and the specified file.
	 *
	 * @access public
	 *
	 * @param string $section A name of section.
	 * @param string $filename A name of file
	 *
	 * @return string
	 */
	public function getFilepath(string $section, string $filename ) {
	}

	/**
	 * Get file contents and return it.
	 *
	 * @access public
	 *
	 * @param string $section A name of section.
	 * @param string $filename A name of file
	 *
	 * @return string
	 */
	public function loadTextFile(string $section, string $filename ) {
	}

	/**
	 * Return translated message.
	 *
	 * @param string $word
	 *
	 * @return string
	 *
	 * @note This member function is test.
	 */
	public function translate(string $word ) {
		return $word;
	}

	/**
	 * Return default language name.
	 *
	 * @access protected
	 * @return string
	 */
	public function getFallbackLanguage(): string
    {
		return "en"; // !Todo check UTF-8
	}

	/**
	 * Return default locale name.
	 *
	 * @access protected
	 * @return string
	 */
	public function getFallbackLocale(): string
    {
		return "EN"; // !Todo check UTF-8
	}

	public function encodeUTF8( $str ) {
		return $str;
	}

	public function decodeUTF8( $str ) {
		return $str;
	}
}
