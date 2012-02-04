<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH .'/class/ModuleStore.class.php';

/**
* XoopsObjectGenericHandler extends
*/
class Xupdate_ModuleStoreHandler extends XoopsObjectGenericHandler
{
//	public $mTable = '{dirname}_modulestore';//TODO
	public $mTable = 'xupdate_modulestore';//TODO {dirname}が使えないのはなぜ?
	public $mPrimary = 'id';
	//XoopsSimpleObject
	public $mClass = 'Xupdate_ModuleStore';

	public $items = array();
	public $store = array();
	public $mSiteModuleObjects = array();

	public function Xupdate_ModuleStoreHandler(&$db) {

		parent::__construct($db);

		//for test start ---------------------------
		$this->_storelist();
		//for test end ---------------------------

		//登録済のデータをマージして保持する
		$this->_setmSiteModuleObjects();
	}

	public function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();

		//for test start ---------------------------
		if ( !is_array($this->items)) {
			return $ret;
		}
		//for test end ---------------------------

		//未登録のデータは自動で登録
		foreach($this->items as $key => $myrow){

			if ($myrow['type'] == 'TrustModule' ){
				$this->_setDataTrustModule($myrow);
			}else{
				$this->_setDataSingleModule($myrow);
			}
		}

		//追加後の表示データ criteria付き
//		$mObjects =& parent::getObjects($criteria ,null,null, false);
		$mObjects =& parent::getObjects($criteria);//TODO bug $id_as_key=true
		foreach($mObjects as $key => $mobj){
			$mobj->setmModule();//判定用のインストール済みのモジュール情報の保持を追加
			if ($id_as_key) {
				$id = $mobj->getVar('id');
				$ret[$id] = $mobj;
			}else{
				$ret[] = $mobj;
			}
		}
		return $ret;
	}

	/**
	 * Delete $obj.
	 *
	 * @return bool
	 */
	function delete(&$obj, $force = false)
	{
		//
		// Because Criteria can generate the most appropriate sentence, use
		// criteria even if this approach is few slow.
		//
		$criteria =new Criteria($this->mPrimary, $obj->getVar($this->mPrimary));//TODO fix get ->getVar
		$sql = "DELETE FROM `" . $this->mTable . "` WHERE " . $this->_makeCriteriaElement4sql($criteria, $obj);

		$result = $force ? $this->db->queryF($sql) : $this->db->query($sql);
		if($result==true) $this->_callDelegate('delete', $obj);

		return $result;
	}

//for test start ---------------------------
	private function _storelist ()
	{
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/modules.ini';
		$this->items = $items;
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/store.ini';
		$this->store = $store;

	}
//for test end ---------------------------

	private function _setmSiteModuleObjects()
	{
		//この該当サイト登録済みデータを全部確認する
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'sid', $this->store['sid'] ) );

		$siteModuleStoreObjects =& parent::getObjects($criteria);
		foreach($siteModuleStoreObjects as $key => $mobj){
			$_ismodulestore = false;
			if ($mobj->getVar('type') == 'TrustModule' ){
				foreach($this->items as $key => $myrow){
					if ($mobj->getVar('trust_dirname') == $myrow['dirname']){
						//重複
						if (!isset($this->mSiteModuleObjects[$mobj->getVar('dirname')])){
							$this->mSiteModuleObjects[$mobj->getVar('dirname')]=$mobj;
							$_ismodulestore = true;
							break;
						}
					}
				}
			}else{
				foreach($this->items as $key => $myrow){
					if ($mobj->getVar('trust_dirname') == '' && $mobj->getVar('dirname') == $myrow['dirname'] ){
						//重複
						if (!isset($this->mSiteModuleObjects[$mobj->getVar('dirname')])){
							$this->mSiteModuleObjects[$mobj->getVar('dirname')]=$mobj;
							$_ismodulestore = true;
							break;
						}
					}
				}
			}

			//このサイトにもう登録されていないデータか重複
			if ($_ismodulestore == false){
				adump($this->mPrimary);
				adump($mobj->getVar($this->mPrimary));
				$this->delete($mobj,true);
			}
		}

	}
/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _storeupdate ($obj , $oldobj)
	{
		$newdata['last_update'] = $obj->getVar('last_update');
		$newdata['version'] = $obj->getVar('version');
		$olddata['last_update'] = $oldobj->getVar('last_update');
		$olddata['version'] = $oldobj->getVar('version');
		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->insert($obj ,true);
		}

	}
	private function _setDataSingleModule($myrow)
	{
		//trusモジュールでない(複製可能なものはどうしよう)
		$mModuleStore = new $this->mClass();
		$mModuleStore->assignVars($myrow);
		$mModuleStore->assignVar('sid',$this->store['sid']);

		$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

		$mModuleStore->setmModule();
		$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

		if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
			$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
			$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
		}else{
			$mModuleStore->setNew();
			$this->insert($mModuleStore ,true);
		}
		unset($mModuleStore);

	}
	private function _setDataTrustModule($myrow)
	{
		//インストール済みの同じtrustモージュールのリストを取得
		$list = Legacy_Utils::getDirnameListByTrustDirname($myrow['dirname']);

		if (empty($list)){
			//インストール済みの同じtrustモージュール無し
			$mModuleStore = new $this->mClass();
			$mModuleStore->assignVars($myrow);
			$mModuleStore->set('sid',$this->store['sid']);

			$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
			$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

			$mModuleStore->setmModule();
			$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );
			if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
				$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
				$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
			}else{
				$mModuleStore->setNew();
				$this->insert($mModuleStore ,true);
			}
			unset($mModuleStore);

		}else{

			$_isrootdirmodule = false;
			foreach($list as $dirname){
				$mModuleStore = new $this->mClass();
				$mModuleStore->assignVars($myrow);
				$mModuleStore->assignVar('sid',$this->store['sid']);

				$mModuleStore->assignVar('dirname',$dirname);
				$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
				$mModuleStore->assignVar('rootdirname',$myrow['dirname']);
				$mModuleStore->setmModule();
				$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

				if ( $dirname == $myrow['dirname'] ){
					$_isrootdirmodule = true;
				}
				if (isset($this->mSiteModuleObjects[$dirname])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$dirname]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
				}else{
					$mModuleStore->setNew();
					$this->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
			//そのままインストールしていない場合、そのまま追加可能なので
			if ( $_isrootdirmodule == false ){
				$mModuleStore = new $this->mClass();
				$mModuleStore->assignVars($myrow);
				$mModuleStore->assignVar('sid',$this->store['sid']);

				$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
				$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

				$mModuleStore->setmModule();
				$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );
				if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
				}else{
					$mModuleStore->setNew();
					$this->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
		}

	}


} // end class

?>