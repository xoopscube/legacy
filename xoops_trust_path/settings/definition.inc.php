<?php
/**
 *
 * @package Legacy
 * @version $Id: definition.inc.php, Syntax replace define with const v2.3.0 2021/05/15 @gigamaster XCL-PHP7
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 *
 */
// Enum
const XOOPS_SIDEBLOCK_LEFT      = 0;
const XOOPS_SIDEBLOCK_RIGHT     = 1;
const XOOPS_SIDEBLOCK_BOTH      = 2;
const XOOPS_CENTERBLOCK_LEFT    = 3;
const XOOPS_CENTERBLOCK_RIGHT   = 4;
const XOOPS_CENTERBLOCK_CENTER  = 5;
const XOOPS_CENTERBLOCK_ALL     = 6;
const XOOPS_BLOCK_INVISIBLE     = 0;
const XOOPS_BLOCK_VISIBLE       = 1;

const XOOPS_MATCH_START         = 0;
const XOOPS_MATCH_END           = 1;
const XOOPS_MATCH_EQUAL         = 2;
const XOOPS_MATCH_CONTAIN       = 3;

// Smarty
const SMARTY_DIR                = XOOPS_TRUST_PATH . "/libs/smarty/";
const XOOPS_COMPILE_PATH        = XOOPS_TRUST_PATH . "/templates_c";

// Path
const XOOPS_CACHE_PATH          = XOOPS_TRUST_PATH . "/cache";
const XOOPS_MODULE_PATH         = XOOPS_ROOT_PATH . "/modules";
const XOOPS_UPLOAD_PATH         = XOOPS_ROOT_PATH . "/uploads";
const XOOPS_THEME_PATH          = XOOPS_ROOT_PATH . "/themes";

// Library
const LIBRARY_PATH              = XOOPS_TRUST_PATH . "/libs";
const PEAR_PATH                 = XOOPS_TRUST_PATH . "/PEAR";
const VENDOR_PATH               = XOOPS_TRUST_PATH . "/vendor";

// URL
const XOOPS_MODULE_URL          = XOOPS_URL . "/modules";
const XOOPS_UPLOAD_URL          = XOOPS_URL . "/uploads";
const XOOPS_THEME_URL           = XOOPS_URL . "/themes";

const XOOPS_LEGACY_PROC_NAME    = "legacy";

// USER
const XCUBE_CORE_USER_MODULE_NAME = "user";
const XCUBE_CORE_USER_UTILS_CLASS = "UserAccountUtils";    // not use


const XCUBE_CORE_PM_MODULE_NAME = "pm";

const LEGACY_SYSTEM_COMMENT = 14;

//
// Name of the render-system used by the embedded template of XoopsForm.
//
const XOOPSFORM_DEPENDENCE_RENDER_SYSTEM = 'Legacy_RenderSystem';
