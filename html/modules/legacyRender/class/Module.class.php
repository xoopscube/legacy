<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRender_Module extends Legacy_ModuleAdapter
{
    public function LegacyRender_Module(&$xoopsModule)
    {
        self::__construct($xoopsModule);
    }

    public function __construct(&$xoopsModule)
    {
        parent::__construct($xoopsModule);
        $this->mGetAdminMenu =new XCube_Delegate();
        $this->mGetAdminMenu->register('LegacyRender_Module.getAdminMenu');
    }
    
    public function getAdminMenu()
    {
        $menu = parent::getAdminMenu();
        $this->mGetAdminMenu->call(new XCube_Ref($menu));
        
        ksort($menu);
        
        return $menu;
    }
}
