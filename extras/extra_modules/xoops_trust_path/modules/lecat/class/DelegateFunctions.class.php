<?php
/**
 * @package lecat
 * @version $Id: DelegateFunctions.class.php,v 1.1 2007/05/15 02:35:07 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();


class Lecat_CoolUriDelegate
{
	/**
	 * getNormalUri
	 *
	 * @param string	$uri
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$data_id
	 * @param string	$authType
	 * @param string	$query
	 *
	 * @return	void
	 */ 
	public static function getNormalUri(/*** string ***/ &$uri, /*** string ***/ $dirname, /*** string ***/ $dataname=null, /*** int ***/ $data_id=0, /*** string ***/ $authType=null, /*** string ***/ $query=null)
	{
		$sUri = '/%s/index.php?action=%s%s';
		$lUri = '/%s/index.php?action=%s%s&%s=%d';
		$table = isset($dataname) ? $dataname : 'cat';
	
		$key = $table.'_id';
	
		if(isset($dataname)){
			if($data_id>0){
				if(isset($authType)){
					$uri = sprintf($lUri, $dirname, ucfirst($dataname), ucfirst($authType), $key, $data_id);
				}
				else{
					$uri = sprintf($lUri, $dirname, ucfirst($dataname), 'View', $key, $data_id);
				}
			}
			else{
				if(isset($authType)){
					$uri = sprintf($sUri, $dirname, ucfirst($dataname), ucfirst($authType));
				}
				else{
					$uri = sprintf($sUri, $dirname, ucfirst($dataname), 'List');
				}
			}
			$uri = isset($query) ? $uri.'&'.$query : $uri;
		}
		else{
			if($data_id>0){
				if(isset($authType)){
					die('invalid uri');
				}
				else{
					$handler = Legacy_Utils::getModuleHandler($table, $dirname);
					$key = $handler->mPrimary;
					$uri = sprintf($lUri, $dirname, ucfirst($table).'View', ucfirst($authType), $key, $data_id);
				}
				$uri = isset($query) ? $uri.'&'.$query : $uri;
			}
			else{
				if(isset($authType)){
					die('invalid uri');
				}
				else{
					$uri = sprintf('/%s/', $dirname);
					$uri = isset($query) ? $uri.'index.php?'.$query : $uri;
				}
			}
		}
	}
}

class Lecat_ImageClientDelegate
{
	/**
	 * getClientList
	 *
	 * @param mixed[]	&$list
	 *  @list[]['dirname']
	 *  @list[]['dataname']
	 * @param string	$dirname
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $dirname)
	{
		$dirnames = Legacy_Utils::getDirnameListByTrustDirname('lecat');
	
		//don't call this method multiple times when site owner duplicate.
		static $isCalled = false;
		if($isCalled === true){
			return;
		}
	
		foreach($dirnames as $dir){
			$list[] = array('dirname'=>$dir, 'dataname'=>'cat');
		}
	
		$isCalled = true;
	}
}


class Lecat_DelegateFunctions implements Legacy_iCategoryDelegate
{
	/**
	 * getTitle
	 *
	 * @param string 	&$title
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId
	 *
	 * @return	void
	 */ 
	public static function getTitle(/*** string ***/ &$title, /*** string ***/ $catDir, /*** int ***/ $catId)
	{
		$cat = Legacy_Utils::getModuleHandler('cat', $catDir)->get($catId);
		if(is_object($cat)){
			$title = $cat->get('title');
		}
	}

	/**
	 * getTree
	 * Get category Legacy_AbstractCategoryObject array in parent-child tree order
	 *
	 * @param Legacy_AbstractCategoryObject[] $tree
	 * @param string $catDir	category module's directory name
	 * @param string 	$authType	ex) viewer, editor, manager
	 * @param int 		$catId	get tree under this cat_id
	 * @param string	$module module confinement
	 *
	 * @return	void
	 */ 
	public static function getTree(/*** Legacy_AbstractCategoryObject[] ***/ &$tree, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $catId=0, /*** string ***/ $module=null)
	{
		$handler = Legacy_Utils::getModuleHandler('cat', $catDir);
		if($handler){
			$tree = $handler->getTree(intval($catId));
			$tree = $handler->filterCategory($tree, $authType, Legacy_Utils::getUid(), false);
		}
	}

	/**
	 * getTitleList
	 *
	 * @param string &$titleList
	 * @param string $catDir	category module's directory name
	 *
	 * @return	void
	 */ 
	public static function getTitleList(/*** string[] ***/ &$titleList, /*** string ***/ $catDir)
	{
		$catObjs = Legacy_Utils::getModuleHandler('cat', $catDir)->getObjects();
		foreach(array_keys($catObjs) as $key){
			if($catObjs[$key]->checkModule()){
				$titleList[$catObjs[$key]->get('cat_id')] = $catObjs[$key]->get('title');
			}
		}
	}

	/**
	 * hasPermission
	 *
	 * @param bool &$check
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	module confinement
	 *
	 * @return	void
	 */ 
	public static function hasPermission(/*** bool ***/ &$check, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null)
	{
		$check = false;
		$obj = Legacy_Utils::getModuleHandler('cat', $catDir)->get($catId);
		if($obj){
			$check = $obj->checkPermitByUid($authType, Legacy_Utils::getUid(), $module);
		}
	}

	/**
	 * getParent		Legacy_Category.GetParent
	 * get the parent category object.
	 *
	 * @param Legacy_AbstractCategoryObject &$parent
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId
	 *
	 * @return	void
	 */ 
	public static function getParent(/*** Legacy_AbstractCategoryObject ***/ &$parent, /*** string ***/ $catDir, /*** int ***/ $catId)
	{
		$handler = Legacy_Utils::getModuleHandler('cat', $catDir);
		$pId = $handler->get($catId)->get('p_id');
		$parent = $handler->get($pId);
	}

	/**
	 * getChildren		Legacy_Category.GetChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Legacy_AbstractCategoryObject[] &$children
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId	the parent's category id
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public static function getChildren(/*** Legacy_AbstractCategoryObject[] ***/ &$children, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null)
	{
		$handler = Legacy_Utils::getModuleHandler('cat', $catDir);
		$cat = $handler->get($catId);
		if($cat){
			$cat->loadChildren($module);
			foreach(array_keys($cat->mChildren) as $key){
				$children['catObj'][$key] = $cat->mChildren[$key];
				if($authType){
					if($cat->mChildren[$key]->checkPermitByUserId($authType, Legacy_Utils::getUid()=='true')){
						$children['permit'][$key] = 1;
					}
					else{
						$children['permit'][$key] = 0;
					}
				}
				else{
					$children['permit'][$key] = 1;
				}
			}
		}
	}

	/**
	 * getCatPath		Legacy_Category.GetCatPath
	 * get category path array from top to the given category.
	 *
	 * @param string[] &$catPath
	 *	 $catPath['cat_id']
	 *	 $catPath['title']
	 * @param string $catDir	category module's directory name
	 * @param int $catId		terminal category id in the category path
	 * @param string $order		'ASC' or 'DESC'
	 *
	 * @return	void
	 */ 
	public static function getCatPath(/*** array ***/ &$catPath, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $order='ASC')
	{
		$cat = Legacy_Utils::getModuleHandler('cat', $catDir)->get($catId);
		if(is_object($cat)){
			$cat->loadCatPath();
			//add current category
			array_unshift($cat->mCatPath['cat_id'], $cat->get('cat_id'));
			array_unshift($cat->mCatPath['title'], $cat->get('title'));
		
			//sort category path
			if($order=='ASC' && count($cat->mCatPath)>0){
				$catPath['cat_id'] = array_reverse($cat->mCatPath['cat_id']);
				$catPath['title'] = array_reverse($cat->mCatPath['title']);
			}
			else{
				$catPath = $cat->mCatPath;
			}
		}
	}

	/**
	 * getPermittedIdList		Legacy_Category.GetPermittedIdList
	 * get category ids of permission.
	 *
	 * @param int[]		&$idList
	 * @param string	$catDir	category module's directory name
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param int		$uid
	 * @param int		$catId	get result under this cat_id
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public static function getPermittedIdList(/*** int[] ***/ &$idList, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module=null)
	{
		$handler = Legacy_Utils::getModuleHandler('cat', $catDir);
		$tree = $handler->getTree(intval($catId), $module);
		$tree = $handler->filterCategory($tree, $authType, $uid, true);
		foreach(array_keys($tree) as $key){
			$idList[] = $tree[$key]->get('cat_id');
		}
	}
}

?>
