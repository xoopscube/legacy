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
			$this->setVar('last_update', $this->mModule->getVar('last_update'));
			$this->modinfo =& $this->mModule->getInfo();
			$trust_dirname = $this->mModule->getVar('trust_dirname');

			if ( empty($trust_dirname) ){
				if ( isset($this->modinfo['trust_dirname']) || !empty($this->modinfo['trust_dirname']) ){
					$this->mModule->setVar('trust_dirname',$this->modinfo['trust_dirname']);
				}elseif ( !isset($this->modinfo['trust_dirname']) || empty($this->modinfo['trust_dirname']) ){
					//for d3modules
					if ( file_exists(XOOPS_MODULE_PATH.'/'.$this->getVar('dirname').'/mytrustdirname.php') ) {
						$mytrustdirname='';
						include XOOPS_MODULE_PATH.'/'.$this->getVar('dirname').'/mytrustdirname.php';
						$this->modinfo['trust_dirname'] = $mytrustdirname;
						$this->mModule->setVar('trust_dirname',$mytrustdirname);
					}
				}
			}else{
				if ( !isset($this->modinfo['trust_dirname']) || empty($this->modinfo['trust_dirname']) ){
					$this->modinfo['trust_dirname'] = $trust_dirname;
				}
			}

		}else{
			$this->mModule = new XoopsModule();//空のobject
			$this->mModule->cleanVars();
		}
	}
	/**
	 * @return bool
	 */
	public function isDirnameError()
	{

		if ( $this->getVar('type') == 'TrustModule' ){
			if ( is_object($this->mModule) ){
				if ( $this->mModule->getVar('mid') ){
					if ( $this->getVar('trust_dirname') == $this->mModule->getVar('trust_dirname') ){
						return false;//upadte
					}
					if ( !isset($this->modinfo['trust_dirname']) ){
						return true;
					}
					if ( empty($this->modinfo['trust_dirname']) ){
						return true;
					}
					if ( $this->modinfo['trust_dirname'] != $this->getVar('trust_dirname') ){
						return true;
					}
				}
			}
		}else{
			if ( is_object($this->mModule) ){
				if ( $this->mModule->getVar('mid') ){
					if ( isset($this->modinfo['trust_dirname']) && !empty($this->modinfo['trust_dirname']) ){
						return true;
					}
				}
			}
		}
		return false;

	}
	/**
	 * @return bool
	 */
	public function hasNeedUpdate()
	{
		if (empty($this->modinfo)){
			return false;
		}else{
			return ($this->getVar('version') != Legacy_Utils::convertVersionFromModinfoToInt($this->modinfo['version']));
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