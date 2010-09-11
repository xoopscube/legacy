<?php
/**
 * @file
 * @package legacy
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class jQuery_ExternalLink extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Site.JQuery.AddFunction',array(&$this, 'addScript'));
    }

    public function addScript(&$jQuery)
    {
        $jQuery->addScript(
        '$("a[rel=\'external\']").click(function(){
window.open($(this).attr("href"));
return false;
});');
    }
}
?>
