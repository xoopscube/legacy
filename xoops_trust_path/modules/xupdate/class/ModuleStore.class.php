<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

//require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';
/**
* XoopsSimpleObject
*/
class Xupdate_ModuleStore extends XoopsSimpleObject {

	public $mModule ;
	public $modinfo = array();

	public function __construct()
	{
		parent::__construct() ;

		//項目
		$this->initVar('id', XOBJ_DTYPE_INT, '0', false);//Primary key
		$this->initVar('sid', XOBJ_DTYPE_INT, '0', false);//store join

		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('type', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('version', XOBJ_DTYPE_INT, 100, false);
		$this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
		$this->initVar('trust_dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('rootdirname', XOBJ_DTYPE_STRING, '', false);

	}

	/**
	 * @return string
	 */
	function getRenderedVersion()
	{
		return sprintf('%01.2f', $this->getVar('version') / 100);
	}
	/**
	 * @
	 */
	public function setmModule()
	{
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$this->mModule =& $hModule->getByDirname($this->getVar('dirname')) ;
		if (is_object($this->mModule)){
			$this->modinfo =& $this->mModule->getInfo();
		}else{
			$this->mModule = new XoopsModule();//空のobject
			$this->mModule->cleanVars();
		}
	}
	/**
	 * @return bool
	 */
	public function isTrustDirnameError()
	{
		$ret = false;
		if ( $this->getVar('type') == 'TrustModule' ){
			$trust_dirname = $this->mModule->getVar('trust_dirname');
			if ( !empty($trust_dirname) && $this->getVar('trust_dirname') != $trust_dirname
				&& (isset($this->modinfo['trust_dirname']) && $this->getVar('trust_dirname') != $this->modinfo['trust_dirname'] ) ){
				$ret = true;
			}
		}
		return $ret;
	}
	/**
	 * @return bool
	 */
	public function hasNeedUpdate()
	{
		if (empty($this->modinfo)){
			return false;
		}else{
			return ($this->getVar('version') < Legacy_Utils::convertVersionFromModinfoToInt($this->modinfo['version']));
		}
	}

	public function get_StoreUrl()
	{
		//TODO for test dirname ?
		$ret = XOOPS_URL .'/modules/xupdate/admin/index.php?action=ModuleInstall&target_key='
			.$this->getVar('dirname') .'&target_type='.$this->getVar('type');
		return $ret;
	}
	public function get_InstallUrl()
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}
	public function get_UpdateUrl()
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}

} // end class

?>