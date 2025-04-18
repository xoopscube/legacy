<?php
/**
 * Message module for private messages and forward to email
 * 
 * @package    Message
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2025 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once _MY_MODULE_PATH.'class/AbstractAction.class.php';

class ModController
{
    private $act;

    public function __construct()
    {
        $root = XCube_Root::getSingleton();
        $this->act = $root->mContext->mRequest->getRequest('action');
        if ('' == $this->act) {
            $this->act = 'index';
        }
        if (!preg_match("/^\w+$/", $this->act)) {
            exit('bad action name');
        }
    }

    public function execute($controller)
    {
        $className = $this->act.'Action';
        $fileName = _MY_MODULE_PATH.'actions/'.$className.'.class.php';
        if (!is_file($fileName)) {
            exit('file not found');
        }
        require $fileName;

        $Action = new $className($controller);
        $Action->execute();

        if ($Action->getisError()) {
            $controller->executeRedirect($Action->getUrl(), 2, $Action->geterrMsg());
        } else {
            $Action->executeView($controller->mRoot->mContext->mModule->getRenderTarget());
        }
    }
}
