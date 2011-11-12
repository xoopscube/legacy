<?php
/**
 * @version $Id: Legacy_RenderTarget.class.php,v 1.1 2007/05/15 02:35:07 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

define("LEGACY_RENDER_TARGET_TYPE_BUFFER", null);
define("LEGACY_RENDER_TARGET_TYPE_THEME", 'theme');
define("LEGACY_RENDER_TARGET_TYPE_BLOCK", 'block');
define("LEGACY_RENDER_TARGET_TYPE_MAIN", 'main');

class Legacy_AbstractThemeRenderTarget extends XCube_RenderTarget
{
	var $mSendHeaderFlag=false;

	function Legacy_AbstractThemeRenderTarget()
	{
		parent::XCube_RenderTarget();
		$this->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_THEME);
	}

	function sendHeader()
	{
		header('Content-Type:text/html; charset='._CHARSET);
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

	function setResult($result)
	{
		parent::setResult($result);
		if(!$this->mSendHeaderFlag) {
			$this->sendHeader();
			$this->mSendHeaderFlag=true;
		}

		print $result;
	}
}

class Legacy_ThemeRenderTarget extends Legacy_AbstractThemeRenderTarget
{
	function Legacy_ThemeRenderTarget()
	{
		parent::Legacy_AbstractThemeRenderTarget();
		$this->setAttribute("isFileTheme",true);
	}
}

class Legacy_DialogRenderTarget extends Legacy_AbstractThemeRenderTarget
{
	function Legacy_DialogRenderTarget()
	{
		parent::Legacy_AbstractThemeRenderTarget();
		$this->setAttribute("isFileTheme",false);
	}
	
	function getTemplateName()
	{
		return "legacy_render_dialog.html";
	}

}

class Legacy_RenderTargetMain extends XCube_RenderTarget
{
	function Legacy_RenderTargetMain()
	{
		parent::XCube_RenderTarget();
		$this->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_MAIN);
	}
}

?>