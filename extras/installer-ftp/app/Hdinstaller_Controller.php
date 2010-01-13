<?php
/**
 *  Hdinstaller_Controller.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.controller.php 695 2009-01-20 13:24:16Z sotarok $
 */

/** Application base directory */
define('BASE', dirname(dirname(__FILE__)));

/** include_path setting (adding "/app" and "/lib" directory to include_path) */
$app = BASE . "/app";
$lib = BASE . "/lib";
//ini_set('include_path', implode(PATH_SEPARATOR, array($app, $lib)) . PATH_SEPARATOR . ini_get('include_path'));
ini_set('include_path', implode(PATH_SEPARATOR, array($app, $lib)));
ini_set('max_execution_time', '120');
ini_set('memory_limit', '32M');


/** including application library. */
require_once 'Curaga/Ethna.php';
require_once 'Hdinstaller_Error.php';
require_once 'Hdinstaller_ActionClass.php';
require_once 'Hdinstaller_ActionForm.php';
require_once 'Hdinstaller_ViewClass.php';
require_once 'Hdinstaller_ClassFactory.php';
require_once 'Hdinstaller_Plugin.php';

/**
 *  Hdinstaller application Controller definition.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Controller extends Ethna_Controller
{
    /**#@+
     *  @access private
     */

    /**
     *  @var    string  Application ID(appid)
     */
    var $appid = 'HDINSTALLER';

    /**
     *  @var    array   forward definition.
     */
    var $forward = array(
        /*
         *  TODO: write forward definition here.
         *
         *  Example:
         *
         *  'index'         => array(
         *      'view_name' => 'Hdinstaller_View_Index',
         *  ),
         */
    );

    /**
     *  @var    array   action definition.
     */
    var $action = array(
        /*
         *  TODO: write action definition here.
         *
         *  Example:
         *
         *  'index'     => array(),
         */
    );

    /**
     *  @var    array   SOAP action definition.
     */
    var $soap_action = array(
        /*
         *  TODO: write action definition for SOAP application here.
         *  Example:
         *
         *  'sample'            => array(),
         */
    );

    /**
     *  @var    array       application directory.
     */
    var $directory = array(
        'action'        => 'app/action',
        'action_cli'    => 'app/action_cli',
        'action_xmlrpc' => 'app/action_xmlrpc',
        'app'           => 'app',
        'plugin'        => 'app/plugin',
        'bin'           => 'bin',
        'etc'           => 'etc',
        'filter'        => 'app/filter',
        'locale'        => 'locale',
        'log'           => 'log',
        'plugins'       => array('app/plugin/Smarty',),
        'template'      => 'template',
        'template_c'    => 'tmp',
        'tmp'           => 'tmp',
        'view'          => 'app/view',
        'www'           => 'www',
        'test'          => 'app/test',
    );

    /**
     *  @var    array       database access definition.
     */
    var $db = array(
        ''              => DB_TYPE_RW,
    );

    /**
     *  @var    array       extention(.php, etc) configuration.
     */
    var $ext = array(
        'php'           => 'php',
        'tpl'           => 'php',
    );

    /**
     *  @var    array   class definition.
     */
    var $class = array(
        /*
         *  TODO: When you override Configuration class, Logger class,
         *        SQL class, don't forget to change definition as follows!
         */
        'class'         => 'Hdinstaller_ClassFactory',
        'backend'       => 'Ethna_Backend',
        'config'        => 'Ethna_Config',
        'db'            => 'Ethna_DB_PEAR',
        'error'         => 'Ethna_ActionError',
        'form'          => 'Hdinstaller_ActionForm',
        'i18n'          => 'Ethna_I18N',
        'logger'        => 'Ethna_Logger',
        'plugin'        => 'Hdinstaller_Plugin',
        'session'       => 'Ethna_Session',
        'sql'           => 'Ethna_AppSQL',
        'view'          => 'Hdinstaller_ViewClass',
//        'renderer'      => 'Ethna_Renderer_Smarty',
        'renderer'      => 'Hdinstaller_Renderer_Php',
        'url_handler'   => 'Hdinstaller_UrlHandler',
    );

    /**
     *  @var    array       list of application id where Ethna searches plugin.
     */
    var $plugin_search_appids = array(
        /*
         *  write list of application id where Ethna searches plugin.
         *
         *  Example:
         *  When there are plugins whose name are like "Common_Plugin_Foo_Bar" in
         *  application plugin directory, Ethna searches them in the following order.
         *
         *  1. Common_Plugin_Foo_Bar,
         *  2. Hdinstaller_Plugin_Foo_Bar
         *  3. Ethna_Plugin_Foo_Bar
         *
         *  'Common', 'Hdinstaller', 'Ethna',
         */
        'Hdinstaller', 'Ethna',
    );

    /**
     *  @var    array       filter definition.
     */
    var $filter = array(
        /*
         *  TODO: when you use filter, write filter plugin name here.
         *  (If you specify class name, Ethna reads filter class in 
         *   filter directory)
         *
         *  Example:
         *
         *  'ExecutionTime',
         */
    );

    /**
     *  @var    array   smarty modifier definition.
     */
    var $smarty_modifier_plugin = array(
        /*
         *  TODO: write user defined smarty modifier here.
         *
         *  Example:
         *
         *  'smarty_modifier_foo_bar',
         */
    );

    /**
     *  @var    array   smarty function definition.
     */
    var $smarty_function_plugin = array(
        /*
         *  TODO: write user defined smarty function here.
         *
         *  Example:
         *
         *  'smarty_function_foo_bar',
         */
    );

    /**
     *  @var    array   smarty block definition.
     */
    var $smarty_block_plugin = array(
        /*
         *  TODO: write user defined smarty block here.
         *
         *  Example:
         * 
         *  'smarty_block_foo_bar',
         */
    );

    /**
     *  @var    array   smarty prefilter definition.
     */
    var $smarty_prefilter_plugin = array(
        /*
         *  TODO: write user defined smarty prefilter here.
         *
         *  Example:
         *
         *  'smarty_prefilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty postfilter definition.
     */
    var $smarty_postfilter_plugin = array(
        /*
         *  TODO: write user defined smarty postfilter here.
         *
         *  Example:
         *
         *  'smarty_postfilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty outputfilter definition.
     */
    var $smarty_outputfilter_plugin = array(
        /*
         *  TODO: write user defined smarty outputfilter here.
         *
         *  Example:
         *
         *  'smarty_outputfilter_foo_bar',
         */
    );

    /**#@-*/

    /**
     *  Get Default language and locale setting.
     *  If you want to change Ethna's output encoding, override this method.
     *
     *  @access protected
     *  @return array   locale name(e.x ja_JP, en_US .etc),
     *                  system encoding name,
     *                  client encoding name(= template encoding)
     *                  (locale name is "ll_cc" format. ll = language code. cc = country code.)
     */
    function _getDefaultLanguage()
    {
		$config = $this->getConfig();
		$allow_language = $config->get('allow_language');
		
		$lang = null;
		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'get')===0){
			$lang = isset($_GET['lang']) ? $_GET['lang'] : null;
		}
		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post')===0){
			$lang = isset($_POST['lang']) ? $_POST['lang'] : null;
		}

		if ($lang){
			foreach ($allow_language as $s=>$l){
				if ($l['lang'] === $lang){
					return array($lang, 'UTF-8', 'UTF-8');
				}
			}
		}
		
		$lang = 'en_US';
		$accept = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);  
		$a_langs = explode(',', $accept[0]); 
		foreach ($a_langs as $al){
			foreach ($allow_language as $s=>$l){
				if (strncasecmp($l['lang'], $al, 2)===0){
					$lang = $l['lang'];
					return array($lang, 'UTF-8', 'UTF-8');
				}
			}
		}
		
        return array($lang, 'UTF-8', 'UTF-8');
    }
	
	
	/// Localeは要らない
    function getTemplatedir()
    {
        $template = $this->getDirectory('template');

        // 言語別ディレクトリ
        // _getDerfaultLanguageメソッドでロケールが指定されていた場合は、
        // テンプレートディレクトリにも自動的にそれを付加する。
        if (!empty($this->locale)) {
//            $template .= '/' . $this->locale;
        }

        return $template;
    }
	
	/// Repository URLをCacheファイルの形に
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function repositoryURL2CacheFile($url)
	{
		$url = parse_url($url);
		$ret = sprintf('%s_%s_%s',
					   $url['scheme'],
					   $url['host'],
					   str_replace(array('/', '_'), array('%', '.'), $url['path'])
					   );
		if (isset($url['query']) && $url['query']){
			$ret .= '_'.md5($url['query']);
		}
		
		return BASE.'/tmp/'.$ret;
	}
	
	
	/// 
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function package2dataFile($package, $version)
	{
		$filename = urlencode($package). urlencode($version);
		return sprintf('%s/%s', BASE.'/tmp', $filename);
	}
	
	/// Tmpディレクトリをまっさらに
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function cleanUpTmp($dir='')
	{
		if (!$dir) $dir = $this->getDirectory('tmp');
		
		$dh = opendir($dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				else if (is_dir($dir . '/' . $file)) {
					$this->cleanUpTmp($dir . '/' . $file);
					rmdir($dir . '/' . $file);
				}
				else {
					$f = $dir . "/" . $file;
					unlink($f);
				}
			}
			closedir($dh);
		}
	}
}

?>
