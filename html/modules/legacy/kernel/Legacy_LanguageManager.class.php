<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_LanguageManager.class.php,v 1.6 2008/09/25 15:11:57 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_LanguageManager.class.php";

class Legacy_LanguageManager extends XCube_LanguageManager
{
	function prepare()
	{
		parent::prepare();
		
		$this->_setupDatabase();
		$this->loadGlobalMessageCatalog();
		
		$this->_setupMbstring();
	}
	
	/**
	 * Load the additional file to control DB.
	 */
	function _setupDatabase()
	{
		$filename = XOOPS_MODULE_PATH . '/legacy/language/' . $this->mLanguageName . '/charset_' . XOOPS_DB_TYPE . '.php';
		if (file_exists($filename)) {
			require_once($filename);
		}
	}
	
	function _setupMbstring()
	{
		#ifdef _MBSTRING_LANGUAGE
		if (defined('_MBSTRING_LANGUAGE') && function_exists("mb_language")) {
			if (@mb_language(_MBSTRING_LANGUAGE) != false && @mb_internal_encoding(_CHARSET) != false) {
				define('MBSTRING', true);
			}
			else {
				mb_language("neutral");
				mb_internal_encoding("ISO-8859-1");
				if (!defined('MBSTRING')) {
					define('MBSTRING', false);
				}
			}
			
			if (function_exists('mb_regex_encoding')) {
				@mb_regex_encoding(_CHARSET);
			}
			
			ini_set( 'mbstring.http_input', 'pass');
			ini_set( 'mbstring.http_output', 'pass');
			ini_set( 'mbstring.substitute_character', 'none');
		}
		#endif
		
		if (!defined( "MBSTRING")) {
			define( "MBSTRING", FALSE);
		}
	}

	function loadGlobalMessageCatalog()
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
		if (!defined("XOOPS_USE_MULTIBYTES")) {
			define("XOOPS_USE_MULTIBYTES", 0);
		}
	}

	/**
	 * Load the special message catalog which is defined has been the XOOPS2
	 * generation.
	 * 
	 * @access public
	 * @param string $type
	 */
	function loadPageTypeMessageCatalog($type)
	{
		if (strpos($type, '.') === false) {
			if (!$this->_loadFile(XOOPS_ROOT_PATH . '/language/' . $this->mLanguageName . '/' . $type . '.php')) {
				$this->_loadFile(XOOPS_ROOT_PATH . '/language/' . $this->getFallbackLanguage() . '/' . $type . '.php');
			}
		}
	}

	/**
	 * Load the message catalog of the specified module.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadModuleMessageCatalog($moduleName)
	{
		$this->_loadLanguage($moduleName, 'main');
	}
	
	/**
	 * Load the message catalog of the specified module for admin.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadModuleAdminMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, 'admin');
	}

	/**
	 * Load the message catalog of the specified module for block.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadBlockMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, 'blocks');
	}

	/**
	 * Load the message catalog of the specified module for modinfo.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadModinfoMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, 'modinfo');
	}

	/**
	 * @access protected
	 * @param $dirname      module directory name
	 * @param $fileBodyName language file body name
	 */
	function _loadLanguage($dirname, $fileBodyName)
	{
		static $trust_dirnames = array();
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
	 * @param $filename A filename.
	 * @param $dirname A dirname of module. (for D3 module)
	 */
	function _loadFile($filename, $mydirname = null)
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
	function existFile($section, $filename)
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
	function getFilepath($section, $filename)
	{
		$filepath = XOOPS_ROOT_PATH . '/languages/' . $this->mLanguageName . ($section?"/${section}/${filename}":"/${filename}");
		
		if (file_exists($filepath)) {
			return $filepath;
		}
		else {
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
	function loadTextFile($section, $filename)
	{
		$filepath = $this->getFilepath($section, $filename);
		return file_get_contents($filepath);
	}
	
	function getFallbackLanguage()
	{
		return 'english';
	}

	function encodeUTF8($text)
	{
		if (XOOPS_USE_MULTIBYTES == 1) {
			if (function_exists('mb_convert_encoding')) {
				return mb_convert_encoding($text, 'UTF-8', _CHARSET);
			}
		}
		
		return utf8_encode($text);
	}
	
	function decodeUTF8($text)
	{
		if (XOOPS_USE_MULTIBYTES == 1) {
			if (function_exists('mb_convert_encoding')) {
				return mb_convert_encoding($text, _CHARSET, 'UTF-8');
			}
		}
		
		return utf8_decode($text);
	}
}

?>
