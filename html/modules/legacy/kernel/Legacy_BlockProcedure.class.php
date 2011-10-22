<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_BlockProcedure.class.php,v 1.4 2008/09/25 15:11:56 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) die();

/**
 * The class for blocks which has interfaces to exchange informations with the
 * controller. The sub-class must implement these interfaces with helper
 * functions, to be called back by the controller.
 */
class Legacy_AbstractBlockProcedure
{
    /**
     * @var XCube_RenderTarget
     */
    var $mRender = null;
    
    function Legacy_AbstractBlockProcedure()
    {
    }
    
    /**
     * Preparation. If it's in exception case, returns false.
     * @return bool
     */
    function prepare()
    {
        return true;
    }
    
    /**
     * @var XCube_RenderTarget
     */
    function &getRenderTarget()
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
    function getRenderSystemName()
    {
        $root =& XCube_Root::getSingleton();
        return $root->mContext->mBaseRenderSystemName;
    }
    
    /**
     * Creates a instance of the render buffer, and set it to the property.
     * This is a helper function for sub-classes.
     * @access protected
     */
    function &_createRenderTarget()
    {
        $this->mRender = new XCube_RenderTarget();
        $this->mRender->setType(XCUBE_RENDER_TARGET_TYPE_BLOCK);
        
        return $this->mRender;
    }

    /**
     * Gets a number as ID.
     * @return int
     */
    function getId()
    {
    }

    /**
     * Gets a name of this block.
     * @return string
     */ 
    function getName()
    {
    }
    
    /**
     * Gets a value indicating whether the block can be cached.
     * @return bool
     */ 
    function isEnableCache()
    {
    }
    
    /**
     * Return cache time
     * @return int
     */
    function getCacheTime()
    {
    }

    /**
     * Gets a title of this block.
     * @return string
     */
    function getTitle()
    {
        return $this->_mBlock->get('title');
    }
    
    /**
     * Gets a column index of this block.
     * @return int
     */
    function getEntryIndex()
    {
    }
    
    /**
     * Gets a weight of this block.
     * @return int
     */
    function getWeight()
    {
    }

    /**
     * Gets a value indicating whether this block nees to display its content.
     * @return bool
     */
    function isDisplay()
    {
        return true;
    }
    
    function &createCacheInfo()
    {
        $cacheInfo = new Legacy_BlockCacheInformation();
        $cacheInfo->setBlock($this);
        return $cacheInfo;
    }
    
    function execute()
    {
    }
}

/**
 * This class extends the base class to exchange of information with the
 * controller. And, it has a XoopsBlock instance, and some public methods for
 * the public side and the control panel side.
 */
class Legacy_BlockProcedure extends Legacy_AbstractBlockProcedure
{
    /**
     * @var XoopsBlock
     */
    var $_mBlock = null;
    
    /**
     * @var XCube_RenderTarget
     */
    var $mRender = null;
    
    function Legacy_BlockProcedure(&$block)
    {
        $this->_mBlock =& $block;
    }
    
    function prepare()
    {
        return true;
    }
    
    function getId()
    {
        return $this->_mBlock->get('bid');
    }
    
    function getName()
    {
        return $this->_mBlock->get('name');
    }
    
    function isEnableCache()
    {
        return $this->_mBlock->get('bcachetime') > 0;
    }
    
    function getCacheTime()
    {
        return $this->_mBlock->get('bcachetime');
    }

    function getTitle()
    {
        return $this->_mBlock->get('title');
    }
    
    function getEntryIndex()
    {
        return $this->_mBlock->getVar('side');
    }
    
    function getWeight()
    {
        return $this->_mBlock->get('weight');
    }
    
    /**
     * @public
     * @breaf [Secret Agreement] Gets a value indicating whether the option form of this block needs the row to display the form.
     * @remark Only block management actions should use this method, and this method should not be overridden usually.
     */
    function _hasVisibleOptionForm()
    {
        return true;
    }
    
    /**
     * Gets rendered HTML buffer for the option form of the control panel.
     * @return string
     */
    function getOptionForm()
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
    var $_mDisplayFlag = true;
    
    function execute()
    {
        $result =& $this->_mBlock->buildBlock();
        
        if (empty($result)) {
            $this->_mDisplayFlag = false;
            return;
        }
        
        $render =& $this->getRenderTarget();
        $render->setAttribute("mid", $this->_mBlock->get('mid'));
        $render->setAttribute("bid", $this->_mBlock->get('bid'));
        
        if ($this->_mBlock->get('template') == null) {
            $render->setTemplateName('system_dummy.html');
            $render->setAttribute('dummy_content', $result['content']);
        }
        else {
            $render->setTemplateName($this->_mBlock->get('template'));
            $render->setAttribute('block', $result);
        }
        
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
        
        $renderSystem->renderBlock($render);
    }
    
    function isDisplay()
    {
        return $this->_mDisplayFlag;
    }

    function _hasVisibleOptionForm()
    {
        return ($this->_mBlock->get('func_file') && $this->_mBlock->get('edit_func'));
    }
    
    function getOptionForm()
    {
        if ($this->_mBlock->get('func_file') && $this->_mBlock->get('edit_func')) {
            $func_file = XOOPS_MODULE_PATH . "/" . $this->_mBlock->get('dirname') . "/blocks/" . $this->_mBlock->get('func_file');
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
            
            $buf = "";
            $options = explode('|', $this->_mBlock->get('options'));
            foreach ($options as $val) {
                $val = $textFilter->ToEdit($val);
                $buf .= "<input type='hidden' name='options[]' value='${val}'/>";
            }
            
            return $buf;
        }

        return null;
    }
}

?>
