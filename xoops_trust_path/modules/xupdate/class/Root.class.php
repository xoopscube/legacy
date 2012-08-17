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
		$this->params['temp_path'] = XOOPS_TRUST_PATH . '/'.trim($this->mod_config['temp_path'],'/') ;
		//adump($this->params['temp_path']);

		$tmpf = rtrim($this->params['temp_path'], '/');
		$tmpf_realpath = realpath($tmpf);
		if (empty($tmpf_realpath)){
			$this->params['is_writable']['path'] = $tmpf ;//NG
		}else{
			$this->params['is_writable']['path'] = $tmpf_realpath ;//OK
		}
		$this->params['is_writable']['result'] = Xupdate_Utils::checkDirWritable($tmpf_realpath);

		// Ftp class
		require_once dirname(__FILE__) . '/Ftp.class.php';
		$this->Ftp = new Xupdate_Ftp($this) ;

		// Func class
		require_once dirname(__FILE__).'/Func.class.php' ;
		$this->func = new Xupdate_Func($this) ;
	}

	public function get($key, $default = null) {

		return $this->Delete_Nullbyte($this->mRoot->mContext->mRequest->getRequest($key));

	}

	public function post($key, $default = null) {

		return $this->Delete_Nullbyte($this->mRoot->mContext->mRequest->getRequest($key));

	}

	private function Delete_Nullbyte( $value )
	{
		if( is_array( $value ) ){
			return array_map( array( &$this, 'Delete_Nullbyte' ), $value ) ;
		} else {
			return str_replace( pack( 'x' ), '', $value ) ;
		}
	}

} // end class

?>