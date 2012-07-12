<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

//require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';
/**
* XoopsSimpleObject
*/
class Xupdate_ModuleStore extends XoopsSimpleObject {

	public $mModule ;
	public $modinfo = array();
	public $detailed_version = '' ;
	public $options = array();

	public function __construct()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '0', false);//Primary key
		$this->initVar('sid', XOBJ_DTYPE_INT, '0', false);//store join

		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('trust_dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('version', XOBJ_DTYPE_INT, 100, false);
		$this->initVar('license', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('required', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
		$this->initVar('target_key', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('target_type', XOBJ_DTYPE_STRING, '', false);

		$this->initVar('replicatable', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('description', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('unzipdirlevel', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('addon_url', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('detail_url', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
		
		// ver <= 0.06
		$this->initVar('isactive', XOBJ_DTYPE_INT, '-1', false);
		$this->initVar('hasupdate', XOBJ_DTYPE_INT, '0', false);

		parent::__construct() ;

	}

	/**
	 * @return string
	 */
	function getRenderedVersion()
	{
		return ($this->getVar('version') > 0)? sprintf('%01.2f', $this->getVar('version') / 100) : '';
	}
	/**
	 * @
	 */
	public function setmModule($readini = true)
	{
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$this->mModule =& $hModule->getByDirname($this->getVar('dirname')) ;
		if (is_object($this->mModule)){
			$this->setVar('last_update', $this->mModule->getVar('last_update'));
			$this->modinfo =& $this->mModule->getInfo();
			$this->modinfo['version'] = sprintf('%01.2f', $this->mModule->getVar('version') / 100);
			$trust_dirname = $this->mModule->getVar('trust_dirname');
			
			$this->options = $this->unserialize_options();
			if ($readini) {
				if (($this->getVar('version') && $this->mModule->getVar('version') != $this->getVar('version'))
						|| 
					(isset($this->modinfo['detailed_version']) && $this->modinfo['detailed_version'] != $this->options['detailed_version'])) {
					$this->setVar('hasupdate', 1);
				} else {
					$this->setVar('hasupdate', 0);
				}
				if ($this->mModule->getVar('isactive')) {
					$this->setVar('isactive', 1);
				} else {
					$this->setVar('isactive', 0);
				}
			}
			
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

		} else {
			$this->mModule = new XoopsModule();//空のobject
			$this->mModule->cleanVars();
			
			$this->options = $this->unserialize_options();
			
			$this->mModule->setVar('version', $this->getVar('version'));
			if ($readini) {
				// for Theme
				if ($this->getVar('target_type') == 'Theme') {
					$t_dir = XOOPS_ROOT_PATH . '/themes/' . $this->getVar('dirname');
					if (is_dir($t_dir)) {
						$this->setVar('isactive', 1);
						$this->setVar('last_update', filemtime($t_dir));
						if (! $this->getVar('version')) {
							$m_file = $t_dir . '/' . 'manifesto.ini.php';
							if (is_file($m_file)) {
								if ($manifesto = @ parse_ini_file($m_file)) {
									if (!empty($manifesto['Version'])) {
										$this->setVar('version', $manifesto['Version'] * 100);
									}
								}
							}
						}
					}
				}
				if (($this->getVar('version') && $this->mModule->getVar('version') != $this->getVar('version'))
						||
						(isset($this->modinfo['detailed_version']) && $this->modinfo['detailed_version'] != $this->options['detailed_version'])) {
					$this->setVar('hasupdate', 1);
				} else {
					$this->setVar('hasupdate', 0);
				}
			}
		}
	}
	/**
	 * @return bool
	 */
	public function isDirnameError()
	{

		if ( $this->getVar('target_type') == 'TrustModule' ){
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

	/**
	 * Check need update of detailed_version
	 * @return boolean
	 */
	public function hasNeedUpdateDetail()
	{
		if (empty($this->modinfo)){
			return false;
		}else{
			return (isset($this->modinfo['detailed_version']) && isset($this->options['detailed_version']) && $this->modinfo['detailed_version'] != $this->options['detailed_version']);
		}
	}

	public function get_StoreUrl()
	{
		//TODO for test dirname ?
		$root =& XCube_Root::getSingleton();
		$modDirname = $root->mContext->mModule->mAssetManager->mDirname;
		$ret = XOOPS_MODULE_URL .'/'.$modDirname.'/admin/index.php?action=ModuleInstall'
			.'&id='.$this->getVar('id') .'&dirname='.$this->getVar('dirname');
		return $ret;
	}
	public function get_InstallUrl()
	{
		$ret = XOOPS_MODULE_URL .'/legacy/admin/index.php?action=ModuleInstall&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}
	public function get_UpdateUrl()
	{
		$ret = XOOPS_MODULE_URL .'/legacy/admin/index.php?action=ModuleUpdate&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}

	/**
	 * get_DetailUrl
	 *
	 * @param string $downloadUrlFormat
	 * @param string $target_key
	 *
	 * @return	string
	 **/
	public function get_DetailUrl( $target_key, $downloadUrlFormat )
	{
		// TODO ファイルNotFound対策
		$url = sprintf( $downloadUrlFormat, $target_key );
		return $url;
	}

	/**
	 * [modules.ini] Options unserializer
	 * @param object $mobj
	 * @param string $dirname
	 * @return array
	 */
	public function unserialize_options()
	{
		$dirname = $this->getVar('dirname');
		 
		//unserialize xin option fileld and replace dirname
		$options = array();
		if ($option = $this->get('options')) {
			if (! $options = @unserialize($this->get('options'))) {
				$options = array();
			}
		}
		if(isset($options['writable_dir'])) {
			array_walk( $options['writable_dir'], array($this, '_printf'), array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
		} else {
			$options['writable_dir'] = array();
		}
		if(isset($options['writable_file'])) {
			array_walk( $options['writable_file'], array($this, '_printf'), array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
		} else {
			$options['writable_file'] = array();
		}
		if(isset($options['install_only'])) {
			array_walk( $options['install_only'], array($this, '_printf'), array($dirname, XOOPS_ROOT_PATH, XOOPS_TRUST_PATH) );
		} else {
			$options['install_only'] = array();
		}
		if(! isset($options['detailed_version'])) {
			$options['detailed_version'] = '';
		} else {
			$options['detailed_version'] = Xupdate_Utils::toShow($options['detailed_version']);
		}
		if(! isset($options['screen_shot'])) {
			$options['screen_shot'] = '';
		} else {
			$options['screen_shot'] = Xupdate_Utils::toShow($options['screen_shot']);
		}
		return $options;
	}
	
	/**
	 *
	 * @param $format
	 * @param $key
	 * @param $args
	 */
	private function _printf(&$format, $key, $args ) {
		$format = sprintf( $format, $args[0], $args[1], $args[2]);
	}

} // end class

/**
* XoopsObjectGenericHandler extends
*/
class Xupdate_ModuleStoreHandler extends XoopsObjectGenericHandler
{
	public $mTable = '{dirname}_modulestore';

	public $mPrimary = 'id';
	//XoopsSimpleObject
	public $mClass = 'Xupdate_ModuleStore';


	public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
	{
		$this->mTable = strtr($this->mTable,array('{dirname}' => $dirname));
		parent::__construct($db);

	}


	public function &getObjects($criteria = null, $limit = null, $start = null,  $id_as_key = false)
	{
		$ret = array();

		$mObjects =& parent::getObjects($criteria ,$limit,$start, $id_as_key);
		//return $mObjects;

		foreach($mObjects as $key => $mobj){
			$mobj->setmModule(false);//判定用のインストール済みのモジュール情報の保持を追加
			if ($id_as_key) {
				$id = $mobj->getVar('id');
				$ret[$id] = $mobj;// do not add &
			}else{
				$ret[] = $mobj;// do not add &
			}
		}
		return $ret;
	}


} // end class

?>