<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyTheme
{
	var $mDirName=null;
	var $mFileName=null;
	var $ScreenShot=null;
	var $mManifesto=null;
	
	function LegacyTheme($dirName,$manifesto=null)
	{
		$this->mDirName=$dirName;
		if($manifesto!=null) {
			$this->initializeByManifesto($manifesto);
		}
	}
	
	function initializeByManifesto($manifesto)
	{
		//
		// TODO We must check url to guard against that an attacker triggers javascript with wrong theme.
		//
		$this->mManifesto=$manifesto;
		$this->ScreenShot=$manifesto['Theme']['ScreenShot'];
	}
}

class LegacyThemeHandler
{
	var $_mThemeList;

	function LegacyThemeHandler()
	{
		$this->_mThemeList=array();

		if($handler=opendir(XOOPS_THEME_PATH)) {
			while(($dir=readdir($handler))!==false) {
				if($dir=="." || $dir=="..") {
					continue;
				}

				$themeDir=XOOPS_THEME_PATH."/".$dir;
				if (is_dir($themeDir)) {
					$manifesto = array();
					if (file_exists($mnfFile = $themeDir . "/manifesto.ini.php")) {
						$iniHandler = new XCube_IniHandler($mnfFile, true);
						$manifesto = $iniHandler->getAllConfig();
					}
					
					if(count($manifesto) > 0) {
						//
						// If this system can use this theme, add this to list.
						//
						if(isset($manifesto['Manifesto']) && isset($manifesto['Manifesto']['Depends']) && $manifesto['Manifesto']['Depends'] == "Legacy_RenderSystem") {
							$this->_mThemeList[]=new LegacyTheme($dir,$manifesto);
						}
					}
					else {
						$file=$themeDir."/theme.html";
						if(file_exists($file)) {
							$this->_mThemeList[]=new LegacyTheme($dir);
						}
					}
				}
			}
			closedir($handler);
		}
	}

	function &enumAll()
	{
		return $this->_mThemeList;
	}
}

?>