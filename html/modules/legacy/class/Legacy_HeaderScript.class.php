<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_HeaderScript
{
	protected $_mType = 'google';
	protected $_mCore = "1";
	protected $_mUi = "1";

	protected $_mLibrary = array();
	protected $_mScript = array();
	protected $_mMeta = array('keywords'=>'','description'=>'','robots'=>'','rating'=>'','author'=>'','copyright'=>'',);
	protected $_mOnloadScript = array();
	protected $_mStylesheet = array();
	protected $_mLink = array();

	public $mUsePrototype = false;	//use prototype.js ?
	public $mFuncNamePrefix = "";	//jQuery $() function's name prefix for compatibility with prototype.js

	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$root = XCube_Root::getSingleton();
	
		//setup jQuery library location
		$this->_mCore = $this->_getRenderConfig('jquery_core');
		$this->_mUi = $this->_getRenderConfig('jquery_ui');
		$core = str_replace('.', '', $this->_mCore);
		$this->_mType = is_numeric($core) ? 'google' : 'local';
	
		//use compatibility mode with prototype.js ?
		if($root->getSiteConfig('jQuery', 'usePrototype')==1){
			$this->mUsePrototype = true;
			$this->mPrototypeUrl = $root->getSiteConfig('jQuery', 'prototypeUrl');
			$this->mFuncNamePrefix = $root->getSiteConfig('jQuery', 'funcNamePrefix');
		}
	
		$this->_setupDefaultStylesheet();
	}

	/**
	 * _setupDefaultStylesheet
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function _setupDefaultStylesheet()
	{
		if($this->_getRenderConfig('css_file')) $this->addStylesheet($this->_getRenderConfig('css_file'), false);
	}

	/**
	 * addLibrary
	 * 
	 * @param	string $url
	 * @param	bool $xoopsUrl
	 * 
	 * @return	void
	**/
	public function addLibrary($url, $xoopsUrl=true)
	{
		$libUrl = ($xoopsUrl==true) ? XOOPS_URL. $url : $url;
		if(! in_array($libUrl, $this->_mLibrary)){
			 $this->_mLibrary[] = $libUrl;
		}
	}

	/**
	 * addStylesheet
	 * 
	 * @param	string $url
	 * @param	bool $xoopsUrl
	 * 
	 * @return	void
	**/
	public function addStylesheet($url, $xoopsUrl=true)
	{
		$libUrl = ($xoopsUrl==true) ? XOOPS_URL. $url : $url;
		if(! in_array($libUrl, $this->_mStylesheet)){
			 $this->_mStylesheet[] = $libUrl;
		}
	}

	/**
	 * addScript
	 * 
	 * @param	string $script
	 * @param	bool $isOnloadFunction
	 * 
	 * @return	void
	**/
	public function addScript($script, $isOnloadFunction=true)
	{
		if($isOnloadFunction==true){
			$this->_mOnloadScript[] = $script;
		}
		else{
			$this->_mScript[] = $script;
		}
	}

	/**
	 * getLibraryArr
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	public function getLibraryArr()
	{
		return $this->_mLibrary;
	}

	/**
	 * getScriptArr
	 * 
	 * @param	bool	$isOnloadFunction
	 * 
	 * @return	string[]
	**/
	public function getScriptArr($isOnloadFunction=true)
	{
		if($isOnloadFunction==true){
			return $this->_mOnloadScript;
		}
		else{
			return $this->_mScript;
		}
	}

	/**
	 * addLink
	 * 
	 * @param	string	$rel
	 * @param	string	$href
	 * @param	string	$type
	 * @param	string	$title
	 * 
	 * @return	void
	**/
	public function addLink(/*** string ***/ $rel, /*** string ***/ $href, /*** string ***/ $type, /*** string ***/ $title=null)
	{
		$this->_mLink[] = array('rel'=>$rel, 'type'=>$type, 'title'=>$title, 'href'=>$href);
	}

	/**
	 * addMeta
	 * 
	 * @param	string	$name
	 * @param	string	$content
	 * 
	 * @return	void
	**/
	public function addMeta(/*** string ***/ $name, /*** string ***/ $content)
	{
		$this->_mMeta[$name] = $content;
	}

	/**
	 * getMeta
	 * 
	 * @param	string	$name
	 * 
	 * @return	string
	**/
	public function getMeta(/*** string ***/ $name)
	{
		return $this->_mMeta[$name];
	}

	/**
	 * createLibraryTag
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function createLibraryTag()
	{
		$html = "";
	
		//prototype.js compatibility
		if($this->mUsePrototype){
			$html .= '<script type="text/javascript" src="'. $this->mPrototypeUrl .'"></script>';
		}
		
		//load main library
		if($this->_mType=='google'){
			$html .= $this->_loadGoogleJQueryLibrary();
		}
		elseif($this->_mType=='local'){
			$html .= $this->_loadLocalJQueryLibrary();
		}
	
		//load plugin libraries
		foreach($this->_mLibrary as $lib){
			$html .= "<script type=\"text/javascript\" src=\"". $lib ."\"></script>\n";
		}
	
		//load css
		foreach($this->_mStylesheet as $css){
			$html .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"". $css ."\" />\n";
		}
	
		//load link
		foreach($this->_mLink as $link){
			$title = $link['title'] ? 'title="'.$link['title'].'" ' : null;
			$html .= sprintf("<link type=\"%s\" rel=\"%s\" href=\"%s\" $title/>\n", $link['type'], $link['rel'], $link['href']);
		}
	
		//set rss auto-discovery
		if($this->_getRenderConfig('feed_url')){
			$html .= sprintf('<link rel="alternate" type="application/rss+xml" title="rss" href="%s" />'."\n", $this->_getRenderConfig('feed_url'));
		}
		return $html;
	}

	/**
	 * _loadGoogleJQueryLibrary
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _loadGoogleJQueryLibrary()
	{
		$apiKey = XCube_Root::getSingleton()->getSiteConfig('jQuery', 'GoogleApiKey');
		$apiKey = (isset($apiKey)) ? '?key='.$apiKey : null;
		return '<script type="text/javascript" src="http://www.google.com/jsapi'.$apiKey.'"></script>
<script type="text/javascript"><!--
google.load("language", "1"); 
google.load("jquery", "'. $this->_mCore .'");
google.load("jqueryui", "'. $this->_mUi .'");
//-->
</script>
';
	}

	/**
	 * _loadLocalJQueryLibrary
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _loadLocalJQueryLibrary()
	{
		$html = "";
		if($this->_mCore) $html .= '<script type="text/javascript" src="'. $this->_mCore .'"></script>';
		if($this->_mUi) $html .= '<script type="text/javascript" src="'. $this->_mUi .'"></script>';
	
		return $html;
	}

	/**
	 * createOnloadFunctionTag
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	public function createOnloadFunctionTag()
	{
		$html = null;
		if(count($this->_mOnloadScript)>0||count($this->_mScript)>0){
			$html = "<script type=\"text/javascript\"><!--\n";
			if($this->_mType == "google"){
				$html .= "google.setOnLoadCallback(function() {\n";
			}
			if($this->mUsePrototype == true){
				$html .= "jQuery.noConflict();\n";
			}
			$html .= "jQuery(function($){\n";
			$html .= $this->_makeScript(true);
			if($this->_mType == "google"){
				$html .= "\n});\n";
			}
			$html .= "\n});\n";
			$html .= $this->_makeScript(false);
			$html .= "//--></script>"."\n";
		}
		return $html;
	}

	/**
	 * _makeScript
	 * 
	 * @param	bool	$isOnloadFunction
	 * 
	 * @return	string
	**/
	protected function _makeScript($isOnloadFunction=true)
	{
		$html = null;
		$scriptArr = ($isOnloadFunction===true) ? $this->_mOnloadScript : $this->_mScript;
		foreach($scriptArr as $script){
			$html .= $this->_convertFuncName($script);
		}
		return $html;
	}

	/**
	 * _convertFuncName
	 * 
	 * @param	string $script
	 * 
	 * @return	string
	**/
	protected function _convertFuncName($script)
	{
		if($this->mFuncNamePrefix){
			$script = str_replace("$(", $this->mFuncNamePrefix."$(", $script);
		}
		return $script;
	}

	/**
	 * _getRenderConfig
	 * 
	 * @param	string $key
	 * 
	 * @return	string
	**/
	protected function _getRenderConfig($key)
	{
		$handler =& xoops_gethandler('config');
		$configArr =& $handler->getConfigsByDirname('legacyRender');
		return $configArr[$key];
	}

}
?>
