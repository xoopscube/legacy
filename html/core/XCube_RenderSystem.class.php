<?php
/**
 * XCube_RenderSystem.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      This is a target whom a render-system renders. This has a buffer and receives
 * a result of a render-system to the buffer. A developer can control rendering
 * BY using this class.
 *
 * We had to define classes that are XCube_RenderTargetBuffer, XCube_RenderTargetTheme,
 * XCube_RenderTargetBlock and XCube_RenderTargetMain. And, a render-system had
 * to define render-sub-system that renders to these render-target. However, this
 * style gives a heavy load to our XOOPSCube system that is a PHP application.
 *
 * We prepare the following constants for the flag of a render-target instead of
 * the group of many classes.
 */

define( 'XCUBE_RENDER_MODE_NORMAL', 1 );
define( 'XCUBE_RENDER_MODE_DIALOG', 2 );


define( 'XCUBE_RENDER_TARGET_TYPE_BUFFER', null );
define( 'XCUBE_RENDER_TARGET_TYPE_THEME', 'theme' );
define( 'XCUBE_RENDER_TARGET_TYPE_BLOCK', 'block' );
define( 'XCUBE_RENDER_TARGET_TYPE_MAIN', 'main' );


class XCube_RenderTarget {
	public $mName;

	public $mRenderBuffer;

	public $mModuleName;

	public $mTemplateName;

	public $mAttributes = [];

	/**
	 * @deprecated
	 */
	public $mType = XCUBE_RENDER_TARGET_TYPE_BUFFER;

	public $mCacheTime;

	public function __construct() {
	}

	public function setName( $name ) {
		$this->mName = $name;
	}

	public function getName() {
		return $this->mName;
	}

	public function setTemplateName( $name ) {
		$this->mTemplateName = $name;
	}

	public function getTemplateName() {
		return $this->mTemplateName;
	}

	public function setAttribute( $key, $value ) {
		$this->mAttributes[ $key ] = $value;
	}

	public function setAttributes( $attr ) {
		$this->mAttributes = $attr;
	}

	public function getAttribute( $key ) {
		return $this->mAttributes[$key] ?? null;
	}

	public function getAttributes() {
		return $this->mAttributes;
	}

	/**
	 * Set render-target type.
	 *
	 * @param int $type Use constants that are defined by us.
	 *
	 * @deprecated
	 */
	public function setType( $type ) {
		$this->mType = $type;
		$this->setAttribute( 'legacy_buffertype', $type );
	}

	/**
	 * Return render-target type.
	 * @return int
	 * @deprecated
	 */
	public function getType(): ?int
    {
		return $this->getAttribute( 'legacy_buffertype' );
		//return $this->mType;
	}

	public function setResult( &$result ) {
		$this->mRenderBuffer = $result;
	}

	public function getResult() {
		return $this->mRenderBuffer;
	}

	/**
	 * Reset a template name and attributes in own properties.
	 */
	public function reset() {
		$this->setTemplateName( null );
		unset( $this->mAttributes );
		$this->mAttributes   = [];
		$this->mRenderBuffer = null;
	}
}

/**
 * This system is responsible for rendering and managing the content cache.
 * For cache management, this system must interoperate with a business logic before it can be executed.
 * This class is poorly designed, so the template engine is heavily tied to cache management.
 * We need to split this class into two parts: the renderer and the cache management.
 */
class XCube_RenderSystem {
	/**
	 * @access private
	 */
	public $mController;

	public $mRenderMode = XCUBE_RENDER_MODE_NORMAL;

	public function __construct() {
	}

	/**
	 * Prepare.
	 *
	 * @param XCube_Controller $controller
	 */
	public function prepare( &$controller ) {
		$this->mController =& $controller;
	}

	/**
	 * Create an object of the render-target, and return it.
	 *
	 * @return XCube_RenderTarget
	 */
	public function &createRenderTarget() {
		$renderTarget = new XCube_RenderTarget();

		return $renderTarget;
	}

	/**
	 * Render to $target.
	 *
	 * @param XCube_RenderTarget $target
	 */
	public function render( &$target ) {
	}
}
