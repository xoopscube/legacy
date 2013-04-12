<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit();
}

/**
 * Lecat_ListBlock
**/
class Lecat_ListBlock extends Legacy_BlockProcedure
{
    /**
     * @var Lecat_CatHandler
     * 
     * @private
    **/
    var $_mHandler = null;
    
    /**
     * @var Lecat_CatObject
     * 
     * @private
    **/
    var $_mObject = null;
    
    /**
     * @var string[]
     * 
     * @private
    **/
    var $_mOptions = array();
    
    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  bool
     * 
     * @public
    **/
    public function prepare()
    {
        return parent::prepare() && $this->_parseOptions() && $this->_setupObject();
    }
    
    /**
     * _parseOptions
     * 
     * @param   void
     * 
     * @return  bool
     * 
     * @private
    **/
    protected function _parseOptions()
    {
        $opts = explode('|',$this->_mBlock->get('options'));
        $this->_mOptions = array(
            'parent_id'	=> (intval($opts[0])>0 ? intval($opts[0]) : 0),
        );
        return true;
    }
    
    /**
     * getBlockOption
     * 
     * @param   string  $key
     * 
     * @return  string
     * 
     * @public
    **/
    public function getBlockOption($key)
    {
        return isset($this->_mOptions[$key]) ? $this->_mOptions[$key] : null;
    }
    
    /**
     * getOptionForm
     * 
     * @param   void
     * 
     * @return  string
     * 
     * @public
    **/
    public function getOptionForm()
    {
        if(!$this->prepare())
        {
            return null;
        }
		$form = '<label for="'. $this->_mBlock->get('dirname') .'block_parent_id">'._AD_LECAT_LANG_PARENT_ID.'</label>&nbsp;:
		<input type="text" size="5" name="options[0]" id="'. $this->_mBlock->get('dirname') .'block_parent_id" value="'.$this->getBlockOption('parent_id').'" />';
		return $form;
    }

    /**
     * _setupObject
     * 
     * @param   void
     * 
     * @return  bool
     * 
     * @private
    **/
    protected function _setupObject()
    {
        $categoryIds = null;

        //get block options
        $parentId = $this->getBlockOption('parent_id');
    
        //get module asset for handlers
        $asset = null;
        XCube_DelegateUtils::call(
            'Module.lecat.Global.Event.GetAssetManager',
            new XCube_Ref($asset),
            $this->_mBlock->get('dirname')
        );
    
        $this->_mHandler =& $asset->getObject('handler','cat');
        $this->_mObject = $this->_mHandler->getTree($parentId);

        return true;
    }

    /**
     * execute
     * 
     * @param   void
     * 
     * @return  void
     * 
     * @public
    **/
    function execute()
    {
        $root = XCube_Root::getSingleton();
    
        $render = $this->getRenderTarget();
        $render->setTemplateName($this->_mBlock->get('template'));
        $render->setAttribute('block', $this->_mObject);
        $render->setAttribute('dirname', $this->_mBlock->get('dirname'));
        $render->setAttribute('parentId', $this->getBlockOption('parent_id'));
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
        $renderSystem->renderBlock($render);
    }
}

?>
