<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_LanguageManager.class.php,v 1.6 2008/09/25 15:11:57 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
		if (!$this->_loadFile(XOOPS_ROOT_PATH . "/modules/legacy/language/" . $this->mLanguageName . "/global.php")) {
			$this->_loadFile(XOOPS_ROOT_PATH . "/modules/legacy/language/english/global.php");
		}
		if (!$this->_loadFile(XOOPS_ROOT_PATH . "/modules/legacy/language/" . $this->mLanguageName . "/setting.php")) {
			$this->_loadFile(XOOPS_ROOT_PATH . "/modules/legacy/language/english/setting.php");
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
			$filename = XOOPS_ROOT_PATH . "/language/" . $this->mLanguageName . "/" . $type . ".php";
			if (!$this->_loadFile($filename)) {
				$filename = XOOPS_ROOT_PATH . "/language/" . $this->getFallbackLanguage() . "/" . $type . ".php";
				$this->_loadFile($filename);
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
		$this->_loadLanguage($moduleName, "main");
	}
	
	/**
	 * Load the message catalog of the specified module for admin.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadModuleAdminMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, "admin");
	}

	/**
	 * Load the message catalog of the specified module for block.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadBlockMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, "blocks");
	}

	/**
	 * Load the message catalog of the specified module for modinfo.
	 * 
	 * @access public
	 * @param $dirname A dirname of module.
	 */
	function loadModinfoMessageCatalog($dirname)
	{
		$this->_loadLanguage($dirname, "modinfo");
	}

	/**
	 * @access protected
	 * @param $dirname      module directory name
	 * @param $fileBodyName language file body name
	 */
	function _loadLanguage($dirname, $fileBodyName)
	{
		$fileName = XOOPS_MODULE_PATH . "/" . $dirname . "/language/" . $this->mLanguageName . "/" . $fileBodyName . ".php";
		if (!$this->_loadFile($fileName)) {
			$fileName = XOOPS_MODULE_PATH . "/" . $dirname . "/language/english/" . $fileBodyName . ".php";
			$this->_loadFile($fileName);
		}
	}


	/**
	 * @access protected
	 */
	function _loadFile($filename)
	{
		if (file_exists($filename)) {
			global $xoopsDB, $xoopsTpl, $xoopsRequestUri, $xoopsModule, $xoopsModuleConfig,
				   $xoopsModuleUpdate, $xoopsUser, $xoopsUserIsAdmin, $xoopsTheme,
				   $xoopsConfig, $xoopsOption, $xoopsCachedTemplate, $xoopsLogger, $xoopsDebugger;

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
		if ($section != null) {
			$filePath = XOOPS_ROOT_PATH . "/languages/" . $this->mLanguageName . "/${section}/${filename}";
		}
		else {
			$filePath = XOOPS_ROOT_PATH . "/languages/" . $this->mLanguageName . "/${filename}";
		}
		
		return file_exists($filePath);
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
		$filepath = null;
		if ($section != null) {
			$filepath = XOOPS_ROOT_PATH . "/languages/" . $this->mLanguageName . "/${section}/${filename}";
		}
		else {
			$filepath = XOOPS_ROOT_PATH . "/languages/" . $this->mLanguageName . "/${filename}";
		}
		
		if (file_exists($filepath)) {
			return $filepath;
		}
		else {
			if ($section != null) {
				return XOOPS_ROOT_PATH . "/languages/" . $this->getFallbackLanguage() . "/${section}/${filename}";
			}
			else {
				return XOOPS_ROOT_PATH . "/languages/" . $this->getFallbackLanguage() . "/${filename}";
			}
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
		return "english";
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
