<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname(__DIR__) . '/include/altsys_functions.php';

function b_altsys_admin_menu_show($options)
{
    $ret = [];
    global $xoopsUser;

    $mydirname = empty($options[0]) ? 'altsys' : $options[0];

    $this_template = empty($options[1]) ? 'db:' . $mydirname . '_block_admin_menu.html' : trim($options[1]);

    if (preg_match('/[^0-9a-zA-Z_-]/', $mydirname)) {
        die('Invalid mydirname');
    }
    if (!is_object(@$xoopsUser)) {
        return [];
    }

    // core type
    $coretype = altsys_get_core_type();

    // mid_selected
    if (is_object(@$GLOBALS['xoopsModule'])) {
        $mid_selected = $GLOBALS['xoopsModule']->getVar('mid');
        // for system->preferences
        if (1 == $mid_selected && 'preferences' == @$_GET['fct'] && 'showmod' == @$_GET['op'] && !empty($_GET['mod'])) {
            $mid_selected = (int)$_GET['mod'];
        }
    } else {
        $mid_selected = 0;
    }

    $db =& XoopsDatabaseFactory::getDatabaseConnection();

    (method_exists('MyTextSanitizer', 'sGetInstance') and $myts =& MyTextSanitizer::sGetInstance()) || $myts =& MyTextSanitizer::getInstance();

    $module_handler =& xoops_gethandler('module');

    $current_module =& $module_handler->getByDirname($mydirname);

    $config_handler =& xoops_gethandler('config');

    $current_configs = $config_handler->getConfigList($current_module->mid());

    $moduleperm_handler =& xoops_gethandler('groupperm');

    $admin_mids = $moduleperm_handler->getItemIds('module_admin', $xoopsUser->getGroups());

    $modules = $module_handler->getObjects(new Criteria('mid', '(' . implode(',', $admin_mids) . ')', 'IN'), true);

    $block = [
        'mydirname' => $mydirname,
        'mod_url' => XOOPS_URL . '/modules/' . $mydirname,
        'mod_imageurl' => XOOPS_URL . '/modules/' . $mydirname . '/' . $current_configs['images_dir'],
        'mod_config' => $current_configs,
    ];

    foreach ($modules as $mod) {

        $mid = (int)$mod->getVar('mid');

        $dirname = $mod->getVar('dirname');

        // Since XCL 2.3.x gigamaster add module icon
        $moduleIcon = '<img class="svg" src="'.XOOPS_URL.'/modules/'.$dirname.'/images/module_icon.svg" width="1em" height="1em" alt="module-icon">';
        $modinfo = $mod->getInfo();

        $submenus4assign = [];

        $adminmenu = [];

        $adminmenu4altsys = [];

        unset($adminmenu_use_altsys);

        @include XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . @$modinfo['adminmenu'];
        // from admin_menu.php etc.

        $adminmenu = array_merge($adminmenu, $adminmenu4altsys);

        foreach ($adminmenu as $sub) {
            $link = empty($sub['altsys_link']) ? $sub['link'] : $sub['altsys_link'];
            if (isset($sub['show']) && false === $sub['show']) {
                continue;
            }
            $submenus4assign[] = [
            'title' => $myts->makeTboxData4Show($sub['title']),
            'url' => XOOPS_URL . '/modules/' . $dirname . '/' . htmlspecialchars($link, ENT_QUOTES),
            ];
        }

        // for modules overriding Module.class.php (eg. Analyzer for XC)
        if (empty($submenus4assign) && defined('XOOPS_CUBE_LEGACY') && !empty($modinfo['cube_style'])) {

            $module_handler =& xoops_gethandler('module');

            $module =& $module_handler->get($mid);

            $moduleObj =& Legacy_Utils::createModule($module);

            $modinfo['adminindex'] = $moduleObj->getAdminIndex();

            $modinfo['adminindex_absolute'] = true;

            foreach ($moduleObj->getAdminMenu() as $sub) {
                if (false === @$sub['show']) {
                    continue;
                }
                $submenus4assign[] = [
                'title' => $myts->makeTboxData4Show($sub['title']),
                'url' => 0 === strncmp($sub['link'], 'http', 4) ? htmlspecialchars($sub['link'], ENT_QUOTES) : XOOPS_URL . '/modules/' . $dirname . '/' . htmlspecialchars($sub['link'], ENT_QUOTES),
                ];
            }
        } elseif (empty($adminmenu4altsys)) {

            // add preferences
            if ($mod->getVar('hasconfig') && !in_array($mod->getVar('dirname'), ['system', 'legacy'])) {
                $submenus4assign[] = [
                    'title' => _PREFERENCES,
                    'url' => htmlspecialchars(altsys_get_link2modpreferences($mid), ENT_QUOTES),
                ];
            }

            // add help
            if (defined('XOOPS_CUBE_LEGACY') && !empty($modinfo['help'])) {
                $submenus4assign[] = [
                    'title' => _HELP,
                    'url' => XOOPS_URL . '/modules/legacy/admin/index.php?action=Help&amp;dirname=' . $dirname,
                ];
            }
        }
        // Since XCL 2.3.x  gigamaster add module icon
        $module4assign = [
            'mid' => $mid,
            'dirname' => $dirname,
            'name' => $mod->getVar('name'),
            'icon' => $moduleIcon,
            'version_in_db' => sprintf('%.2f', $mod->getVar('version') / 100.0),
            'version_in_file' => sprintf('%.2f', $modinfo['version']),
            'description' => htmlspecialchars(@$modinfo['description'], ENT_QUOTES),
//            'image' => htmlspecialchars($modinfo['image'], ENT_QUOTES),
            'isactive' => $mod->getVar('isactive'),
            'hasmain' => $mod->getVar('hasmain'),
            'hasadmin' => $mod->getVar('hasadmin'),
            'hasconfig' => $mod->getVar('hasconfig'),
            'weight' => $mod->getVar('weight'),
            'adminindex' => htmlspecialchars(@$modinfo['adminindex'], ENT_QUOTES),
            'adminindex_absolute' => @$modinfo['adminindex_absolute'],
            'submenu' => $submenus4assign,
            //'selected' => $mid == $mid_selected ? true : false, TODO gigamaster check
            'selected' => $mid == $mid_selected,
            'dot_suffix' => $mid == $mid_selected ? 'selected_opened' : 'closed',
        ];
        $block['modules'][] = $module4assign;
    }

    require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';

    $tpl = new D3Tpl();

    $tpl->assign('block', $block);

    $ret['content'] = $tpl->fetch($this_template);

    return $ret;
}


function b_altsys_admin_menu_edit($options)
{
    $mydirname = empty($options[0]) ? 'd3forum' : $options[0];

    $this_template = empty($options[1]) ? 'db:' . $mydirname . '_block_admin_menu.html' : trim($options[1]);

    if (preg_match('/[^0-9a-zA-Z_-]/', $mydirname)) {
        die('Invalid mydirname');
    }

    $form = "
		<input type='hidden' name='options[0]' value='$mydirname'>
		<label for='this_template'>" . _MB_ALTSYS_THISTEMPLATE . "</label>&nbsp;:
		<input type='text' size='60' name='options[1]' id='this_template' value='" . htmlspecialchars($this_template, ENT_QUOTES) . "'>
		<br>
	\n";

    return $form;
}
