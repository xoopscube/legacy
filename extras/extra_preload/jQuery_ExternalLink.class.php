<?php
/**
 * @file
 * @package legacy
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_jQuery extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Site.JQuery.AddFunction',array(&$this, '_addScript'));
    }

    protected function _addScript(&$jQuery)
    {
        $jQuery->addScript(
        '$("a[rel=\'external\']").click(function(){
window.open($(this).attr("href"));
return false;
});');
    }
}
?>
