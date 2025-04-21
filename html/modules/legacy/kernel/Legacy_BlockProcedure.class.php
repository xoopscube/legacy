<?php
/**
 *
 * @package    Legacy
 * @author     Nobuhiro YASUTOMI, PHP8
 * @version    $Id: Legacy_BlockProcedure.class.php,v 1.4 2008/09/25 15:11:56 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    die();
}

/**
 * The class for blocks which has interfaces to exchange information with the
 * controller. The sub-class must implement these interfaces with helper
 * functions, to be called back by the controller.
 */
class Legacy_AbstractBlockProcedure
{
    /**
     * @var XCube_RenderTarget
     */
    public $mRender = null;

    public function __construct()
    {
    }

    /**
     * Preparation. If it's in exception case, returns false.
     * @return bool
     */
    public function prepare()
    {
        return true;
    }

    /**
     * @return \XCube_RenderTarget
     * @var XCube_RenderTarget
     */
    public function &getRenderTarget()
    {
        if (!is_object($this->mRender)) {
            $this->_createRenderTarget();
        }

        return $this->mRender;
    }

    /**
     * Gets a name of the dependence render-system.
     * @return string
     */
    public function getRenderSystemName()
    {
        $root =& XCube_Root::getSingleton();
        return $root->mContext->mBaseRenderSystemName;
    }

    /**
     * Creates an instance of the render buffer, and set it to the property.
     * This is a helper function for sub-classes.
     * @access protected
     */
    public function &_createRenderTarget()
    {
        $this->mRender = new XCube_RenderTarget();
        $this->mRender->setType(XCUBE_RENDER_TARGET_TYPE_BLOCK);

        return $this->mRender;
    }

    /**
     * Gets the ID number.
     * @return void
     */
    public function getId()
    {
    }

    /**
     * Gets the name of this block.
     * @return string
     */
    public function getName()
    {
    }

    /**
     * Gets a value indicating whether the block can be cached.
     * @return void
     */
    public function isEnableCache()
    {
    }

    /**
     * Return cache time
     * @return void
     */
    public function getCacheTime()
    {
    }

    /**
     * Gets a title of this block.
     * @return string
     */
    public function getTitle()
    {
        return $this->_mBlock->get('title');
    }

    // TODO @gigamaster gettemplate (block dropdown edit)
    public function getTemplate()
    {
       // return $this->_mBlock->get('template');
    }
    /**
     * Gets a column index of this block.
     * @return void
     */
    public function getEntryIndex()
    {
    }

    /**
     * Gets a weight of this block.
     * @return void
     */
    public function getWeight()
    {
    }

    /**
     * Gets a value indicating whether this block needs to display its content.
     * @return bool
     */
    public function isDisplay()
    {
        return true;
    }

    public function &createCacheInfo()
    {
        $cacheInfo = new Legacy_BlockCacheInformation();
        $cacheInfo->setBlock($this);
        return $cacheInfo;
    }

    public function execute()
    {
    }
}

/**
 * This class extends the base class to exchange of information with the
 * controller. And, it has a XoopsBlock instance, and some public methods
 * for the public side and the control panel side.
 */
class Legacy_BlockProcedure extends Legacy_AbstractBlockProcedure
{
    /**
     * @var XoopsBlock
     */
    public $_mBlock = null;

    /**
     * @var XCube_RenderTarget
     */
    public $mRender = null;

    public function __construct(&$block)
    {
        $this->_mBlock =& $block;
    }

    public function prepare()
    {
        return true;
    }

    public function getId()
    {
        return $this->_mBlock->get('bid');
    }

    public function getName()
    {
        return $this->_mBlock->get('name');
    }

    public function isEnableCache()
    {
        return $this->_mBlock->get('bcachetime') > 0;
    }

    public function getCacheTime()
    {
        return $this->_mBlock->get('bcachetime');
    }

    public function getTitle()
    {
        return $this->_mBlock->get('title');
    }

    // @gigamaster gettemplate (block dropdown edit)
    public function getTemplate()
    {
        return $this->_mBlock->get('template');
    }

    public function getEntryIndex()
    {
        return $this->_mBlock->getVar('side');
    }

    public function getWeight()
    {
        return $this->_mBlock->get('weight');
    }

    /**
     * @public
     * @breaf [Secret Agreement] Gets a value indicating whether the option form of this block needs the row to display the form.
     * @remark Only block management actions should use this method, and generally this method should not be replaced.
     */
    public function _hasVisibleOptionForm()
    {
        return true;
    }

    /**
     * Gets rendered HTML buffer for the option form of the control panel.
     * @return string
     */
    public function getOptionForm()
    {
        return null;
    }
}

/**
 * The adapter class for XoopsBlock objects of XOOPS2 JP.
 * @see Legacy_AbstractBlockProcedure
 */
class Legacy_BlockProcedureAdapter extends Legacy_BlockProcedure
{
    public $_mDisplayFlag = true;

    public function execute()
    {

        $result =& $this->_mBlock->buildBlock();

        if (empty($result)) {
            $this->_mDisplayFlag = false;
            return;
        }

        $render =& $this->getRenderTarget();
        $render->setAttribute('mid', $this->_mBlock->get('mid'));
        $render->setAttribute('bid', $this->_mBlock->get('bid'));


        if (null == $this->_mBlock->get('template')) {
            $render->setTemplateName('system_dummy.html');
            $render->setAttribute('dummy_content', $result['content']);
        } else {
            $render->setTemplateName($this->_mBlock->get('template'));
            $render->setAttribute('block', $result);
        }

        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

        $renderSystem->renderBlock($render);
    }

    public function isDisplay()
    {
        return $this->_mDisplayFlag;
    }

    public function _hasVisibleOptionForm()
    {
        return ($this->_mBlock->get('func_file') && $this->_mBlock->get('edit_func'));
    }

    public function getOptionForm()
    {
        if ($this->_mBlock->get('func_file') && $this->_mBlock->get('edit_func')) {
            $func_file = XOOPS_MODULE_PATH . '/' . $this->_mBlock->get('dirname') . '/blocks/' . $this->_mBlock->get('func_file');
            if (file_exists($func_file)) {
                require $func_file;
                $edit_func = $this->_mBlock->get('edit_func');

                $options = explode('|', $this->_mBlock->get('options'));

                if (function_exists($edit_func)) {
                    //
                    // load language file.
                    //
                    $root =& XCube_Root::getSingleton();
                    $langManager =& $root->getLanguageManager();
                    $langManager->loadBlockMessageCatalog($this->_mBlock->get('dirname'));

                    return call_user_func($edit_func, $options);
                }
            }
        }

        //
        // The block may have options, even it doesn't have end_func
        //
        if ($this->_mBlock->get('options')) {
            $root =& XCube_Root::getSingleton();
            $textFilter =& $root->getTextFilter();

            $buf = '';
            $options = explode('|', $this->_mBlock->get('options'));
            foreach ($options as $val) {
                $val = $textFilter->ToEdit($val);
                $buf .= "<input type='hidden' name='options[]' value='{$val}'>";
            }

            return $buf;
        }

        return null;
    }
}
