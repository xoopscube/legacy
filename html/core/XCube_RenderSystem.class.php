<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_RenderSystem.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

define("XCUBE_RENDER_MODE_NORMAL",1);
define("XCUBE_RENDER_MODE_DIALOG",2);

/**
 * We had to define classes that are XCube_RenderTargetBuffer, XCube_RenderTargetTheme,
 * XCube_RenderTargetBlock and XCube_RenderTargetMain. And, a render-system had
 * to define render-sub-system that renders to these render-target. However, this
 * style gives a heavy load to our XOOPS Cube system that is a PHP application.
 *
 * We prepare the following constants for the flag of a render-target instead of
 * the group of many classes. 
 */
define("XCUBE_RENDER_TARGET_TYPE_BUFFER", null);
define("XCUBE_RENDER_TARGET_TYPE_THEME", 'theme');
define("XCUBE_RENDER_TARGET_TYPE_BLOCK", 'block');
define("XCUBE_RENDER_TARGET_TYPE_MAIN", 'main');

/**
 * This is a target whom a render-system renders. This has a buffer and receives
 * a result of a render-system to the buffer. A developer can control rendering
 * with using this class.
 */
class XCube_RenderTarget
{
	var $mName = null;

	var $mRenderBuffer = null;
	
	var $mModuleName = null;
	
	var $mTemplateName = null;

	var $mAttributes = array();
	
	/**
	 * @deprecated
	 */
	var $mType = XCUBE_RENDER_TARGET_TYPE_BUFFER;
	
	var $mCacheTime = null;
		
	function XCube_RenderTarget()
	{
	}

	function setName($name)
	{
		$this->mName = $name;
	}

	function getName()
	{
		return $this->mName;
	}
	
	function setTemplateName($name)
	{
		$this->mTemplateName = $name;
	}

	function getTemplateName()
	{
		return $this->mTemplateName;
	}
	
	function setAttribute($key,$value)
	{
		$this->mAttributes[$key] = $value;
	}
	
	function setAttributes($attr)
	{
		$this->mAttributes = $attr;
	}
	
	function getAttribute($key)
	{
		return isset($this->mAttributes[$key]) ? $this->mAttributes[$key] : null;
	}

	function getAttributes()
	{
		return $this->mAttributes;
	}
	
	/**
	 * Set render-target type.
	 * @param $type int Use constants that are defined by us.
	 * @deprecated
	 */
	function setType($type)
	{
		$this->mType = $type;
		$this->setAttribute('legacy_buffertype', $type);
	}
	
	/**
	 * Return render-target type.
	 * @return int
	 * @deprecated
	 */
	function getType()
	{
		return $this->getAttribute('legacy_buffertype', $type);
		//return $this->mType;
	}
	
	function setResult(&$result)
	{
		$this->mRenderBuffer = $result;
	}
	
	function getResult()
	{
		return $this->mRenderBuffer;
	}
	
	/**
	 * Reset a template name and attributes in own properties.
	 */
	function reset()
	{
		$this->setTemplateName(null);
		unset($this->mAttributes);
		$this->mAttributes = array();
		$this->mRenderBuffer = null;
	}
}

/**
 * This system is in charge of rendering and contents cache management.
 * For cache management, this system must talk with a business logic before business logic executes.
 * This class has a bad design so that the template engine is strongly tied to cache management.
 * We must divide this class into renderer and cache management.
 */
class XCube_RenderSystem
{
	/**
	 @access private
	 */
	var $mController;

	var $mRenderMode = XCUBE_RENDER_MODE_NORMAL;
	
	function XCube_RenderSystem()
	{
	}
	
	/**
	 * Prepare.
	 *
	 * @param XCube_Controller $controller
	 */
	function prepare(&$controller)
	{
		$this->mController =& $controller;
	}
	
	/**
	 * Create an object of the render-target, and return it.
	 *
	 * @return XCube_RenderTarget
	 */
	function &createRenderTarget()
	{
		$renderTarget = new XCube_RenderTarget();
		return $renderTarget;
	}

	/**
	 * Render to $target.
	 *
	 * @param XCube_RenderTarget $target
	 */
	function render(&$target)
	{
	}
}

?>