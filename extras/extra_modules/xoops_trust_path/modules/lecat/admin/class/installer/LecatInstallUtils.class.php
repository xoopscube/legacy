<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

if(class_exists('Lecat_InstallUtils'))
{
	return;
}

/**
 * Lecat_InstallUtils
**/
class Lecat_InstallUtils
{
	/**
	 * installSQLAutomatically
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installSQLAutomatically(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$sqlFileInfo =& $module->getInfo('sqlfile');
		if(!isset($sqlFileInfo[XOOPS_DB_TYPE]))
		{
			return true;
		}
		$sqlFile = $sqlFileInfo[XOOPS_DB_TYPE];
	
		$dirname = $module->getVar('dirname');
		$sqlFilePath = sprintf('%s/%s/%s',XOOPS_MODULE_PATH,$dirname,$sqlFile);
		if(!file_exists($sqlFilePath))
		{
			$sqlFilePath = sprintf(
				'%s/modules/%s/%s',
				XOOPS_TRUST_PATH,
				$module->modinfo['trust_dirname'],
				$sqlFile
			);
		}
	
		require_once XOOPS_MODULE_PATH . '/legacy/admin/class/Legacy_SQLScanner.class.php';    // TODO will be use other class?
		$scanner =new Legacy_SQLScanner();
		$scanner->setDB_PREFIX(XOOPS_DB_PREFIX);
		$scanner->setDirname($dirname);
		if(!$scanner->loadFile($sqlFilePath))
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_SQL_FILE_NOT_FOUND,
					$sqlFile
				)
			);
			return false;
		}
	
		$scanner->parse();
		$root =& XCube_Root::getSingleton();
		$db =& $root->mController->getDB();
	
		foreach($scanner->getSQL() as $sql)
		{
			if(!$db->query($sql))
			{
				$log->addError($db->error());
				return false;
			}
		}
		$log->addReport(_MI_LECAT_INSTALL_MSG_DB_SETUP_FINISHED);
		return true;
	}

	/**
	 * DBquery
	 * 
	 * @param	string	$query
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function DBquery(/*** string ***/ $query,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		require_once XOOPS_MODULE_PATH . '/legacy/admin/class/Legacy_SQLScanner.class.php';    // TODO will be use other class?
		$scanner =new Legacy_SQLScanner();
		$scanner->setDB_PREFIX(XOOPS_DB_PREFIX);
		$scanner->setDirname($module->get('dirname'));
		$scanner->setBuffer($query);
		$scanner->parse();
		$sqls = $scanner->getSQL();
	
		$root =& XCube_Root::getSingleton();
	
		$successFlag = true;
		foreach($sqls as $sql)
		{
			if($root->mController->mDB->query($sql))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_SQL_SUCCESS,
						$sql
					)
				);
			}
			else
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_SQL_ERROR,
						$sql
					)
				);
				$successFlag = false;
			}
		}
		return $successFlag;
	}

	/**
	 * replaceDirname
	 * 
	 * @param	string	$from
	 * @param	string	$dirname
	 * @param	string	$trustDirname
	 * 
	 * @return	{string 'public',string 'trust'}
	**/
	public static function replaceDirname(/*** string ***/ $from,/*** string ***/ $dirname,/*** string ***/ $trustDirname = null)
	{
		return array(
			'public' => str_replace('{dirname}',$dirname,$from),
			'trust' => ($trustDirname != null) ? str_replace('{dirname}',$trustDirname,$from) : null
		);
	}

	/**
	 * readTemplateFile
	 * 
	 * @param	string	$dirname
	 * @param	string	$trustDirname
	 * @param	string	$filename
	 * @param	bool  $isBlock
	 * 
	 * @return	string
	**/
	public static function readTemplateFile(/*** string ***/ $dirname,/*** string ***/ $trustDirname,/*** string ***/ $filename,/*** bool ***/ $isBlock = false)
	{
		$filePath = sprintf(
			'%s/%s/templates/%s%s',
			XOOPS_MODULE_PATH,
			$dirname,
			($isBlock ? 'blocks/' : ''),
			$filename
		);
	
		if(!file_exists($filePath))
		{
			$filePath = sprintf(
				'%s/modules/%s/templates/%s%s',
				XOOPS_TRUST_PATH,
				$trustDirname,
				($isBlock ? 'blocks/' : ''),
				$filename
			);
			if(!file_exists($filePath))
			{
				return false;
			}
		}
	
		if(!($lines = file($filePath)))
		{
			return false;
		}
	
		$tplData = '';
		foreach($lines as $line)
		{
			$tplData .= str_replace("\n","\r\n",str_replace("\r\n","\n",$line));
		}
	
		return $tplData;
	}

	/**
	 * installAllOfModuleTemplates
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function installAllOfModuleTemplates(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$templates =& $module->getInfo('templates');
		if(is_array($templates) && count($templates) > 0)
		{
			foreach($templates as $template)
			{
				Lecat_InstallUtils::installModuleTemplate($module,$template,$log);
			}
		}
	}

	/**
	 * installModuleTemplate
	 * 
	 * @param	XoopsModule  &$module
	 * @param	string[]  $template
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installModuleTemplate(/*** XoopsModule ***/ &$module,/*** string[] ***/ $template,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$dirname = $module->getVar('dirname');
		$trustDirname =& $module->getInfo('trust_dirname');
		$tplHandler =& Lecat_Utils::getXoopsHandler('tplfile');
		$filename	=  Lecat_InstallUtils::replaceDirname(trim($template['file']),$dirname,$trustDirname);
		$tplData	=  Lecat_InstallUtils::readTemplateFile($dirname,$trustDirname,$filename['trust']);
	
		if($tplData == false)
		{
			return false;
		}
	
		$tplFile =& $tplHandler->create();
		$tplFile->setVar('tpl_refid'	   ,$module->getVar('mid'));
		$tplFile->setVar('tpl_lastimported',0);
		$tplFile->setVar('tpl_lastmodified',time());
		$tplFile->setVar('tpl_type' 	   ,(substr($filename['trust'],-4) == '.css') ? 'css' : 'module');
		$tplFile->setVar('tpl_source'	   ,$tplData,true);
		$tplFile->setVar('tpl_module'	   ,$module->getVar('dirname'));
		$tplFile->setVar('tpl_tplset'	   ,'default');
		$tplFile->setVar('tpl_file' 	   ,$filename['public'],true);
		$tplFile->setVar('tpl_desc' 	   ,isset($template['desctiption']) ? $template['description'] : '',true);
	
		if($tplHandler->insert($tplFile))
		{
			$log->addReport(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_MSG_TPL_INSTALLED,
					$filename['public']
				)
			);
		}
		else
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_TPL_INSTALLED,
					$filename['public']
				)
			);
			return false;
		}
	
		return true;
	}

	/**
	 * uninstallAllOfModuleTemplates
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * @param	bool  $defaultOnly
	 * 
	 * @return	void
	**/
	public static function uninstallAllOfModuleTemplates(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log,/*** bool ***/ $defaultOnly = true)
	{
		$tplHandler   =& Lecat_Utils::getXoopsHandler('tplfile');
	
		$delTemplates =& $tplHandler->find($defaultOnly ? 'default' : null,'module',$module->get('mid'));
	
		if(is_array($delTemplates) && count($delTemplates) > 0)
		{
			$xoopsTpl =new XoopsTpl();
			$xoopsTpl->clear_cache(null,'mod_' . $module->get('dirname'));
			foreach($delTemplates as $tpl)
			{
				if(!$tplHandler->delete($tpl))
				{
					$log->addError(
						XCube_Utils::formatString(
							_MI_LECAT_INSTALL_ERROR_TPL_UNINSTALLED,
							$tpl->get('tpl_file')
						)
					);
				}
			}
		}
	}

	/**
	 * installAllOfBlocks
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installAllOfBlocks(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$blocks =& $module->getInfo('blocks');
		if(is_array($blocks) && count($blocks) > 0)
		{
			foreach($blocks as $block)
			{
				$newBlock =& Lecat_InstallUtils::createBlockByInfo($module,$block);
				Lecat_InstallUtils::installBlock($module,$newBlock,$block,$log);
			}
		}
		return true;
	}

	/**
	 * &createBlockByInfo
	 * 
	 * @param	XoopsModule  &$module
	 * @param	string[]  $block
	 * 
	 * @return	XoopsBlock
	**/
	public static function &createBlockByInfo(/*** XoopsModule ***/ &$module,/*** string[] ***/ $block)
	{
		$visible = isset($block['visible']) ?
			$block['visible'] :
			(isset($block['visible_any']) ? $block['visible_any'] : 0);
		$filename = isset($block['template']) ?
			Lecat_InstallUtils::replaceDirname($block['template'],$module->get('dirname')) :
			null;
	
		$blockHandler =& Lecat_Utils::getXoopsHandler('block');
		$blockObj =& $blockHandler->create();
	
		$blockObj->set('mid',$module->getVar('mid'));
		$blockObj->set('options',isset($block['options']) ? $block['options'] : null);
		$blockObj->set('name',$block['name']);
		$blockObj->set('title',$block['name']);
		$blockObj->set('block_type','M');
		$blockObj->set('c_type','1');
		$blockObj->set('isactive',1);
		$blockObj->set('dirname',$module->getVar('dirname'));
		$blockObj->set('func_file',$block['file']);
		$blockObj->set('show_func','cl::' . $block['class']);
		$blockObj->set('template',$filename['public']);
		$blockObj->set('last_modified',time());
		$blockObj->set('visible',$visible);
		$blockObj->set('func_num',intval($block['func_num']));
		return $blockObj;
	}

	/**
	 * installBlock
	 * 
	 * @param	XoopsModule  &$module
	 * @param	XoopsBlock	&$blockObj
	 * @param	string[]  &$block
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installBlock(/*** XoopsModule ***/ &$module,/*** XoopsBlock ***/ &$blockObj,/*** string[] ***/ &$block,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$isNew = $blockObj->isNew();
		$blockHandler =& Lecat_Utils::getXoopsHandler('block');
		$autoLink = isset($block['show_all_module']) ? $block['show_all_module'] : false;
	
		if(!$blockHandler->insert($blockObj,$autoLink))
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_BLOCK_INSTALLED,
					$blockObj->getVar('name')
				)
			);
			return false;
		}
	
		$log->addReport(
			XCube_Utils::formatString(
				_MI_LECAT_INSTALL_MSG_BLOCK_INSTALLED,
				$blockObj->getVar('name')
			)
		);
	
		Lecat_InstallUtils::installBlockTemplate($blockObj,$module,$log);
	
		if(!$isNew)
		{
			return true;
		}
	
		if($autoLink)
		{
			$sql = sprintf(
				'insert into `%s` (`block_id`,`module_id`) values (%d,0);',
				$blockHandler->db->prefix('block_module_link'),
				$blockObj->getVar('bid')
			);
			if(!$blockHandler->db->query($sql))
			{
				$log->addWarning(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_BLOCK_COULD_NOT_LINK,
						$blockObj->getVar('name')
					)
				);
			}
		}
	
		$gpermHandler =& Lecat_Utils::getXoopsHandler('groupperm');
		$perm =& $gpermHandler->create();
		$perm->setVar('gperm_itemid',$blockObj->getVar('bid'));
		$perm->setVar('gperm_name','block_read');
		$perm->setVar('gperm_modid',1);
		if(isset($block['visible_any']) && $block['visible_any'])
		{
			$memberHandler =& Lecat_Utils::getXoopsHandler('member');
			$groups =& $memberHandler->getGroups();
			foreach($groups as $group)
			{
				$perm->setVar('gperm_groupid',$group->getVar('groupid'));
				$perm->setNew();
				if(!$gpermHandler->insert($perm))
				{
					$log->addWarning(
						XCube_Utils::formatString(
							_MI_LECAT_INSTALL_ERROR_PERM_COULD_NOT_SET,
							$blockObj->getVar('name')
						)
					);
				}
			}
		}
		else
		{
			$root =& XCube_Root::getSingleton();
			$groups = $root->mContext->mXoopsUser->getGroups();
			foreach($groups as $group)
			{
				$perm->setVar('gperm_groupid',$group);
				$perm->setNew();
				if(!$gpermHandler->insert($perm))
				{
					$log->addWarning(
						XCube_Utils::formatString(
							_MI_LECAT_INSTALL_ERROR_BLOCK_PERM_SET,
							$blockObj->getVar('name')
						)
					);
				}
			}
		}
	
		return true;
	}

	/**
	 * installBlockTemplate
	 * 
	 * @param	XoopsBlock	&$block
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installBlockTemplate(/*** XoopsBlock ***/ &$block,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		if($block->get('template') == null)
		{
			return true;
		}
	
		$info =& $module->getInfo('blocks');
		$filename = Lecat_InstallUtils::replaceDirname(
			$info[$block->get('func_num')]['template'],
			$module->get('dirname'),
			$module->getInfo('trust_dirname')
		);
		$tplHandler =& Lecat_Utils::getXoopsHandler('tplfile');
	
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('tpl_type','block'));
		$cri->add(new Criteria('tpl_tplset','default'));
		$cri->add(new Criteria('tpl_module',$module->get('dirname')));
		$cri->add(new Criteria('tpl_file',$filename['public']));
	
		$tpls =& $tplHandler->getObjects($cri);
	
		if(count($tpls) > 0)
		{
			$tplFile =& $tpls[0];
		}
		else
		{
			$tplFile =& $tplHandler->create();
			$tplFile->set('tpl_refid',$block->get('bid'));
			$tplFile->set('tpl_tplset','default');
			$tplFile->set('tpl_file',$filename['public']);
			$tplFile->set('tpl_module',$module->get('dirname'));
			$tplFile->set('tpl_type','block');
			//$tplFile->set('tpl_desc',$block->get('description'));
			$tplFile->set('tpl_lastimported',0);
		}
	
		$tplSource = Lecat_InstallUtils::readTemplateFile(
			$module->get('dirname'),
			$module->getInfo('trust_dirname'),
			$filename['trust'],
			true
		);
	
		$tplFile->set('tpl_source',$tplSource);
		$tplFile->set('tpl_lastmodified',time());
		if($tplHandler->insert($tplFile))
		{
			$log->addReport(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_MSG_BLOCK_TPL_INSTALLED,
					$filename['public']
				)
			);
			return true;
		}
	
		$log->addError(
			XCube_Utils::formatString(
				_MI_LECAT_INSTALL_ERROR_BLOCK_TPL_INSTALLED,
				$filename['public']
			)
		);
		return false;
	}

	/**
	 * uninstallAllOfBlocks
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function uninstallAllOfBlocks(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$successFlag = true;
	
		$blockHandler =& Lecat_Utils::getXoopsHandler('block');
		$gpermHandler =& Lecat_Utils::getXoopsHandler('groupperm');
		$cri =new Criteria('mid',$module->get('mid'));
		$blocks =& $blockHandler->getObjectsDirectly($cri);
	
		foreach($blocks as $block)
		{
			if($blockHandler->delete($block))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_BLOCK_UNINSTALLED,
						$block->get('name')
					)
				);
			}
			else
			{
				$log->addWarning(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_BLOCK_UNINSTALLED,
						$block->get('name')
					)
				);
				$successFlag = false;
			}
			
			$cri =new CriteriaCompo();
			$cri->add(new Criteria('gperm_name','block_read'));
			$cri->add(new Criteria('gperm_itemid',$block->get('bid')));
			$cri->add(new Criteria('gperm_modid',1));
			if(!$gpermHandler->deleteAll($cri))
			{
				$log->addWarning(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_BLOCK_PERM_DELETE,
						$block->get('name')
					)
				);
				$successFlag = false;
			}
		}
	
		return $successFlag;
	}

	/**
	 * smartUpdateAllOfBlocks
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function smartUpdateAllOfBlocks(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$dirname = $module->get('dirname');
	
		$fileReader =new Legacy_ModinfoX2FileReader($dirname);
		$dbReader =new Legacy_ModinfoX2DBReader($dirname);
	
		$blocks =& $dbReader->loadBlockInformations();
		$blocks->update($fileReader->loadBlockInformations());
	
		foreach($blocks->mBlocks as $block)
		{
			switch($block->mStatus)
			{
				case LEGACY_INSTALLINFO_STATUS_LOADED:
					Lecat_InstallUtils::updateBlockTemplateByInfo($block,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_UPDATED:
					Lecat_InstallUtils::updateBlockByInfo($block,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_NEW:
					Lecat_InstallUtils::installBlockByInfo($block,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_DELETED:
					Lecat_InstallUtils::uninstallBlockByFuncNum($block->mFuncNum,$module,$log);
					break;
				default:
					break;
			}
		}
	}

	/**
	 * updateBlockTemplateByInfo
	 * 
	 * @param	Legacy_BlockInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function updateBlockTemplateByInfo(/*** Legacy_BlockInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$blockHandler =& Lecat_Utils::getModuleHandler('newblocks','legacy');
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('dirname',$module->get('dirname')));
		$cri->add(new Criteria('func_num',$info->mFuncNum));
		$blocks =& $blockHandler->getObjects($cri);
	
		foreach($blocks as $block)
		{
			Lecat_InstallUtils::uninstallBlockTemplate($block,$module,$log,true);
			Lecat_InstallUtils::installBlockTemplate($block,$module,$log);
		}
	}

	/**
	 * updateBlockByInfo
	 * 
	 * @param	Legacy_BlockInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function updateBlockByInfo(/*** Legacy_BlockInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$blockHandler =& Lecat_Utils::getModuleHandler('newblocks','legacy');
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('dirname',$module->get('dirname')));
		$cri->add(new Criteria('func_num',$info->mFuncNum));
		$blocks =& $blockHandler->getObjects($cri);
	
		foreach($blocks as $block)
		{
			$filename = Lecat_InstallUtils::replaceDirname(
				$info->mTemplate,
				$module->get('dirname'),
				$module->getInfo('trust_dirname')
			);
			$block->set('options',$info->mOptions);
			$block->set('name',$info->mName);
			$block->set('func_file',$info->mFuncFile);
			$block->set('show_func',$info->mShowFunc);
			//$block->set('edit_func',$info->mEditFunc);
			$block->set('template',$filename['public']);
			if($blockHandler->insert($block))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_BLOCK_UPDATED,
						$block->get('name')
					)
				);
			}
			else
			{
				$log->addError(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_BLOCK_UPDATED,
						$block->get('name')
					)
				);
			}
			Lecat_InstallUtils::uninstallBlockTemplate($block,$module,$log,true);
			Lecat_InstallUtils::installBlockTemplate($block,$module,$log);
		}
	}

	/**
	 * installBlockByInfo
	 * 
	 * @param	Legacy_BlockInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installBlockByInfo(/*** Legacy_BlockInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$filename = Lecat_InstallUtils::replaceDirname(
			$info->mTemplate,
			$module->get('dirname'),
			$module->getInfo('trust_dirname')
		);
	
		$blockHandler =& Lecat_Utils::getXoopsHandler('block');
	
		$block =& $blockHandler->create();
		$block->set('mid',$module->get('mid'));
		$block->set('func_num',$info->mFuncNum);
		$block->set('options',$info->mOptions);
		$block->set('name',$info->mName);
		$block->set('title',$info->mName);
		$block->set('dirname',$module->get('dirname'));
		$block->set('func_file',$info->mFuncFile);
		$block->set('show_func',$info->mShowFunc);
		//$block->set('edit_func',$info->mEditFunc);
		$block->set('template',$filename['public']);
		$block->set('block_type','M');
		$block->set('c_type',1);
	
		if(!$blockHandler->insert($block))
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_BLOCK_INSTALLED,
					$block->get('name')
				)
			);
			return false;
		}
	
		$log->addReport(
			XCube_Utils::formatString(
				_MI_LECAT_INSTALL_MSG_BLOCK_INSTALLED,
				$block->get('name')
			)
		);
	
		Lecat_InstallUtils::installBlockTemplate($block,$module,$log);
		return true;
	}

	/**
	 * uninstallBlockByFuncNum
	 * 
	 * @param	int  $func_num
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function uninstallBlockByFuncNum(/*** int ***/ $func_num,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$blockHandler =& Lecat_Utils::getModuleHandler('newblocks','legacy');
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('dirname',$module->get('dirname')));
		$cri->add(new Criteria('func_num',$func_num));
		$blocks =& $blockHandler->getObjects($cri);
	
		$successFlag = true;
		foreach($blocks as $block)
		{
			if($blockHandler->delete($block))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_BLOCK_UNINSTALLED,
						$block->get('name')
					)
				);
			}
			else
			{
				$log->addError(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_BLOCK_UNINSTALLED,
						$block->get('name')
					)
				);
				$successFlag = false;
			}
		}
		return $successFlag;
	}

	/**
	 * uninstallBlockTemplate
	 * 
	 * @param	XoopsBlock	&$block
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * @param	bool  $defaultOnly
	 * 
	 * @return	bool
	**/
	public static function uninstallBlockTemplate(/*** XoopsBlock ***/ &$block,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log,/*** bool ***/ $defaultOnly = false)
	{
		$tplHandler =& Lecat_Utils::getXoopsHandler('tplfile');
		$delTemplates =& $tplHandler->find($defaultOnly ? 'default' : null,'block',$module->get('mid'),$module->get('dirname'),$block->get('template'));
	
		if(is_array($delTemplates) && count($delTemplates) > 0)
		{
			foreach($delTemplates as $tpl)
			{
				if(!$tplHandler->delete($tpl))
				{
					$log->addError(
						XCube_Utils::formatString(
							_MI_LECAT_INSTALL_ERROR_TPL_UNINSTALLED,
							$tpl->get('tpl_file')
						)
					);
				}
			}
		}
	
		$log->addReport(
			XCube_Utils::formatString(
				_MI_LECAT_INSTALL_MSG_BLOCK_TPL_UNINSTALLED,
				$block->get('template')
			)
		);
		return true;
	}

	/**
	 * installAllOfConfigs
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function installAllOfConfigs(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$successFlag = true;
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
		$fileReader =new Legacy_ModinfoX2FileReader($module->get('dirname'));	 // TODO will be use other class?
		$preferences =& $fileReader->loadPreferenceInformations();
	
		foreach($preferences->mPreferences as $info)
		{
			$config =& $configHandler->createConfig();
			$config->set('conf_modid',$module->get('mid'));
			$config->set('conf_catid',0);
			$config->set('conf_name',$info->mName);
			$config->set('conf_title',$info->mTitle);
			$config->set('conf_desc',$info->mDescription);
			$config->set('conf_formtype',$info->mFormType);
			$config->set('conf_valuetype',$info->mValueType);
			$config->setConfValueForInput($info->mDefault);
			$config->set('conf_order',$info->mOrder);
	
			if(count($info->mOption->mOptions) > 0)
			{
				foreach($info->mOption->mOptions as $opt)
				{
					$option = $configHandler->createConfigOption();
					$option->set('confop_name',$opt->mName);
					$option->set('confop_value',$opt->mValue);
					$config->setConfOptions($option);
					unset($option);
				}
			}
	
			if($configHandler->insertConfig($config))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_CONFIG_ADDED,
						$config->get('conf_name')
					)
				);
			}
			else
			{
				$log->addError(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_CONFIG_ADDED,
						$config->get('conf_name')
					)
				);
				$successFlag = false;
			}
		}
	
		return $successFlag;
	}

	/**
	 * installConfigByInfo
	 * 
	 * @param	Legacy_PreferenceInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function installConfigByInfo(/*** Legacy_PreferenceInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
		$config =& $configHandler->createConfig();
		$config->set('conf_modid',$module->get('mid'));
		$config->set('conf_catid',0);
		$config->set('conf_name',$info->mName);
		$config->set('conf_title',$info->mTitle);
		$config->set('conf_desc',$info->mDescription);
		$config->set('conf_formtype',$info->mFormType);
		$config->set('conf_valuetype',$info->mValueType);
		$config->setConfValueForInput($info->mDefault);
		$config->set('conf_order',$info->mOrder);
	
		if(count($info->mOption->mOptions) > 0)
		{
			foreach($info->mOption->mOptions as $opt)
			{
				$option = $configHandler->createConfigOption();
				$option->set('confop_name',$opt->mName);
				$option->set('confop_value',$opt->mValue);
				$config->setConfOptions($option);
				unset($option);
			}
		}
	
		if($configHandler->insertConfig($config))
		{
			$log->addReport(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_MSG_CONFIG_ADDED,
					$config->get('conf_name')
				)
			);
		}
		else
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_CONFIG_ADDED,
					$config->get('conf_name')
				)
			);
		}
		
	}

	/**
	 * uninstallAllOfConfigs
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function uninstallAllOfConfigs(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		if($module->get('hasconfig') == 0)
		{
			return true;
		}
	
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
		$configs =& $configHandler->getConfigs(new Criteria('conf_modid',$module->get('mid')));
	
		if(count($configs) == 0)
		{
			return true;
		}
	
		$sucessFlag = true;
		foreach($configs as $config)
		{
			if($configHandler->deleteConfig($config))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_CONFIG_DELETED,
						$config->getVar('conf_name')
					)
				);
			}
			else
			{
				$log->addWarning(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_CONFIG_DELETED,
						$config->getVar('conf_name')
					)
				);
				$sucessFlag = false;
			}
		}
		return $sucessFlag;
	}

	/**
	 * uninstallConfigByOrder
	 * 
	 * @param	int  $order
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function uninstallConfigByOrder(/*** int ***/ $order,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
	
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('conf_modid',$module->get('mid')));
		$cri->add(new Criteria('conf_catid',0));
		$cri->add(new Criteria('conf_order',$order));
		$configs = $configHandler->getConfigs($cri);
	
		foreach($configs as $config)
		{
			if($configHandler->deleteConfig($config))
			{
				$log->addReport(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_MSG_CONFIG_DELETED,
						$config->get('conf_name')
					)
				);
			}
			else
			{
				$log->addError(
					XCube_Utils::formatString(
						_MI_LECAT_INSTALL_ERROR_CONFIG_DELETED,
						$config->get('conf_name')
					)
				);
			}
		}
	}

	/**
	 * smartUpdateAllOfConfigs
	 * 
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	void
	**/
	public static function smartUpdateAllOfConfigs(/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$dirname = $module->get('dirname');
	
		$fileReader =new Legacy_ModinfoX2FileReader($dirname);
		$dbReader =new Legacy_ModinfoX2DBReader($dirname);
	
		$configs  =& $dbReader->loadPreferenceInformations();
		$configs->update($fileReader->loadPreferenceInformations());
	
		foreach($configs->mPreferences as $config)
		{
			switch($config->mStatus)
			{
				case LEGACY_INSTALLINFO_STATUS_UPDATED:
					Lecat_InstallUtils::updateConfigByInfo($config,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED:
					Lecat_InstallUtils::updateConfigOrderByInfo($config,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_NEW:
					Lecat_InstallUtils::installConfigByInfo($config,$module,$log);
					break;
				case LEGACY_INSTALLINFO_STATUS_DELETED:
					Lecat_InstallUtils::uninstallConfigByOrder($config->mOrder,$module,$log);
					break;
				default:
					break;
			}
		}
	}

	/**
	 * updateConfigByInfo
	 * 
	 * @param	Legacy_PreferenceInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function updateConfigByInfo(/*** Legacy_PreferenceInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('conf_modid',$module->get('mid')));
		$cri->add(new Criteria('conf_catid',0));
		$cri->add(new Criteria('conf_name',$info->mName));
		$configs =& $configHandler->getConfigs($cri);
	
		if(!(count($configs) > 0 && is_object($configs[0])))
		{
			$log->addError(_MI_LECAT_INSTALL_ERROR_CONFIG_NOT_FOUND);
			return false;
		}
	
		$config =& $configs[0];
		$config->set('conf_title',$info->mTitle);
		$config->set('conf_desc',$info->mDescription);
		if($config->get('conf_formtype') != $info->mFormType && $config->get('conf_valuetype') != $info->mValueType)
		{
			$config->set('conf_formtype',$info->mFormType);
			$config->set('conf_valuetype',$info->mValueType);
			$config->setConfValueForInput($info->mDefault);
		}
		else
		{
			$config->set('conf_formtype',$info->mFormType);
			$config->set('conf_valuetype',$info->mValueType);
		}
		$config->set('conf_order',$info->mOrder);
	
		$options =& $configHandler->getConfigOptions(new Criteria('conf_id',$config->get('conf_id')));
		if(is_array($options))
		{
			foreach($options as $opt)
			{
				$configHandler->_oHandler->delete($opt);  // TODO will be use other method
			}
		}
	
		if(count($info->mOption->mOptions) > 0)
		{
			foreach($info->mOption->mOptions as $opt)
			{
				$option =& $configHandler->createConfigOption();
				$option->set('confop_name',$opt->mName);
				$option->set('confop_value',$opt->mValue);
				$option->set('conf_id',$option->get('conf_id'));	// TODO check conf_id is right
				$config->setConfOptions($option);
				unset($option);
			}
		}
	
		if($configHandler->insertConfig($config))
		{
			$log->addReport(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_MSG_CONFIG_UPDATED,
					$config->get('conf_name')
				)
			);
			return true;
		}
	
		$log->addError(
			XCube_Utils::formatString(
				_MI_LECAT_INSTALL_ERROR_CONFIG_UPDATED,
				$config->get('conf_name')
			)
		);
		return false;
	}

	/**
	 * updateConfigOrderByInfo
	 * 
	 * @param	Legacy_PreferenceInformation  &$info
	 * @param	XoopsModule  &$module
	 * @param	Legacy_ModuleInstallLog  &$log
	 * 
	 * @return	bool
	**/
	public static function updateConfigOrderByInfo(/*** Legacy_PreferenceInformation ***/ &$info,/*** XoopsModule ***/ &$module,/*** Legacy_ModuleInstallLog ***/ &$log)
	{
		$configHandler =& Lecat_Utils::getXoopsHandler('config');
		$cri =new CriteriaCompo();
		$cri->add(new Criteria('conf_modid',$module->get('mid')));
		$cri->add(new Criteria('conf_catid',0));
		$cri->add(new Criteria('conf_name',$info->mName));
		$configs =& $configHandler->getConfigs($cri);
	
		if(!(count($configs) > 0 && is_object($configs[0])))
		{
			$log->addError(_MI_LECAT_INSTALL_ERROR_CONFIG_NOT_FOUND);
			return false;
		}
	
		$config =& $configs[0];
		$config->set('conf_order',$info->mOrder);
		if(!$configHandler->insertConfig($config))
		{
			$log->addError(
				XCube_Utils::formatString(
					_MI_LECAT_INSTALL_ERROR_CONFIG_UPDATED,
					$config->get('conf_name')
				)
			);
			return false;
		}
		return true;
	}
}

?>
