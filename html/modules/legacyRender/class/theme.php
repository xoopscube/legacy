<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyRenderThemeObject extends XoopsSimpleObject
{
	var $mPackage = array();
	var $mActiveResource = true;
	
	function LegacyRenderThemeObject()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', true);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('tplset_id', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('enable_select', XOBJ_DTYPE_BOOL, '0', true);
	}
	
	function loadPackage()
	{
		$themeDir = XOOPS_THEME_PATH . "/" . $this->get('name');

		if (file_exists($mnfFile = $themeDir . "/manifesto.ini.php")) {
			$iniHandler = new XCube_IniHandler($mnfFile, true);
			$this->mPackage = $iniHandler->getAllConfig();
		}
					
		if (isset($this->mPackage['Manifesto'])) {
			//
			// If this system can use this theme, add this to list.
			//
			if (isset($this->mPackage['Manifesto']) && isset($this->mPackage['Manifesto']['Depends'])) {
				$this->mActiveResource = ($this->mPackage['Manifesto']['Depends'] == "Legacy_RenderSystem");
			}
		}
		else {
			$file = XOOPS_THEME_PATH . "/" . $this->get('name') . "/theme.html";
			$this->mActiveResource = file_exists($file);
		}
	}
	
	function isActiveResource()
	{
		return $this->mActiveResource;
	}
}

class LegacyRenderThemeHandler extends XoopsObjectGenericHandler
{
	var $mTable = "legacyrender_theme";
	var $mPrimary = "id";
	var $mClass = "LegacyRenderThemeObject";
	
	function &getByName($themeName)
	{
		$criteria = new Criteria('name', $themeName);
		$obj =& $this->getObjects($criteria);
		if (count($obj) > 0) {
			return $obj[0];
		}
		else {
			$obj =& $this->create();
			return $obj;
		}
	}

	/**
	 * Search themes that Legacy_RenderSystem can render in file system, then register by handler.
	 */	
	function searchThemes()
	{
		$themeList = array();
		
		if($handler=opendir(XOOPS_THEME_PATH)) {
			while(($dir=readdir($handler))!==false) {
				if($dir=="." || $dir=="..") {
					continue;
				}

				$themeDir=XOOPS_THEME_PATH."/".$dir;
				if(is_dir($themeDir)) {
					$manifesto = array();
					if (file_exists($mnfFile = $themeDir . "/manifesto.ini.php")) {
						$iniHandler = new XCube_IniHandler($mnfFile, true);
						$manifesto = $iniHandler->getAllConfig();
					}
					
					if(count($manifesto) > 0) {
						//
						// If this system can use this theme, add this to list.
						//
						if(isset($manifesto['Manifesto']) && isset($manifesto['Manifesto']['Depends']) && preg_match('/Legacy_RenderSystem(\s|,|$)/', $manifesto['Manifesto']['Depends'])) {
							$themeList[]=$dir;
						}
					}
					else {
						$file=$themeDir."/theme.html";
						if(file_exists($file)) {
							$themeList[]=$dir;
						}
					}
				}
			}
			closedir($handler);
		}
		
		return $themeList;
	}
	
	function updateThemeList()
	{
		$diskThemeNames = $this->searchThemes();
		$DBthemes =& $this->getObjects();

		//
		// At first, check new theme.
		//		
		foreach ($diskThemeNames as $name) {
			$findFlag = false;
			foreach ($DBthemes as $theme) {
				if ($theme->get('name') == $name) {
					$findFlag = true;
					break;
				}
			}
			
			//
			// If $findFlag is false, $name is new theme that is not registered to DB, yet.
			//
			if (!$findFlag) {
				$obj =& $this->create();
				$obj->set('name', $name);
				$this->insert($obj, true);
			}
		}
		
		//
		//	Next, check themes that we got from DB. If it had removed from disk system,
		// We also have to remove from DB.
		//
		foreach ($DBthemes as $theme) {
			if (!in_array($theme->get('name'), $diskThemeNames)) {
				$this->delete($theme, true);
			}
		}
	}
}

?>
