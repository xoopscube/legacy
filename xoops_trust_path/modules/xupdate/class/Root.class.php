<?php

class Xupdate_Root extends XoopsSimpleObject {

	public $mRoot ;
	public $xoops_root_path ;
	public $mydirname ;
	public $mid = 0 ;
	public $mname = null ;
	public $mod_config ;
	public $myts ;
	public $db = null ;	// Database instance
	public $Ftp = null ;	// Ftp instance
	public $func = null ;	// Functions instance
	public $params = array() ;	//some parameters

	public function __construct()
	{
        if(!defined('XOOPS_ROOT_PATH'))
        {
            exit;
        }

		$this->xoops_root_path = XOOPS_ROOT_PATH;

		$this->mRoot = $root = XCube_Root::getSingleton();

		$this->db = $root->mController->mDB;

		// module ID & name
		$this_module = $root->mContext->mXoopsModule; 
		$this->mid = $this_module->get('mid');
		$this->mname = $this_module->get('name');
		$this->mydirname = $this_module->get('dirname');
		// module config
		$this->mod_config = $root->mContext->mModuleConfig;
		//adump($this->mod_config);

		// mytextsanitizer   ToDo --> Cube Style ??
		$this->myts =& MyTextSanitizer::getInstance();

		// set temp_path
		$this->params['temp_dirname'] = trim( strrchr( trim($this->mod_config['temp_path'],'/'), '/'), '/') ;
			//adump($this->params['temp_dirname']);
		$this->params['temp_path'] = dirname(dirname(dirname(dirname(__FILE__)))).'/'.trim($this->mod_config['temp_path'],'/') ;
			//adump($this->params['temp_path']);
		$tmpf = $this->params['is_writable']['path'] = rtrim($this->params['temp_path'], '/');
		$this->params['is_writable']['result'] = is_writable( $tmpf ) && is_executable( $tmpf ) ;

		// Ftp class
		require_once dirname(__FILE__) . '/Ftp.class.php';
		$this->Ftp = new Xupdate_Ftp($this) ;

		// Func class
		require_once dirname(__FILE__).'/Func.class.php' ;
		$this->func = new Xupdate_Func($this) ;
	}

	public function get($key, $default = null) {

		return $this->mRoot->mContext->mRequest->getRequest($key);
		//$request = ( isset($_GET[$key]) ) ? $_GET[$key] : $default;
		//return $request;
	}

	public function post($key, $default = null) {

		return $this->mRoot->mContext->mRequest->getRequest($key);
		//$request = ( isset($_POST[$key]) ) ? $_POST[$key] : $default;
		//return $request;
	}

} // end class

?>