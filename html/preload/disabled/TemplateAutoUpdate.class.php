<?php
/**
 * XOOPSCube Preload - Template Auto Update
 * This site preload automatically reflects updated templates for XOOPSCube LegacyRender. 
 * Altsys also features a Template management functionality, and its installation is recommended!
 * 
 * @version XCL 2.5.0
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

//
// Specify the directory name (dirname) of the target module. 
// If this field is left empty, this preload will apply to all currently active modules.
//
define('TEMPLATEAUTOUPDATE_TARGET_DIRNAME', "");

//
// Specify the name of the target template set (tplset). 
// If this value is left empty, this preload will use the currently active tplset. 
// However, directly specifying "default" here will not update the system's default tplset.
//
define('TEMPLATEAUTOUPDATE_TARGET_TPLSET', "default");

class TemplateAutoUpdate extends XCube_ActionFilter
{
    function preBlockFilter()	
    {
        $modulelist = array();
        if (TEMPLATEAUTOUPDATE_TARGET_DIRNAME === "") {
            $handler = xoops_gethandler('module');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('isactive', 1));

            $modules = $handler->getObjects($criteria);

            foreach ($modules as $module) {
                $modulelist[] = $module->get('dirname');
            }
        } else {
            $modulelist[] = TEMPLATEAUTOUPDATE_TARGET_DIRNAME;
        }

        foreach ($modulelist as $dirname) {
            $handler = xoops_getmodulehandler('tplfile', 'legacyRender');
            $criteria = new CriteriaCompo();
            if (TEMPLATEAUTOUPDATE_TARGET_TPLSET === "") {
                if ($this->mRoot->mContext->mXoopsConfig['template_set'] == 'default') {
                    return;
                }

                $criteria->add(new Criteria('tpl_tplset', $this->mRoot->mContext->mXoopsConfig['template_set']));
            } else {
                $criteria->add(new Criteria('tpl_tplset', TEMPLATEAUTOUPDATE_TARGET_TPLSET));
            }
            
            $criteria->add(new Criteria('tpl_module', $dirname));
            
            $tplfiles = $handler->getObjects($criteria);
            foreach ($tplfiles as $tplfile)
            {
                $file = "";
                if ($tplfile->get('tpl_type') == 'module') {
                    $file = XOOPS_MODULE_PATH . "/" . $dirname . "/templates/" . $tplfile->get('tpl_file');
                }
                elseif ($tplfile->get('tpl_type') == 'block') {
                    $file = XOOPS_MODULE_PATH . "/" . $dirname . "/templates/blocks/" . $tplfile->get('tpl_file');
                }
                else {
                    continue;
                }
                
                if (!file_exists($file))
                    continue;
    
                $mtime = filemtime($file);
                
                if ($mtime > $tplfile->get('tpl_lastmodified') && $mtime > $tplfile->get('tpl_lastimported')) {
                    $tplfile->loadSource();
                    $tplfile->set('tpl_lastmodified', $mtime);
                    $tplfile->Source->set('tpl_source', file_get_contents($file));
                    if ($handler->insert($tplfile, true)) {
                        require_once XOOPS_ROOT_PATH . "/class/template.php";
                        $xoopsTpl = new XoopsTpl();
                        $xoopsTpl->clear_cache('db:' . $tplfile->get('tpl_file'));
                        $xoopsTpl->clear_compiled_tpl('db:' . $tplfile->get('tpl_file'));
                    }
                }
            }
        }

    }
}
