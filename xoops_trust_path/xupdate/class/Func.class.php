<?php

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

if( ! class_exists( 'Xupdate_Func' ) ) {

class Xupdate_Func {

	/* xupdate module variables */
	var $XupdateObj = null ;	// xupdate module object
	var $mod_config ;

	public function __construct($XupdateObj)
	{
		$this->XupdateObj = $XupdateObj ;
		$this->mod_config = $this->XupdateObj->mod_config ;
		//$this->_makeTmpDir();
	}

	public function & getInstance($mydirname)
	{
		static $instance ;
		if( ! isset( $instance[$mydirname] ) ) {
			$instance[$mydirname] = new Xupdate_Func($mydirname) ;
		}
		return $instance[$mydirname] ;
	}

} // end class
} // end if

?>