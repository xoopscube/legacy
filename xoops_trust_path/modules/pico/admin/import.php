<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once dirname(__DIR__) . '/include/main_functions.php';
require_once dirname(__DIR__) . '/include/common_functions.php';
require_once dirname(__DIR__) . '/include/transact_functions.php';
require_once dirname(__DIR__) . '/include/import_functions.php';
require_once dirname(__DIR__) . '/class/gtickets.php';

(method_exists('MyTextSanitizer', 'sGetInstance') and $myts = &MyTextSanitizer::sGetInstance()) || $myts = &(new MyTextSanitizer)->getInstance();

$db = XoopsDatabaseFactory::getDatabaseConnection();


$module_handler     = &xoops_gethandler('module');
$modules            = &$module_handler->getObjects();
$importable_modules = [];
foreach ($modules as $module) {
    $mid            = $module->getVar('mid');
    $dirname        = $module->getVar('dirname');
    $dirpath        = XOOPS_ROOT_PATH . '/modules/' . $dirname;
    $mytrustdirname = '';
    
    $tables = $module -> getInfo('tables');
    if (file_exists($dirpath . '/mytrustdirname.php')) {
        include $dirpath . '/mytrustdirname.php';
    }
    if ('pico' === $mytrustdirname && $dirname !== $mydirname) {
        // pico
        $importable_modules[$mid] = 'pico:' . $module->getVar('name') . " ($dirname	)";
    } else if (is_array($tables) && isset($tables[0]) && stripos(@$tables[0], 'tinycontent') !== false) {
        // tinyd
        $importable_modules[$mid] = 'tinyd:' . $module->getVar('name') . " ($dirname)";
	} else if ( stripos ( is_string ( @ $tables [ 0 ] ) ? @ $tables [ 0 ] : '' , 'tinycontent' ) !== false ) {
        $importable_modules[$mid] = 'smartsection:' .
        $module-> getVar('name') . "($dirname)";
    }

}

//
// transaction stage
//

if (! empty($_POST['do_import']) && ! empty($_POST['import_mid'])) {
    @set_time_limit(0);

    if (! $xoopsGTicket->check(true, 'pico_admin')) {
        redirect_header(XOOPS_URL . '/', 2, $xoopsGTicket->getErrors());
    }

    $import_mid = (int) @$_POST['import_mid'];
    if (empty($importable_modules[ $import_mid ])) {
        die(_MD_A_PICO_ERR_INVALIDMID);
    }
    [ $fromtype, ] = explode(':', $importable_modules[ $import_mid ]);
    switch ($fromtype) {
        case 'pico':
            pico_import_from_pico($mydirname, $import_mid);
            break;
        case 'tinyd':
            pico_import_from_tinyd($mydirname, $import_mid);
            break;
        case 'smartsection':
            pico_import_from_smartsection($mydirname, $import_mid);
            break;
    }

    redirect_header(XOOPS_URL . "/modules/$mydirname/admin/index.php?page=import", 1, _MD_A_PICO_MSG_IMPORTDONE);
    exit;
}

if (! empty($_POST['do_syncall'])) {
    @set_time_limit(0);

    if (! $xoopsGTicket->check(true, 'pico_admin')) {
        redirect_header(XOOPS_URL . '/', 2, $xoopsGTicket->getErrors());
    }

    pico_sync_all($mydirname);

    redirect_header(XOOPS_URL . "/modules/$mydirname/admin/index.php?page=import", 1, _MD_A_PICO_MSG_SYNCALLDONE);
    exit;
}

if (! empty($_POST['do_clearbodycache'])) {
    @set_time_limit(0);

    if (! $xoopsGTicket->check(true, 'pico_admin')) {
        redirect_header(XOOPS_URL . '/', 2, $xoopsGTicket->getErrors());
    }

    pico_clear_body_cache($mydirname);

    redirect_header(XOOPS_URL . "/modules/$mydirname/admin/index.php?page=import", 1, _MD_A_PICO_MSG_CLEARBODYCACHEDONE);
    exit;
}

//
// form stage
//

//
// display stage
//

xoops_cp_header();

include __DIR__ . '/mymenu.php';

$tpl = new XoopsTpl();

$tpl->assign(
    [
        'mydirname'           => $mydirname,
        'mod_name'            => $xoopsModule->getVar('name'),
        'mod_url'             => XOOPS_URL . '/modules/' . $mydirname,
        'mod_imageurl'        => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
        'mod_config'          => $xoopsModuleConfig,
        'import_from_options' => $importable_modules,
        'gticket_hidden'      => $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'pico_admin'),
    ]
);

$tpl->display('db:' . $mydirname . '_admin_import.html');

xoops_cp_footer();
