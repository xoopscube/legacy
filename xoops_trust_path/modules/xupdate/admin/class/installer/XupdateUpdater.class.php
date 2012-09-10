<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateInstallUtils.class.php';

/**
 * Xupdate_Updater
**/
class Xupdate_Updater
{
    public /*** Legacy_ModuleInstallLog ***/ $mLog = null;

    private /*** string[] ***/ $_mMileStone = array(
    		'006' => 'update006',
    		'011' => 'update011',
    		'022' => 'update022'
    	);

    private /*** XoopsModule ***/ $_mCurrentXoopsModule = null;

    private /*** XoopsModule ***/ $_mTargetXoopsModule = null;

    private /*** int ***/ $_mCurrentVersion = 0;

    private /*** int ***/ $_mTargetVersion = 0;

    private /*** bool ***/ $_mForceMode = false;
	
    private function update006()
    {
    	$this->mLog->addReport('DB upgrade start (for Ver 0.06)');
    	
    	// Update database table index.
    	$root =& XCube_Root::getSingleton();
    	$db =& $root->mController->getDB();
    	$table = $db->prefix($this->_mCurrentXoopsModule->get('dirname') . '_modulestore');
    	 
    	$sql = 'SELECT `isactive` FROM '.$table ;
    	if(! $db->query($sql)) {
    		$sql = 'ALTER TABLE `'.$table.'` ADD `isactive` int(11) NOT NULL DEFAULT \'-1\'';
    		if ($db->query($sql)) {
    			$this->mLog->addReport('Success updated '.$table.' - ADD isactive');
    		} else {
    			$this->mLog->addError('Error update '.$table.' - ADD isactive');
    		}
    	}
    	 
    	$sql = 'SELECT `hasupdate` FROM '.$table ;
    	if(! $db->query($sql)) {
    		$sql = 'ALTER TABLE `'.$table.'` ADD `hasupdate` tinyint(1) NOT NULL DEFAULT \'0\'';
    		if ($db->query($sql)) {
    			$this->mLog->addReport('Success updated '.$table.' - ADD hasupdate');
    		} else {
    			$this->mLog->addError('Error update '.$table.' - ADD hasupdate');
    		}
    	}
    	
    	if (!$this->_mForceMode && $this->mLog->hasError())
    	{
    		$this->_processReport();
    		return false;
    	}
    	
    	return true;
    }
    
    private function update011()
    {
    	$this->mLog->addReport('DB upgrade start (for Ver 0.11)');
    	 
    	// Update database table index.
    	$root =& XCube_Root::getSingleton();
    	$db =& $root->mController->getDB();
    	$table = $db->prefix($this->_mCurrentXoopsModule->get('dirname') . '_modulestore');
    
    	$sql = 'SELECT `contents` FROM '.$table ;
    	if(! $db->query($sql)) {
    		$sql = 'ALTER TABLE `'.$table.'` ADD `contents` varchar(255) NOT NULL default \'\'';
    		if ($db->query($sql)) {
    			$this->mLog->addReport('Success updated '.$table.' - ADD contents');
    		} else {
    			$this->mLog->addError('Error update '.$table.' - ADD contents');
    		}
    		$sql = 'ALTER TABLE `'.$table.'` ADD INDEX ( `contents` )';
    		if ($db->query($sql)) {
    			$this->mLog->addReport('Success updated '.$table.' - ADD Index contents');
    		} else {
    			$this->mLog->addError('Error update '.$table.' - ADD Index contents');
    		}
    	}
    	 
    	if (!$this->_mForceMode && $this->mLog->hasError())
    	{
    		$this->_processReport();
    		return false;
    	}
    	 
    	return true;
    }

    private function update022()
    {
    	$this->mLog->addReport('DB upgrade start (for Ver 0.22)');
    
    	// Update database table index.
    	$root =& XCube_Root::getSingleton();
    	$db =& $root->mController->getDB();
    	$table = $db->prefix($this->_mCurrentXoopsModule->get('dirname') . '_modulestore');
    
   		$sql = 'ALTER TABLE `'.$table.'` CHANGE `dirname` `dirname` varchar(255) NOT NULL default \'\'';
    	if ($db->query($sql)) {
    		$this->mLog->addReport('Success updated '.$table.' - `dirname` VARCHAR( 255 )');
    	} else {
    		$this->mLog->addError('Error update '.$table.' - `dirname` VARCHAR( 255 )');
    	}
    	$sql = 'ALTER TABLE `'.$table.'` CHANGE `trust_dirname` `trust_dirname` varchar(255) default \'\'';
    	if ($db->query($sql)) {
    		$this->mLog->addReport('Success updated '.$table.' - `trust_dirname` VARCHAR( 255 )');
    	} else {
    		$this->mLog->addError('Error update '.$table.' - `trust_dirname` VARCHAR( 255 )');
    	}
    
    	if (!$this->_mForceMode && $this->mLog->hasError())
    	{
    		$this->_processReport();
    		return false;
    	}
    
    	return true;
    }
    
    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->mLog = new Legacy_ModuleInstallLog();
    }

    /**
     * setForceMode
     * 
     * @param   bool  $isForceMode
     * 
     * @return  void
    **/
    public function setForceMode(/*** bool ***/ $isForceMode)
    {
        $this->_mForceMode = $isForceMode;
    }

    /**
     * setCurrentXoopsModule
     * 
     * @param   XoopsModule  &$module
     * 
     * @return  void
    **/
    public function setCurrentXoopsModule(/*** XoopsModule ***/ &$module)
    {
        $moduleHandler =& Xupdate_Utils::getXoopsHandler('module');
        $cloneModule =& $moduleHandler->create();
    
        $cloneModule->unsetNew();
        $cloneModule->set('mid',$module->get('mid'));
        $cloneModule->set('name',$module->get('name'));
        $cloneModule->set('version',$module->get('version'));
        $cloneModule->set('last_update',$module->get('last_update'));
        $cloneModule->set('weight',$module->get('weight'));
        $cloneModule->set('isactive',$module->get('isactive'));
        $cloneModule->set('dirname',$module->get('dirname'));
        //$cloneModule->set('trust_dirname',$module->get('trust_dirname'));
        $cloneModule->set('hasmain',$module->get('hasmain'));
        $cloneModule->set('hasadmin',$module->get('hasadmin'));
        $cloneModule->set('hasconfig',$module->get('hasconfig'));
    
        $this->_mCurrentXoopsModule =& $cloneModule;
        $this->_mCurrentVersion = $cloneModule->get('version');
    }

    /**
     * setTargetXoopsModule
     * 
     * @param   XoopsModule  &$module
     * 
     * @return  void
    **/
    public function setTargetXoopsModule(/*** XoopsModule ***/ &$module)
    {
        $this->_mTargetXoopsModule =& $module;
        $this->_mTargetVersion = $this->getTargetPhase();
    }

    /**
     * getCurrentVersion
     * 
     * @param   void
     * 
     * @return  int
    **/
    public function getCurrentVersion()
    {
        return intval($this->_mCurrentVersion);
    }

    /**
     * getTargetPhase
     * 
     * @param   void
     * 
     * @return  int
    **/
    public function getTargetPhase()
    {
        ksort($this->_mMileStone);
    
        foreach($this->_mMileStone as $tVer => $tMethod)
        {
            if($tVer > $this->getCurrentVersion())
            {
                return intval($tVer);
            }
        }
    
        return $this->_mTargetXoopsModule->get('version');
    }

    /**
     * hasUpgradeMethod
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function hasUpgradeMethod()
    {
        ksort($this->_mMileStone);
    
        foreach($this->_mMileStone as $tVer => $tMethod)
        {
            if($tVer > $this->getCurrentVersion() && is_callable(array($this,$tMethod)))
            {
                return true;
            }
        }
    
        return false;
    }

    /**
     * isLatestUpgrade
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function isLatestUpgrade()
    {
        return ($this->_mTargetXoopsModule->get('version') == $this->getTargetPhase());
    }

    /**
     * _updateModuleTemplates
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _updateModuleTemplates()
    {
        Xupdate_InstallUtils::uninstallAllOfModuleTemplates($this->_mTargetXoopsModule,$this->mLog);
        Xupdate_InstallUtils::installAllOfModuleTemplates($this->_mTargetXoopsModule,$this->mLog);
    }

    /**
     * _updateBlocks
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _updateBlocks()
    {
        Xupdate_InstallUtils::smartUpdateAllOfBlocks($this->_mTargetXoopsModule,$this->mLog);
    }

    /**
     * _updatePreferences
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _updatePreferences()
    {
        Xupdate_InstallUtils::smartUpdateAllOfConfigs($this->_mTargetXoopsModule,$this->mLog);
    }

    /**
     * executeUpgrade
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function executeUpgrade()
    {
    	// remove modules.ini cache
    	$root =& XCube_Root::getSingleton();
    	$ch =& xoops_gethandler('config');
    	$mconf = $ch->getConfigsByDirname($this->_mCurrentXoopsModule->get('dirname'));
    	$cdir = XOOPS_TRUST_PATH . '/'.trim($mconf['temp_path'], '/');
    	if (is_dir($cdir)) {
    		if ($dh = opendir($cdir)) {
    			while (($file = readdir($dh)) !== false) {
    				if (substr($file, -8) === '.ini.php') {
    					if (@ unlink($cdir.'/'.$file)) {
    						$this->mLog->addReport('Deleted cache "'.$file.'" OK.');
    					}
    				}
    			}
    			closedir($dh);
    		}
    	}
    	return ($this->hasUpgradeMethod() ?
            $this->_callUpgradeMethod() :
            $this->executeAutomaticUpgrade());
    }

    /**
     * _callUpgradeMethod
     * 
     * @param   void
     * 
     * @return  bool
    **/
    private function _callUpgradeMethod()
    {
        ksort($this->_mMileStone);
    
        foreach($this->_mMileStone as $tVer => $tMethod)
        {
            if($tVer > $this->getCurrentVersion() && is_callable(array($this,$tMethod)))
            {
                if (! $this->$tMethod()) {
                	return false;
                }
            }
        }
    
        return $this->executeAutomaticUpgrade();
    }

    /**
     * executeAutomaticUpgrade
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function executeAutomaticUpgrade()
    {
        $this->mLog->addReport(_MI_XUPDATE_INSTALL_MSG_UPDATE_STARTED);
    
        $this->_updateModuleTemplates();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_updateBlocks();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_updatePreferences();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->saveXoopsModule($this->_mTargetXoopsModule);
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_processReport();
    
        return true;
    }

    /**
     * saveXoopsModule
     * 
     * @param   XoopsModule  &$module
     * 
     * @return  void
    **/
    public function saveXoopsModule(/*** XoopsModule ***/ &$module)
    {
        $moduleHandler =& Xupdate_Utils::getXoopsHandler('module');
    
        if($moduleHandler->insert($module))
        {
            $this->mLog->addReport(_MI_XUPDATE_INSTALL_MSG_UPDATE_FINISHED);
        }
        else
        {
            $this->mLog->addError(_MI_XUPDATE_INSTALL_ERROR_UPDATE_FINISHED);
        }
    }

    /**
     * _processReport
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _processReport()
    {
        if(!$this->mLog->hasError())
        {
            $this->mLog->add(
                XCube_Utils::formatString(
                    _MI_XUPDATE_INSTALL_MSG_MODULE_UPDATED,
                    $this->_mCurrentXoopsModule->get('name')
                )
            );
        }
        else
        {
            $this->mLog->add(
                XCube_Utils::formatString(
                    _MI_XUPDATE_INSTALL_ERROR_MODULE_UPDATED,
                    $this->_mCurrentXoopsModule->get('name')
                )
            );
        }
    }
}

?>
