<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Legacy_AbstractClientObjectHandler
**/
abstract class Legacy_AbstractClientObjectHandler extends XoopsObjectGenericHandler
{
	protected $_mClientField = array('title'=>'title', 'category'=>'category_id', 'posttime'=>'posttime');
	protected $_mClientConfig = array('tag'=>'tag_dirname', 'image'=>'use_image', 'workflow'=>'use_workflow', 'activity'=>'use_activity');

	/**
	 * _getTagList
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	string[]
	 */
	protected function _getTagList(/*** XoopsSimpleObject ***/ $obj)
	{
	}

	/**
	 * _getTempImageList
	 *
	 * @param void
	 *
	 * @return	string[]	//$_FILES[$formName]['tmp_name']
	 */
	protected function _getTempImageList()
	{
	}

	/**
	 * insert data to table
	 *
	 * @param XoopsSimpleObject	$obj
	 * @param bool	$force
	 *
	 * @return	bool
	 */
	public function insert(/*** XoopsSimpleObject ***/ $obj, /*** bool ***/ $force=false)
	{
		$ret = parent::insert($obj, $force);
		$this->_setClientData($obj);
	
		return $ret;
	}

	/**
	 * delete data from table
	 *
	 * @param XoopsSimpleObject	$obj
	 * @param bool	$force
	 *
	 * @return	bool
	 */
	public function delete(/*** XoopsSimpleObject ***/ $obj, /*** bool ***/ $force=false)
	{
		$ret = parent::delete($obj, $force);
		$this->_deleteClientData($obj);
	
		return $ret;
	}

	/**
	 * set client data: tag, image, activity, workflow
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _setClientData(/*** XoopsSimpleObject ***/ $obj)
	{
		$handler = xoops_gethandler('config');
		$conf = $handler->getConfigsByDirname($obj->getDirname());
	
		$ret = true;
		if($this->_isActivityClient($conf)===true){
			if($this->_setActivity($obj)===false){
				$ret = false;
			}
		}
	
		if($this->_isTagClient($conf)===true){
			if($this->_setTags($obj, $conf[$this->_mClientConfig['tag']])===false){
				$ret = false;
			}
		}
	
		if($this->_isWorkflowClient($conf)===true){
			$this->_setWorkflow($obj);
		}
	
		if($this->_isImageClient($conf)===true){
			if($this->_setImages($obj)===false){
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * delete client data: tag, activity, workflow, image
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _deleteClientData(/*** XoopsSimpleObject ***/ $obj)
	{
		$handler = xoops_gethandler('config');
		$conf = $handler->getConfigsByDirname($obj->getDirname());
	
		$ret = true;
		if($this->_isActivityClient($conf)===true){
			if($this->_deleteActivity($obj)===false){
				$ret = false;
			}
		}
	
		if($this->_isTagClient($conf)===true){
			if($this->_deleteTags($obj, $tagDirname)===false){
				$ret = false;
			}
		}
	
		if($this->_isWorkflowClient($conf)===true){
			$ret = $this->_deleteWorkflow($obj);
		}
	
		if($this->_isImageClient($conf)===true){
			if($this->_deleteImages($obj)===false){
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * set activity
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _setActivity(/*** XoopsSimpleObject ***/ $obj)
	{
		$ret = false;
		XCube_DelegateUtils::call(
			'Legacy_Activity.AddActivity', 
			new XCube_Ref($ret),
			$obj->get('uid'),
			$obj->get($this->_mClientField['category']),
			$obj->getDirname(), 
			$this->getDataname(), 
			$obj->get($this->mPrimary),
			$obj->get($this->_mClientField['posttime'])
		);
		return $ret;
	}

	/**
	 * set tags
	 *
	 * @param XoopsSimpleObject	$obj
	 * @param string	$tagDirname
	 *
	 * @return	bool
	 */
	protected function _setTags(/*** XoopsSimpleObject ***/ $obj, /*** string ***/ $tagDirname)
	{
		$ret = false;
		XCube_DelegateUtils::call('Legacy_Tag.'.$tagDirname.'.SetTags', 
			new XCube_Ref($ret), 
			$tagDirname, 
			$obj->getDirname(),
			$this->getDataname(),
			$obj->get($this->mPrimary), 
			$obj->get($this->_mClientField['posttime']), 
			$this->_getTagList($obj)
		);
		return $ret;
	}

	/**
	 * set workflow
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	void
	 */
	protected function _setWorkflow(/*** XoopsSimpleObject ***/ $obj)
	{
		XCube_DelegateUtils::call(
			'Legacy_Workflow.AddItem', 
			$obj->getShow($this->mPrimary), 
			$obj->getDirname(), 
			$this->getDataname(), 
			$obj->get($obj->getPrimary()), 
			Legacy_Utils::renderUri($obj->getDirname(), $this->getDataname(), $obj->get($this->mPrimary))
		);
	}

	/**
	 * upload and set images
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _setImages(/*** XoopsSimpleObject ***/ $obj)
	{
		$ret = true;
		$imageList = $this->_getTempImageList($obj);
		foreach(array_keys($imageList) as $key){
			$imageObjs = array();
			XCube_DelegateUtils::call('Legacy_Image.GetImageObjects', new XCube_Ref($imageObjs), $this->getDirname(), $this->getDataname(), $obj->get($this->mPrimary), $key+1);
			if(count($imageObjs)>0){
				$image = array_shift($imageObjs);
			}
			else{
				$image = null;
				XCube_DelegateUtils::call('Legacy_Image.CreateImageObject', new XCube_Ref($image));
				$image->set('title', $obj->get($this->_mClientField['title']));
				$image->set('uid', Legacy_Utils::getUid());
				$image->set('dirname', $this->getDirname());
				$image->set('dataname', $this->getDataname());
				$image->set('data_id', $obj->get($this->mPrimary));
				$image->set('num', $key+1);
			}
		
			$result = false;
			XCube_DelegateUtils::call('Legacy_Image.SaveImage', new XCube_Ref($result), $imageList[$key], $image);
			if($result===false){
				$ret = false;
			}
		}
	
		return $ret;
	}

	/**
	 * delete activity
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _deleteActivity(/*** XoopsSimpleObject ***/ $obj)
	{
		$ret = false;
		XCube_DelegateUtils::call('Legacy_Activity.DeleteActivity', new XCube_Ref($ret), $obj->getDirname(), $this->getDataname(), $obj->get($this->mPrimary));
		return $ret;
	}

	/**
	 * delete tags
	 *
	 * @param XoopsSimpleObject	$obj
	 * @param string	$tagDirname
	 *
	 * @return	bool
	 */
	protected function _deleteTags(/*** XoopsSimpleObject ***/ $obj, /*** string ***/ $tagDirname)
	{
		$ret = false;
		XCube_DelegateUtils::call(
			'Legacy_Tag.'.$tagDirname.'.SetTags',
			new XCube_Ref($ret),
			$tagDirname,
			$obj->getDirname(),
			$this->getDataname(),
			$obj->get($this->mPrimary),
			$obj->get($this->_mClientField['posttime']),
			array()
		);
		return $ret;
	}

	/**
	 * delete workflow
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	void
	 */
	protected function _deleteWorkflow(/*** XoopsSimpleObject ***/ $obj)
	{
		XCube_DelegateUtils::call('Legacy_Workflow.DeleteItem', $obj->getDirname(), $this->getDataname(), $obj->get($this->mPrimary));
	}

	/**
	 * delete images
	 *
	 * @param XoopsSimpleObject	$obj
	 *
	 * @return	bool
	 */
	protected function _deleteImages(/*** XoopsSimpleObject ***/ $obj)
	{
		$imageObjs = array();
		XCube_DelegateUtils::call('Legacy_Image.GetImageObjects', new XCube_Ref($imageObjs), $obj->getDirname(), $this->getDataname(), $obj->get($obj->getPrimary()));
		$ret = true;
		foreach($imageObjs as $image){
			$result = false;
			XCube_DelegateUtils::call('Legacy_Image.DeleteImage', new XCube_Ref($result), $image);
			if($result===false){
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * check if use Legacy_Activity
	 *
	 * @param mixed[]	$conf
	 *
	 * @return	bool
	 */
	protected function _isActivityClient(/*** mixed[] ***/ $conf)
	{
		return $conf[$this->_mClientConfig['activity']]==1 ? true : false;
	}

	/**
	 * check if use Legacy_Tag
	 *
	 * @param mixed[]	$conf
	 *
	 * @return	bool
	 */
	protected function _isTagClient(/*** mixed[] ***/ $conf)
	{
		return $conf[$this->_mClientConfig['tag']] ? true : false;
	}

	/**
	 * check if use Legacy_Workflow
	 *
	 * @param mixed[]	$conf
	 *
	 * @return	bool
	 */
	protected function _isWorkflowClient(/*** mixed[] ***/ $conf)
	{
		return $conf[$this->_mClientConfig['workflow']]==1 ? true : false;
	}

	/**
	 * check if use Legacy_Image
	 *
	 * @param mixed[]	$conf
	 *
	 * @return	bool
	 */
	protected function _isImageClient(/*** mixed[] ***/ $conf)
	{
		return $conf[$this->_mClientConfig['image']]==1 ? true : false;
	}
}

?>
