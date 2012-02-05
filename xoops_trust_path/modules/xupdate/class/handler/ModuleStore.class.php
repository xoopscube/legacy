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

	public $items ;
	public $sid = 1;
	public $mSiteModuleObjects = array();

	public function __construct(&$db) {

		parent::__construct($db);

	}

	function prepare($sid = null)
	{
		//for test start ---------------------------
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/modules.ini';
		//このストアーごとのアイテム配列をセットしてください
		//テストなので$sid = 1のみ
		$sid = empty($sid)? 1:  (int)$sid ;
		if ($sid != 1){
			$this->items = array();
		}else{
			$this->items[1] = $items;
		}
		//for test end ---------------------------

		$this->sid = $sid;
		//登録済のデータをマージして保持する
		$this->_setmSiteModuleObjects($sid);

		//未登録のデータは自動で登録
		foreach($this->items as $sid => $items){
			foreach($items as $key => $myrow){

				if ($myrow['type'] == 'TrustModule' ){
					$this->_setDataTrustModule($sid , $myrow);
				}else{
					$this->_setDataSingleModule($sid , $myrow);
				}
			}
		}


	}

	public function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();

		//for test start ---------------------------
		if ( !is_array($this->items)) {
			return $ret;
		}
		//for test end ---------------------------

		//追加後の表示データ criteria付き
//		$mObjects =& parent::getObjects($criteria ,null,null, $id_as_key);
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

	private function _setmSiteModuleObjects($sid = null)
	{
		//この該当サイト登録済みデータを全部確認する
		if ( empty($sid)){
			$siteModuleStoreObjects =& parent::getObjects();
		}else{
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'sid', $sid ) );
			$siteModuleStoreObjects =& parent::getObjects($criteria);
		}
		foreach($siteModuleStoreObjects as $id => $mobj){
			if (isset($this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('dirname')])){
				//データ重複
				$this->delete($mobj,true);
			}else{
				$this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('dirname')]=$mobj;
			}
		}

	}

	private function _setDataSingleModule($sid , $myrow)
	{
		//trusモジュールでない(複製可能なものはどうしよう)
		$mModuleStore = new $this->mClass();
		$mModuleStore->assignVars($myrow);
		$mModuleStore->assignVar('sid', $sid);
//[$mobj->getVar('sid')]
		$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

		$mModuleStore->setmModule();
		$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

		if (isset($this->mSiteModuleObjects[$sid][$myrow['dirname']])){
			$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$myrow['dirname']]->getVar('id') );
			$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$myrow['dirname']]);
		}else{
			$mModuleStore->setNew();
			$this->insert($mModuleStore ,true);
		}
		unset($mModuleStore);

	}
	private function _setDataTrustModule($sid ,$myrow)
	{
		//インストール済みの同じtrustモージュールのリストを取得
		$list = Legacy_Utils::getDirnameListByTrustDirname($myrow['dirname']);

		if (empty($list)){
			//インストール済みの同じtrustモージュール無し
			$mModuleStore = new $this->mClass();
			$mModuleStore->assignVars($myrow);
			$mModuleStore->set('sid',$sid);

			$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
			$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

			$mModuleStore->setmModule();
			$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );
			if (isset($this->mSiteModuleObjects[$sid][$myrow['dirname']])){
				$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$myrow['dirname']]->getVar('id') );
				$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$myrow['dirname']]);
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
				$mModuleStore->assignVar('sid',$sid);

				$mModuleStore->assignVar('dirname',$dirname);
				$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
				$mModuleStore->assignVar('rootdirname',$myrow['dirname']);
				$mModuleStore->setmModule();
				$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

				if ( $dirname == $myrow['dirname'] ){
					$_isrootdirmodule = true;
				}
				if (isset($this->mSiteModuleObjects[$sid][$dirname])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$dirname]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$myrow['dirname']]);
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
				$mModuleStore->assignVar('sid',$sid);

				$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
				$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

				$mModuleStore->setmModule();
				$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

				if (isset($this->mSiteModuleObjects[$sid][$myrow['dirname']])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$sid][$myrow['dirname']]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$sid][$myrow['dirname']]);
				}else{
					$mModuleStore->setNew();
					$this->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
		}

	}

/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _storeupdate ($obj , $oldobj)
	{
		$newdata['type'] = $obj->getVar('type');
		$newdata['last_update'] = $obj->getVar('last_update');
		$newdata['version'] = $obj->getVar('version');
		$olddata['type'] = $oldobj->getVar('type');
		$olddata['last_update'] = $oldobj->getVar('last_update');
		$olddata['version'] = $oldobj->getVar('version');
		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->insert($obj ,true);
		}

	}

} // end class

?>