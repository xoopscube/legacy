<?php
/**
 * Cache Clear Block
 * @package stdCache
 * @author  Kazuhisa Minato aka minahito, Core developer
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Stdcache_CacheclearBlock extends Legacy_BlockProcedure
{
    public $_mFilePath = null;
    
    public function prepare()
    {
        $this->_mFilePath = XOOPS_CACHE_PATH . '/' . md5(XOOPS_SALT) . '.cache.html';
        return parent::prepare();
    }
    
    public function execute()
    {
        $root =& XCube_Root::getSingleton();
    
        if (!$root->mController->isEnableCacheFeature()) {
            return false;
        }
        
        //
        // Check timer
        //
        $options = explode('|', $this->_mBlock->get('options'));
        if (!file_exists($this->_mFilePath)) {
            $this->updateTimer();
        }
        
        if (filemtime($this->_mFilePath) < time() - (int)$options[0] * 60) {
            if ($handler = opendir(XOOPS_CACHE_PATH)) {
                while (false !== ($file = readdir($handler))) {
                    if (preg_match("/\w+\.cache\.html$/", $file, $matches)) {
                        @unlink(XOOPS_CACHE_PATH . '/' . $matches[0]);
                    }
                }
                closedir($handler);
            }
            
            $this->updateTimer();
        }
    }
    
    public function updateTimer()
    {
        $fp = fopen($this->_mFilePath, 'wb');
        fclose($fp);
    }
    
    public function isDisplay()
    {
        return false;
    }

    public function getOptionForm()
    {
        $options = explode('|', $this->_mBlock->get('options'));
        
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem('Legacy_AdminRenderSystem');
        $renderTarget =& $renderSystem->createRenderTarget();
        
        $renderTarget->setAttribute('legacy_module', 'stdCache');
        $renderTarget->setTemplateName('block_cacheclear_option.html');
        $renderTarget->setAttribute('timer', $options[0]);

        $renderSystem->render($renderTarget);
        return $renderTarget->getResult();
    }
}
