<?php

class Xupdate_Root extends XoopsSimpleObject {

	var $mRoot ;
	var $xoops_root_path ;
	var $mydirname ;
	var $mid = 0 ;
	var $mname = null ;
	var $mod_config ;
	var $myts ;
	var $db = null ;	// Database instance
	var $Ftp = null ;	// Ftp instance
	var $func = null ;	// Functions instance
	var $params = array() ;	//some parameters

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
	//	adump($this->mod_config);

		// mytextsanitizer   ToDo --> Cube Style ??
		$this->myts =& MyTextSanitizer::getInstance();

		// set temp_path
		$this->params['temp_dirname'] = trim( strrchr( trim($this->mod_config['temp_path'],'/'), '/'), '/') ;
			//adump($this->params['temp_dirname']);
		$this->params['temp_path'] = dirname(dirname(dirname(dirname(__FILE__)))).'/'.trim($this->mod_config['temp_path'],'/') ;
			//adump($this->params['temp_path']);

		// Ftp class
		require_once dirname(__FILE__) . '/Ftp.class.php';
		$this->Ftp = new Xupdate_Ftp($this) ;

		// Func class
		require_once dirname(__FILE__).'/Func.class.php' ;
		$this->func = new Xupdate_Func($this) ;
	}

    public function get($name, $default = null)
    {
        return $this->mRoot->mContext->mRequest->getRequest($name);
        //$request = ( isset($_GET[$name]) ) ? $_GET[$name] : $default;
        //return $request;
    }
    public function post($name, $default = null)
    {
        return $this->mRoot->mContext->mRequest->getRequest($name);
        //$request = ( isset($_POST[$name]) ) ? $_POST[$name] : $default;
        //return $request;
    }

} // end class

?>