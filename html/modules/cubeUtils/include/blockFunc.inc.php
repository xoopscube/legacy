<?php
/**
 *
 * @package CubeUtils
 * @version $Id: xoops_version.php 1294 2008-01-31 05:32:20Z nobunobu $
 * @copyright Copyright 2006-2008 NobuNobuXOOPS Project <http://sourceforge.net/projects/nobunobuxoops/>
 * @author NobuNobu <nobunobu@nobunobu.com>
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
function cubeUtils_GetBlock($bid, $useCache=true) {
    $blockHandler =& xoops_gethandler('block');
    $blockObject =& $blockHandler->get($bid);
    if (!$blockObject) return false;
    $blockProcedure =& Legacy_Utils::createBlockProcedure($blockObject);
    if ($blockProcedure->prepare()) {
        $root=&XCube_Root::getSingleton();
        $controller = $root->mController;
        $context =& $root->mContext;

        $usedCacheFlag = false;
        $cacheInfo = null;
        
        if ($useCache) {
            if ($controller->isEnableCacheFeature() && $blockProcedure->isEnableCache()) {
                $cacheInfo =& $blockProcedure->createCacheInfo();
                
                $controller->mSetBlockCachePolicy->call(new XCube_Ref($cacheInfo));
                $filepath = $cacheInfo->getCacheFilePath();
                
                if ($cacheInfo->isEnableCache() && $controller->existActiveCacheFile($filepath, $blockProcedure->getCacheTime())) {
                    $content = $controller->loadCache($filepath);
                    if ($blockProcedure->isDisplay() && !empty($content)) {
                        $block['content'] = $content;
                    }
                        
                    $usedCacheFlag = true;
                }
            }
        }

        if (!$usedCacheFlag) {
            $blockProcedure->execute();

            $renderBuffer = null;
            if ($blockProcedure->isDisplay()) {
                $renderBuffer =& $blockProcedure->getRenderTarget();
                $block['content'] = $renderBuffer->getResult();
            }
            else {
                $renderBuffer = new XCube_RenderTarget();
            }
            if ($useCache) {
                if ($controller->isEnableCacheFeature() && $blockProcedure->isEnableCache() && is_object($cacheInfo) && $cacheInfo->isEnableCache()) {
                    $controller->cacheRenderTarget($cacheInfo->getCacheFilePath(), $renderBuffer);
                }
            }
        }
    }
    return $block;
}
?>
